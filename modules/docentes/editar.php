<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/docentes/editar.php
|--------------------------------------------------------------------------
| Descripción :
| Formulario para editar un docente existente.
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
// 2. PERMISOS Y BASE DE DATOS
//=====================================================

require_once '../../includes/permisos.php';
require_once '../../config/database.php';

requiereRol('superadmin');


//=====================================================
// 3. OBTENER ID
//=====================================================

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id || $id <= 0) {

    $_SESSION['error'] = 'Docente no válido.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 4. OBTENER DOCENTE
//=====================================================

$sql = "
    SELECT
        id,
        nombres,
        apellidos,
        correo,
        activo
    FROM docentes
    WHERE id = ?
";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] = 'No fue posible consultar el docente.';

    header('Location: index.php');
    exit();
}


$stmt->bind_param('i', $id);

$stmt->execute();

$resultado = $stmt->get_result();

$docente = $resultado->fetch_assoc();

$stmt->close();


if (!$docente) {

    $_SESSION['error'] = 'El docente no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 5. DATOS DEL FORMULARIO
//=====================================================

$nombres = $docente['nombres'];

$apellidos = $docente['apellidos'];

$correo = $docente['correo'];

$activo = (int) $docente['activo'];

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Editar docente</title>


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
                    Editar docente
                </h1>

                <p>
                    Modificar los datos del docente seleccionado
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

                    <i class="fa-solid fa-floppy-disk"></i>

                    Guardar cambios

                </button>

            </div>

        </div>


        <!--=================================================
         FORMULARIO
        ==================================================-->

        <form
            id="form-docente"
            action="actualizar.php"
            method="post"
            class="formulario"
            autocomplete="off">


            <!--=================================================
             ID DEL DOCENTE
            ==================================================-->

            <input
                type="hidden"
                name="id"
                value="<?= (int) $docente['id'] ?>">


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
                                <?= $activo === 1 ? 'selected' : '' ?>>
                                Activo
                            </option>

                            <option
                                value="0"
                                <?= $activo === 0 ? 'selected' : '' ?>>
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
