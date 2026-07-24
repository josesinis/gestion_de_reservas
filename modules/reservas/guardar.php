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

$fecha = trim($_POST['fecha'] ?? '');

$bloqueId = intval($_POST['bloque_id'] ?? 0);

$docenteId = intval($_POST['docente_id'] ?? 0);

$cursoId = intval($_POST['curso_id'] ?? 0);

$asignaturaId = intval($_POST['asignatura_id'] ?? 0);

$actividad = trim($_POST['actividad'] ?? '');

$permiteEntrega = isset($_POST['permite_entrega']) ? 1 : 0;

//=====================================================
// VALIDACIONES
//=====================================================

$errores = [];

if ($fecha === '') {
    $errores[] = 'La fecha es obligatoria.';
}

if ($bloqueId <= 0) {
    $errores[] = 'Debe seleccionar un bloque.';
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

    header('Location: agregar.php');

    exit();
}


//=====================================================
// VERIFICAR RESERVA EXISTENTE
//=====================================================

$sql = "SELECT id
        FROM reservas
        WHERE fecha = ?
        AND bloque_id = ?";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("si", $fecha, $bloqueId);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    $_SESSION['error'] = 'Ya existe una reserva para esa fecha y bloque.';

    $stmt->close();

    header('Location: agregar.php');

    exit();
}

$stmt->close();


//=====================================================
// GUARDAR RESERVA
//=====================================================

$sql = "INSERT INTO reservas (
            docente_id,
            curso_id,
            asignatura_id,
            bloque_id,
            fecha,
            actividad,
            permite_entrega
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "iiiissi",
    $docenteId,
    $cursoId,
    $asignaturaId,
    $bloqueId,
    $fecha,
    $actividad,
    $permiteEntrega
);

if ($stmt->execute()) {

    $_SESSION['exito'] = 'La reserva fue creada correctamente.';
} else {

    $_SESSION['error'] = 'Ocurrió un error al guardar la reserva.';
}

$stmt->close();

header('Location: index.php');
exit();
