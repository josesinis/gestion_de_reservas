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

$permiteEntrega = isset($_POST['permite_entrega']) ? 1 : 0;

$fechaEntregaOficial = !empty($_POST['fecha_entrega_oficial'])
    ? $_POST['fecha_entrega_oficial']
    : null;

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
    actividad,
    permite_entrega,
    fecha_cierre,
    cierre_manual,
    estado,
    tipo_reserva,
    fecha_entrega_oficial

)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "iiiiissisisss",
    $docenteId,
    $usuarioId,
    $cursoId,
    $asignaturaId,
    $bloqueId,
    $fecha,
    $actividad,
    $permiteEntrega,
    $fechaCierre,
    $cierreManual,
    $estado,
    $tipoReserva,
    $fechaEntregaOficial
);

if ($stmt->execute()) {

    $_SESSION['exito'] = 'La reserva fue creada correctamente.';
} else {

    $_SESSION['error'] = 'Ocurrió un error al guardar la reserva.';
}

$stmt->close();

header('Location: agenda.php');
exit();
