<?php
//=====================================================
// VER.PHP
// Muestra el detalle de una reserva.
//=====================================================
/*
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit();
}
*/
require_once '../../config/database.php';

//=====================================================
// VALIDAR ID
//=====================================================

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {

    $_SESSION['error'] = 'La reserva no es válida.';

    header('Location: index.php');

    exit();
}

/*
|--------------------------------------------------------------------------
| Buscar la reserva
|--------------------------------------------------------------------------
*/
$sql = "SELECT
            r.id,
            r.fecha,
            r.estado,
            b.hora_termino
        FROM reservas r
        INNER JOIN bloques b ON r.bloque_id = b.id
        WHERE r.id = ?";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$reserva = $resultado->fetch_assoc();

/*
|--------------------------------------------------------------------------
| Solo se pueden eliminar reservas en estado 'reservada'
|--------------------------------------------------------------------------
*/
if ($reserva['estado'] !== 'reservada') {
    header("Location: ver.php?id=" . $id);
    exit();
}

/*
|--------------------------------------------------------------------------
| Verificar que el bloque ya terminó
|--------------------------------------------------------------------------
*/
$fechaHoraFin = new DateTime($reserva['fecha'] . ' ' . $reserva['hora_termino']);
$ahora = new DateTime();

if ($ahora < $fechaHoraFin) {
    header("Location: ver.php?id=" . $id);
    exit();
}

/*
|--------------------------------------------------------------------------
| Eliminar reserva
|--------------------------------------------------------------------------
*/
$sqlEliminar = "DELETE FROM reservas WHERE id = ?";

$stmtEliminar = $conexion->prepare($sqlEliminar);
$stmtEliminar->bind_param("i", $id);
$stmtEliminar->execute();

header("Location: index.php");
exit();
