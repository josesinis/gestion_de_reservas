<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/usuarios/cambiar_acceso.php
|--------------------------------------------------------------------------
| Descripción :
| Activa o bloquea el acceso de un usuario.
|
| Acceso :
| Exclusivo para superadmin.
|
| Protecciones :
| - Un superadmin no puede bloquearse a sí mismo.
| - No se puede bloquear al último superadmin activo.
|--------------------------------------------------------------------------
*/


//=====================================================
// 1. AUTENTICACIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();


//=====================================================
// 2. PERMISOS
//=====================================================

require_once '../../includes/permisos.php';

requiereRol('superadmin');


//=====================================================
// 3. BASE DE DATOS
//=====================================================

require_once '../../config/database.php';


//=====================================================
// 4. OBTENER DATOS
//=====================================================

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

$accion = $_GET['accion'] ?? '';


//=====================================================
// 5. VALIDAR DATOS RECIBIDOS
//=====================================================

if (!$id || $id <= 0) {

    $_SESSION['error'] =
        'El usuario seleccionado no es válido.';

    header('Location: index.php');
    exit();

}


if (
    $accion !== 'activar'
    && $accion !== 'bloquear'
) {

    $_SESSION['error'] =
        'La acción solicitada no es válida.';

    header('Location: index.php');
    exit();

}


//=====================================================
// 6. OBTENER USUARIO
//=====================================================

$sql = "

    SELECT
        id,
        nombres,
        apellidos,
        usuario,
        rol,
        acceso

    FROM usuarios

    WHERE id = ?

    LIMIT 1

";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible consultar el usuario.';

    header('Location: index.php');
    exit();

}

$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

$usuarioBD = $resultado->fetch_assoc();

$stmt->close();


//=====================================================
// 7. VALIDAR EXISTENCIA
//=====================================================

if (!$usuarioBD) {

    $_SESSION['error'] =
        'El usuario seleccionado no existe.';

    header('Location: index.php');
    exit();

}


//=====================================================
// 8. IDENTIFICAR USUARIO ACTUAL
//=====================================================

$usuarioActualId =
    (int) ($_SESSION['usuario_id'] ?? 0);


//=====================================================
// 9. IMPEDIR BLOQUEO PROPIO
//=====================================================

if (
    $accion === 'bloquear'
    && $id === $usuarioActualId
) {

    $_SESSION['error'] =
        'No puede bloquear su propia cuenta.';

    header('Location: index.php');
    exit();

}


//=====================================================
// 10. IMPEDIR BLOQUEAR AL ÚLTIMO SUPERADMIN
//=====================================================

if (
    $accion === 'bloquear'
    && $usuarioBD['rol'] === 'superadmin'
    && (int) $usuarioBD['acceso'] === 1
) {

    $sqlSuperadmin = "

        SELECT COUNT(*) AS cantidad

        FROM usuarios

        WHERE rol = 'superadmin'
          AND acceso = 1

    ";

    $resultadoSuperadmin =
        $conexion->query($sqlSuperadmin);

    if (!$resultadoSuperadmin) {

        $_SESSION['error'] =
            'No fue posible verificar los superadmin activos.';

        header('Location: index.php');
        exit();

    }

    $filaSuperadmin =
        $resultadoSuperadmin->fetch_assoc();

    $cantidadSuperadmin =
        (int) $filaSuperadmin['cantidad'];


    if ($cantidadSuperadmin <= 1) {

        $_SESSION['error'] =
            'No se puede bloquear al último superadmin activo.';

        header('Location: index.php');
        exit();

    }

}


//=====================================================
// 11. DETERMINAR NUEVO ESTADO
//=====================================================

$nuevoAcceso =
    $accion === 'activar'
        ? 1
        : 0;


//=====================================================
// 12. ACTUALIZAR ACCESO
//=====================================================

$sqlActualizar = "

    UPDATE usuarios

    SET acceso = ?

    WHERE id = ?

";

$stmtActualizar =
    $conexion->prepare($sqlActualizar);

if (!$stmtActualizar) {

    $_SESSION['error'] =
        'No fue posible preparar la actualización.';

    header('Location: index.php');
    exit();

}

$stmtActualizar->bind_param(
    'ii',
    $nuevoAcceso,
    $id
);


//=====================================================
// 13. EJECUTAR
//=====================================================

if (!$stmtActualizar->execute()) {

    $_SESSION['error'] =
        'No fue posible modificar el acceso del usuario.';

    $stmtActualizar->close();

    header('Location: index.php');
    exit();

}

$stmtActualizar->close();


//=====================================================
// 14. MENSAJE DE ÉXITO
//=====================================================

if ($nuevoAcceso === 1) {

    $_SESSION['exito'] =
        'El acceso del usuario fue activado correctamente.';

} else {

    $_SESSION['exito'] =
        'El acceso del usuario fue bloqueado correctamente.';

}


//=====================================================
// 15. VOLVER AL LISTADO
//=====================================================

header('Location: index.php');

exit();
