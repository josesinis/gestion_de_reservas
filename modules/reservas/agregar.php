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
/*
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit();
}
*/

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

$fecha = $_GET['fecha'] ?? '';


$bloqueId = isset($_GET['bloque']) ? (int) $_GET['bloque'] : 0;

$tipoReserva = $_GET['tipo'] ?? 'completo';

//=====================================================
// 4. VALIDAR VARIABLES
//=====================================================

if ($fecha === '' || $bloqueId <= 0) {
    die('Parámetros inválidos.');
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

//-----------------------------------------------------
// 5.1.1 HORARIO SEGÚN TIPO DE RESERVA
//-----------------------------------------------------

$horaInicio = substr($bloque['hora_inicio'], 0, 5);

$horaTermino = substr($bloque['hora_termino'], 0, 5);

$horario = obtenerHorarioReserva(
    $bloque,
    $tipoReserva
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

$asignaturas = obtenerAsignaturas($conexion);

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

                <label class="agenda-opcion-reserva">

                    <input
                        type="radio"
                        name="tipo_reserva"
                        value="<?= htmlspecialchars($tipoReserva); ?>"
                        checked>

                    <span>
                        <?= formatearTipoReserva($tipoReserva); ?>
                    </span>

                    <small>
                        <?= htmlspecialchars($horario); ?>
                    </small>

                </label>

                <?php if ($tipoReserva !== 'completo'): ?>

                    <label class="agenda-opcion-reserva">

                        <input
                            type="radio"
                            name="tipo_reserva"
                            value="completo">

                        <span>
                            Bloque completo
                        </span>

                        <small>
                            <?= $horaInicio . ' - ' . $horaTermino; ?>
                        </small>

                    </label>

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
                        required>

                        <option value="">
                            Seleccionar asignatura
                        </option>

                        <?php foreach ($asignaturas as $asignatura): ?>

                            <option value="<?= $asignatura['id']; ?>">
                                <?= htmlspecialchars($asignatura['asignatura_nombre']); ?>
                            </option>

                        <?php endforeach; ?>

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
                    required
                    placeholder="Describa brevemente la actividad a realizar"></textarea>

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
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>

<?php
//=====================================================
// FIN DEL ARCHIVO
//=====================================================
