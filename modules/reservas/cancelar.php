<?php
//=====================================================
// CANCELAR.PHP
// Cancela una reserva sin eliminarla de la base de datos.
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
// VALIDAR ID
//=====================================================

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {

    $_SESSION['error'] = 'La reserva no es válida.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// VALIDAR EXISTENCIA
//=====================================================

if (!existeReserva($conexion, $id)) {

    $_SESSION['error'] = 'La reserva no existe.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// OBTENER RESERVA
//=====================================================

$reserva = obtenerReservaPorId(
    $conexion,
    $id
);

if (!$reserva) {

    $_SESSION['error'] = 'La reserva no existe.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// VALIDAR SI SE PUEDE MODIFICAR
//=====================================================

if (!reservaEsModificable($reserva)) {

    $_SESSION['error'] =
        'No es posible cancelar una reserva cuyo bloque ya terminó.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// VALIDAR ESTADO
//=====================================================

if ($reserva['estado'] !== 'reservada') {

    $_SESSION['error'] =
        'Solo se pueden cancelar reservas activas.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// CANCELAR RESERVA
//=====================================================

$sql = "
    UPDATE reservas
    SET estado = 'cancelada'
    WHERE id = ?
    AND estado = 'reservada'
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'Ocurrió un error al preparar la cancelación.';

    header('Location: ver.php?id=' . $id);

    exit();
}

$stmt->bind_param(
    "i",
    $id
);

if ($stmt->execute()) {

    $_SESSION['exito'] =
        'La reserva fue cancelada correctamente.';
} else {

    $_SESSION['error'] =
        'Ocurrió un error al cancelar la reserva.';
}

$stmt->close();

header('Location: agenda.php');
exit();
