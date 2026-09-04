<?php
//=====================================================
// CONFIRMAR.PHP
// Confirma el uso de una reserva o de una ocurrencia
// de horario fijo.
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
// VALIDAR IDENTIFICADOR
//=====================================================

$reservaId = (int) ($_GET['id'] ?? 0);

$ocurrenciaId = (int) (
    $_GET['horario_fijo_ocurrencia_id'] ?? 0
);

if ($reservaId <= 0 && $ocurrenciaId <= 0) {

    $_SESSION['error'] =
        'La reserva o la ocurrencia no es válida.';

    header('Location: agenda.php');
    exit();
}

if ($reservaId > 0 && $ocurrenciaId > 0) {

    $_SESSION['error'] =
        'La confirmación no es válida.';

    header('Location: agenda.php');
    exit();
}

//=====================================================
// OBTENER ELEMENTO A CONFIRMAR
//=====================================================

$reserva = null;
$ocurrencia = null;
$esHorarioFijo = false;

if ($reservaId > 0) {

    $reserva = obtenerReservaPorId(
        $conexion,
        $reservaId
    );

    if (!$reserva) {

        $_SESSION['error'] =
            'La reserva no existe.';

        header('Location: agenda.php');
        exit();
    }

    if ($reserva['estado'] !== 'reservada') {

        $_SESSION['error'] =
            'Esta reserva ya no se encuentra disponible para confirmar.';

        header('Location: agenda.php');
        exit();
    }

    if (!reservaPuedeConfirmarse($reserva)) {

        $_SESSION['error'] =
            'El uso de la sala todavía no puede ser confirmado porque el horario aún no comienza.';

        header('Location: ver.php?id=' . $reservaId);
        exit();
    }
} else {

    $esHorarioFijo = true;

    $ocurrencia = obtenerOcurrenciaHorarioFijo(
        $conexion,
        $ocurrenciaId
    );

    if (!$ocurrencia) {

        $_SESSION['error'] =
            'La ocurrencia del horario fijo no existe.';

        header('Location: agenda.php');
        exit();
    }

    if ($ocurrencia['estado'] !== 'pendiente') {

        $_SESSION['error'] =
            'Esta ocurrencia de horario fijo ya no está disponible para confirmar.';

        header('Location: agenda.php');
        exit();
    }

    if (!ocurrenciaHorarioFijoPuedeConfirmarse($ocurrencia)) {

        $_SESSION['error'] =
            'El uso de la sala todavía no puede ser confirmado porque el horario aún no comienza.';

        header(
            'Location: confirmar.php?horario_fijo_ocurrencia_id='
                . $ocurrenciaId
        );
        exit();
    }
}

//=====================================================
// OBTENER RECURSOS
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

//=====================================================
// DATOS PARA MOSTRAR
//=====================================================

if ($esHorarioFijo) {

    $fecha = $ocurrencia['fecha'];
    $numeroBloque = $ocurrencia['numero_bloque'];
    $horaInicio = $ocurrencia['hora_inicio'];
    $horaTermino = $ocurrencia['hora_termino'];
    $curso = $ocurrencia['nombre_curso'];
    $asignatura = $ocurrencia['asignatura_nombre'];
    $docente = $ocurrencia['docente'];

    $accionVolver =
        'agenda.php';
} else {

    $fecha = $reserva['fecha'];
    $numeroBloque = $reserva['numero_bloque'];
    $horaInicio = $reserva['hora_inicio'];
    $horaTermino = $reserva['hora_termino'];
    $curso = $reserva['nombre_curso'];
    $asignatura = $reserva['asignatura_nombre'];

    $docente =
        $reserva['nombres'] . ' ' . $reserva['apellidos'];

    $accionVolver =
        'ver.php?id=' . $reservaId;
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

            <div class="tabla-responsive">

                <table class="tabla-detalle">

                    <tbody>

                        <tr>
                            <th>Fecha</th>

                            <td>
                                <?= htmlspecialchars(
                                    formatearFechaLarga($fecha)
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Bloque</th>

                            <td>
                                Bloque <?= htmlspecialchars($numeroBloque) ?>

                                (<?= substr($horaInicio, 0, 5) ?>
                                -
                                <?= substr($horaTermino, 0, 5) ?>)
                            </td>
                        </tr>

                        <tr>
                            <th>Curso</th>

                            <td>
                                <?= htmlspecialchars($curso) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Asignatura</th>

                            <td>
                                <?= htmlspecialchars($asignatura) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Docente</th>

                            <td>
                                <?= htmlspecialchars($docente) ?>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <form
                action="guardar_confirmacion.php"
                method="POST"
                class="formulario-confirmacion">

                <?php if ($esHorarioFijo): ?>

                    <input
                        type="hidden"
                        name="horario_fijo_ocurrencia_id"
                        value="<?= $ocurrenciaId ?>">

                <?php else: ?>

                    <input
                        type="hidden"
                        name="reserva_id"
                        value="<?= $reservaId ?>">

                <?php endif; ?>

                <div class="campo-formulario">

                    <label for="actividad">
                        Actividad
                    </label>

                    <textarea
                        id="actividad"
                        name="actividad"
                        rows="3"
                        maxlength="150"
                        required><?= htmlspecialchars(
                                        $esHorarioFijo
                                            ? ''
                                            : ($reserva['actividad'] ?? '')
                                    ) ?></textarea>

                </div>

                <div class="campo-formulario">

                    <label for="objetivo_clase">
                        Objetivo de la clase
                    </label>

                    <textarea
                        id="objetivo_clase"
                        name="objetivo_clase"
                        rows="4"
                        maxlength="150"
                        required><?= htmlspecialchars(
                                        $esHorarioFijo
                                            ? ''
                                            : ($reserva['objetivo_clase'] ?? '')
                                    ) ?></textarea>

                </div>

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

                <div class="campo-formulario">

                    <label for="observaciones">
                        Observaciones
                    </label>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        rows="4"></textarea>

                </div>

                <div class="acciones">

                    <button
                        type="submit"
                        class="btn btn-primario">
                        Confirmar uso
                    </button>

                    <a
                        href="<?= htmlspecialchars($accionVolver) ?>"
                        class="btn btn-secundario">
                        Volver
                    </a>

                </div>

            </form>

        </section>

    </main>

</body>

</html>
