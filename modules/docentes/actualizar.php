<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/docentes/actualizar.php
|--------------------------------------------------------------------------
| Descripción :
| Actualiza los datos de un docente existente.
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

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

$nombres = trim($_POST['nombres'] ?? '');

$apellidos = trim($_POST['apellidos'] ?? '');

$correo = trim($_POST['correo'] ?? '');

$activo = $_POST['activo'] ?? '';


//=====================================================
// 6. VALIDAR ID
//=====================================================

if (!$id || $id <= 0) {

    $_SESSION['error'] = 'Docente no válido.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 7. VALIDAR CAMPOS OBLIGATORIOS
//=====================================================

if ($nombres === '' || $apellidos === '' || $correo === '') {

    $_SESSION['error'] = 'Todos los campos son obligatorios.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 8. VALIDAR LONGITUDES
//=====================================================

if (mb_strlen($nombres) > 50) {

    $_SESSION['error'] = 'El nombre no puede superar los 50 caracteres.';

    header('Location: editar.php?id=' . $id);
    exit();
}


if (mb_strlen($apellidos) > 50) {

    $_SESSION['error'] = 'Los apellidos no pueden superar los 50 caracteres.';

    header('Location: editar.php?id=' . $id);
    exit();
}


if (mb_strlen($correo) > 50) {

    $_SESSION['error'] = 'El correo no puede superar los 50 caracteres.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 9. VALIDAR CORREO
//=====================================================

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error'] = 'El correo electrónico no es válido.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 10. VALIDAR ESTADO
//=====================================================

if ($activo !== '0' && $activo !== '1') {

    $_SESSION['error'] = 'El estado seleccionado no es válido.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 11. COMPROBAR QUE EL DOCENTE EXISTA
//=====================================================

$sql = "
    SELECT id
    FROM docentes
    WHERE id = ?
";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] = 'No fue posible consultar el docente.';

    header('Location: index.php');
    exit();
}


$stmt->bind_param('i', $id);

$stmt->execute();

$resultado = $stmt->get_result();

$docente = $resultado->fetch_assoc();

$stmt->close();


if (!$docente) {

    $_SESSION['error'] = 'El docente no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 12. ACTUALIZAR DOCENTE
//=====================================================

$sql = "
    UPDATE docentes
    SET
        nombres = ?,
        apellidos = ?,
        correo = ?,
        activo = ?
    WHERE id = ?
";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] = 'No fue posible preparar la actualización.';

    header('Location: editar.php?id=' . $id);
    exit();
}


$activoInt = (int) $activo;


$stmt->bind_param(
    'sssii',
    $nombres,
    $apellidos,
    $correo,
    $activoInt,
    $id
);


if (!$stmt->execute()) {

    $_SESSION['error'] = 'No fue posible actualizar el docente.';

    $stmt->close();

    header('Location: editar.php?id=' . $id);
    exit();
}


$stmt->close();


//=====================================================
// 13. MENSAJE DE ÉXITO
//=====================================================

$_SESSION['exito'] = 'Docente actualizado correctamente.';


//=====================================================
// 14. REDIRECCIÓN
//=====================================================

header('Location: index.php');

exit();
