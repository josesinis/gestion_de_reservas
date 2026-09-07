<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/usuarios/editar.php
|--------------------------------------------------------------------------
| Descripción :
| Formulario para editar un usuario existente.
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
// 4. OBTENER ID
//=====================================================

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {

    $_SESSION['error'] =
        'El usuario seleccionado no es válido.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 5. BUSCAR USUARIO
//=====================================================

$sql = "

    SELECT
        id,
        nombres,
        apellidos,
        correo,
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
// 6. VALIDAR EXISTENCIA
//=====================================================

if (!$usuarioBD) {

    $_SESSION['error'] =
        'El usuario seleccionado no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 7. DATOS DEL USUARIO
//=====================================================

$nombres = $usuarioBD['nombres'];

$apellidos = $usuarioBD['apellidos'];

$correo = $usuarioBD['correo'];

$usuario = $usuarioBD['usuario'];

$rol = $usuarioBD['rol'];

$acceso = (int) $usuarioBD['acceso'];

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Editar usuario</title>


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
                    Editar usuario
                </h1>

                <p>
                    Modificar los datos de la cuenta de acceso
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

                    <i class="fa-solid fa-floppy-disk"></i>

                    Guardar cambios

                </button>

            </div>

        </div>


        <!--=================================================
         FORMULARIO
    ==================================================-->

        <form
            id="form-usuario"
            action="actualizar.php"
            method="post"
            class="formulario"
            autocomplete="off">


            <!--=================================================
             ID
        ==================================================-->

            <input
                type="hidden"
                name="id"
                value="<?= (int) $usuarioBD['id'] ?>">


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
                            Nueva contraseña
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            maxlength="255"
                            autocomplete="new-password">

                        <small>
                            Deje este campo vacío para conservar la contraseña actual.
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
                                <?= $rol === 'usuario' ? 'selected' : '' ?>>
                                Usuario
                            </option>

                            <option
                                value="admin"
                                <?= $rol === 'admin' ? 'selected' : '' ?>>
                                Administrador
                            </option>

                            <option
                                value="superadmin"
                                <?= $rol === 'superadmin' ? 'selected' : '' ?>>
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
                                <?= $acceso === 1 ? 'selected' : '' ?>>
                                Activo
                            </option>

                            <option
                                value="0"
                                <?= $acceso === 0 ? 'selected' : '' ?>>
                                Bloqueado
                            </option>

                        </select>

                    </div>


                </div>

            </section>


        </form>


    </div>


</body>

</html>
