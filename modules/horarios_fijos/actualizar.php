<?php
//=====================================================
// HORARIOS FIJOS - ACTUALIZAR
//
// Recibe y valida los datos enviados desde editar.php.
//=====================================================

declare(strict_types=1);

session_start();

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';


//=====================================================
// VALIDAR MÉTODO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');

    exit();
}


//=====================================================
// RECIBIR DATOS
//=====================================================

$id = (int) ($_POST['id'] ?? 0);

$diaSemana = (int) (
    $_POST['dia_semana'] ?? 0
);

$bloqueId = (int) (
    $_POST['bloque_id'] ?? 0
);

$tipo = trim(
    $_POST['tipo'] ?? ''
);

$modalidad = trim(
    $_POST['modalidad'] ?? ''
);

$docenteId = (int) (
    $_POST['docente_id'] ?? 0
);

$cursoId = (int) (
    $_POST['curso_id'] ?? 0
);

$asignaturaId = !empty($_POST['asignatura_id'])
    ? (int) $_POST['asignatura_id']
    : null;

$fechaInicio = trim(
    $_POST['fecha_inicio'] ?? ''
);

$fechaFin = !empty($_POST['fecha_fin'])
    ? trim($_POST['fecha_fin'])
    : null;

$observaciones = trim(
    $_POST['observaciones'] ?? ''
);


//=====================================================
// VALIDAR ID DEL HORARIO FIJO
//=====================================================

$horarioActual =
    obtenerHorarioFijoPorId(
        $conexion,
        $id
    );

if (!$horarioActual) {

    $_SESSION['error'] =
        'El horario fijo seleccionado no existe.';

    header('Location: index.php');

    exit();
}


//=====================================================
// ERRORES
//=====================================================

$errores = [];


//=====================================================
// VALIDAR DÍA
//=====================================================

if (
    $diaSemana < 1 ||
    $diaSemana > 5
) {

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

if (!in_array(
    $tipo,
    $tiposPermitidos,
    true
)) {

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

if (!in_array(
    $modalidad,
    $modalidadesPermitidas,
    true
)) {

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

if (!$stmt) {

    $errores[] =
        'No fue posible validar el docente.';
} else {

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
}

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
// VALIDAR ASIGNATURA / TALLER
//=====================================================
//
// Debe:
//
// - Existir.
// - Corresponder a la modalidad.
// - Estar asociado al docente.
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
            'No fue posible validar la asignatura o taller.';
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
                'La asignatura o taller seleccionado no corresponde a la modalidad o al docente seleccionado.';
        }

        $stmt->close();
    }
}

//=====================================================
// VALIDAR FECHA DE INICIO
//=====================================================

if ($fechaInicio === '') {

    $errores[] =
        'La fecha de inicio es obligatoria.';
} else {

    $fechaInicioObj =
        DateTime::createFromFormat(
            'Y-m-d',
            $fechaInicio
        );

    if (
        !$fechaInicioObj ||
        $fechaInicioObj->format('Y-m-d') !== $fechaInicio
    ) {

        $errores[] =
            'La fecha de inicio no es válida.';
    }
}


//=====================================================
// VALIDAR FECHA DE TÉRMINO
//=====================================================

if ($fechaFin !== null) {

    $fechaFinObj =
        DateTime::createFromFormat(
            'Y-m-d',
            $fechaFin
        );

    if (
        !$fechaFinObj ||
        $fechaFinObj->format('Y-m-d') !== $fechaFin
    ) {

        $errores[] =
            'La fecha de término no es válida.';
    } elseif (
        isset($fechaInicioObj) &&
        $fechaFinObj < $fechaInicioObj
    ) {

        $errores[] =
            'La fecha de término no puede ser anterior a la fecha de inicio.';
    }
}

//=====================================================
// VALIDAR CONFLICTO DE HORARIO FIJO
//=====================================================
//
// Se verifica que no exista otro horario fijo activo
// ocupando el mismo día, bloque y tipo durante un
// período superpuesto.
//
// El propio horario que estamos editando se excluye.
//=====================================================

$sql = "
    SELECT id
    FROM horarios_fijos
    WHERE
        id <> ?

        AND dia_semana = ?
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

    $errores[] =
        'No fue posible comprobar los conflictos de horario.';
} else {

    $stmt->bind_param(
        "iiisss",
        $id,
        $diaSemana,
        $bloqueId,
        $tipo,
        $fechaFin,
        $fechaInicio
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->fetch_assoc()) {

        $errores[] =
            'Ya existe otro horario fijo que ocupa el mismo día, bloque y tipo durante el período seleccionado.';
    }

    $stmt->close();
}

//=====================================================
// VALIDAR ERRORES
//=====================================================

if (!empty($errores)) {

    $_SESSION['error'] =
        implode('<br>', $errores);

    header(
        'Location: editar.php?id='
            . $id
    );

    exit();
}

//=====================================================
// INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();


//=====================================================
// ACTUALIZAR HORARIO FIJO
//=====================================================

$stmt = $conexion->prepare("
    UPDATE horarios_fijos
    SET
        dia_semana = ?,
        bloque_id = ?,
        tipo = ?,
        modalidad = ?,
        docente_id = ?,
        curso_id = ?,
        asignatura_id = ?,
        fecha_inicio = ?,
        fecha_fin = ?,
        observaciones = ?
    WHERE id = ?
");

if (!$stmt) {

    $conexion->rollback();

    $_SESSION['error'] =
        'No fue posible preparar la actualización del horario fijo.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();
}

$stmt->bind_param(
    "iissiiisssi",
    $diaSemana,
    $bloqueId,
    $tipo,
    $modalidad,
    $docenteId,
    $cursoId,
    $asignaturaId,
    $fechaInicio,
    $fechaFin,
    $observaciones,
    $id
);

if (!$stmt->execute()) {

    $stmt->close();

    $conexion->rollback();

    $_SESSION['error'] =
        'No fue posible actualizar el horario fijo.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();
}

$stmt->close();


//=====================================================
// FECHA DE SINCRONIZACIÓN
//=====================================================
//
// Las ocurrencias anteriores a hoy forman parte del
// historial y no serán modificadas.
//=====================================================

$fechaSincronizacion =
    date('Y-m-d');


//=====================================================
// ELIMINAR OCURRENCIAS PENDIENTES FUTURAS
//=====================================================

if (
    !eliminarOcurrenciasPendientesFuturas(
        $conexion,
        $id,
        $fechaSincronizacion
    )
) {

    $conexion->rollback();

    $_SESSION['error'] =
        'No fue posible actualizar las ocurrencias futuras del horario fijo.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();
}


//=====================================================
// GENERAR NUEVAS OCURRENCIAS
//=====================================================
//
// Se generan únicamente para el horario fijo que
// acabamos de actualizar.
//=====================================================

$creadas = crearOcurrenciasHorarioFijo(
    $conexion,
    $id,
    $fechaSincronizacion,
    $fechaFin
);


//=====================================================
// CONFIRMAR TRANSACCIÓN
//=====================================================

$conexion->commit();


//=====================================================
// MENSAJE DE ÉXITO
//=====================================================

$_SESSION['exito'] =
    'El horario fijo fue actualizado correctamente.';


//=====================================================
// VOLVER AL LISTADO
//=====================================================

header('Location: index.php');

exit();
