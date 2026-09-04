<?php
//=====================================================
// HORARIOS FIJOS - DESACTIVAR
//
// Desactiva un horario fijo sin eliminarlo.
//
// Las ocurrencias históricas se conservan.
//=====================================================

declare(strict_types=1);

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
// VALIDAR MÉTODO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    header('Location: index.php');

    exit();
}


//=====================================================
// RECIBIR ID
//=====================================================

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


//=====================================================
// VALIDAR ID
//=====================================================

if ($id <= 0) {

    $_SESSION['error'] =
        'El horario fijo seleccionado no es válido.';

    header('Location: index.php');

    exit();
}


//=====================================================
// OBTENER HORARIO FIJO
//=====================================================

$horario =
    obtenerHorarioFijoPorId(
        $conexion,
        $id
    );

if (!$horario) {

    $_SESSION['error'] =
        'El horario fijo no existe.';

    header('Location: index.php');

    exit();
}

//=====================================================
// VALIDAR ESTADO ACTUAL
//=====================================================
//
// Solo se puede desactivar un horario que actualmente
// esté activo.
//=====================================================

if ((int)$horario['activo'] !== 1) {

    $_SESSION['error'] =
        'El horario fijo seleccionado ya está inactivo.';

    header('Location: index.php');

    exit();
}

//=====================================================
// INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();

//=====================================================
// DESACTIVAR HORARIO FIJO
//=====================================================
//
// No eliminamos el registro.
// Solamente cambiamos activo de 1 a 0.
//=====================================================

$stmt = $conexion->prepare("
    UPDATE horarios_fijos
    SET activo = 0
    WHERE id = ?
");

if (!$stmt) {

    $conexion->rollback();

    $_SESSION['error'] =
        'No fue posible preparar la desactivación del horario fijo.';

    header('Location: index.php');

    exit();
}

$stmt->bind_param(
    "i",
    $id
);

if (!$stmt->execute()) {

    $stmt->close();

    $conexion->rollback();

    $_SESSION['error'] =
        'No fue posible desactivar el horario fijo.';

    header('Location: index.php');

    exit();
}

$stmt->close();

//=====================================================
// ELIMINAR OCURRENCIAS PENDIENTES FUTURAS
//=====================================================
//
// Al desactivar el horario fijo, eliminamos únicamente
// las ocurrencias pendientes desde hoy en adelante.
//
// Las ocurrencias históricas o ya procesadas permanecen
// intactas.
//=====================================================

$fechaDesde = date('Y-m-d');

if (
    !eliminarOcurrenciasPendientesFuturas(
        $conexion,
        $id,
        $fechaDesde
    )
) {

    $conexion->rollback();

    $_SESSION['error'] =
        'No fue posible actualizar las ocurrencias del horario fijo.';

    header('Location: index.php');

    exit();
}

//=====================================================
// CONFIRMAR TRANSACCIÓN
//=====================================================

$conexion->commit();


//=====================================================
// MENSAJE DE ÉXITO
//=====================================================

$_SESSION['exito'] =
    'El horario fijo fue desactivado correctamente.';


//=====================================================
// VOLVER AL LISTADO
//=====================================================

header('Location: index.php');

exit();
