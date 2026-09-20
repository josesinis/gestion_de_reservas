<?php
/*
 * --------------------------------------------------------------------------
 * Sistema     : Gestión Institucional
 * Archivo     : modules/entregas/actualizar.php
 * --------------------------------------------------------------------------
 * Descripción :
 * Crea, modifica o elimina una prórroga individual de un estudiante.
 *
 * Acceso      :
 * Exclusivo para superadmin.
 *
 * Importante  :
 * La prórroga es una excepción al período normal de entrega.
 * No modifica el estado del trabajo.
 * --------------------------------------------------------------------------
 */

declare(strict_types=1);


//=====================================================
// 1. AUTENTICACIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();


//=====================================================
// 2. PERMISOS Y BASE DE DATOS
//=====================================================

require_once '../../includes/permisos.php';
require_once '../../config/database.php';

requiereRol('superadmin');


//=====================================================
// 3. VALIDAR MÉTODO DE ENVÍO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit();
}


//=====================================================
// 4. OBTENER DATOS DEL FORMULARIO
//=====================================================

$id = (int) ($_POST['id'] ?? 0);

$prorrogaId =
    (int) ($_POST['prorroga_id'] ?? 0);

$accion =
    trim($_POST['accion'] ?? '');

$alumnoId =
    (int) ($_POST['alumno_id'] ?? 0);

$fechaProrrogaInicio = trim(
    $_POST['prorroga_fecha_inicio'] ?? ''
);

$fechaProrrogaFin = trim(
    $_POST['prorroga_fecha_fin'] ?? ''
);


//=====================================================
// 5. VALIDAR ID DEL TRABAJO
//=====================================================

if ($id <= 0) {

    $_SESSION['error'] =
        'El trabajo seleccionado no es válido.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 6. OBTENER TRABAJO Y CURSO
//=====================================================

$sql = "
    SELECT
        t.id,
        t.estado,
        t.fecha_limite,
        r.curso_id
    FROM trabajos t
    INNER JOIN reservas r
        ON r.id = t.reserva_id
    WHERE t.id = ?
    LIMIT 1
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible consultar el trabajo.';

    header('Location: editar.php?id=' . $id);
    exit();
}

$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

$trabajo = $resultado->fetch_assoc();

$stmt->close();


if (!$trabajo) {

    $_SESSION['error'] =
        'El trabajo seleccionado no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 7. ELIMINAR PRÓRROGA
//=====================================================

if ($accion === 'eliminar') {

    if ($prorrogaId <= 0) {

        $_SESSION['error'] =
            'La prórroga seleccionada no es válida.';

        header('Location: editar.php?id=' . $id);
        exit();
    }


    $stmt = $conexion->prepare("
        DELETE FROM trabajo_prorrogas
        WHERE id = ?
          AND trabajo_id = ?
    ");

    if (!$stmt) {

        $_SESSION['error'] =
            'No fue posible preparar la eliminación de la prórroga.';

        header('Location: editar.php?id=' . $id);
        exit();
    }


    $stmt->bind_param(
        'ii',
        $prorrogaId,
        $id
    );


    if (!$stmt->execute()) {

        $stmt->close();

        $_SESSION['error'] =
            'No fue posible eliminar la prórroga.';

        header('Location: editar.php?id=' . $id);
        exit();
    }


    $eliminadas =
        $stmt->affected_rows;

    $stmt->close();


    if ($eliminadas === 0) {

        $_SESSION['error'] =
            'La prórroga seleccionada no existe.';

        header('Location: editar.php?id=' . $id);
        exit();
    }


    $_SESSION['exito'] =
        'La prórroga fue eliminada correctamente.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 8. VALIDAR ALUMNO
//=====================================================

$errores = [];

if ($alumnoId <= 0) {

    $errores[] =
        'Debe seleccionar un estudiante.';
}


//=====================================================
// 9. COMPROBAR QUE EL ALUMNO PERTENECE AL CURSO
//=====================================================

if ($alumnoId > 0) {

    $cursoId = (int) $trabajo['curso_id'];

    $stmt = $conexion->prepare("
        SELECT id
        FROM alumnos
        WHERE id = ?
          AND curso_id = ?
          AND activo = 1
        LIMIT 1
    ");

    if (!$stmt) {

        $errores[] =
            'No fue posible validar el estudiante seleccionado.';

    } else {

        $stmt->bind_param(
            'ii',
            $alumnoId,
            $cursoId
        );

        $stmt->execute();

        $resultado = $stmt->get_result();

        $alumnoValido =
            $resultado->fetch_assoc();

        $stmt->close();

        if (!$alumnoValido) {

            $errores[] =
                'El estudiante seleccionado no pertenece al curso del trabajo o no está activo.';
        }
    }
}


//=====================================================
// 10. VALIDAR PRESENCIA DE LAS FECHAS
//=====================================================

if ($fechaProrrogaInicio === '') {

    $errores[] =
        'Debe indicar la fecha de inicio de la prórroga.';
}

if ($fechaProrrogaFin === '') {

    $errores[] =
        'Debe indicar la fecha de término de la prórroga.';
}


//=====================================================
// 11. CONVERTIR Y VALIDAR FECHAS
//=====================================================

function convertirFechaProrroga(
    string $fecha
): ?DateTime {

    $fechaObj =
        DateTime::createFromFormat(
            'Y-m-d\TH:i',
            $fecha
        );

    if ($fechaObj !== false) {

        $erroresFecha =
            DateTime::getLastErrors();

        if (
            $erroresFecha === false
            || (
                $erroresFecha['warning_count'] === 0
                && $erroresFecha['error_count'] === 0
            )
        ) {

            return $fechaObj;
        }
    }


    // Permitir también Y-m-d H:i:s
    // por compatibilidad.

    $fechaObj =
        DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $fecha
        );

    if ($fechaObj !== false) {

        $erroresFecha =
            DateTime::getLastErrors();

        if (
            $erroresFecha === false
            || (
                $erroresFecha['warning_count'] === 0
                && $erroresFecha['error_count'] === 0
            )
        ) {

            return $fechaObj;
        }
    }

    return null;
}


$inicioObj =
    $fechaProrrogaInicio !== ''
        ? convertirFechaProrroga(
            $fechaProrrogaInicio
        )
        : null;

$finObj =
    $fechaProrrogaFin !== ''
        ? convertirFechaProrroga(
            $fechaProrrogaFin
        )
        : null;


if (
    $fechaProrrogaInicio !== ''
    && $inicioObj === null
) {

    $errores[] =
        'La fecha de inicio de la prórroga no es válida.';
}

if (
    $fechaProrrogaFin !== ''
    && $finObj === null
) {

    $errores[] =
        'La fecha de término de la prórroga no es válida.';
}


//=====================================================
// 12. VALIDAR RANGO
//=====================================================

if (
    $inicioObj !== null
    && $finObj !== null
    && $inicioObj >= $finObj
) {

    $errores[] =
        'La fecha de término de la prórroga debe ser posterior a la fecha de inicio.';
}


//=====================================================
// 13. VALIDAR INICIO CONTRA FECHA LÍMITE ORIGINAL
//=====================================================

if (
    $inicioObj !== null
    && !empty($trabajo['fecha_limite'])
) {

    try {

        $fechaLimiteObj =
            new DateTime(
                $trabajo['fecha_limite']
            );

        if ($inicioObj < $fechaLimiteObj) {

            $errores[] =
                'La fecha de inicio de la prórroga no puede ser anterior a la fecha límite original del trabajo.';
        }

    } catch (Throwable $e) {

        $errores[] =
            'No fue posible validar la fecha límite original del trabajo.';
    }
}


//=====================================================
// 14. VALIDAR ERRORES
//=====================================================

if (!empty($errores)) {

    $_SESSION['error'] =
        implode('<br>', $errores);

    header(
        'Location: editar.php?id='
        . $id
        . (
            $prorrogaId > 0
                ? '&prorroga_id=' . $prorrogaId
                : ''
        )
    );

    exit();
}


//=====================================================
// 15. PREPARAR FECHAS PARA MYSQL
//=====================================================

$inicioMysql =
    $inicioObj->format(
        'Y-m-d H:i:s'
    );

$finMysql =
    $finObj->format(
        'Y-m-d H:i:s'
    );


//=====================================================
// 16. VALIDAR QUE NO EXISTA OTRA PRÓRROGA SOLAPADA
//=====================================================
//
// Un estudiante no debe tener dos prórrogas activas o
// períodos superpuestos para el mismo trabajo.
//
//=====================================================

$sql = "
    SELECT id
    FROM trabajo_prorrogas
    WHERE trabajo_id = ?
      AND alumno_id = ?
      AND fecha_inicio < ?
      AND fecha_fin > ?
";

if ($prorrogaId > 0) {

    $sql .= "
      AND id <> ?
    ";
}

$sql .= "
    LIMIT 1
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible validar las prórrogas existentes.';

    header(
        'Location: editar.php?id='
        . $id
        . (
            $prorrogaId > 0
                ? '&prorroga_id=' . $prorrogaId
                : ''
        )
    );

    exit();
}

if ($prorrogaId > 0) {

    $stmt->bind_param(
        'iissi',
        $id,
        $alumnoId,
        $finMysql,
        $inicioMysql,
        $prorrogaId
    );

} else {

    $stmt->bind_param(
        'iiss',
        $id,
        $alumnoId,
        $finMysql,
        $inicioMysql
    );
}

$stmt->execute();

$resultado = $stmt->get_result();

$prorrogaSolapada =
    $resultado->fetch_assoc();

$stmt->close();

if ($prorrogaSolapada) {

    $_SESSION['error'] =
        'El estudiante seleccionado ya tiene una prórroga que se superpone con el período indicado.';

    header(
        'Location: editar.php?id='
        . $id
        . (
            $prorrogaId > 0
                ? '&prorroga_id=' . $prorrogaId
                : ''
        )
    );

    exit();
}


//=====================================================
// 17. EDITAR PRÓRROGA EXISTENTE
//=====================================================

if ($prorrogaId > 0) {

    $stmt = $conexion->prepare("
        UPDATE trabajo_prorrogas
        SET
            alumno_id = ?,
            fecha_inicio = ?,
            fecha_fin = ?
        WHERE id = ?
          AND trabajo_id = ?
    ");

    if (!$stmt) {

        $_SESSION['error'] =
            'No fue posible preparar la actualización de la prórroga.';

        header(
            'Location: editar.php?id='
            . $id
            . '&prorroga_id='
            . $prorrogaId
        );

        exit();
    }

    $stmt->bind_param(
        'issii',
        $alumnoId,
        $inicioMysql,
        $finMysql,
        $prorrogaId,
        $id
    );

    if (!$stmt->execute()) {

        $stmt->close();

        $_SESSION['error'] =
            'No fue posible guardar los cambios de la prórroga.';

        header(
            'Location: editar.php?id='
            . $id
            . '&prorroga_id='
            . $prorrogaId
        );

        exit();
    }

    $stmt->close();

    $_SESSION['exito'] =
        'La prórroga fue actualizada correctamente.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();
}


//=====================================================
// 18. CREAR NUEVA PRÓRROGA
//=====================================================

$cursoId =
    (int) $trabajo['curso_id'];

$stmt = $conexion->prepare("
    INSERT INTO trabajo_prorrogas (
        trabajo_id,
        curso_id,
        alumno_id,
        fecha_inicio,
        fecha_fin
    )
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible preparar el registro de la prórroga.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();
}

$stmt->bind_param(
    'iiiss',
    $id,
    $cursoId,
    $alumnoId,
    $inicioMysql,
    $finMysql
);

if (!$stmt->execute()) {

    $stmt->close();

    $_SESSION['error'] =
        'No fue posible guardar la prórroga.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();
}

$stmt->close();


//=====================================================
// 19. FINALIZAR
//=====================================================

$_SESSION['exito'] =
    'La prórroga fue registrada correctamente.';

header(
    'Location: editar.php?id=' . $id
);

exit();
