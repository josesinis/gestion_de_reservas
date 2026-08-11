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
    die($conexion->error);
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

if ($stmt->execute()) {

    $_SESSION['exito'] = 'La reserva fue creada correctamente.';
} else {

    $_SESSION['error'] = 'Ocurrió un error al guardar la reserva.';
}

$stmt->close();

header('Location: agenda.php');
exit();
