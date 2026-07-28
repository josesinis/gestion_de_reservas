<?php

/*
|--------------------------------------------------------------------------
| Sistema     : Gestión de Reservas
| Módulo      : Reservas
| Archivo     : editar.php
|--------------------------------------------------------------------------
| Descripción :
| Permite modificar una reserva existente.
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
// 3. RECIBIR PARÁMETROS
//=====================================================

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

//=====================================================
// 4. VALIDAR PARÁMETROS
//=====================================================

if ($id <= 0) {
    die('Reserva no válida.');
}

//=====================================================
// 5. OBTENER RESERVA
//=====================================================

$sql = "
SELECT
    r.*,

    b.numero_bloque,
    b.hora_inicio,
    b.hora_termino

FROM Reservas r

INNER JOIN Bloques b
    ON b.id = r.bloque_id

WHERE r.id = ?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$reserva = $stmt->get_result()->fetch_assoc();

if (!$reserva) {
    die('La reserva no existe.');
}

//=====================================================
// 6. OBTENER DOCENTES
//=====================================================

$docentes = [];

$resultadoDocentes = $conexion->query("
SELECT
    id,
    CONCAT(nombres,' ',apellidos) AS nombre
FROM Docentes
ORDER BY apellidos,nombres
");

while ($fila = $resultadoDocentes->fetch_assoc()) {
    $docentes[] = $fila;
}

//=====================================================
// 7. OBTENER CURSOS
//=====================================================

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

//=====================================================
// 8. OBTENER ASIGNATURAS
//=====================================================

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

    <h1>Editar Reserva</h1>

    <form
        action="actualizar.php"
        method="post"
        autocomplete="off">

        <input
            type="hidden"
            name="id"
            value="<?= $reserva['id']; ?>">


        <div class="grupo-formulario">

            <label for="fecha_reserva">Fecha</label>

            <input
                type="text"
                id="fecha_reserva"
                value="<?= date('d/m/Y', strtotime($reserva['fecha'])) ?>"
                readonly
                autocomplete="off">

        </div>

        <div class="grupo-formulario">

            <label for="bloque">Bloque</label>

            <input
                type="text"
                id="bloque"
                value="Bloque <?= htmlspecialchars($reserva['numero_bloque']) ?>"
                readonly
                autocomplete="off">

        </div>

        <div class="grupo-formulario">

            <label for="horario">Horario</label>

            <input
                type="text"
                id="horario"
                value="<?= substr($reserva['hora_inicio'], 0, 5) ?> - <?= substr($reserva['hora_termino'], 0, 5) ?>"
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

                    <option
                        value="<?= $docente['id'] ?>"
                        <?= $docente['id'] == $reserva['docente_id'] ? 'selected' : '' ?>>
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

                    <option
                        value="<?= $curso['id'] ?>"
                        <?= $curso['id'] == $reserva['curso_id'] ? 'selected' : '' ?>>
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

                    <option
                        value="<?= $asignatura['id'] ?>"
                        <?= $asignatura['id'] == $reserva['asignatura_id'] ? 'selected' : '' ?>>
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
                required><?= htmlspecialchars($reserva['actividad']) ?>
            </textarea>

        </div>

        <div class="grupo-checkbox">

            <label for="permite_entrega">

                <input
                    type="checkbox"
                    id="permite_entrega"
                    name="permite_entrega"
                    value="1"
                    <?= $reserva['permite_entrega'] ? 'checked' : '' ?>>

                Permitir entrega de archivos

            </label>

        </div>

        <div class="botones">

            <button type="submit" class="btn btn-primario">
                Guardar Cambios
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
