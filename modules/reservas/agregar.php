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

$sqlBloque = "
SELECT
    numero_bloque,
    hora_inicio,
    hora_termino
FROM Bloques
WHERE id = ?
";

$stmtBloque = $conexion->prepare($sqlBloque);
$stmtBloque->bind_param('i', $bloqueId);
$stmtBloque->execute();

$bloque = $stmtBloque->get_result()->fetch_assoc();

//-----------------------------------------------------
// 5.1.1 HORARIO SEGÚN TIPO DE RESERVA
//-----------------------------------------------------

$horaInicio = substr($bloque['hora_inicio'], 0, 5);
$horaTermino = substr($bloque['hora_termino'], 0, 5);

// Calcular hora intermedia del bloque
$inicio = strtotime($bloque['hora_inicio']);
$termino = strtotime($bloque['hora_termino']);

$horaMedia = date(
    'H:i',
    $inicio + (($termino - $inicio) / 2)
);

switch ($tipoReserva) {

    case 'sub1':
        $horario = $horaInicio . ' - ' . $horaMedia;
        break;

    case 'sub2':
        $horario = $horaMedia . ' - ' . $horaTermino;
        break;

    default:
        $horario = $horaInicio . ' - ' . $horaTermino;
        break;
}

if (!$bloque) {
    die('Bloque no encontrado.');
}

//-----------------------------------------------------
// 5.2 DOCENTES
//-----------------------------------------------------

$docentes = [];

$resultadoDocentes = $conexion->query("
SELECT
    id,
    CONCAT(nombres, ' ', apellidos) AS nombre
FROM Docentes
ORDER BY apellidos, nombres
");

while ($fila = $resultadoDocentes->fetch_assoc()) {
    $docentes[] = $fila;
}

//-----------------------------------------------------
// 5.3 CURSOS
//-----------------------------------------------------

$cursos = [];

$resultadoCursos = $conexion->query("
SELECT
    id,
    nombre_curso
FROM Cursos
ORDER BY nombre_curso
");

while ($fila = $resultadoCursos->fetch_assoc()) {
    $cursos[] = $fila;
}

//-----------------------------------------------------
// 5.4 ASIGNATURAS
//-----------------------------------------------------

$asignaturas = [];

$resultadoAsignaturas = $conexion->query("
SELECT
    id,
    asignatura_nombre
FROM Asignaturas
ORDER BY asignatura_nombre
");

while ($fila = $resultadoAsignaturas->fetch_assoc()) {
    $asignaturas[] = $fila;
}

?>

<link rel="stylesheet" href="../../assets/css/estilos.css">
<link rel="stylesheet" href="../../assets/css/formularios.css">
<link rel="stylesheet" href="../../assets/css/botones.css">
<link rel="stylesheet" href="../../assets/css/reservas.css">

<div class="contenedor-formulario">

    <h1>

        Nueva Reserva

        <?php if ($tipoReserva !== 'completo'): ?>

            <small>

                (
                <?= $tipoReserva === 'sub1'
                    ? 'Primer subbloque'
                    : 'Segundo subbloque'; ?>
                )

            </small>

        <?php endif; ?>

    </h1>

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

            <label for="fecha_reserva">Fecha</label>

            <div class="agenda-resumen-seleccion">

                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Fecha
                    </span>

                    <strong id="resumen_fecha">
                        <?= formatearFechaLarga($fecha); ?>
                    </strong>

                </div>


                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Bloque
                    </span>

                    <strong id="resumen_bloque">
                        Bloque <?= $bloque['numero_bloque']; ?>
                    </strong>

                </div>


                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Horario seleccionado
                    </span>

                    <strong id="resumen_horario">
                        <?= htmlspecialchars($horario); ?>
                    </strong>

                </div>

            </div>

            <fieldset class="agenda-tipo-reserva" id="tipo_reserva_opciones">

                <legend>Tipo de reserva</legend>

                <label class="agenda-opcion-reserva">

                    <input
                        type="radio"
                        name="tipo_reserva"
                        value="sub1"
                        data-horario="10:20 - 11:05"
                        checked>

                    <span>
                        Primer subbloque
                    </span>

                    <small>
                        10:20 - 11:05
                    </small>

                </label>


                <label class="agenda-opcion-reserva">

                    <input
                        type="radio"
                        name="tipo_reserva"
                        value="completo"
                        data-horario="10:20 - 11:50">

                    <span>
                        Bloque completo
                    </span>

                    <small>
                        10:20 - 11:50
                    </small>

                </label>

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

                            <option value="<?= $docente['id'] ?>">
                                <?= htmlspecialchars($docente['nombre']) ?>
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

                            <option value="<?= $curso['id'] ?>">
                                <?= htmlspecialchars($curso['nombre_curso']) ?>
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

                            <option value="<?= $asignatura['id'] ?>">
                                <?= htmlspecialchars($asignatura['asignatura_nombre']) ?>
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
                            min="2026-06-08">

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

                <button
                    type="button"
                    class="btn btn-secundario">
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar reserva
                </button>

            </div>


            <?php require_once '../../includes/footer.php'; ?>

            <?php
//=====================================================
// FIN DEL ARCHIVO
//=====================================================
