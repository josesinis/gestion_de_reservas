<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/docentes/guardar.php
|--------------------------------------------------------------------------
| Descripción :
| Guarda un nuevo docente en la base de datos.
|
| Acceso :
| Exclusivo para superadmin.
|--------------------------------------------------------------------------
*/


//=====================================================
// 1. AUTENTICACIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();


//=====================================================
// 2. PERMISOS Y BASE DE DATOS
//=====================================================

require_once '../../includes/permisos.php';
require_once '../../config/database.php';


//=====================================================
// 3. VALIDAR ROL
//=====================================================

requiereRol('superadmin');


//=====================================================
// 4. VALIDAR MÉTODO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['error'] = 'Solicitud no válida.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 5. RECIBIR DATOS
//=====================================================

$nombres = trim($_POST['nombres'] ?? '');

$apellidos = trim($_POST['apellidos'] ?? '');

$correo = trim($_POST['correo'] ?? '');

$activo = $_POST['activo'] ?? '';


//=====================================================
// 6. VALIDAR DATOS
//=====================================================

if ($nombres === '' || $apellidos === '' || $correo === '') {

    $_SESSION['error'] = 'Todos los campos son obligatorios.';

    header('Location: crear.php');
    exit();
}


if (mb_strlen($nombres) > 50) {

    $_SESSION['error'] = 'El nombre no puede superar los 50 caracteres.';

    header('Location: crear.php');
    exit();
}


if (mb_strlen($apellidos) > 50) {

    $_SESSION['error'] = 'Los apellidos no pueden superar los 50 caracteres.';

    header('Location: crear.php');
    exit();
}


if (mb_strlen($correo) > 50) {

    $_SESSION['error'] = 'El correo no puede superar los 50 caracteres.';

    header('Location: crear.php');
    exit();
}


if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error'] = 'El correo electrónico no es válido.';

    header('Location: crear.php');
    exit();
}


if ($activo !== '0' && $activo !== '1') {

    $_SESSION['error'] = 'El estado seleccionado no es válido.';

    header('Location: crear.php');
    exit();
}


//=====================================================
// 7. INSERTAR DOCENTE
//=====================================================

$sql = "
    INSERT INTO docentes (
        nombres,
        apellidos,
        correo,
        activo
    )
    VALUES (?, ?, ?, ?)
";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] = 'No fue posible preparar el registro del docente.';

    header('Location: crear.php');
    exit();
}


$activoInt = (int) $activo;


$stmt->bind_param(
    'sssi',
    $nombres,
    $apellidos,
    $correo,
    $activoInt
);


if (!$stmt->execute()) {

    $_SESSION['error'] = 'No fue posible guardar el docente.';

    $stmt->close();

    header('Location: crear.php');
    exit();
}


$stmt->close();


//=====================================================
// 8. MENSAJE DE ÉXITO
//=====================================================

$_SESSION['exito'] = 'Docente creado correctamente.';


//=====================================================
// 9. REDIRECCIÓN
//=====================================================

header('Location: index.php');

exit();
