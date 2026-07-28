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

$id = intval($_POST['id'] ?? 0);

$docenteId = intval($_POST['docente_id'] ?? 0);

$cursoId = intval($_POST['curso_id'] ?? 0);

$asignaturaId = intval($_POST['asignatura_id'] ?? 0);

$actividad = trim($_POST['actividad'] ?? '');

$permiteEntrega = isset($_POST['permite_entrega']) ? 1 : 0;

//=====================================================
// VALIDACIONES
//=====================================================

$errores = [];

if ($id <= 0) {
    $errores[] = 'La reserva no es válida.';
}

if ($docenteId <= 0) {
    $errores[] = 'Debe seleccionar un docente.';
}

if ($cursoId <= 0) {
    $errores[] = 'Debe seleccionar un curso.';
}

if ($asignaturaId <= 0) {
    $errores[] = 'Debe seleccionar una asignatura.';
}

if ($actividad === '') {
    $errores[] = 'Debe ingresar una actividad.';
}

if (mb_strlen($actividad) > 150) {
    $errores[] = 'La actividad no puede superar los 150 caracteres.';
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

$sql = "
SELECT id
FROM Reservas
WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows === 0) {

    $_SESSION['error'] = 'La reserva no existe.';

    $stmt->close();

    header('Location: index.php');

    exit();
}

$stmt->close();

//=====================================================
// ACTUALIZAR RESERVA
//=====================================================

$sql = "
UPDATE Reservas
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
