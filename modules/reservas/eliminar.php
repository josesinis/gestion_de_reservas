<?php
//=====================================================
// ELIMINAR.PHP
// Elimina una reserva.
//=====================================================

/*
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit();
}
*/

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
// VALIDAR FECHA
//=====================================================

if (!puedeModificarReserva($reserva['fecha'])) {

    $_SESSION['error'] =
        'No es posible eliminar reservas de fechas pasadas.';

    header('Location: ver.php?id=' . $id);

    exit();
}

//=====================================================
// VALIDAR ESTADO
//=====================================================

if ($reserva['estado'] !== 'reservada') {

    $_SESSION['error'] =
        'Solo se pueden eliminar reservas activas.';

    header('Location: ver.php?id=' . $id);

    exit();
}

//=====================================================
// ELIMINAR RESERVA
//=====================================================

$sql = "
    DELETE FROM reservas
    WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

if ($stmt->execute()) {

    $_SESSION['exito'] =
        'La reserva fue eliminada correctamente.';

} else {

    $_SESSION['error'] =
        'Ocurrió un error al eliminar la reserva.';
}

$stmt->close();

header('Location: agenda.php');
exit();
