<?php
//=====================================================
// VER.PHP
// Muestra el detalle de una reserva.
//=====================================================
/*
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit();
}
*/
require_once '../../config/database.php';

//=====================================================
// VALIDAR ID
//=====================================================

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {

    $_SESSION['error'] = 'La reserva no es válida.';

    header('Location: index.php');

    exit();
}

//=====================================================
// OBTENER RESERVA
//=====================================================

$sql = "SELECT
            r.id,
            r.fecha,
            r.actividad,
            r.permite_entrega,
            r.fecha_cierre,
            r.cierre_manual,

            c.nombre_curso,
            a.asignatura_nombre,

            b.numero_bloque,
            b.hora_inicio,
            b.hora_termino,

            d.nombres,
            d.apellidos

        FROM reservas r

        INNER JOIN cursos c
            ON c.id = r.curso_id

        INNER JOIN asignaturas a
            ON a.id = r.asignatura_id

        INNER JOIN bloques b
            ON b.id = r.bloque_id

        INNER JOIN docentes d
            ON d.id = r.docente_id

        WHERE r.id = ?";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$resultado = $stmt->get_result();

$reserva = $resultado->fetch_assoc();

$stmt->close();


//=====================================================
// VALIDAR RESERVA
//=====================================================

if (!$reserva) {

    $_SESSION['error'] = 'La reserva no existe.';

    header('Location: index.php');

    exit();
}


//=====================================================
// FORMATEAR DATOS
//=====================================================

setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain', 'Spanish');

$fecha = strftime('%A %d de %B de %Y', strtotime($reserva['fecha']));

$fecha = ucfirst($fecha);


/*****************************************************/
/*echo '<pre>';
print_r($reserva);
echo '</pre>';*/
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalle de la reserva</title>

    <link rel="stylesheet" href="../../assets/css/estilos.css">
    <link rel="stylesheet" href="../../assets/css/botones.css">
    <link rel="stylesheet" href="../../assets/css/reservas.css">

</head>

<body>

<main class="contenedor">

    <h1>Detalle de la reserva</h1>

    <table class="tabla-detalle">

        <tr>
            <th>Fecha</th>
            <td><?= htmlspecialchars($fecha) ?></td>
        </tr>

        <tr>
            <th>Bloque</th>
            <td>
                Bloque <?= htmlspecialchars($reserva['numero_bloque']) ?>
                (<?= substr($reserva['hora_inicio'], 0, 5) ?> -
                <?= substr($reserva['hora_termino'], 0, 5) ?>)
            </td>
        </tr>

        <tr>
            <th>Curso</th>
            <td><?= htmlspecialchars($reserva['nombre_curso']) ?></td>
        </tr>

        <tr>
            <th>Asignatura</th>
            <td><?= htmlspecialchars($reserva['asignatura_nombre']) ?></td>
        </tr>

        <tr>
            <th>Docente</th>
            <td><?= htmlspecialchars($reserva['nombres'] . ' ' . $reserva['apellidos']) ?></td>
        </tr>

        <tr>
            <th>Actividad</th>
            <td><?= nl2br(htmlspecialchars($reserva['actividad'])) ?></td>
        </tr>

        <tr>
            <th>Permite entrega</th>
            <td><?= $reserva['permite_entrega'] ? 'Sí' : 'No'; ?></td>
        </tr>

    </table>

    <div class="acciones">

        <a href="editar.php?id=<?= $reserva['id']; ?>" class="btn btn-primario">
            Editar
        </a>

        <a href="eliminar.php?id=<?= $reserva['id']; ?>" class="btn btn-peligro">
            Eliminar
        </a>

        <a href="index.php" class="btn btn-secundario">
            Volver
        </a>

    </div>

</main>

</body>

</html>
