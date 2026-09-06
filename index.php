<?php

require_once 'includes/auth.php';

requiereLogin();

$seccionActual = 'inicio';

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Inicio - Gestión Institucional
    </title>


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    <!-- CSS generales -->

    <link
        rel="stylesheet"
        href="assets/css/estilos.css">

    <link
        rel="stylesheet"
        href="assets/css/botones.css">

    <link
        rel="stylesheet"
        href="assets/css/formularios.css">

    <link
        rel="stylesheet"
        href="assets/css/tablas.css">

</head>


<body>


    <?php

    require_once 'includes/menu.php';

    ?>


    <main class="contenedor">

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Inicio
                </h1>

                <p>
                    Sistema de Gestión Institucional
                </p>

            </div>

        </div>


        <div class="panel">

            <h2>
                Bienvenido
            </h2>

            <p>
                Seleccione una opción del menú para comenzar.
            </p>

        </div>


    </main>


</body>

</html>
