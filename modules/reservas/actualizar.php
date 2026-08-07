<?php
//=====================================================
// ACTUALIZAR.PHP
// Actualiza una reserva existente.
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
// VALIDAR MÉTODO DE ENVÍO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

//=====================================================
// OBTENER DATOS DEL FORMULARIO
//=====================================================

$id = (int)($_POST['id'] ?? 0);

$docenteId = (int)($_POST['docente_id'] ?? 0);

$cursoId = (int)($_POST['curso_id'] ?? 0);

$asignaturaId = (int)($_POST['asignatura_id'] ?? 0);

$actividad = trim($_POST['actividad'] ?? '');

$permiteEntrega = isset($_POST['permite_entrega']) ? 1 : 0;

//=====================================================
// VALIDACIONES
//=====================================================

$errores = validarReserva(
    $docenteId,
    $cursoId,
    $asignaturaId,
    $actividad
);

if ($id <= 0) {
    $errores[] = 'La reserva no es válida.';
}

//=====================================================
// VALIDAR ERRORES
//=====================================================

if (!empty($errores)) {

    $_SESSION['error'] = implode('<br>', $errores);

    header("Location: editar.php?id=$id");

    exit();
}

//=====================================================
// VERIFICAR QUE LA RESERVA EXISTA
//=====================================================

if (!existeReserva($conexion, $id)) {

    $_SESSION['error'] = 'La reserva no existe.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// ACTUALIZAR RESERVA
//=====================================================

$sql = "
UPDATE reservas
SET
    docente_id = ?,
    curso_id = ?,
    asignatura_id = ?,
    actividad = ?,
    permite_entrega = ?
WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "iiisii",
    $docenteId,
    $cursoId,
    $asignaturaId,
    $actividad,
    $permiteEntrega,
    $id
);

if ($stmt->execute()) {

    $_SESSION['exito'] = 'La reserva fue actualizada correctamente.';
} else {

    $_SESSION['error'] = 'Ocurrió un error al actualizar la reserva.';
}

$stmt->close();

//=====================================================
// REDIRECCIONAR
//=====================================================

header("Location: ver.php?id=$id");
exit();
