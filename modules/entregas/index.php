<?php

//=====================================================
// TRABAJOS - LISTADO
//=====================================================
//
// Muestra los trabajos registrados en el sistema.
//
// Permisos:
// - admin: puede consultar el listado.
// - superadmin: puede consultar y administrar prórrogas.
//
// El estado del trabajo se determina según su
// fecha límite.
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
// 5. OBTENER TRABAJOS
//=====================================================

$trabajos = [];

$sql = "

    SELECT

        t.id,
        t.titulo,
        t.estado,
        t.fecha_inicio,
        t.fecha_limite,

        COALESCE(pr.cantidad_prorrogas, 0) AS cantidad_prorrogas,

        d.nombres AS docente_nombres,
        d.apellidos AS docente_apellidos,

        c.nombre_curso,

        a.asignatura_nombre

    FROM trabajos t

    INNER JOIN reservas r
        ON r.id = t.reserva_id

    INNER JOIN docentes d
        ON d.id = r.docente_id

    INNER JOIN cursos c
        ON c.id = r.curso_id

    INNER JOIN asignaturas a
        ON a.id = r.asignatura_id

    LEFT JOIN (
        SELECT
            trabajo_id,
            COUNT(*) AS cantidad_prorrogas
        FROM trabajo_prorrogas
        GROUP BY trabajo_id
    ) pr
        ON pr.trabajo_id = t.id

    ORDER BY
        t.fecha_limite ASC,
        t.titulo ASC

";

$resultado = $conexion->query($sql);

if ($resultado) {

    $trabajos = $resultado->fetch_all(
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

    <title>Trabajos</title>


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


    <div class="contenedor contenedor-horarios-fijos">


        <!--=================================================
             ENCABEZADO
        ==================================================-->

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Trabajos
                </h1>

                <p>
                    Administración de los trabajos registrados en el sistema
                </p>

            </div>

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
                <?= count($trabajos) ?>
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
                            Trabajo
                        </th>

                        <th>
                            Docente
                        </th>

                        <th>
                            Curso
                        </th>

                        <th>
                            Asignatura
                        </th>

                        <th>
                            Fecha inicio
                        </th>

                        <th>
                            Fecha límite
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Prórroga
                        </th>

                        <?php if ($rolUsuario === 'superadmin'): ?>

                            <th>
                                Acciones
                            </th>

                        <?php endif; ?>

                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($trabajos)): ?>

                        <tr>

                            <td
                                colspan="<?= $rolUsuario === 'superadmin' ? 9 : 8 ?>"
                                style="text-align:center;">

                                No existen trabajos registrados.

                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach ($trabajos as $trabajo): ?>

                            <tr>


                                <!-- TRABAJO -->

                                <td>

                                    <?= htmlspecialchars(
                                        $trabajo['titulo']
                                    ) ?>

                                </td>


                                <!-- DOCENTE -->

                                <td>

                                    <?= htmlspecialchars(
                                        $trabajo['docente_nombres']
                                            . ' '
                                            . $trabajo['docente_apellidos']
                                    ) ?>

                                </td>


                                <!-- CURSO -->

                                <td>

                                    <?= htmlspecialchars(
                                        $trabajo['nombre_curso']
                                    ) ?>

                                </td>


                                <!-- ASIGNATURA -->

                                <td>

                                    <?= htmlspecialchars(
                                        $trabajo['asignatura_nombre']
                                    ) ?>

                                </td>


                                <!-- FECHA INICIO -->

                                <td>

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $trabajo['fecha_inicio']
                                        )
                                    ) ?>

                                </td>


                                <!-- FECHA LÍMITE -->

                                <td>

                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $trabajo['fecha_limite']
                                        )
                                    ) ?>

                                </td>


                                <!-- ESTADO -->

                                <td>

                                    <?php if (
                                        $trabajo['estado']
                                        === 'en_proceso'
                                    ): ?>

                                        <span class="estado-activo">
                                            En proceso
                                        </span>

                                    <?php else: ?>

                                        <span class="estado-inactivo">
                                            Cerrado
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- PRÓRROGA -->

                                <td>

                                    <?php if (
                                        (int) $trabajo['cantidad_prorrogas'] > 0
                                    ): ?>

                                        <?= (int) $trabajo['cantidad_prorrogas'] ?>

                                        <?= (int) $trabajo['cantidad_prorrogas'] === 1
                                            ? 'prórroga'
                                            : 'prórrogas' ?>

                                    <?php else: ?>

                                        <span>
                                            Sin prórroga
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ACCIONES -->

                                <?php if (
                                    $rolUsuario === 'superadmin'
                                ): ?>

                                    <td>

                                        <!-- VER ENTREGAS -->

                                        <a
                                            href="ver_entregas.php?id=<?= (int) $trabajo['id'] ?>"
                                            class="btn btn-primary btn-sm">

                                            <i class="fa-solid fa-folder-open"></i>

                                            Ver entregas

                                        </a>


                                        <!-- EDITAR PRÓRROGAS -->

                                        <a
                                            href="editar.php?id=<?= (int) $trabajo['id'] ?>"
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
