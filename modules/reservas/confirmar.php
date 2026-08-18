<?php
//=====================================================
// CONFIRMAR.PHP
// Confirma el uso de una reserva y genera la bitácora.
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// VALIDAR ID
//=====================================================

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {

    $_SESSION['error'] = 'La reserva no es válida.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// OBTENER RESERVA
//=====================================================

$reserva = obtenerReservaPorId(
    $conexion,
    $id
);

if (!$reserva) {

    $_SESSION['error'] = 'La reserva no existe.';

    header('Location: agenda.php');

    exit();
}

//=====================================================
// VALIDAR ESTADO
//=====================================================

if ($reserva['estado'] !== 'reservada') {

    $_SESSION['error'] =
        'Esta reserva ya no se encuentra disponible para confirmar.';

    header('Location: ver.php?id=' . $id);

    exit();
}

//=====================================================
// OBTENER RECURSOS DISPONIBLES
//=====================================================

$sqlRecursos = "
    SELECT
        id,
        nombre_recurso
    FROM recursos
    ORDER BY nombre_recurso
";

$resultadoRecursos = $conexion->query($sqlRecursos);

$recursos = [];

if ($resultadoRecursos) {

    while ($recurso = $resultadoRecursos->fetch_assoc()) {

        $recursos[] = $recurso;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Confirmar uso de la sala</title>

    <link rel="stylesheet" href="../../assets/css/estilos.css">
    <link rel="stylesheet" href="../../assets/css/botones.css">
    <link rel="stylesheet" href="../../assets/css/reservas.css">
    <link rel="stylesheet" href="../../assets/css/tablas.css">

</head>

<body>

    <main class="contenedor">

        <section class="panel">

            <h1>Confirmar uso de la sala</h1>

            <!-- ========================================= -->
            <!-- DATOS DE LA RESERVA -->
            <!-- ========================================= -->

            <div class="tabla-responsive">

                <table class="tabla-detalle">

                    <tbody>

                        <tr>
                            <th>Fecha</th>

                            <td>
                                <?= htmlspecialchars(
                                    formatearFechaLarga($reserva['fecha'])
                                ) ?>
                            </td>
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
                            <th>Curso</th>

                            <td>
                                <?= htmlspecialchars($reserva['nombre_curso']) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Asignatura</th>

                            <td>
                                <?= htmlspecialchars($reserva['asignatura_nombre']) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Docente</th>

                            <td>
                                <?= htmlspecialchars(
                                    $reserva['nombres'] . ' ' . $reserva['apellidos']
                                ) ?>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- ========================================= -->
            <!-- FORMULARIO -->
            <!-- ========================================= -->

            <form
                action="guardar_confirmacion.php"
                method="POST"
                class="formulario-confirmacion">

                <input
                    type="hidden"
                    name="reserva_id"
                    value="<?= $reserva['id'] ?>">


                <!-- ACTIVIDAD -->

                <div class="campo-formulario">

                    <label for="actividad">
                        Actividad
                    </label>

                    <textarea
                        id="actividad"
                        name="actividad"
                        rows="3"
                        maxlength="150"
                        required><?= htmlspecialchars($reserva['actividad'] ?? '') ?></textarea>

                </div>


                <!-- OBJETIVO -->

                <div class="campo-formulario">

                    <label for="objetivo_clase">
                        Objetivo de la clase
                    </label>

                    <textarea
                        id="objetivo_clase"
                        name="objetivo_clase"
                        rows="4"
                        required><?= htmlspecialchars($reserva['objetivo_clase'] ?? '') ?></textarea>

                </div>


                <!-- RECURSOS -->

                <div class="campo-formulario">

                    <label>
                        Recursos utilizados
                    </label>

                    <?php if (!empty($recursos)): ?>

                        <div class="lista-recursos">

                            <?php foreach ($recursos as $recurso): ?>

                                <label>

                                    <input
                                        type="checkbox"
                                        name="recursos[]"
                                        value="<?= $recurso['id'] ?>">

                                    <?= htmlspecialchars(
                                        $recurso['nombre_recurso']
                                    ) ?>

                                </label>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <p>
                            No hay recursos registrados.
                        </p>

                    <?php endif; ?>

                </div>


                <!-- OBSERVACIONES -->

                <div class="campo-formulario">

                    <label for="observaciones">
                        Observaciones
                    </label>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        rows="4"></textarea>

                </div>


                <!-- ACCIONES -->

                <div class="acciones">

                    <button
                        type="submit"
                        class="btn btn-primario">
                        Confirmar uso
                    </button>

                    <a
                        href="ver.php?id=<?= $reserva['id'] ?>"
                        class="btn btn-secundario">
                        Volver
                    </a>

                </div>

            </form>

        </section>

    </main>

</body>

</html>
