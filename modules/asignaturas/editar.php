<?php

/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/asignaturas/editar.php
|--------------------------------------------------------------------------
| Descripción :
| Formulario para editar una asignatura existente.
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

    $_SESSION['error'] = 'Asignatura no válida.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 4. OBTENER ASIGNATURA
//=====================================================

$sql = "

    SELECT
        id,
        asignatura_nombre,
        modalidad,
        activo

    FROM asignaturas

    WHERE id = ?

";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible consultar la asignatura.';

    header('Location: index.php');
    exit();
}


$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

$asignatura = $resultado->fetch_assoc();

$stmt->close();


if (!$asignatura) {

    $_SESSION['error'] =
        'La asignatura no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 5. DATOS DEL FORMULARIO
//=====================================================

$asignaturaNombre =
    $asignatura['asignatura_nombre'];

$modalidad =
    $asignatura['modalidad'];

$activo =
    (int) $asignatura['activo'];

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Editar asignatura</title>


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
                    Editar asignatura
                </h1>

                <p>
                    Modificar los datos de la asignatura seleccionada
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
                    form="form-asignatura"
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
            id="form-asignatura"
            action="actualizar.php"
            method="post"
            class="formulario"
            autocomplete="off">


            <!--=================================================
                 ID DE LA ASIGNATURA
            ==================================================-->

            <input
                type="hidden"
                name="id"
                value="<?= (int) $asignatura['id'] ?>">


            <!--=================================================
                 DATOS DE LA ASIGNATURA
            ==================================================-->

            <section class="panel">

                <h2>
                    Datos de la asignatura
                </h2>


                <div class="formulario-grid">


                    <!-- ASIGNATURA -->

                    <div class="grupo-formulario">

                        <label for="asignatura_nombre">
                            Nombre de la asignatura
                        </label>

                        <input
                            type="text"
                            id="asignatura_nombre"
                            name="asignatura_nombre"
                            maxlength="50"
                            value="<?= htmlspecialchars($asignaturaNombre) ?>"
                            required
                            autofocus>

                    </div>


                    <!-- MODALIDAD -->

                    <div class="grupo-formulario">

                        <label for="modalidad">
                            Modalidad
                        </label>

                        <select
                            id="modalidad"
                            name="modalidad"
                            required>

                            <option
                                value="asignatura"
                                <?= $modalidad === 'asignatura' ? 'selected' : '' ?>>

                                Asignatura

                            </option>

                            <option
                                value="taller"
                                <?= $modalidad === 'taller' ? 'selected' : '' ?>>

                                Taller

                            </option>

                        </select>

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
