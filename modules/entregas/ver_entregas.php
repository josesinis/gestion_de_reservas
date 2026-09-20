<?php

// ============================================================
// INICIO DE SESIÓN Y SEGURIDAD
// ============================================================

require_once '../../includes/auth.php';
requiereLogin();

require_once '../../includes/permisos.php';
require_once '../../config/database.php';

requiereRol(['admin', 'superadmin']);


// ============================================================
// OBTENER ID DEL TRABAJO
// ============================================================

$trabajoId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($trabajoId <= 0) {
    $_SESSION['error'] = 'Trabajo no válido.';
    header('Location: index.php');
    exit();
}


// ============================================================
// OBTENER INFORMACIÓN DEL TRABAJO
// ============================================================

$sqlTrabajo = "
    SELECT
        t.id,
        t.titulo,
        t.estado,
        t.fecha_inicio,
        t.fecha_limite,
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

$stmtTrabajo = $conexion->prepare($sqlTrabajo);
$stmtTrabajo->bind_param('i', $trabajoId);
$stmtTrabajo->execute();

$resultadoTrabajo = $stmtTrabajo->get_result();
$trabajo = $resultadoTrabajo->fetch_assoc();

$stmtTrabajo->close();

if (!$trabajo) {
    $_SESSION['error'] = 'El trabajo no existe.';
    header('Location: index.php');
    exit();
}


// ============================================================
// OBTENER ENTREGAS
//
// Se consultan primero en orden cronológico ASCENDENTE.
// Esto permite determinar correctamente:
//
// primera entrega  = versión 1
// segunda entrega  = versión 2
// tercera entrega  = versión 3
//
// Después, mediante PHP, se ordenarán nuevamente para mostrar
// la entrega más reciente primero.
// ============================================================

$sqlEntregas = "
    SELECT
        e.id,
        e.trabajo_id,
        e.curso_id,
        e.asignatura_id,
        e.alumno_id,
        e.nombre_archivo,
        e.ruta_archivo,
        e.fecha_hora_entrega,
        c.nombre_curso,
        a.asignatura_nombre,
        al.nombres AS alumno_nombres,
        al.apellidos AS alumno_apellidos
    FROM entregas e
    INNER JOIN cursos c
        ON c.id = e.curso_id
    INNER JOIN asignaturas a
        ON a.id = e.asignatura_id
    INNER JOIN alumnos al
        ON al.id = e.alumno_id
    WHERE e.trabajo_id = ?
    ORDER BY
        e.alumno_id ASC,
        e.fecha_hora_entrega ASC,
        e.id ASC
";

$stmtEntregas = $conexion->prepare($sqlEntregas);
$stmtEntregas->bind_param('i', $trabajoId);
$stmtEntregas->execute();

$resultadoEntregas = $stmtEntregas->get_result();

$entregas = [];

while ($fila = $resultadoEntregas->fetch_assoc()) {
    $entregas[] = $fila;
}

$stmtEntregas->close();


// ============================================================
// CALCULAR VERSIÓN DE CADA ENTREGA
//
// La numeración comienza nuevamente para cada alumno.
//
// Ejemplo:
//
// Alumno 1:
//    entrega -> versión 1
//    entrega -> versión 2
//
// Alumno 2:
//    entrega -> versión 1
//    entrega -> versión 2
//    entrega -> versión 3
// ============================================================

$versionesPorAlumno = [];

foreach ($entregas as $indice => $entrega) {

    $alumnoId = (int) $entrega['alumno_id'];

    if (!isset($versionesPorAlumno[$alumnoId])) {
        $versionesPorAlumno[$alumnoId] = 0;
    }

    $versionesPorAlumno[$alumnoId]++;

    $entregas[$indice]['version'] = $versionesPorAlumno[$alumnoId];
}


// ============================================================
// GUARDAR CUÁL ES LA ÚLTIMA VERSIÓN DE CADA ALUMNO
// ============================================================

$ultimaVersionPorAlumno = [];

foreach ($entregas as $entrega) {

    $alumnoId = (int) $entrega['alumno_id'];
    $version = (int) $entrega['version'];

    $ultimaVersionPorAlumno[$alumnoId] = $version;
}


// ============================================================
// ORDENAR PARA MOSTRAR LA ENTREGA MÁS RECIENTE PRIMERO
//
// La numeración ya fue calculada anteriormente, por lo que
// invertir el orden no altera el número de versión.
// ============================================================

usort($entregas, function ($a, $b) {

    $comparacionFecha = strcmp(
        $b['fecha_hora_entrega'],
        $a['fecha_hora_entrega']
    );

    if ($comparacionFecha !== 0) {
        return $comparacionFecha;
    }

    return (int) $b['id'] <=> (int) $a['id'];
});


// ============================================================
// CONFIGURACIÓN DEL MENÚ
// ============================================================

$seccionActual = 'entregas';

require_once '../../includes/menu.php';


// ============================================================
// DATOS DEL USUARIO
// ============================================================

$rolUsuario = $_SESSION['rol'] ?? 'usuario';

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Entregas del trabajo</title>

    <link rel="stylesheet" href="../../assets/css/estilos.css">
    <link rel="stylesheet" href="../../assets/css/botones.css">
    <link rel="stylesheet" href="../../assets/css/formularios.css">
    <link rel="stylesheet" href="../../assets/css/tablas.css">

</head>

<body>

    <div class="contenedor contenedor-horarios-fijos">

        <!-- ======================================================
         TÍTULO
         ====================================================== -->

        <h1>Entregas del trabajo</h1>


        <!-- ======================================================
         INFORMACIÓN DEL TRABAJO
         ====================================================== -->

        <div class="grupo-formulario">

            <p>
                <strong>Trabajo:</strong>
                <?= htmlspecialchars($trabajo['titulo']) ?>
            </p>

            <p>
                <strong>Docente:</strong>
                <?= htmlspecialchars(
                    $trabajo['docente_nombres'] . ' ' .
                        $trabajo['docente_apellidos']
                ) ?>
            </p>

            <p>
                <strong>Curso:</strong>
                <?= htmlspecialchars($trabajo['nombre_curso']) ?>
            </p>

            <p>
                <strong>Asignatura:</strong>
                <?= htmlspecialchars($trabajo['asignatura_nombre']) ?>
            </p>

            <p>
                <strong>Total de entregas:</strong>
                <?= count($entregas) ?>
            </p>

        </div>


        <!-- ======================================================
         TABLA DE ENTREGAS
         ====================================================== -->

        <?php if (empty($entregas)): ?>

            <p>
                No existen entregas para este trabajo.
            </p>

        <?php else: ?>

            <div class="tabla-responsive">

                <table class="tabla">

                    <thead>

                        <tr>

                            <th>Curso</th>

                            <th>Estudiante</th>

                            <th>Versión</th>

                            <th>Archivo</th>

                            <th>Fecha de entrega</th>

                            <th>Acción</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($entregas as $entrega): ?>

                            <?php

                            $alumnoId = (int) $entrega['alumno_id'];

                            $version = (int) $entrega['version'];

                            $esUltimaVersion =
                                $version === ($ultimaVersionPorAlumno[$alumnoId] ?? 0);

                            ?>

                            <tr>

                                <!-- CURSO -->

                                <td>
                                    <?= htmlspecialchars($entrega['nombre_curso']) ?>
                                </td>


                                <!-- ESTUDIANTE -->

                                <td>
                                    <?= htmlspecialchars(
                                        trim(
                                            $entrega['alumno_apellidos'] . ', ' .
                                                $entrega['alumno_nombres']
                                        )
                                    ) ?>
                                </td>


                                <!-- VERSIÓN -->

                                <td>

                                    Versión <?= $version ?>

                                    <?php if ($esUltimaVersion): ?>

                                        <br>

                                        <small>
                                            <strong>Última entrega</strong>
                                        </small>

                                    <?php endif; ?>

                                </td>


                                <!-- ARCHIVO -->

                                <td>
                                    <?= htmlspecialchars($entrega['nombre_archivo']) ?>
                                </td>


                                <!-- FECHA -->

                                <td>
                                    <?= htmlspecialchars($entrega['fecha_hora_entrega']) ?>
                                </td>


                                <!-- ACCIÓN -->

                                <td>

                                    <a
                                        href="descargar.php?id=<?= (int) $entrega['id'] ?>"
                                        class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-download"></i>
                                        Descargar
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>


        <!-- ======================================================
         VOLVER
         ====================================================== -->

        <div style="margin-top: 20px;">

            <a
                href="index.php"
                class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>

        </div>

    </div>

</body>

</html>
