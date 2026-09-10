<?php

//=====================================================
// ASIGNATURAS - LISTADO
//=====================================================
//
// Muestra las asignaturas registradas en el sistema.
//
// Permisos:
// - admin: puede consultar el listado.
// - superadmin: puede crear y editar.
//
// Las asignaturas no se eliminan físicamente.
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
// 5. OBTENER ASIGNATURAS
//=====================================================

$asignaturas = [];

$sql = "

    SELECT
        id,
        asignatura_nombre,
        modalidad,
        activo

    FROM asignaturas

    ORDER BY
        asignatura_nombre ASC

";


$resultado = $conexion->query($sql);

if ($resultado) {

    $asignaturas = $resultado->fetch_all(
        MYSQLI_ASSOC
    );
}


//=====================================================
// 6. CONVERTIR MODALIDAD A TEXTO
//=====================================================

function textoModalidadAsignatura(string $modalidad): string
{
    return match ($modalidad) {

        'asignatura' => 'Asignatura',

        'taller' => 'Taller',

        default => $modalidad
    };
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Asignaturas</title>


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
                    Asignaturas
                </h1>

                <p>
                    Administración de las asignaturas registradas en el sistema
                </p>

            </div>


            <?php if ($rolUsuario === 'superadmin'): ?>

                <div>

                    <a
                        href="crear.php"
                        class="btn btn-primary">

                        <i class="fa-solid fa-book"></i>

                        Nueva asignatura

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
                <?= count($asignaturas) ?>
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
                            Asignatura
                        </th>

                        <th>
                            Modalidad
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

                    <?php if (empty($asignaturas)): ?>

                        <tr>

                            <td
                                colspan="<?= $rolUsuario === 'superadmin' ? 4 : 3 ?>"
                                style="text-align:center;">

                                No existen asignaturas registradas.

                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach ($asignaturas as $asignatura): ?>

                            <tr>


                                <!-- ASIGNATURA -->

                                <td>

                                    <?= htmlspecialchars(
                                        $asignatura['asignatura_nombre']
                                    ) ?>

                                </td>


                                <!-- MODALIDAD -->

                                <td>

                                    <?= htmlspecialchars(
                                        textoModalidadAsignatura(
                                            $asignatura['modalidad']
                                        )
                                    ) ?>

                                </td>


                                <!-- ESTADO -->

                                <td>

                                    <?php if (
                                        (int) $asignatura['activo'] === 1
                                    ): ?>

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
                                            href="editar.php?id=<?= (int) $asignatura['id'] ?>"
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
