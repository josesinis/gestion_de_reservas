<?php

//=====================================================
// ASIGNATURAS - CREAR
//=====================================================
//
// Formulario para crear una nueva asignatura.
//
// Acceso:
// - Exclusivo para superadmin.
//
//=====================================================


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

$asignaturaNombre = '';
$modalidad = 'asignatura';
$activo = 1;

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Nueva asignatura</title>


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
                    Nueva asignatura
                </h1>

                <p>
                    Crear una nueva asignatura para el sistema
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

                    <i class="fa-solid fa-book"></i>

                    Crear asignatura

                </button>

            </div>

        </div>


        <!--=================================================
             FORMULARIO
        ==================================================-->

        <form
            id="form-asignatura"
            action="guardar.php"
            method="post"
            class="formulario"
            autocomplete="off">


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
