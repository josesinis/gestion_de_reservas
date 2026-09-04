<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : hashear.php
|--------------------------------------------------------------------------
| Descripción :
| Herramienta temporal para generar un hash de contraseña.
|
| IMPORTANTE:
| Este archivo debe eliminarse después de utilizarlo.
|--------------------------------------------------------------------------
*/

$hash = '';
$passwordIngresada = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $passwordIngresada = $_POST['password'] ?? '';

    if ($passwordIngresada !== '') {

        $hash = password_hash(
            $passwordIngresada,
            PASSWORD_DEFAULT
        );
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Generar hash de contraseña</title>

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

            <h1>Generar hash de contraseña</h1>

            <p>
                Herramienta temporal para actualizar contraseñas.
            </p>

            <form method="post">

                <div class="grupo-formulario">

                    <label for="password">
                        Nueva contraseña
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autofocus>

                </div>

                <div class="acciones">

                    <button
                        type="submit"
                        class="btn btn-primario">

                        Generar hash

                    </button>

                </div>

            </form>

            <?php if ($hash !== ''): ?>

                <div class="grupo-formulario">

                    <label for="hash">
                        Hash generado
                    </label>

                    <textarea
                        id="hash"
                        rows="4"
                        readonly><?= htmlspecialchars($hash) ?></textarea>

                </div>

                <p>
                    Copia este hash al campo
                    <strong>password</strong>
                    del usuario correspondiente en la base de datos.
                </p>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>
