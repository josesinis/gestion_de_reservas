<?php
//=====================================================
// HORARIOS FIJOS - ACTIVAR
//
// Reactiva un horario fijo sin modificar su historial.
//
// Las ocurrencias futuras se volverán a generar.
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
    ? (int)$_GET['id']
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
// Solo se puede activar un horario que actualmente
// esté inactivo.
//=====================================================

if ((int)$horario['activo'] !== 0) {

    $_SESSION['error'] =
        'El horario fijo seleccionado ya está activo.';

    header('Location: index.php');

    exit();
}

//=====================================================
// INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();


//=====================================================
// ACTIVAR HORARIO FIJO
//=====================================================
//
// No modificamos ninguna otra información del horario.
// Solamente cambiamos activo de 0 a 1.
//=====================================================

$stmt = $conexion->prepare("
    UPDATE horarios_fijos
    SET activo = 1
    WHERE id = ?
");

if (!$stmt) {

    $conexion->rollback();

    $_SESSION['error'] =
        'No fue posible preparar la activación del horario fijo.';

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
        'No fue posible activar el horario fijo.';

    header('Location: index.php');

    exit();
}

$stmt->close();

//=====================================================
// GENERAR OCURRENCIAS FUTURAS
//=====================================================
//
// Al reactivar el horario fijo, se generan nuevamente
// las ocurrencias correspondientes desde hoy.
//
// Las ocurrencias históricas no se modifican.
//=====================================================

$fechaDesde = date('Y-m-d');

$creadas = crearOcurrenciasHorarioFijo(
    $conexion,
    $id,
    $fechaDesde,
    $horario['fecha_fin']
);

//=====================================================
// CONFIRMAR TRANSACCIÓN
//=====================================================

$conexion->commit();


//=====================================================
// MENSAJE DE ÉXITO
//=====================================================

$_SESSION['exito'] =
    'El horario fijo fue activado correctamente.';


//=====================================================
// VOLVER AL LISTADO
//=====================================================

header('Location: index.php');

exit();
