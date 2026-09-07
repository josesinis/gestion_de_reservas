<?php

//=====================================================
// DOCENTES - LISTADO
//=====================================================
//
// Muestra los docentes registrados en el sistema.
//
// Permisos:
// - admin: puede consultar el listado.
// - superadmin: puede crear y editar.
//
// Los docentes no se eliminan físicamente.
// El estado se controla mediante el campo "activo"
// desde el formulario de edición.
//
//=====================================================


//=====================================================
// 1. VALIDAR SESIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();


//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../includes/permisos.php';
require_once '../../config/database.php';


//=====================================================
// 3. VALIDAR ROL
//=====================================================

requiereRol([
    'admin',
    'superadmin'
]);

$rolUsuario = $_SESSION['rol'] ?? 'usuario';


//=====================================================
// 4. MENSAJES
//=====================================================

$mensajeExito = $_SESSION['exito'] ?? '';

$mensajeError = $_SESSION['error'] ?? '';

unset($_SESSION['exito']);

unset($_SESSION['error']);


//=====================================================
// 5. OBTENER DOCENTES
//=====================================================

$docentes = [];

$sql = "

    SELECT

        id,
        nombres,
        apellidos,
        correo,
        activo

    FROM docentes

    ORDER BY
        apellidos ASC,
        nombres ASC

";

$resultado = $conexion->query($sql);

if ($resultado) {

    $docentes = $resultado->fetch_all(
        MYSQLI_ASSOC
    );
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Docentes</title>


    <!--=================================================
         FONT AWESOME
    ==================================================-->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    <!--=================================================
         CSS GENERALES
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

    <link
        rel="stylesheet"
        href="../../assets/css/tablas.css">

</head>


<body>


    <?php

    $seccionActual = 'administracion';

    require_once '../../includes/menu.php';

    ?>


    <div class="contenedor contenedor-usuarios">


        <!--=================================================
             ENCABEZADO
        ==================================================-->

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Docentes
                </h1>

                <p>
                    Administración de los docentes registrados en el sistema
                </p>

            </div>


            <?php if ($rolUsuario === 'superadmin'): ?>

                <div>

                    <a
                        href="crear.php"
                        class="btn btn-primary">

                        <i class="fa-solid fa-user-plus"></i>

                        Nuevo docente

                    </a>

                </div>

            <?php endif; ?>

        </div>


        <!--=================================================
             MENSAJES
        ==================================================-->

        <?php if ($mensajeExito !== ''): ?>

            <div class="mensaje mensaje-exito">

                <?= htmlspecialchars($mensajeExito) ?>

            </div>

        <?php endif; ?>


        <?php if ($mensajeError !== ''): ?>

            <div class="mensaje mensaje-error">

                <?= htmlspecialchars($mensajeError) ?>

            </div>

        <?php endif; ?>


        <!--=================================================
             RESUMEN
        ==================================================-->

        <div class="resumen-usuarios">

            Registros encontrados:

            <strong>
                <?= count($docentes) ?>
            </strong>

        </div>


        <!--=================================================
             TABLA
        ==================================================-->

        <div class="tabla-contenedor">

            <table class="tabla">

                <thead>

                    <tr>

                        <th>
                            Nombre
                        </th>

                        <th>
                            Apellidos
                        </th>

                        <th>
                            Correo
                        </th>

                        <th>
                            Estado
                        </th>

                        <?php if ($rolUsuario === 'superadmin'): ?>

                            <th>
                                Acciones
                            </th>

                        <?php endif; ?>

                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($docentes)): ?>

                        <tr>

                            <td
                                colspan="<?= $rolUsuario === 'superadmin' ? 5 : 4 ?>"
                                style="text-align:center;">

                                No existen docentes registrados.

                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach ($docentes as $docente): ?>

                            <tr>


                                <!-- NOMBRE -->

                                <td>

                                    <?= htmlspecialchars(
                                        $docente['nombres']
                                    ) ?>

                                </td>


                                <!-- APELLIDOS -->

                                <td>

                                    <?= htmlspecialchars(
                                        $docente['apellidos']
                                    ) ?>

                                </td>


                                <!-- CORREO -->

                                <td>

                                    <?= htmlspecialchars(
                                        $docente['correo']
                                    ) ?>

                                </td>


                                <!-- ESTADO -->

                                <td>

                                    <?php if ((int) $docente['activo'] === 1): ?>

                                        <span class="estado-activo">
                                            Activo
                                        </span>

                                    <?php else: ?>

                                        <span class="estado-inactivo">
                                            Inactivo
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ACCIONES -->

                                <?php if ($rolUsuario === 'superadmin'): ?>

                                    <td>

                                        <a
                                            href="editar.php?id=<?= (int) $docente['id'] ?>"
                                            class="btn btn-secondary btn-sm">

                                            <i class="fa-solid fa-pen"></i>

                                            Editar

                                        </a>

                                    </td>

                                <?php endif; ?>


                            </tr>

                        <?php endforeach; ?>


                    <?php endif; ?>

                </tbody>

            </table>

        </div>


    </div>


</body>

</html>
