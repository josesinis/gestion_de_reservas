<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/usuarios/crear.php
|--------------------------------------------------------------------------
| Descripción :
| Formulario para crear un nuevo usuario del sistema.
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
$usuario = '';
$rol = 'usuario';
$acceso = 1;

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Nuevo usuario</title>


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
                    Nuevo usuario
                </h1>

                <p>
                    Crear una nueva cuenta de acceso al sistema
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
                    form="form-usuario"
                    class="btn btn-primario">
                    <i class="fa-solid fa-user-plus"></i>
                    Crear usuario
                </button>

            </div>

        </div>


        <!--=================================================
         FORMULARIO
    ==================================================-->

        <form
            id="form-usuario"
            action="guardar.php"
            method="post"
            class="formulario"
            autocomplete="off">


            <!--=================================================
             DATOS DEL USUARIO
        ==================================================-->

            <section class="panel">

                <h2>
                    Datos del usuario
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


                </div>

            </section>


            <!--=================================================
             DATOS DE ACCESO
        ==================================================-->

            <section class="panel">

                <h2>
                    Datos de acceso
                </h2>


                <div class="formulario-grid">


                    <!-- USUARIO -->

                    <div class="grupo-formulario">

                        <label for="usuario">
                            Usuario
                        </label>

                        <input
                            type="text"
                            id="usuario"
                            name="usuario"
                            maxlength="30"
                            value="<?= htmlspecialchars($usuario) ?>"
                            required>

                        <small>
                            Nombre utilizado para iniciar sesión.
                        </small>

                    </div>


                    <!-- CONTRASEÑA -->

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

                        <small>
                            La contraseña se almacenará de forma segura.
                        </small>

                    </div>


                    <!-- ROL -->

                    <div class="grupo-formulario">

                        <label for="rol">
                            Rol
                        </label>

                        <select
                            id="rol"
                            name="rol"
                            required>

                            <option
                                value="usuario"
                                selected>
                                Usuario
                            </option>

                            <option value="admin">
                                Administrador
                            </option>

                            <option value="superadmin">
                                Superadmin
                            </option>

                        </select>

                    </div>


                    <!-- ACCESO -->

                    <div class="grupo-formulario">

                        <label for="acceso">
                            Acceso al sistema
                        </label>

                        <select
                            id="acceso"
                            name="acceso"
                            required>

                            <option
                                value="1"
                                selected>
                                Activo
                            </option>

                            <option value="0">
                                Bloqueado
                            </option>

                        </select>

                    </div>


                </div>

            </section>


            <!--=================================================
             ACCIONES
        ==================================================

            <div class="acciones acciones-formulario-usuario">

                <a
                    href="index.php"
                    class="btn btn-secondary">

                    Cancelar

                </a>


                <button
                    type="submit"
                    class="btn btn-primario">

                    <i class="fa-solid fa-user-plus"></i>

                    Crear usuario

                </button>

            </div> -->


        </form>


    </div>


</body>

</html>
