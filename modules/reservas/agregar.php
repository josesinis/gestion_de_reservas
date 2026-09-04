<?php

/*
|--------------------------------------------------------------------------
| Sistema     : Gestión de Reservas
| Módulo      : Reservas
| Archivo     : agregar.php
|--------------------------------------------------------------------------
| Descripción :
| Formulario para registrar una nueva reserva.
|--------------------------------------------------------------------------
*/

//=====================================================
// 1. VALIDAR SESIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../includes/header.php';
require_once '../../includes/menu.php';
require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// 3. RECIBIR VARIABLES
//=====================================================

$modo = $_GET['modo'] ?? 'normal';

$ocurrenciaId = isset($_GET['horario_fijo_ocurrencia_id'])
    ? (int) $_GET['horario_fijo_ocurrencia_id']
    : 0;

$fecha = $_GET['fecha'] ?? '';

$bloqueId = isset($_GET['bloque'])
    ? (int) $_GET['bloque']
    : 0;

$tipoReserva = $_GET['tipo'] ?? 'completo';

/*echo '<pre>';
var_dump($_GET);
echo '</pre>';
exit;*/

//=====================================================
// 4. VALIDAR VARIABLES
//=====================================================

//=====================================================
// MODO REASIGNACIÓN DE HORARIO FIJO
//=====================================================

$ocurrenciaHorarioFijo = null;

if ($modo === 'reasignar') {

    if ($ocurrenciaId <= 0) {
        die('Ocurrencia de horario fijo no válida.');
    }

    $ocurrenciaHorarioFijo =
        obtenerOcurrenciaHorarioFijo(
            $conexion,
            $ocurrenciaId
        );

    if (!$ocurrenciaHorarioFijo) {
        die('La ocurrencia del horario fijo no existe.');
    }

    // La información real se obtiene desde la BD.

    $fecha = $ocurrenciaHorarioFijo['fecha'];

    $bloqueId =
        (int) $ocurrenciaHorarioFijo['bloque_id'];

    $tipoReserva =
        $ocurrenciaHorarioFijo['tipo'];

    // Solo se pueden reasignar horarios de asignatura.

    if (
        $ocurrenciaHorarioFijo['modalidad']
        !== 'asignatura'
    ) {
        die('Este horario fijo no puede ser reasignado.');
    }

    // Solo una ocurrencia pendiente puede ser reasignada.

    if (
        $ocurrenciaHorarioFijo['estado']
        !== 'pendiente'
    ) {
        die('Esta ocurrencia ya no está disponible para reasignación.');
    }
} else {

    //=================================================
    // RESERVA NORMAL
    //=================================================

    if ($fecha === '' || $bloqueId <= 0) {
        die('Parámetros inválidos.');
    }
}

//=====================================================
// 5. CONSULTAS
//=====================================================

//-----------------------------------------------------
// 5.1 OBTENER BLOQUE
//-----------------------------------------------------

$bloque = obtenerBloque(
    $conexion,
    $bloqueId
);

if (!$bloque) {
    die('Bloque no encontrado.');
}

$tiposReservados = obtenerTiposReservados(
    $conexion,
    $fecha,
    $bloqueId
);

//-----------------------------------------------------
// 5.1.1 HORARIO SEGÚN TIPO DE RESERVA
//-----------------------------------------------------

$horaInicio = substr($bloque['hora_inicio'], 0, 5);

$horaTermino = substr($bloque['hora_termino'], 0, 5);

$horario = obtenerHorarioReserva(
    $bloque,
    $tipoReserva
);

$opcionesReserva = obtenerOpcionesTipoReserva(
    $conexion,
    $fecha,
    $bloqueId,
    $bloque
);

//-----------------------------------------------------
// 5.2 DOCENTES
//-----------------------------------------------------

$docentes = obtenerDocentes($conexion);

//-----------------------------------------------------
// 5.3 CURSOS
//-----------------------------------------------------

$cursos = obtenerCursos($conexion);

//-----------------------------------------------------
// 5.4 ASIGNATURAS
//-----------------------------------------------------

/*$asignaturas = obtenerAsignaturas($conexion);*/

?>

<!-- CSS generales -->
<link rel="stylesheet" href="../../assets/css/estilos.css">
<link rel="stylesheet" href="../../assets/css/botones.css">
<link rel="stylesheet" href="../../assets/css/formularios.css">
<link rel="stylesheet" href="../../assets/css/tablas.css">

<!-- CSS módulo Reservas -->
<link rel="stylesheet" href="../../assets/css/reservas.css">

<!-- CSS exclusivo del prototipo -->
<link rel="stylesheet" href="../../assets/css/agenda_mockup.css">

<div class="contenedor-formulario">

    <h1>Nueva reserva</h1>

    <?php if ($modo === 'reasignar'): ?>

        <div class="agenda-reasignacion">

            <h2>Reasignación de horario fijo</h2>

            <p>
                Esta reserva reemplazará temporalmente
                el horario fijo planificado.
            </p>

            <div class="agenda-reasignacion-datos">

                <div>
                    <strong>Docente original:</strong>

                    <?= htmlspecialchars(
                        $ocurrenciaHorarioFijo['docente']
                    ); ?>
                </div>

                <div>
                    <strong>Curso original:</strong>

                    <?= htmlspecialchars(
                        $ocurrenciaHorarioFijo['nombre_curso']
                    ); ?>
                </div>

                <div>
                    <strong>Asignatura original:</strong>

                    <?= htmlspecialchars(
                        $ocurrenciaHorarioFijo['asignatura_nombre']
                    ); ?>
                </div>

            </div>

        </div>

    <?php endif; ?>

    <p class="agenda-subtitulo">
        Complete los datos para registrar la reserva.
    </p>

    <form
        action="guardar.php"
        method="post"
        autocomplete="off" class="agenda-form">

        <input
            type="hidden"
            name="fecha"
            value="<?= htmlspecialchars($fecha) ?>">

        <input
            type="hidden"
            name="bloque_id"
            value="<?= $bloqueId ?>">

        <input
            type="hidden"
            name="tipo_reserva"
            value="<?= htmlspecialchars($tipoReserva) ?>">

        <?php if ($modo === 'reasignar'): ?>

            <input
                type="hidden"
                name="modo"
                value="reasignar">

            <input
                type="hidden"
                name="horario_fijo_ocurrencia_id"
                value="<?= $ocurrenciaId; ?>">

        <?php endif; ?>

        <div class="grupo-formulario">

            <div class="agenda-resumen-seleccion">

                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Fecha
                    </span>

                    <strong>
                        <?= formatearFechaLarga($fecha); ?>
                    </strong>

                </div>

                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Bloque
                    </span>

                    <strong>
                        Bloque <?= $bloque['numero_bloque']; ?>
                    </strong>

                </div>

                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Horario seleccionado
                    </span>

                    <strong>
                        <?= htmlspecialchars($horario); ?>
                    </strong>

                </div>

            </div>

            <fieldset class="agenda-tipo-reserva">

                <legend>Tipo de reserva</legend>

                <?php if ($modo === 'reasignar'): ?>

                    <div class="agenda-reserva-info">

                        <strong>
                            <?php
                            if ($tipoReserva === 'completo') {
                                echo 'Bloque completo';
                            } elseif ($tipoReserva === 'sub1') {
                                echo 'Primer bloque (45 min)';
                            } elseif ($tipoReserva === 'sub2') {
                                echo 'Segundo bloque (45 min)';
                            }
                            ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars($horaInicio); ?>
                            -
                            <?= htmlspecialchars($horaTermino); ?>
                        </span>

                    </div>

                    <input
                        type="hidden"
                        name="tipo_reserva"
                        value="<?= htmlspecialchars($tipoReserva); ?>">

                <?php elseif (count($opcionesReserva) === 1): ?>

                    <div class="agenda-reserva-info">

                        <strong>
                            <?= htmlspecialchars(
                                $opcionesReserva[0]['texto']
                            ); ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $opcionesReserva[0]['horario']
                            ); ?>
                        </span>

                    </div>

                    <input
                        type="hidden"
                        name="tipo_reserva"
                        value="<?= htmlspecialchars(
                                    $opcionesReserva[0]['tipo']
                                ); ?>">

                <?php else: ?>

                    <?php foreach ($opcionesReserva as $opcion): ?>

                        <label class="agenda-opcion-reserva">

                            <input
                                type="radio"
                                name="tipo_reserva"
                                value="<?= htmlspecialchars(
                                            $opcion['tipo']
                                        ); ?>"
                                <?= $opcion['tipo'] === $tipoReserva
                                    ? 'checked'
                                    : ''; ?>>

                            <span>
                                <?= htmlspecialchars(
                                    $opcion['texto']
                                ); ?>
                            </span>

                            <small>
                                <?= htmlspecialchars(
                                    $opcion['horario']
                                ); ?>
                            </small>

                        </label>

                    <?php endforeach; ?>

                <?php endif; ?>

            </fieldset>

            <div class="agenda-form-grid">

                <div class="grupo-formulario">

                    <label for="docente">
                        Docente
                    </label>

                    <select
                        id="docente_id"
                        name="docente_id"
                        required>

                        <option value="">
                            Seleccionar docente
                        </option>

                        <?php foreach ($docentes as $docente): ?>

                            <option value="<?= $docente['id']; ?>">
                                <?= htmlspecialchars($docente['nombre']); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="grupo-formulario">

                    <label for="curso_id">
                        Curso
                    </label>

                    <select
                        id="curso_id"
                        name="curso_id"
                        required>

                        <option value="">
                            Seleccionar curso
                        </option>

                        <?php foreach ($cursos as $curso): ?>

                            <option value="<?= $curso['id']; ?>">
                                <?= htmlspecialchars($curso['nombre_curso']); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="grupo-formulario">

                    <label for="asignatura">
                        Asignatura
                    </label>

                    <select
                        id="asignatura_id"
                        name="asignatura_id"
                        required
                        disabled>

                        <option value="">
                            Seleccionar docente primero
                        </option>

                    </select>

                </div>

            </div>

            <div class="agenda-entrega">

                <div class="grupo-checkbox">

                    <label>

                        <input
                            type="checkbox"
                            id="permite_entrega"
                            name="permite_entrega"
                            value="1">

                        Permitir entrega de trabajos

                    </label>

                </div>


                <div
                    class="agenda-entrega-opciones"
                    id="opciones_entrega">

                    <div class="grupo-formulario">

                        <label for="fecha_entrega_oficial">
                            Fecha oficial de entrega
                        </label>

                        <input
                            type="date"
                            id="fecha_entrega_oficial"
                            name="fecha_entrega_oficial"
                            min="<?= htmlspecialchars($fecha); ?>">

                    </div>

                </div>

            </div>

            <div class="grupo-formulario agenda-form-actividad">

                <label for="actividad">
                    Actividad
                </label>

                <textarea
                    id="actividad"
                    name="actividad"
                    rows="3"
                    maxlength="150"
                    placeholder="Describa brevemente la actividad a realizar"></textarea>

            </div>

            <div class="grupo-formulario agenda-form-objetivo">

                <label for="objetivo_clase">
                    Objetivo de la clase
                </label>

                <textarea
                    id="objetivo_clase"
                    name="objetivo_clase"
                    rows="3"
                    placeholder="Indique el objetivo de la clase"></textarea>

            </div>

            <div class="botones">

                <a
                    href="agenda.php"
                    class="btn btn-secundario">
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar reserva
                </button>

            </div>
            <script src="../../assets/js/reservas.js"></script>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>

<?php
//=====================================================
// FIN DEL ARCHIVO
//=====================================================
