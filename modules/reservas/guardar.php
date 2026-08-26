<?php
//=====================================================
// GUARDAR.PHP
// Guarda una nueva reserva.
//=====================================================

/*session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit();
}
*/
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

$objetivo_clase = trim($_POST['objetivo_clase'] ?? '');

$permiteEntrega = isset($_POST['permite_entrega']) ? 1 : 0;

$fechaEntregaOficial = !empty($_POST['fecha_entrega_oficial'])
    ? $_POST['fecha_entrega_oficial']
    : null;

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

$usuarioId = 1;          // Temporal hasta implementar login

$estado = 'reservada';

$cierreManual = 0;

$fechaCierre = $fechaEntregaOficial;

//=====================================================
// 5. VALIDAR DATOS
//=====================================================

$errores = validarReserva(
    $docenteId,
    $cursoId,
    $asignaturaId,
    $actividad
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
    fecha_entrega_oficial

)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible preparar el registro de la reserva.';

    header('Location: agregar.php');

    exit();
}

$stmt->bind_param(
    "iiiiisssisisss",
    $docenteId,
    $usuarioId,
    $cursoId,
    $asignaturaId,
    $bloqueId,
    $fecha,
    $actividad,
    $objetivo_clase,
    $permiteEntrega,
    $fechaCierre,
    $cierreManual,
    $estado,
    $tipoReserva,
    $fechaEntregaOficial
);

//=====================================================
// RESERVA NORMAL
//=====================================================

if ($modo !== 'reasignar') {

    if ($stmt->execute()) {

        $_SESSION['exito'] =
            'La reserva fue creada correctamente.';
    } else {

        $_SESSION['error'] =
            'Ocurrió un error al guardar la reserva.';
    }

    $stmt->close();

    header('Location: agenda.php');
    exit();
}

//=====================================================
// REASIGNACIÓN DE HORARIO FIJO
//=====================================================

/*
 * Desde aquí comienza una operación que afecta
 * tres elementos:
 *
 * 1. nueva reserva
 * 2. ocurrencia del horario fijo
 * 3. bitácora
 *
 * Las tres operaciones deben completarse juntas.
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


    //=================================================
    // 3. CREAR BITÁCORA
    //=================================================

    $observaciones =
        'Reasignación de horario fijo. '
        . 'El horario original fue reasignado a la nueva reserva.';

    $sqlBitacora = "
        INSERT INTO bitacoras (
            reserva_id,
            horario_fijo_ocurrencia_id,
            observaciones
        )
        VALUES (?, ?, ?)
    ";

    $stmtBitacora =
        $conexion->prepare($sqlBitacora);

    if (!$stmtBitacora) {

        throw new Exception(
            'No fue posible preparar la bitácora.'
        );
    }

    $stmtBitacora->bind_param(
        "iis",
        $reservaId,
        $ocurrenciaId,
        $observaciones
    );

    if (!$stmtBitacora->execute()) {

        throw new Exception(
            'No fue posible registrar la bitácora.'
        );
    }

    $stmtBitacora->close();


    //=================================================
    // 4. CONFIRMAR TRANSACCIÓN
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
