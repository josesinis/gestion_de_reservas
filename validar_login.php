<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : validar_login.php
|--------------------------------------------------------------------------
| Descripción :
| Valida las credenciales de inicio de sesión.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';


//=====================================================
// VALIDAR MÉTODO DE ACCESO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: login.php');
    exit();
}


//=====================================================
// RECIBIR DATOS
//=====================================================

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';


//=====================================================
// VALIDAR CAMPOS
//=====================================================

if ($usuario === '' || $password === '') {

    $_SESSION['error'] =
        'Debe ingresar usuario y contraseña.';

    header('Location: login.php');
    exit();
}


//=====================================================
// BUSCAR USUARIO
//=====================================================

$sql = "
    SELECT
        id,
        nombres,
        apellidos,
        usuario,
        password,
        rol,
        acceso
    FROM usuarios
    WHERE usuario = ?
    LIMIT 1
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible procesar el inicio de sesión.';

    header('Location: login.php');
    exit();
}

$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();
$usuarioBD = $resultado->fetch_assoc();

$stmt->close();


//=====================================================
// VALIDAR CREDENCIALES Y ACCESO
//=====================================================

if (!$usuarioBD) {

    $_SESSION['error'] =
        'Usuario o contraseña incorrectos.';

    header('Location: login.php');
    exit();
}

if ((int) $usuarioBD['acceso'] !== 1) {

    $_SESSION['error'] =
        'El usuario no tiene acceso al sistema.';

    header('Location: login.php');
    exit();
}

if (!password_verify($password, $usuarioBD['password'])) {

    $_SESSION['error'] =
        'Usuario o contraseña incorrectos.';

    header('Location: login.php');
    exit();
}


//=====================================================
// CREAR SESIÓN
//=====================================================

session_regenerate_id(true);

$_SESSION['usuario_id'] = (int) $usuarioBD['id'];

$_SESSION['usuario'] = $usuarioBD['usuario'];

$_SESSION['nombre_usuario'] =
    trim(
        $usuarioBD['nombres'] . ' ' .
            $usuarioBD['apellidos']
    );

$_SESSION['rol'] = $usuarioBD['rol'];


//=====================================================
// ACTUALIZAR ÚLTIMO ACCESO
//=====================================================

$sqlAcceso = "
    UPDATE usuarios
    SET ultimo_acceso = NOW()
    WHERE id = ?
";

$stmtAcceso = $conexion->prepare($sqlAcceso);

if ($stmtAcceso) {

    $stmtAcceso->bind_param(
        "i",
        $usuarioBD['id']
    );

    $stmtAcceso->execute();
    $stmtAcceso->close();
}


//=====================================================
// INGRESO CORRECTO
//=====================================================

header('Location: /gestion_de_reservas/modules/reservas/agenda.php');
exit();
