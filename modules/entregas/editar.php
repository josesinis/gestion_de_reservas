<?php
/*
 * --------------------------------------------------------------------------
 * Sistema     : Gestión Institucional
 * Archivo     : modules/entregas/editar.php
 * --------------------------------------------------------------------------
 * Descripción :
 * Administra las prórrogas individuales de los estudiantes de un trabajo.
 *
 * Acceso      :
 * Exclusivo para superadmin.
 *
 * Importante  :
 * La prórroga es una excepción al período normal de entrega.
 * No modifica el estado del trabajo.
 * Cada estudiante puede tener su propia prórroga.
 * --------------------------------------------------------------------------
 */

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
    'superadmin'
]);


//=====================================================
// 4. OBTENER ID DEL TRABAJO Y DE LA PRÓRROGA
//=====================================================

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$prorrogaId = isset($_GET['prorroga_id'])
    ? (int) $_GET['prorroga_id']
    : 0;

if ($id <= 0) {

    $_SESSION['error'] =
        'El trabajo seleccionado no es válido.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 5. OBTENER TRABAJO
//=====================================================

$sql = "
    SELECT
        t.id,
        t.titulo,
        t.estado,
        t.fecha_inicio,
        t.fecha_limite,

        r.curso_id,

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

    WHERE t.id = ?

    LIMIT 1
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible consultar el trabajo.';

    header('Location: index.php');
    exit();
}

$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

$trabajo = $resultado->fetch_assoc();

$stmt->close();


if (!$trabajo) {

    $_SESSION['error'] =
        'El trabajo seleccionado no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 6. PREPARAR DATOS DEL FORMULARIO
//=====================================================

$alumnoId = 0;
$prorrogaFechaInicio = '';
$prorrogaFechaFin = '';


//=====================================================
// 7. OBTENER PRÓRROGA EN EDICIÓN
//=====================================================

if ($prorrogaId > 0) {

    $sql = "
        SELECT
            id,
            alumno_id,
            fecha_inicio,
            fecha_fin
        FROM trabajo_prorrogas
        WHERE id = ?
          AND trabajo_id = ?
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {

        $_SESSION['error'] =
            'No fue posible consultar la prórroga.';

        header('Location: editar.php?id=' . $id);
        exit();
    }

    $stmt->bind_param(
        'ii',
        $prorrogaId,
        $id
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $prorroga = $resultado->fetch_assoc();

    $stmt->close();

    if (!$prorroga) {

        $_SESSION['error'] =
            'La prórroga seleccionada no existe.';

        header('Location: editar.php?id=' . $id);
        exit();
    }

    $alumnoId = (int) $prorroga['alumno_id'];

    $prorrogaFechaInicio = date(
        'Y-m-d\TH:i',
        strtotime($prorroga['fecha_inicio'])
    );

    $prorrogaFechaFin = date(
        'Y-m-d\TH:i',
        strtotime($prorroga['fecha_fin'])
    );
}


//=====================================================
// 8. OBTENER ALUMNOS ACTIVOS DEL CURSO
//=====================================================

$alumnos = [];

$sql = "
    SELECT
        id,
        nombres,
        apellidos
    FROM alumnos
    WHERE curso_id = ?
      AND activo = 1
    ORDER BY
        apellidos,
        nombres
";

$stmt = $conexion->prepare($sql);

if ($stmt) {

    $cursoId = (int) $trabajo['curso_id'];

    $stmt->bind_param(
        'i',
        $cursoId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $alumnos =
        $resultado->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
}


//=====================================================
// 9. OBTENER PRÓRROGAS EXISTENTES
//=====================================================

$prorrogas = [];

$sql = "
    SELECT
        tp.id,
        tp.alumno_id,
        tp.fecha_inicio,
        tp.fecha_fin,
        al.nombres,
        al.apellidos
    FROM trabajo_prorrogas tp

    INNER JOIN alumnos al
        ON al.id = tp.alumno_id

    WHERE tp.trabajo_id = ?

    ORDER BY
        al.apellidos,
        al.nombres,
        tp.fecha_inicio
";

$stmt = $conexion->prepare($sql);

if ($stmt) {

    $stmt->bind_param(
        'i',
        $id
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $prorrogas =
        $resultado->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Administrar prórrogas</title>

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


    <style>

        /*=====================================================
          CONTENEDOR DE INFORMACIÓN DEL TRABAJO
        =====================================================*/

        .prorroga-card {
            background: #ffffff;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            padding: 24px 28px;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .prorroga-card-titulo {
            margin: 0 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e5e5;
            font-size: 1.15rem;
            font-weight: 600;
        }

        .informacion-trabajo {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 24px;
        }

        .informacion-trabajo .grupo-formulario {
            margin-bottom: 0;
        }

        .informacion-trabajo .grupo-formulario:first-child {
            grid-column: 1 / -1;
        }

        /*=====================================================
          FORMULARIO DE PRÓRROGA
        =====================================================*/

        .formulario-prorroga {
            margin-top: 24px;
        }

        .formulario-prorroga .grupo-formulario {
            margin-bottom: 18px;
        }

        .formulario-prorroga select,
        .formulario-prorroga input[type="datetime-local"] {
            width: 100%;
            box-sizing: border-box;
        }

        .texto-ayuda-prorroga {
            margin: 6px 0 0;
            font-size: 0.9rem;
            color: #666;
        }

        .acciones-formulario-prorroga {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #e5e5e5;
        }

        /*=====================================================
          TABLA DE PRÓRROGAS
        =====================================================*/

        .tabla-prorrogas {
            margin-top: 32px;
        }

        .tabla-prorrogas h2 {
            margin-bottom: 14px;
        }

        .tabla-prorrogas-card {
            background: #ffffff;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .tabla-prorrogas-card .tabla-contenedor {
            margin: 0;
        }

        .tabla-prorrogas table {
            width: 100%;
        }

        .tabla-prorrogas th,
        .tabla-prorrogas td {
            vertical-align: middle;
        }

        .tabla-prorrogas th {
            white-space: nowrap;
        }

        .tabla-prorrogas td:nth-child(2),
        .tabla-prorrogas td:nth-child(3),
        .tabla-prorrogas td:nth-child(4) {
            white-space: nowrap;
        }

        .estado-prorroga {
            white-space: nowrap;
            font-weight: 600;
        }

        .acciones-prorroga {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .acciones-prorroga form {
            margin: 0;
        }

        /*=====================================================
          RESPONSIVE
        =====================================================*/

        @media (max-width: 700px) {

            .prorroga-card {
                padding: 18px;
            }

            .informacion-trabajo {
                grid-template-columns: 1fr;
            }

            .informacion-trabajo .grupo-formulario:first-child {
                grid-column: auto;
            }

            .acciones-formulario-prorroga {
                flex-wrap: wrap;
            }

        }

    </style>

</head>

<body>

    <?php

    $seccionActual = 'administracion';

    require_once '../../includes/menu.php';

    ?>

    <div class="contenedor">

        <!--=================================================
             ENCABEZADO
        ==================================================-->

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Administrar prórrogas
                </h1>

                <p>
                    Prórrogas individuales del trabajo
                </p>

            </div>

        </div>


        <!--=================================================
             INFORMACIÓN DEL TRABAJO
        ==================================================-->

        <div class="prorroga-card">

            <h2 class="prorroga-card-titulo">
                Información del trabajo
            </h2>

            <div class="informacion-trabajo">

        <div class="grupo-formulario">

            <label>
                Trabajo
            </label>

            <input
                type="text"
                value="<?= htmlspecialchars($trabajo['titulo']) ?>"
                readonly>

        </div>


        <div class="grupo-formulario">

            <label>
                Docente
            </label>

            <input
                type="text"
                value="<?= htmlspecialchars(
                    $trabajo['docente_nombres']
                    . ' '
                    . $trabajo['docente_apellidos']
                ) ?>"
                readonly>

        </div>


        <div class="grupo-formulario">

            <label>
                Curso
            </label>

            <input
                type="text"
                value="<?= htmlspecialchars(
                    $trabajo['nombre_curso']
                ) ?>"
                readonly>

        </div>


        <div class="grupo-formulario">

            <label>
                Asignatura
            </label>

            <input
                type="text"
                value="<?= htmlspecialchars(
                    $trabajo['asignatura_nombre']
                ) ?>"
                readonly>

        </div>


        <div class="grupo-formulario">

            <label>
                Fecha de inicio
            </label>

            <input
                type="text"
                value="<?= date(
                    'd/m/Y',
                    strtotime($trabajo['fecha_inicio'])
                ) ?>"
                readonly>

        </div>


        <div class="grupo-formulario">

            <label>
                Fecha límite original
            </label>

            <input
                type="text"
                value="<?= date(
                    'd/m/Y H:i',
                    strtotime($trabajo['fecha_limite'])
                ) ?>"
                readonly>

        </div>


            </div>

        </div>


        <!--=================================================
             FORMULARIO DE PRÓRROGA INDIVIDUAL
        ==================================================-->

        <div class="prorroga-card formulario-prorroga">

            <h2 class="prorroga-card-titulo">
                <?= $prorrogaId > 0
                    ? 'Editar prórroga'
                    : 'Agregar prórroga' ?>
            </h2>

            <form
                action="actualizar.php"
                method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= (int) $trabajo['id'] ?>">

            <input
                type="hidden"
                name="prorroga_id"
                value="<?= (int) $prorrogaId ?>">

            <div class="grupo-formulario">

                <label for="alumno_id">
                    Estudiante
                </label>

                <select
                    id="alumno_id"
                    name="alumno_id"
                    required>

                    <option value="">
                        Seleccione un estudiante
                    </option>

                    <?php foreach ($alumnos as $alumno): ?>

                        <option
                            value="<?= (int) $alumno['id'] ?>"
                            <?= $alumnoId === (int) $alumno['id']
                                ? 'selected'
                                : '' ?>>

                            <?= htmlspecialchars(
                                $alumno['apellidos']
                                . ', '
                                . $alumno['nombres']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <p class="texto-ayuda-prorroga">
                    Solo se muestran estudiantes activos del curso del trabajo.
                </p>

            </div>


            <div class="grupo-formulario">

                <label for="prorroga_fecha_inicio">
                    Inicio de la prórroga
                </label>

                <input
                    type="datetime-local"
                    id="prorroga_fecha_inicio"
                    name="prorroga_fecha_inicio"
                    value="<?= htmlspecialchars(
                        $prorrogaFechaInicio
                    ) ?>"
                    required>

            </div>


            <div class="grupo-formulario">

                <label for="prorroga_fecha_fin">
                    Fin de la prórroga
                </label>

                <input
                    type="datetime-local"
                    id="prorroga_fecha_fin"
                    name="prorroga_fecha_fin"
                    value="<?= htmlspecialchars(
                        $prorrogaFechaFin
                    ) ?>"
                    required>

                <p class="texto-ayuda-prorroga">
                    La prórroga debe comenzar desde la fecha límite
                    original o después de ella.
                </p>

            </div>


            <!--=================================================
                 BOTONES
            ==================================================-->

            <div class="acciones-formulario-prorroga">

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa-solid fa-floppy-disk"></i>

                    <?= $prorrogaId > 0
                        ? 'Guardar cambios'
                        : 'Agregar prórroga' ?>

                </button>


                <?php if ($prorrogaId > 0): ?>

                    <a
                        href="editar.php?id=<?= (int) $id ?>"
                        class="btn btn-secondary">

                        <i class="fa-solid fa-xmark"></i>

                        Cancelar edición

                    </a>

                <?php else: ?>

                    <a
                        href="index.php"
                        class="btn btn-secondary">

                        <i class="fa-solid fa-arrow-left"></i>

                        Volver

                    </a>

                <?php endif; ?>

            </div>

            </form>

        </div>


        </form>


        <!--=================================================
             PRÓRROGAS EXISTENTES
        ==================================================-->

        <div class="tabla-prorrogas">

            <div class="tabla-prorrogas-card">

            <h2>
                Prórrogas registradas
            </h2>

            <?php if (empty($prorrogas)): ?>

                <p>
                    Este trabajo todavía no tiene prórrogas
                    registradas.
                </p>

            <?php else: ?>

                <div class="tabla-contenedor">

                    <table>

                        <thead>

                            <tr>

                                <th>Estudiante</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado</th>
                                <th>Acciones</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($prorrogas as $prorroga): ?>

                                <?php

                                $ahora = time();

                                $inicio = strtotime(
                                    $prorroga['fecha_inicio']
                                );

                                $fin = strtotime(
                                    $prorroga['fecha_fin']
                                );

                                if ($ahora < $inicio) {

                                    $estadoProrroga = 'Pendiente';

                                } elseif (
                                    $ahora >= $inicio
                                    && $ahora <= $fin
                                ) {

                                    $estadoProrroga = 'Vigente';

                                } else {

                                    $estadoProrroga = 'Finalizada';
                                }

                                ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars(
                                            $prorroga['apellidos']
                                            . ', '
                                            . $prorroga['nombres']
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            'd/m/Y H:i',
                                            $inicio
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            'd/m/Y H:i',
                                            $fin
                                        ) ?>
                                    </td>

                                    <td class="estado-prorroga">
                                        <?= $estadoProrroga ?>
                                    </td>

                                    <td>

                                        <div class="acciones-prorroga">

                                            <a
                                                href="editar.php?id=<?= (int) $id ?>&prorroga_id=<?= (int) $prorroga['id'] ?>"
                                                class="btn btn-secondary">

                                                <i class="fa-solid fa-pen-to-square"></i>

                                                Editar

                                            </a>

                                            <form
                                                action="actualizar.php"
                                                method="POST"
                                                onsubmit="return confirm('¿Está seguro de eliminar esta prórroga?');">

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $id ?>">

                                                <input
                                                    type="hidden"
                                                    name="prorroga_id"
                                                    value="<?= (int) $prorroga['id'] ?>">

                                                <input
                                                    type="hidden"
                                                    name="accion"
                                                    value="eliminar">

                                                <button
                                                    type="submit"
                                                    class="btn btn-secondary">

                                                    <i class="fa-solid fa-trash"></i>

                                                    Eliminar

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

            </div>

        </div>

    </div>

</body>
</html>
