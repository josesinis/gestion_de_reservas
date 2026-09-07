<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/usuarios/actualizar.php
|--------------------------------------------------------------------------
| Descripción :
| Procesa la modificación de un usuario existente.
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

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

$nombres = trim(
    $_POST['nombres'] ?? ''
);

$apellidos = trim(
    $_POST['apellidos'] ?? ''
);

$correo = trim(
    $_POST['correo'] ?? ''
);

$usuario = trim(
    $_POST['usuario'] ?? ''
);

$password = $_POST['password'] ?? '';

$rol = $_POST['rol'] ?? '';

$acceso = isset($_POST['acceso'])
    ? (int) $_POST['acceso']
    : -1;


//=====================================================
// 6. VALIDAR ID
//=====================================================

if (!$id || $id <= 0) {

    $_SESSION['error'] =
        'El usuario seleccionado no es válido.';

    header('Location: index.php');
    exit();

}


//=====================================================
// 7. VALIDAR CAMPOS OBLIGATORIOS
//=====================================================

if (
    $nombres === ''
    || $apellidos === ''
    || $correo === ''
    || $usuario === ''
    || $rol === ''
) {

    $_SESSION['error'] =
        'Debe completar todos los campos obligatorios.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


//=====================================================
// 8. VALIDAR LONGITUDES
//=====================================================

if (mb_strlen($nombres) > 50) {

    $_SESSION['error'] =
        'Los nombres no pueden superar los 50 caracteres.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


if (mb_strlen($apellidos) > 50) {

    $_SESSION['error'] =
        'Los apellidos no pueden superar los 50 caracteres.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


if (mb_strlen($correo) > 50) {

    $_SESSION['error'] =
        'El correo no puede superar los 50 caracteres.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


if (mb_strlen($usuario) > 30) {

    $_SESSION['error'] =
        'El usuario no puede superar los 30 caracteres.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


if (mb_strlen($password) > 255) {

    $_SESSION['error'] =
        'La contraseña supera la longitud permitida.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


//=====================================================
// 9. VALIDAR CORREO
//=====================================================

if (!filter_var(
    $correo,
    FILTER_VALIDATE_EMAIL
)) {

    $_SESSION['error'] =
        'Ingrese un correo electrónico válido.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


//=====================================================
// 10. VALIDAR ROL
//=====================================================

$rolesPermitidos = [
    'usuario',
    'admin',
    'superadmin'
];

if (!in_array(
    $rol,
    $rolesPermitidos,
    true
)) {

    $_SESSION['error'] =
        'El rol seleccionado no es válido.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


//=====================================================
// 11. VALIDAR ACCESO
//=====================================================

if (
    $acceso !== 0
    && $acceso !== 1
) {

    $_SESSION['error'] =
        'El estado de acceso seleccionado no es válido.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


//=====================================================
// 12. VERIFICAR QUE EL USUARIO EXISTA
//=====================================================

$sqlUsuario = "

    SELECT
        id,
        password

    FROM usuarios

    WHERE id = ?

    LIMIT 1

";

$stmtUsuario = $conexion->prepare(
    $sqlUsuario
);

if (!$stmtUsuario) {

    $_SESSION['error'] =
        'No fue posible consultar el usuario.';

    header('Location: index.php');
    exit();

}

$stmtUsuario->bind_param(
    'i',
    $id
);

$stmtUsuario->execute();

$resultadoUsuario =
    $stmtUsuario->get_result();

$usuarioBD =
    $resultadoUsuario->fetch_assoc();

$stmtUsuario->close();


if (!$usuarioBD) {

    $_SESSION['error'] =
        'El usuario seleccionado no existe.';

    header('Location: index.php');
    exit();

}


//=====================================================
// 13. VERIFICAR CORREO O USUARIO DUPLICADO
//=====================================================

$sqlExiste = "

    SELECT
        id,
        correo,
        usuario

    FROM usuarios

    WHERE (
        correo = ?
        OR usuario = ?
    )

    AND id <> ?

    LIMIT 1

";

$stmtExiste = $conexion->prepare(
    $sqlExiste
);

if (!$stmtExiste) {

    $_SESSION['error'] =
        'No fue posible validar los datos del usuario.';

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}

$stmtExiste->bind_param(
    'ssi',
    $correo,
    $usuario,
    $id
);

$stmtExiste->execute();

$resultadoExiste =
    $stmtExiste->get_result();

$usuarioExistente =
    $resultadoExiste->fetch_assoc();

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

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


//=====================================================
// 14. ACTUALIZAR DATOS
//=====================================================
//
// Si password está vacío:
//
//     conserva la contraseña actual.
//
// Si password contiene un valor:
//
//     genera un nuevo hash.
//
//=====================================================


if ($password === '') {

    $sql = "

        UPDATE usuarios

        SET
            nombres = ?,
            apellidos = ?,
            correo = ?,
            usuario = ?,
            rol = ?,
            acceso = ?

        WHERE id = ?

    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {

        $_SESSION['error'] =
            'No fue posible preparar la actualización.';

        header(
            'Location: editar.php?id=' . $id
        );

        exit();

    }

    $stmt->bind_param(
        'sssssii',
        $nombres,
        $apellidos,
        $correo,
        $usuario,
        $rol,
        $acceso,
        $id
    );

} else {

    $passwordHash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    if ($passwordHash === false) {

        $_SESSION['error'] =
            'No fue posible generar la nueva contraseña de forma segura.';

        header(
            'Location: editar.php?id=' . $id
        );

        exit();

    }


    $sql = "

        UPDATE usuarios

        SET
            nombres = ?,
            apellidos = ?,
            correo = ?,
            usuario = ?,
            password = ?,
            rol = ?,
            acceso = ?

        WHERE id = ?

    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {

        $_SESSION['error'] =
            'No fue posible preparar la actualización.';

        header(
            'Location: editar.php?id=' . $id
        );

        exit();

    }

    $stmt->bind_param(
        'ssssssii',
        $nombres,
        $apellidos,
        $correo,
        $usuario,
        $passwordHash,
        $rol,
        $acceso,
        $id
    );

}


//=====================================================
// 15. EJECUTAR ACTUALIZACIÓN
//=====================================================

if (!$stmt->execute()) {

    if ($stmt->errno === 1062) {

        $_SESSION['error'] =
            'El correo electrónico o nombre de usuario ya está registrado.';

    } else {

        $_SESSION['error'] =
            'No fue posible actualizar el usuario.';

    }

    $stmt->close();

    header(
        'Location: editar.php?id=' . $id
    );

    exit();

}


$stmt->close();


//=====================================================
// 16. ÉXITO
//=====================================================

$_SESSION['exito'] =
    'El usuario fue actualizado correctamente.';


//=====================================================
// 17. VOLVER AL LISTADO
//=====================================================

header('Location: index.php');

exit();
