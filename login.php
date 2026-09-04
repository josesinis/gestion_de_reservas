<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : login.php
|--------------------------------------------------------------------------
| Descripción :
| Formulario de inicio de sesión.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/auth.php';


//=====================================================
// SI YA EXISTE SESIÓN
//=====================================================

if (!empty($_SESSION['usuario_id'])) {
    header('Location: /gestion_de_reservas/modules/reservas/agenda.php');
    exit();
}


//=====================================================
// MENSAJES
//=====================================================

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['exito']);
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión</title>

    <link
        rel="stylesheet"
        href="assets/css/estilos.css">

    <link
        rel="stylesheet"
        href="assets/css/botones.css">

    <link
        rel="stylesheet"
        href="assets/css/formularios.css">

</head>

<body>

    <main class="contenedor">

        <section class="panel">

            <h1>Iniciar sesión</h1>

            <?php if ($error !== ''): ?>

                <div class="mensaje mensaje-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <?php if ($exito !== ''): ?>

                <div class="mensaje mensaje-exito">
                    <?= htmlspecialchars($exito) ?>
                </div>

            <?php endif; ?>

            <form
                action="validar_login.php"
                method="post"
                autocomplete="off">

                <div class="grupo-formulario">

                    <label for="usuario">
                        Usuario
                    </label>

                    <input
                        type="text"
                        id="usuario"
                        name="usuario"
                        maxlength="30"
                        required
                        autofocus>

                </div>

                <div class="grupo-formulario">

                    <label for="password">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        maxlength="255"
                        required>

                </div>

                <div class="acciones">

                    <button
                        type="submit"
                        class="btn btn-primario">

                        Ingresar

                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>
