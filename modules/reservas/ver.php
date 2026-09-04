<?php
//=====================================================
// VER.PHP
// Muestra el detalle de una reserva.
//=====================================================


//=====================================================
// 1. VALIDAR SESIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

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
            r.objetivo_clase,
            r.actividad,
            r.permite_entrega,
            r.fecha_cierre,
            r.cierre_manual,
            r.tipo_reserva,
            r.estado,
            r.fecha_entrega_oficial,
            r.fecha_creacion,
            r.fecha_actualizacion,

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

    header('Location: agenda.php');

    exit();
}

if ($reserva['estado'] !== 'reservada') {

    $_SESSION['error'] =
        'Esta reserva ya no está disponible para consulta.';

    header('Location: agenda.php');

    exit();
}


//=====================================================
// FORMATEAR DATOS
//=====================================================

$fecha = formatearFechaLarga($reserva['fecha']);


/*

echo '<pre>';

echo 'Archivo php.ini cargado: ';
var_dump(php_ini_loaded_file());

echo 'Zona horaria configurada en ini: ';
var_dump(ini_get('date.timezone'));

echo 'Zona horaria PHP: ';
var_dump(date_default_timezone_get());

echo '</pre>';

exit;*/

//var_dump(reservaEsModificable($reserva));
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
                                (<?= substr($reserva['hora_inicio'], 0, 5) ?>
                                -
                                <?= substr($reserva['hora_termino'], 0, 5) ?>)
                            </td>
                        </tr>

                        <tr>
                            <th>Tipo de reserva</th>
                            <td><?= formatearTipoReserva($reserva['tipo_reserva']) ?></td>
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
                            <td>
                                <?= htmlspecialchars(
                                    $reserva['nombres'] . ' ' . $reserva['apellidos']
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Actividad</th>
                            <td><?= nl2br(htmlspecialchars($reserva['actividad'])) ?></td>
                        </tr>

                        <tr>
                            <th>Objetivo de la clase</th>
                            <td>
                                <?= $reserva['objetivo_clase'] !== null && $reserva['objetivo_clase'] !== ''
                                    ? nl2br(htmlspecialchars($reserva['objetivo_clase']))
                                    : 'No registrado'; ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Permite entrega</th>
                            <td><?= $reserva['permite_entrega'] ? 'Sí' : 'No'; ?></td>
                        </tr>

                        <tr>
                            <th>Fecha entrega oficial</th>
                            <td>

                                <?= $reserva['fecha_entrega_oficial']
                                    ? formatearFechaLarga($reserva['fecha_entrega_oficial'])
                                    : 'No aplica'; ?>

                            </td>
                        </tr>

                        <tr>
                            <th>Estado</th>
                            <td><?= ucfirst(htmlspecialchars($reserva['estado'])) ?></td>
                        </tr>

                        <tr>
                            <th>Fecha creación</th>
                            <td><?= htmlspecialchars($reserva['fecha_creacion']) ?></td>
                        </tr>

                        <tr>
                            <th>Última actualización</th>
                            <td><?= htmlspecialchars($reserva['fecha_actualizacion']) ?></td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="acciones">

                <a
                    href="editar.php?id=<?= $reserva['id']; ?>"
                    class="btn btn-primario">

                    Editar

                </a>

                <?php if (
                    $reserva['estado'] === 'reservada'
                    && reservaPuedeConfirmarse($reserva)
                ): ?>

                    <a
                        href="confirmar.php?id=<?= $reserva['id']; ?>"
                        class="btn btn-primario">

                        Confirmar uso

                    </a>

                <?php endif; ?>

                <a
                    href="cancelar.php?id=<?= $reserva['id']; ?>"
                    class="btn btn-peligro"
                    onclick="return confirm('¿Confirma cancelar esta reserva?');">

                    Cancelar

                </a>

                <a
                    href="agenda.php"
                    class="btn btn-secundario">

                    Volver

                </a>

            </div>

        </section>

    </main>

</body>

</html>
