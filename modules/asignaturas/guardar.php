<?php

/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/asignaturas/guardar.php
|--------------------------------------------------------------------------
| Descripción :
| Guarda una nueva asignatura en la base de datos.
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

$asignaturaNombre = trim(
    $_POST['asignatura_nombre'] ?? ''
);

$modalidad = $_POST['modalidad'] ?? '';

$activo = $_POST['activo'] ?? '';


//=====================================================
// 6. VALIDAR CAMPOS OBLIGATORIOS
//=====================================================

if (
    $asignaturaNombre === '' ||
    $modalidad === '' ||
    $activo === ''
) {

    $_SESSION['error'] = 'Todos los campos son obligatorios.';

    header('Location: crear.php');
    exit();
}


//=====================================================
// 7. VALIDAR LONGITUD
//=====================================================

if (mb_strlen($asignaturaNombre) > 50) {

    $_SESSION['error'] =
        'El nombre de la asignatura no puede superar los 50 caracteres.';

    header('Location: crear.php');
    exit();
}


//=====================================================
// 8. VALIDAR MODALIDAD
//=====================================================

if (
    $modalidad !== 'asignatura' &&
    $modalidad !== 'taller'
) {

    $_SESSION['error'] =
        'La modalidad seleccionada no es válida.';

    header('Location: crear.php');
    exit();
}


//=====================================================
// 9. VALIDAR ESTADO
//=====================================================

if (
    $activo !== '0' &&
    $activo !== '1'
) {

    $_SESSION['error'] =
        'El estado seleccionado no es válido.';

    header('Location: crear.php');
    exit();
}


//=====================================================
// 10. COMPROBAR DUPLICADO
//=====================================================
//
// No permitimos dos registros con el mismo nombre
// y modalidad.
//

$sql = "

    SELECT id

    FROM asignaturas

    WHERE asignatura_nombre = ?
      AND modalidad = ?

    LIMIT 1

";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible comprobar la asignatura.';

    header('Location: crear.php');
    exit();
}


$stmt->bind_param(
    'ss',
    $asignaturaNombre,
    $modalidad
);

$stmt->execute();

$resultado = $stmt->get_result();

$existe = $resultado->fetch_assoc();

$stmt->close();


if ($existe) {

    $_SESSION['error'] =
        'Ya existe una asignatura con ese nombre y modalidad.';

    header('Location: crear.php');
    exit();
}


//=====================================================
// 11. INSERTAR ASIGNATURA
//=====================================================

$sql = "

    INSERT INTO asignaturas (
        asignatura_nombre,
        modalidad,
        activo
    )

    VALUES (?, ?, ?)

";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible preparar el registro de la asignatura.';

    header('Location: crear.php');
    exit();
}


$activoInt = (int) $activo;


$stmt->bind_param(
    'ssi',
    $asignaturaNombre,
    $modalidad,
    $activoInt
);


//=====================================================
// 12. EJECUTAR
//=====================================================

if (!$stmt->execute()) {

    $_SESSION['error'] =
        'No fue posible guardar la asignatura.';

    $stmt->close();

    header('Location: crear.php');
    exit();
}


$stmt->close();


//=====================================================
// 13. MENSAJE DE ÉXITO
//=====================================================

$_SESSION['exito'] =
    'Asignatura creada correctamente.';


//=====================================================
// 14. REDIRECCIÓN
//=====================================================

header('Location: index.php');

exit();
