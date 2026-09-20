<?php
//=====================================================
// GUARDAR.PHP
// Guarda una nueva reserva.
//=====================================================

//=====================================================
// 1. VALIDAR SESIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// VALIDAR MÉTODO DE ENVÍO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: agenda.php');
    exit();
}
//=====================================================
// 4. RECIBIR DATOS
//=====================================================

$docenteId = (int) ($_POST['docente_id'] ?? 0);

$cursoId = (int) ($_POST['curso_id'] ?? 0);

$asignaturaId = (int) ($_POST['asignatura_id'] ?? 0);

$bloqueId = (int) ($_POST['bloque_id'] ?? 0);

$fecha = trim($_POST['fecha'] ?? '');

$tipoReserva = trim($_POST['tipo_reserva'] ?? '');

$actividad = trim($_POST['actividad'] ?? '');

$tituloTrabajo = trim(
    $_POST['titulo_trabajo'] ?? ''
);

$objetivo_clase = trim($_POST['objetivo_clase'] ?? '');

$permiteEntrega = isset($_POST['permite_entrega']) ? 1 : 0;

$fechaEntregaOficial = !empty($_POST['fecha_entrega_oficial'])
    ? $_POST['fecha_entrega_oficial']
    : null;

$tipoActividad = trim($_POST['tipo_actividad'] ?? 'nueva');

$trabajoId = isset($_POST['trabajo_id'])
    ? (int) $_POST['trabajo_id']
    : 0;

$modo = $_POST['modo'] ?? 'normal';

$ocurrenciaId = isset($_POST['horario_fijo_ocurrencia_id'])
    ? (int) $_POST['horario_fijo_ocurrencia_id']
    : 0;

$ocurrenciaHorarioFijo = null;

if ($modo === 'reasignar') {

    if ($ocurrenciaId <= 0) {

        $_SESSION['error'] =
            'La ocurrencia del horario fijo no es válida.';

        header('Location: agenda.php');
        exit();
    }

    $ocurrenciaHorarioFijo =
        obtenerOcurrenciaHorarioFijo(
            $conexion,
            $ocurrenciaId
        );


    if (!$ocurrenciaHorarioFijo) {

        $_SESSION['error'] =
            'La ocurrencia del horario fijo no existe.';

        header('Location: agenda.php');
        exit();
    }
}

//=====================================================
// PRUEBA TEMPORAL
//=====================================================
/*
echo '<pre>';

var_dump([
    'docente_id' => $docenteId,
    'curso_id' => $cursoId,
    'asignatura_id' => $asignaturaId,
    'bloque_id' => $bloqueId,
    'fecha' => $fecha,
    'tipo_reserva' => $tipoReserva,
    'actividad' => $actividad,
    'objetivo_clase' => $objetivo_clase,
    'permite_entrega' => $permiteEntrega,
    'fecha_entrega_oficial' => $fechaEntregaOficial
]);

echo '</pre>';

exit;*/

//-----------------------------------------------------
// VARIABLES INTERNAS DEL SISTEMA
//-----------------------------------------------------

$usuarioId = (int) $_SESSION['usuario_id'];

$estado = 'reservada';

$cierreManual = 0;

$fechaCierre = $fechaEntregaOficial;

//=====================================================
// 5. VALIDAR TIPO DE ACTIVIDAD
//=====================================================

$tiposActividadPermitidos = [
    'nueva',
    'continuacion'
];

$errores = [];

if (!in_array($tipoActividad, $tiposActividadPermitidos, true)) {

    $errores[] =
        'El tipo de actividad no es válido.';

    $tipoActividad = 'nueva';
    $trabajoId = 0;
}


//=====================================================
// VALIDAR TÍTULO DEL TRABAJO
//=====================================================

if (
    $tipoActividad === 'nueva'
    && $permiteEntrega === 1
    && $tituloTrabajo === ''
) {

    $errores[] =
        'Debe indicar el título del trabajo.';
}


//=====================================================
// VALIDAR CONTINUACIÓN DE TRABAJO
//=====================================================

if ($tipoActividad === 'continuacion') {

    if ($trabajoId <= 0) {

        $errores[] =
            'Debe seleccionar el trabajo que desea continuar.';

    } else {

        $sqlTrabajo = "
            SELECT
                t.id,
                t.estado,
                r.docente_id,
                r.curso_id,
                r.asignatura_id
            FROM trabajos t
            INNER JOIN reservas r
                ON r.id = t.reserva_id
            WHERE
                t.id = ?
                AND t.estado = 'en_proceso'
            LIMIT 1
        ";

        $stmtTrabajo = $conexion->prepare($sqlTrabajo);

        if (!$stmtTrabajo) {

            $errores[] =
                'No fue posible validar el trabajo seleccionado.';

        } else {

            $stmtTrabajo->bind_param(
                'i',
                $trabajoId
            );

            if (!$stmtTrabajo->execute()) {

                $errores[] =
                    'No fue posible validar el trabajo seleccionado.';

            } else {

                $resultadoTrabajo =
                    $stmtTrabajo->get_result();

                $trabajoSeleccionado =
                    $resultadoTrabajo->fetch_assoc();

                if (!$trabajoSeleccionado) {

                    $errores[] =
                        'El trabajo seleccionado no existe o ya no está en proceso.';

                } else {

                    // Los datos académicos del trabajo son la fuente
                    // válida para una continuación.
                    $docenteId =
                        (int) $trabajoSeleccionado['docente_id'];

                    $cursoId =
                        (int) $trabajoSeleccionado['curso_id'];

                    $asignaturaId =
                        (int) $trabajoSeleccionado['asignatura_id'];
                }
            }

            $stmtTrabajo->close();
        }
    }

    // Una continuación nunca inicia una nueva entrega.
    $permiteEntrega = 0;
    $fechaEntregaOficial = null;
    $fechaCierre = null;
}


//=====================================================
// 6. VALIDAR DATOS
//=====================================================

$erroresReserva = validarReserva(
    $docenteId,
    $cursoId,
    $asignaturaId,
    $actividad
);

$errores = array_merge(
    $errores,
    $erroresReserva
);

if ($fecha === '') {
    $errores[] = 'La fecha es obligatoria.';
}

if ($bloqueId <= 0) {
    $errores[] = 'Debe seleccionar un bloque.';
}


//=====================================================
// 6. VALIDAR TIPO DE RESERVA
//=====================================================

$tiposPermitidos = [
    'completo',
    'sub1',
    'sub2'
];

if (!in_array($tipoReserva, $tiposPermitidos, true)) {

    $errores[] = 'El tipo de reserva no es válido.';
}

//=====================================================
// VALIDAR REASIGNACIÓN DE HORARIO FIJO
//=====================================================

if ($modo === 'reasignar') {

    // La ocurrencia debe estar pendiente.

    if (
        $ocurrenciaHorarioFijo['estado']
        !== 'pendiente'
    ) {

        $errores[] =
            'Esta ocurrencia de horario fijo ya no está disponible para reasignación.';
    }

    // Solo los horarios fijos de tipo asignatura
    // pueden ser reasignados.

    if (
        $ocurrenciaHorarioFijo['modalidad']
        !== 'asignatura'
    ) {

        $errores[] =
            'Este horario fijo no puede ser reasignado.';
    }

    // El tipo de reserva debe ser exactamente
    // el mismo que tenía el horario fijo.

    if (
        $tipoReserva
        !== $ocurrenciaHorarioFijo['tipo']
    ) {

        $errores[] =
            'El tipo de reserva no coincide con el horario fijo.';
    }

    // La fecha debe corresponder a la ocurrencia.

    if (
        $fecha
        !== $ocurrenciaHorarioFijo['fecha']
    ) {

        $errores[] =
            'La fecha no coincide con la ocurrencia del horario fijo.';
    }

    // El bloque debe corresponder a la ocurrencia.

    if (
        $bloqueId
        !== (int) $ocurrenciaHorarioFijo['bloque_id']
    ) {

        $errores[] =
            'El bloque no coincide con la ocurrencia del horario fijo.';
    }
}

//=====================================================
// VALIDAR SI LA RESERVA PUEDE SER CREADA
//=====================================================

$bloque = obtenerBloque(
    $conexion,
    $bloqueId
);

if (!$bloque) {

    $errores[] = 'El bloque seleccionado no existe.';
} elseif (
    !horarioPuedeReservarse(
        $fecha,
        $bloque,
        $tipoReserva
    )
) {

    $errores[] =
        'Ya pasó el tiempo permitido para reservar este horario.';
}

//=====================================================
// VALIDAR SEMANA DE LA RESERVA
//=====================================================

$diasSemanaActual = obtenerDiasSemana();

$fechaInicioSemanaActual = $diasSemanaActual[0]['fecha'];

$diasSemanaReserva = obtenerDiasSemana($fecha);

$fechaInicioSemanaReserva = $diasSemanaReserva[0]['fecha'];

if ($fechaInicioSemanaReserva < $fechaInicioSemanaActual) {

    $errores[] =
        'No se pueden crear reservas en semanas anteriores.';
}

//=====================================================
// VALIDAR ENTREGA DE TRABAJOS
//=====================================================

if ($permiteEntrega === 1) {

    if ($fechaEntregaOficial === null) {

        $errores[] =
            'Debe indicar la fecha oficial de entrega.';
    } elseif ($fechaEntregaOficial < $fecha) {

        $errores[] =
            'La fecha oficial de entrega no puede ser anterior a la fecha de la reserva.';
    }
}

//=====================================================
// VALIDAR ERRORES
//=====================================================

if (!empty($errores)) {

    $_SESSION['error'] =
        implode('<br>', $errores);

    $paramsError = [
        'fecha'  => $fecha,
        'bloque' => $bloqueId,
        'tipo'   => $tipoReserva
    ];

    if ($modo === 'reasignar') {

        $paramsError['modo'] =
            'reasignar';

        $paramsError['horario_fijo_ocurrencia_id'] =
            $ocurrenciaId;
    }

    header(
        'Location: agregar.php?'
            . http_build_query($paramsError)
    );

    exit();
}

//=====================================================
// 6. VALIDAR DISPONIBILIDAD
//=====================================================

if (
    hayConflictoReserva(
        $conexion,
        $fecha,
        $bloqueId,
        $tipoReserva
    )
) {

    $_SESSION['error'] =
        'El horario seleccionado ya se encuentra reservado.';

    header('Location: agregar.php');

    exit();
}


//=====================================================
// GUARDAR RESERVA
//=====================================================

$sql = "INSERT INTO reservas (

    docente_id,
    usuario_id,
    curso_id,
    asignatura_id,
    bloque_id,
    fecha,
    objetivo_clase,
    actividad,
    permite_entrega,
    fecha_cierre,
    cierre_manual,
    estado,
    tipo_reserva,
    fecha_entrega_oficial,
    trabajo_id

)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NULLIF(?, 0))";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible preparar el registro de la reserva.';

    header('Location: agregar.php');

    exit();
}

$stmt->bind_param(
    "iiiiisssisisssi",
    $docenteId,
    $usuarioId,
    $cursoId,
    $asignaturaId,
    $bloqueId,
    $fecha,
    $objetivo_clase,
    $actividad,
    $permiteEntrega,
    $fechaCierre,
    $cierreManual,
    $estado,
    $tipoReserva,
    $fechaEntregaOficial,
    $trabajoId
);

//=====================================================
// RESERVA NORMAL
//=====================================================

if ($modo !== 'reasignar') {

    $conexion->begin_transaction();

    try {

        //=================================================
        // 1. CREAR LA RESERVA
        //=================================================

        if (!$stmt->execute()) {

            throw new Exception(
                'No fue posible guardar la reserva.'
            );
        }

        $reservaId = $conexion->insert_id;

        $stmt->close();


        //=================================================
        // 2. CREAR EL TRABAJO SI CORRESPONDE
        //=================================================

        if (
            $tipoActividad === 'nueva'
            && $permiteEntrega === 1
        ) {

            $fechaInicioTrabajo =
                $fecha . ' 00:00:00';

            $fechaLimiteTrabajo =
                $fechaEntregaOficial . ' 16:15:00';


            $sqlTrabajo = "
                INSERT INTO trabajos (
                    reserva_id,
                    titulo,
                    estado,
                    fecha_inicio,
                    fecha_limite
                )
                VALUES (?, ?, 'en_proceso', ?, ?)
            ";

            $stmtTrabajo =
                $conexion->prepare($sqlTrabajo);

            if (!$stmtTrabajo) {

                throw new Exception(
                    'No fue posible preparar la creación del trabajo.'
                );
            }

            $stmtTrabajo->bind_param(
                "isss",
                $reservaId,
                $tituloTrabajo,
                $fechaInicioTrabajo,
                $fechaLimiteTrabajo
            );

            if (!$stmtTrabajo->execute()) {

                $stmtTrabajo->close();

                throw new Exception(
                    'No fue posible crear el trabajo.'
                );
            }

            $trabajoId =
                $conexion->insert_id;

            $stmtTrabajo->close();


            //=================================================
            // 2.1 VINCULAR EL TRABAJO A LA RESERVA
            //=================================================

            $sqlVincularTrabajo = "
                UPDATE reservas
                SET trabajo_id = ?
                WHERE id = ?
            ";

            $stmtVincularTrabajo =
                $conexion->prepare(
                    $sqlVincularTrabajo
                );

            if (!$stmtVincularTrabajo) {

                throw new Exception(
                    'No fue posible vincular el trabajo con la reserva.'
                );
            }

            $stmtVincularTrabajo->bind_param(
                "ii",
                $trabajoId,
                $reservaId
            );

            if (!$stmtVincularTrabajo->execute()) {

                $stmtVincularTrabajo->close();

                throw new Exception(
                    'No fue posible vincular el trabajo con la reserva.'
                );
            }

            $stmtVincularTrabajo->close();
        }


        //=================================================
        // 3. CONFIRMAR
        //=================================================

        $conexion->commit();

        if ($tipoActividad === 'continuacion') {

            $_SESSION['exito'] =
                'La reserva fue creada y asociada al trabajo correctamente.';

        } else {

            $_SESSION['exito'] =
                $permiteEntrega === 1
                ? 'La reserva y el trabajo fueron creados correctamente.'
                : 'La reserva fue creada correctamente.';
        }
    } catch (Throwable $e) {

        $conexion->rollback();

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        $_SESSION['error'] =
            'No fue posible guardar la reserva: '
            . $e->getMessage();
    }

    header('Location: agenda.php');
    exit();
}

//=====================================================
// REASIGNACIÓN DE HORARIO FIJO
//=====================================================

/*
 * Desde aquí comienza una operación que afecta
 * dos elementos:
 *
 * 1. nueva reserva
 * 2. ocurrencia del horario fijo
 *
 * Las dos operaciones deben completarse juntas.
 *
 * La bitácora NO se crea aquí.
 * Se generará posteriormente al confirmar el uso.
 */


$conexion->begin_transaction();

try {

    //=================================================
    // 1. CREAR LA NUEVA RESERVA
    //=================================================

    if (!$stmt->execute()) {

        throw new Exception(
            'No fue posible crear la reserva.'
        );
    }

    $reservaId = $conexion->insert_id;

    $stmt->close();


    //=================================================
    // 2. ACTUALIZAR LA OCURRENCIA
    //=================================================

    $sqlOcurrencia = "
        UPDATE horarios_fijos_ocurrencias
        SET
            estado = 'reasignada',
            reserva_id = ?,
            usuario_id = ?,
            fecha_confirmacion = NOW()
        WHERE id = ?
          AND estado = 'pendiente'
    ";

    $stmtOcurrencia =
        $conexion->prepare($sqlOcurrencia);

    if (!$stmtOcurrencia) {

        throw new Exception(
            'No fue posible preparar la actualización de la ocurrencia.'
        );
    }

    $stmtOcurrencia->bind_param(
        "iii",
        $reservaId,
        $usuarioId,
        $ocurrenciaId
    );

    if (!$stmtOcurrencia->execute()) {

        throw new Exception(
            'No fue posible actualizar la ocurrencia del horario fijo.'
        );
    }

    /*
     * Si no se actualizó ninguna fila significa que,
     * entre la validación anterior y este momento,
     * la ocurrencia dejó de estar pendiente.
     */

    if ($stmtOcurrencia->affected_rows !== 1) {

        throw new Exception(
            'La ocurrencia del horario fijo ya no está disponible para reasignación.'
        );
    }

    $stmtOcurrencia->close();


    // 3. CONFIRMAR TRANSACCIÓN
    //=================================================

    $conexion->commit();

    $_SESSION['exito'] =
        'La reserva fue creada y el horario fijo fue reasignado correctamente.';
} catch (Throwable $e) {

    //=================================================
    // ERROR → DESHACER TODO
    //=================================================

    $conexion->rollback();

    if (isset($stmt) && $stmt) {
        $stmt->close();
    }

    $_SESSION['error'] =
        'No fue posible realizar la reasignación: '
        . $e->getMessage();
}

//=====================================================
// VOLVER A LA AGENDA
//=====================================================

header('Location: agenda.php');
exit();
