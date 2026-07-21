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
$bloqueId = isset($_GET['bloque']) ? (int)$_GET['bloque'] : 0;

//=====================================================
// 4. VALIDAR VARIABLES
//=====================================================

if ($fecha == '' || $bloqueId <= 0) {
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

$stmt = $conexion->prepare($sqlBloque);
$stmt->bind_param("i", $bloqueId);
$stmt->execute();
$bloque = $stmt->get_result()->fetch_assoc();

if (!$bloque) {
    die('Bloque no encontrado.');
}

//-----------------------------------------------------
// 5.2 DOCENTES
//-----------------------------------------------------

$docentes = [];
$resultado = $conexion->query("
SELECT id,
CONCAT(nombres,' ',apellidos) AS nombre
FROM Docentes
ORDER BY apellidos,nombres");

while ($fila = $resultado->fetch_assoc()) {
    $docentes[] = $fila;
}

//-----------------------------------------------------
// 5.3 CURSOS
//-----------------------------------------------------

$cursos = [];
$resultado = $conexion->query("
SELECT id,nombre_curso
FROM Cursos
ORDER BY nombre_curso");

while ($fila = $resultado->fetch_assoc()) {
    $cursos[] = $fila;
}

//-----------------------------------------------------
// 5.4 ASIGNATURAS
//-----------------------------------------------------

$asignaturas = [];
$resultado = $conexion->query("
SELECT id,asignatura_nombre
FROM Asignaturas
ORDER BY asignatura_nombre");

while ($fila = $resultado->fetch_assoc()) {
    $asignaturas[] = $fila;
}

?>

<link rel="stylesheet" href="../../assets/css/formularios.css">
<link rel="stylesheet" href="../../assets/css/reservas.css">

<div class="contenedor-formulario">

    <h1>Nueva Reserva</h1>

    <form action="guardar.php" method="post">

        <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
        <input type="hidden" name="bloque_id" value="<?= $bloqueId ?>">

        <label>Fecha</label>
        <input type="text"
            value="<?= date('d/m/Y', strtotime($fecha)) ?>"
            readonly>

        <label>Bloque</label>
        <input type="text"
            value="Bloque <?= $bloque['numero_bloque'] ?>"
            readonly>

        <label>Horario</label>
        <input type="text"
            value="<?= substr($bloque['hora_inicio'], 0, 5) ?> - <?= substr($bloque['hora_termino'], 0, 5) ?>"
            readonly>

        <label>Docente</label>
        <select name="docente_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($docentes as $docente): ?>
                <option value="<?= $docente['id'] ?>">
                    <?= htmlspecialchars($docente['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Curso</label>
        <select name="curso_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($cursos as $curso): ?>
                <option value="<?= $curso['id'] ?>">
                    <?= htmlspecialchars($curso['nombre_curso']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Asignatura</label>
        <select name="asignatura_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($asignaturas as $asignatura): ?>
                <option value="<?= $asignatura['id'] ?>">
                    <?= htmlspecialchars($asignatura['asignatura_nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Actividad</label>
        <textarea
            name="actividad"
            rows="5"
            required></textarea>

        <label class="check">
            <input
                type="checkbox"
                name="permite_entrega"
                value="1">
            Permitir entrega de archivos
        </label>

        <div class="botones">
            <button type="submit">Guardar</button>
            <a href="index.php" class="boton">Cancelar</a>
        </div>

    </form>

</div>

<?php require_once '../../includes/footer.php'; ?>
