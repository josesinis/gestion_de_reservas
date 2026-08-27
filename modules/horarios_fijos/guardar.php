<?php
//=====================================================
// HORARIOS FIJOS - GUARDAR
//
// Guarda un nuevo horario fijo y genera sus
// ocurrencias correspondientes.
//=====================================================

session_start();

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// VALIDAR MÉTODO DE ENVÍO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit();
}


//=====================================================
// RECIBIR DATOS
//=====================================================

$diaSemana = (int) ($_POST['dia_semana'] ?? 0);

$bloqueId = (int) ($_POST['bloque_id'] ?? 0);

$tipo = trim($_POST['tipo'] ?? '');

$modalidad = trim($_POST['modalidad'] ?? '');

$docenteId = (int) ($_POST['docente_id'] ?? 0);

$cursoId = (int) ($_POST['curso_id'] ?? 0);

$asignaturaId = !empty($_POST['asignatura_id'])
    ? (int) $_POST['asignatura_id']
    : null;

$fechaInicio = trim($_POST['fecha_inicio'] ?? '');

$fechaFin = !empty($_POST['fecha_fin'])
    ? trim($_POST['fecha_fin'])
    : null;


//=====================================================
// VALIDACIONES
//=====================================================

$errores = [];


//=====================================================
// VALIDAR DÍA
//=====================================================

if ($diaSemana < 1 || $diaSemana > 5) {

    $errores[] =
        'El día seleccionado no es válido.';
}


//=====================================================
// VALIDAR BLOQUE
//=====================================================

$bloque = obtenerBloque(
    $conexion,
    $bloqueId
);

if (!$bloque) {

    $errores[] =
        'El bloque seleccionado no existe.';
}


//=====================================================
// VALIDAR TIPO
//=====================================================

$tiposPermitidos = [
    'completo',
    'sub1',
    'sub2'
];

if (!in_array($tipo, $tiposPermitidos, true)) {

    $errores[] =
        'El tipo de horario no es válido.';
}


//=====================================================
// VALIDAR MODALIDAD
//=====================================================

$modalidadesPermitidas = [
    'asignatura',
    'taller'
];

if (!in_array($modalidad, $modalidadesPermitidas, true)) {

    $errores[] =
        'La modalidad seleccionada no es válida.';
}


//=====================================================
// VALIDAR DOCENTE
//=====================================================

$stmt = $conexion->prepare("
    SELECT id
    FROM docentes
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param(
    "i",
    $docenteId
);

$stmt->execute();

$resultado = $stmt->get_result();

if (!$resultado->fetch_assoc()) {

    $errores[] =
        'El docente seleccionado no existe.';
}

$stmt->close();


//=====================================================
// VALIDAR CURSO
//=====================================================

$stmt = $conexion->prepare("
    SELECT
        id,
        modalidad
    FROM cursos
    WHERE id = ?
    LIMIT 1
");

if (!$stmt) {

    $errores[] =
        'No fue posible validar el curso.';

} else {

    $stmt->bind_param(
        "i",
        $cursoId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $curso = $resultado->fetch_assoc();

    if (!$curso) {

        $errores[] =
            'El curso seleccionado no existe.';

    } elseif (
        $curso['modalidad'] !== $modalidad
    ) {

        $errores[] =
            'El curso seleccionado no corresponde a la modalidad elegida.';
    }

    $stmt->close();
}


//=====================================================
// VALIDAR ASIGNATURA
//=====================================================
//
// La asignatura debe:
//
// - Existir.
// - Corresponder a la modalidad seleccionada.
// - Estar asignada al docente seleccionado.
//
// En modalidad "taller" también se utiliza asignatura_id.
// Por ejemplo:
//
// Taller → Taller → Taller de IA
//
//=====================================================

if ($asignaturaId === null) {

    $errores[] =
        'Debe seleccionar una asignatura o taller.';

} else {

    $stmt = $conexion->prepare("
        SELECT
            a.id,
            a.modalidad
        FROM asignaturas a

        INNER JOIN docentes_asignaturas da
            ON da.asignatura_id = a.id

        WHERE
            a.id = ?
            AND a.modalidad = ?
            AND da.docente_id = ?

        LIMIT 1
    ");

    if (!$stmt) {

        $errores[] =
            'No fue posible validar la asignatura.';

    } else {

        $stmt->bind_param(
            "isi",
            $asignaturaId,
            $modalidad,
            $docenteId
        );

        $stmt->execute();

        $resultado = $stmt->get_result();

        $asignatura = $resultado->fetch_assoc();

        if (!$asignatura) {

            $errores[] =
                'La asignatura seleccionada no corresponde a la modalidad o al docente seleccionado.';
        }

        $stmt->close();
    }
}


//=====================================================
// VALIDAR FECHA DE INICIO
//=====================================================

$fechaInicioObj = null;

if ($fechaInicio === '') {

    $errores[] =
        'La fecha de inicio es obligatoria.';

} else {

    $fechaInicioObj =
        DateTime::createFromFormat(
            'Y-m-d',
            $fechaInicio
        );

    $erroresFecha = DateTime::getLastErrors();

    if (
        !$fechaInicioObj
        ||
        (
            $erroresFecha !== false
            &&
            (
                $erroresFecha['warning_count'] > 0
                ||
                $erroresFecha['error_count'] > 0
            )
        )
    ) {

        $errores[] =
            'La fecha de inicio no es válida.';

        $fechaInicioObj = null;
    }
}


//=====================================================
// GENERAR FECHA DE TÉRMINO AUTOMÁTICA
//=====================================================
//
// Si el usuario no indica una fecha de término,
// se utiliza el 15 de diciembre del mismo año
// de la fecha de inicio.
//
//=====================================================

if (
    $fechaInicioObj !== null
    &&
    $fechaFin === null
) {

    $fechaFin =
        $fechaInicioObj->format('Y')
        . '-12-15';
}


//=====================================================
// VALIDAR FECHA DE TÉRMINO
//=====================================================

$fechaFinObj = null;

if ($fechaFin !== null) {

    $fechaFinObj =
        DateTime::createFromFormat(
            'Y-m-d',
            $fechaFin
        );

    $erroresFecha = DateTime::getLastErrors();

    if (
        !$fechaFinObj
        ||
        (
            $erroresFecha !== false
            &&
            (
                $erroresFecha['warning_count'] > 0
                ||
                $erroresFecha['error_count'] > 0
            )
        )
    ) {

        $errores[] =
            'La fecha de término no es válida.';

        $fechaFinObj = null;

    } elseif (
        $fechaInicioObj !== null
        &&
        $fechaFinObj < $fechaInicioObj
    ) {

        $errores[] =
            'La fecha de término no puede ser anterior a la fecha de inicio.';
    }
}


//=====================================================
// VALIDAR ERRORES
//=====================================================

if (!empty($errores)) {

    $_SESSION['error'] =
        implode('<br>', $errores);

    header('Location: agregar.php');

    exit();
}


//=====================================================
// VALIDAR CONFLICTO DE HORARIO FIJO
//=====================================================
//
// Dos horarios fijos entran en conflicto cuando:
//
// - tienen el mismo día;
// - tienen el mismo bloque;
// - tienen el mismo tipo;
// - y sus períodos de vigencia se superponen.
//
//=====================================================

$sql = "
    SELECT id
    FROM horarios_fijos

    WHERE
        dia_semana = ?
        AND bloque_id = ?
        AND tipo = ?
        AND activo = 1

        AND fecha_inicio <= ?

        AND (
            fecha_fin IS NULL
            OR fecha_fin >= ?
        )

    LIMIT 1
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible validar el horario fijo.';

    header('Location: agregar.php');

    exit();
}

$stmt->bind_param(
    "iisss",
    $diaSemana,
    $bloqueId,
    $tipo,
    $fechaFin,
    $fechaInicio
);

$stmt->execute();

$resultado = $stmt->get_result();

$conflicto = $resultado->fetch_assoc();

$stmt->close();


if ($conflicto) {

    $_SESSION['error'] =
        'Ya existe un horario fijo activo que ocupa ese día, bloque y tipo durante ese período.';

    header('Location: agregar.php');

    exit();
}


//=====================================================
// INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();

try {

    //=================================================
    // GUARDAR HORARIO FIJO
    //=================================================

    $sql = "
        INSERT INTO horarios_fijos (

            dia_semana,
            bloque_id,
            tipo,
            modalidad,
            docente_id,
            curso_id,
            asignatura_id,
            fecha_inicio,
            fecha_fin,
            activo

        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {

        throw new Exception(
            'No fue posible preparar el guardado del horario fijo.'
        );
    }


    $stmt->bind_param(
        "iissiiiss",
        $diaSemana,
        $bloqueId,
        $tipo,
        $modalidad,
        $docenteId,
        $cursoId,
        $asignaturaId,
        $fechaInicio,
        $fechaFin
    );


    if (!$stmt->execute()) {

        throw new Exception(
            'No fue posible guardar el horario fijo.'
        );
    }


    $horarioFijoId =
        $conexion->insert_id;

    $stmt->close();


    //=================================================
    // GENERAR OCURRENCIAS
    //=================================================
    //
    // Se generan todas las ocurrencias correspondientes
    // al período de vigencia del horario fijo.
    //
    //=================================================

    $creadas =
        crearOcurrenciasHorariosFijos(
            $conexion,
            $fechaInicio,
            $fechaFin
        );


    //=================================================
    // CONFIRMAR TRANSACCIÓN
    //=================================================

    $conexion->commit();


    $_SESSION['exito'] =
        'El horario fijo fue creado correctamente.';


    header('Location: index.php');

    exit();

} catch (Throwable $e) {

    //=================================================
    // DESHACER TODO
    //=================================================

    $conexion->rollback();


    $_SESSION['error'] =
        'No fue posible guardar el horario fijo.';

    header('Location: agregar.php');

    exit();
}
