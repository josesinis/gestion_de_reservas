<?php

//=====================================================
// SISTEMA
// Gestión de Reservas
//=====================================================

//=====================================================
// MÓDULO
// Reservas
//=====================================================

//=====================================================
// ARCHIVO
// ver.php
//=====================================================

//=====================================================
// DESCRIPCIÓN
// Muestra el detalle de una reserva.
//=====================================================

/*
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit();
}
*/

//=====================================================
// 1. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';


//=====================================================
// 2. VALIDAR ID
//=====================================================

$idReserva = (int) ($_GET['id'] ?? 0);

if ($idReserva <= 0) {

    $_SESSION['error'] = 'La reserva no es válida.';

    header('Location: agenda.php');

    exit();

}


//=====================================================
// 3. OBTENER RESERVA
//=====================================================

$sql = "

SELECT

    r.id,
    r.fecha,
    r.actividad,
    r.tipo_reserva,
    r.estado,
    r.permite_entrega,
    r.fecha_entrega_oficial,
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

WHERE r.id = ?

LIMIT 1

";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $idReserva);

$stmt->execute();

$resultado = $stmt->get_result();

$reserva = $resultado->fetch_assoc();

$stmt->close();


//=====================================================
// 4. VALIDAR EXISTENCIA
//=====================================================

if (!$reserva) {

    $_SESSION['error'] = 'La reserva no existe.';

    header('Location: agenda.php');

    exit();

}


//=====================================================
// 5. FORMATEAR DATOS
//=====================================================

$fecha = formatearFechaLarga($reserva['fecha']);

switch ($reserva['tipo_reserva']) {

    case 'sub1':
        $tipoReserva = 'Primer subbloque';
        break;

    case 'sub2':
        $tipoReserva = 'Segundo subbloque';
        break;

    default:
        $tipoReserva = 'Bloque completo';
        break;
}

$fechaEntrega = '-';

if (
    $reserva['permite_entrega']
    &&
    !empty($reserva['fecha_entrega_oficial'])
) {

    $fechaEntrega = formatearFechaLarga(
        $reserva['fecha_entrega_oficial']
    );

}
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
    <link rel="stylesheet" href="../../assets/css/tablas.css">

</head>

<body>

    <main class="contenedor">

        <section class="panel">

            <h1>Detalle de la reserva</h1>

            <div class="tabla-responsive">

                <table class="tabla-detalle">

                    <tbody>

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

                    </tbody>

                </table>

            </div>

            <div class="acciones">

                <a href="editar.php?id=<?= $reserva['id']; ?>" class="btn btn-primario">
                    Editar
                </a>

                <a href="eliminar.php?id=<?= $reserva['id']; ?>"
                    class="btn btn-peligro"
                    onclick="return confirm('¿Confirma que desea eliminar esta reserva? Esta acción no se puede deshacer.');">
                    Eliminar
                </a>

                <a href="agenda.php" class="btn btn-secundario">
                    Volver
                </a>

            </div>

        </section>

    </main>

</body>

</html>
