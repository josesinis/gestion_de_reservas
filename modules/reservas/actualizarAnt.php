<?php
//=====================================================
// ACTUALIZAR.PHP
// Actualiza una reserva existente.
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

$objetivo_clase = trim($_POST['objetivo_clase'] ?? '');

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
// VALIDAR SI LA RESERVA PUEDE SER MODIFICADA
//=====================================================

if (!reservaEsModificable($reserva)) {

    $_SESSION['error'] =
        'La reserva ya no puede ser modificada porque su horario ya comenzó.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// VALIDAR SEMANA DE LA RESERVA
//=====================================================

$diasSemanaActual = obtenerDiasSemana();

$fechaInicioSemanaActual = $diasSemanaActual[0]['fecha'];

$diasSemanaReserva = obtenerDiasSemana(
    $reserva['fecha']
);

$fechaInicioSemanaReserva =
    $diasSemanaReserva[0]['fecha'];

if ($fechaInicioSemanaReserva < $fechaInicioSemanaActual) {

    $_SESSION['error'] =
        'No se pueden modificar reservas de semanas anteriores.';

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
    objetivo_clase = ?,
    actividad = ?,
    permite_entrega = ?
WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "iiissii",
    $docenteId,
    $cursoId,
    $asignaturaId,
    $objetivo_clase,
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
