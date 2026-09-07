<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/usuarios/guardar.php
|--------------------------------------------------------------------------
| Descripción :
| Procesa la creación de un nuevo usuario.
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
// 2. PERMISOS
//=====================================================

require_once '../../includes/permisos.php';

requiereRol('superadmin');


//=====================================================
// 3. BASE DE DATOS
//=====================================================

require_once '../../config/database.php';


//=====================================================
// 4. VALIDAR MÉTODO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit();

}


//=====================================================
// 5. RECIBIR DATOS
//=====================================================

$nombres = trim($_POST['nombres'] ?? '');

$apellidos = trim($_POST['apellidos'] ?? '');

$correo = trim($_POST['correo'] ?? '');

$usuario = trim($_POST['usuario'] ?? '');

$password = $_POST['password'] ?? '';

$rol = $_POST['rol'] ?? 'usuario';

$acceso = isset($_POST['acceso'])
    ? (int) $_POST['acceso']
    : 1;


//=====================================================
// 6. VALIDAR CAMPOS OBLIGATORIOS
//=====================================================

if (
    $nombres === ''
    || $apellidos === ''
    || $correo === ''
    || $usuario === ''
    || $password === ''
) {

    $_SESSION['error'] =
        'Debe completar todos los campos obligatorios.';

    header('Location: crear.php');
    exit();

}


//=====================================================
// 7. VALIDAR LONGITUDES
//=====================================================

if (mb_strlen($nombres) > 50) {

    $_SESSION['error'] =
        'Los nombres no pueden superar los 50 caracteres.';

    header('Location: crear.php');
    exit();

}


if (mb_strlen($apellidos) > 50) {

    $_SESSION['error'] =
        'Los apellidos no pueden superar los 50 caracteres.';

    header('Location: crear.php');
    exit();

}


if (mb_strlen($correo) > 50) {

    $_SESSION['error'] =
        'El correo no puede superar los 50 caracteres.';

    header('Location: crear.php');
    exit();

}


if (mb_strlen($usuario) > 30) {

    $_SESSION['error'] =
        'El usuario no puede superar los 30 caracteres.';

    header('Location: crear.php');
    exit();

}


if (mb_strlen($password) > 255) {

    $_SESSION['error'] =
        'La contraseña supera la longitud permitida.';

    header('Location: crear.php');
    exit();

}


//=====================================================
// 8. VALIDAR CORREO
//=====================================================

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error'] =
        'Ingrese un correo electrónico válido.';

    header('Location: crear.php');
    exit();

}


//=====================================================
// 9. VALIDAR ROL
//=====================================================

$rolesPermitidos = [
    'usuario',
    'admin',
    'superadmin'
];

if (!in_array($rol, $rolesPermitidos, true)) {

    $_SESSION['error'] =
        'El rol seleccionado no es válido.';

    header('Location: crear.php');
    exit();

}


//=====================================================
// 10. VALIDAR ACCESO
//=====================================================

if ($acceso !== 0 && $acceso !== 1) {

    $_SESSION['error'] =
        'El estado de acceso seleccionado no es válido.';

    header('Location: crear.php');
    exit();

}


//=====================================================
// 11. VERIFICAR DUPLICADOS
//=====================================================

$sqlExiste = "

    SELECT
        id,
        correo,
        usuario

    FROM usuarios

    WHERE correo = ?
       OR usuario = ?

    LIMIT 1

";

$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {

    $_SESSION['error'] =
        'No fue posible procesar la solicitud.';

    header('Location: crear.php');
    exit();

}


$stmtExiste->bind_param(
    'ss',
    $correo,
    $usuario
);

$stmtExiste->execute();

$resultadoExiste = $stmtExiste->get_result();

$usuarioExistente = $resultadoExiste->fetch_assoc();

$stmtExiste->close();


if ($usuarioExistente) {

    if (
        strcasecmp(
            $usuarioExistente['usuario'],
            $usuario
        ) === 0
    ) {

        $_SESSION['error'] =
            'El nombre de usuario ya está registrado.';

    } else {

        $_SESSION['error'] =
            'El correo electrónico ya está registrado.';

    }

    header('Location: crear.php');
    exit();

}


//=====================================================
// 12. GENERAR HASH DE CONTRASEÑA
//=====================================================

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


if ($passwordHash === false) {

    $_SESSION['error'] =
        'No fue posible generar la contraseña de forma segura.';

    header('Location: crear.php');
    exit();

}


//=====================================================
// 13. INSERTAR USUARIO
//=====================================================

$sql = "

    INSERT INTO usuarios (
        nombres,
        apellidos,
        correo,
        usuario,
        password,
        rol,
        acceso
    )

    VALUES (?, ?, ?, ?, ?, ?, ?)

";


$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible crear el usuario.';

    header('Location: crear.php');
    exit();

}


$stmt->bind_param(
    'ssssssi',
    $nombres,
    $apellidos,
    $correo,
    $usuario,
    $passwordHash,
    $rol,
    $acceso
);


//=====================================================
// 14. EJECUTAR
//=====================================================

if (!$stmt->execute()) {

    /*
     * Control adicional por si otro proceso creó
     * simultáneamente el mismo correo o usuario.
     */

    if ($stmt->errno === 1062) {

        $_SESSION['error'] =
            'El correo electrónico o nombre de usuario ya está registrado.';

    } else {

        $_SESSION['error'] =
            'No fue posible crear el usuario.';

    }

    $stmt->close();

    header('Location: crear.php');
    exit();

}


$stmt->close();


//=====================================================
// 15. ÉXITO
//=====================================================

$_SESSION['exito'] =
    'El usuario fue creado correctamente.';


//=====================================================
// 16. VOLVER AL LISTADO
//=====================================================

header('Location: index.php');

exit();
