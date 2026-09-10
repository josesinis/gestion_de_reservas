<?php

/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/asignaturas/actualizar.php
|--------------------------------------------------------------------------
| Descripción :
| Procesa la modificación de una asignatura existente.
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

requiereRol('superadmin');


//=====================================================
// 3. VALIDAR MÉTODO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['error'] =
        'Solicitud no válida.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 4. RECIBIR DATOS
//=====================================================

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

$asignaturaNombre =
    trim($_POST['asignatura_nombre'] ?? '');

$modalidad =
    $_POST['modalidad'] ?? '';

$activo =
    $_POST['activo'] ?? '';


//=====================================================
// 5. VALIDAR ID
//=====================================================

if (!$id || $id <= 0) {

    $_SESSION['error'] =
        'Asignatura no válida.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 6. VALIDAR NOMBRE
//=====================================================

if ($asignaturaNombre === '') {

    $_SESSION['error'] =
        'El nombre de la asignatura es obligatorio.';

    header('Location: editar.php?id=' . $id);
    exit();
}


if (mb_strlen($asignaturaNombre) > 50) {

    $_SESSION['error'] =
        'El nombre de la asignatura no puede superar los 50 caracteres.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 7. VALIDAR MODALIDAD
//=====================================================

$modalidadesPermitidas = [
    'asignatura',
    'taller'
];

if (!in_array($modalidad, $modalidadesPermitidas, true)) {

    $_SESSION['error'] =
        'La modalidad seleccionada no es válida.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 8. VALIDAR ESTADO
//=====================================================

if ($activo !== '0' && $activo !== '1') {

    $_SESSION['error'] =
        'El estado seleccionado no es válido.';

    header('Location: editar.php?id=' . $id);
    exit();
}

$activo = (int) $activo;


//=====================================================
// 9. VERIFICAR QUE LA ASIGNATURA EXISTA
//=====================================================

$sql = "

    SELECT id

    FROM asignaturas

    WHERE id = ?

    LIMIT 1

";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible verificar la asignatura.';

    header('Location: index.php');
    exit();
}


$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

$existe = $resultado->fetch_assoc();

$stmt->close();


if (!$existe) {

    $_SESSION['error'] =
        'La asignatura no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 10. VERIFICAR DUPLICADO
//=====================================================

$sql = "

    SELECT id

    FROM asignaturas

    WHERE asignatura_nombre = ?
      AND modalidad = ?
      AND id <> ?

    LIMIT 1

";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible validar la asignatura.';

    header('Location: editar.php?id=' . $id);
    exit();
}


$stmt->bind_param(
    'ssi',
    $asignaturaNombre,
    $modalidad,
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

$duplicado = $resultado->fetch_assoc();

$stmt->close();


if ($duplicado) {

    $_SESSION['error'] =
        'Ya existe otra asignatura con el mismo nombre y modalidad.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 11. ACTUALIZAR ASIGNATURA
//=====================================================

$sql = "

    UPDATE asignaturas

    SET
        asignatura_nombre = ?,
        modalidad = ?,
        activo = ?

    WHERE id = ?

";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible preparar la actualización.';

    header('Location: editar.php?id=' . $id);
    exit();
}


$stmt->bind_param(
    'ssii',
    $asignaturaNombre,
    $modalidad,
    $activo,
    $id
);


if ($stmt->execute()) {

    $_SESSION['exito'] =
        'Asignatura actualizada correctamente.';

} else {

    $_SESSION['error'] =
        'No fue posible actualizar la asignatura.';
}


$stmt->close();


//=====================================================
// 12. VOLVER AL LISTADO
//=====================================================

header('Location: index.php');
exit();
