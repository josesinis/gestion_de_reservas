<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/docentes/crear.php
|--------------------------------------------------------------------------
| Descripción :
| Formulario para crear un nuevo docente.
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
// 3. DATOS DEL FORMULARIO
//=====================================================

$nombres = '';
$apellidos = '';
$correo = '';
$activo = 1;

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Nuevo docente</title>


    <!--=================================================
         FONT AWESOME
    ==================================================-->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    <!--=================================================
         CSS
    ==================================================-->

    <link
        rel="stylesheet"
        href="../../assets/css/estilos.css">

    <link
        rel="stylesheet"
        href="../../assets/css/botones.css">

    <link
        rel="stylesheet"
        href="../../assets/css/formularios.css">

</head>


<body>


    <?php

    $seccionActual = 'administracion';

    require_once '../../includes/menu.php';

    ?>


    <div class="contenedor contenedor-usuario-formulario">


        <!--=================================================
         ENCABEZADO
    ==================================================-->

        <div class="encabezado-pagina encabezado-usuario">

            <div>

                <h1>
                    Nuevo docente
                </h1>

                <p>
                    Crear un nuevo docente para el sistema
                </p>

            </div>


            <div class="acciones encabezado-usuario-acciones">

                <a
                    href="index.php"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Volver

                </a>


                <button
                    type="submit"
                    form="form-docente"
                    class="btn btn-primario">

                    <i class="fa-solid fa-user-plus"></i>

                    Crear docente

                </button>

            </div>

        </div>


        <!--=================================================
         FORMULARIO
    ==================================================-->

        <form
            id="form-docente"
            action="guardar.php"
            method="post"
            class="formulario"
            autocomplete="off">


            <!--=================================================
             DATOS DEL DOCENTE
        ==================================================-->

            <section class="panel">

                <h2>
                    Datos del docente
                </h2>


                <div class="formulario-grid">


                    <!-- NOMBRES -->

                    <div class="grupo-formulario">

                        <label for="nombres">
                            Nombres
                        </label>

                        <input
                            type="text"
                            id="nombres"
                            name="nombres"
                            maxlength="50"
                            value="<?= htmlspecialchars($nombres) ?>"
                            required
                            autofocus>

                    </div>


                    <!-- APELLIDOS -->

                    <div class="grupo-formulario">

                        <label for="apellidos">
                            Apellidos
                        </label>

                        <input
                            type="text"
                            id="apellidos"
                            name="apellidos"
                            maxlength="50"
                            value="<?= htmlspecialchars($apellidos) ?>"
                            required>

                    </div>


                    <!-- CORREO -->

                    <div class="grupo-formulario">

                        <label for="correo">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            maxlength="50"
                            value="<?= htmlspecialchars($correo) ?>"
                            required>

                    </div>


                    <!-- ESTADO -->

                    <div class="grupo-formulario">

                        <label for="activo">
                            Estado
                        </label>

                        <select
                            id="activo"
                            name="activo"
                            required>

                            <option
                                value="1"
                                selected>
                                Activo
                            </option>

                            <option value="0">
                                Inactivo
                            </option>

                        </select>

                    </div>


                </div>

            </section>


        </form>


    </div>


</body>

</html>
