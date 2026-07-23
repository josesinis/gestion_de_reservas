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

//=====================================================
// 3. RECIBIR VARIABLES
//=====================================================

$fecha = $_GET['fecha'] ?? '';
$bloqueId = isset($_GET['bloque']) ? (int) $_GET['bloque'] : 0;

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

    <h1>Nueva Reserva</h1>

    <form
        action="guardar.php"
        method="post"
        autocomplete="off">

        <input
            type="hidden"
            name="fecha"
            value="<?= htmlspecialchars($fecha) ?>">

        <input
            type="hidden"
            name="bloque_id"
            value="<?= $bloqueId ?>">

        <div class="grupo-formulario">

            <label for="fecha_reserva">Fecha</label>

            <input
                type="text"
                id="fecha_reserva"
                value="<?= date('d/m/Y', strtotime($fecha)) ?>"
                readonly
                autocomplete="off">

        </div>

        <div class="grupo-formulario">

            <label for="bloque">Bloque</label>

            <input
                type="text"
                id="bloque"
                value="Bloque <?= $bloque['numero_bloque'] ?>"
                readonly
                autocomplete="off">

        </div>

        <div class="grupo-formulario">

            <label for="horario">Horario</label>

            <input
                type="text"
                id="horario"
                value="<?= substr($bloque['hora_inicio'], 0, 5) ?> - <?= substr($bloque['hora_termino'], 0, 5) ?>"
                readonly
                autocomplete="off">

        </div>

        <div class="grupo-formulario">

            <label for="docente_id">Docente</label>

            <select
                id="docente_id"
                name="docente_id"
                required
                autocomplete="off">

                <option value="">Seleccione...</option>

                <?php foreach ($docentes as $docente): ?>

                    <option value="<?= $docente['id'] ?>">
                        <?= htmlspecialchars($docente['nombre']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>
                <div class="grupo-formulario">

            <label for="curso_id">Curso</label>

            <select
                id="curso_id"
                name="curso_id"
                required
                autocomplete="off">

                <option value="">Seleccione...</option>

                <?php foreach ($cursos as $curso): ?>

                    <option value="<?= $curso['id'] ?>">
                        <?= htmlspecialchars($curso['nombre_curso']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="grupo-formulario">

            <label for="asignatura_id">Asignatura</label>

            <select
                id="asignatura_id"
                name="asignatura_id"
                required
                autocomplete="off">

                <option value="">Seleccione...</option>

                <?php foreach ($asignaturas as $asignatura): ?>

                    <option value="<?= $asignatura['id'] ?>">
                        <?= htmlspecialchars($asignatura['asignatura_nombre']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="grupo-formulario">

            <label for="actividad">Actividad</label>

            <textarea
                id="actividad"
                name="actividad"
                rows="5"
                maxlength="150"
                required
                autocomplete="off"></textarea>

        </div>

        <div class="grupo-checkbox">

            <label for="permite_entrega">

                <input
                    type="checkbox"
                    id="permite_entrega"
                    name="permite_entrega"
                    value="1">

                Permitir entrega de archivos

            </label>

        </div>

        <div class="botones">

            <button type="submit" class="btn btn-primario">
                Guardar
            </button>

            <a
                href="index.php"
                class="btn btn-secundario">
                Cancelar
            </a>

        </div>

    </form>

</div>

<?php require_once '../../includes/footer.php'; ?>

<?php
//=====================================================
// FIN DEL ARCHIVO
//=====================================================
