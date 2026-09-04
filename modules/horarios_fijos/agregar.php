<?php
//=====================================================
// HORARIOS FIJOS - AGREGAR
// Formulario para crear un nuevo horario fijo.
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
// OBTENER DATOS PARA LOS SELECT
//=====================================================

$docentes = $conexion->query("
    SELECT
        id,
        CONCAT(nombres, ' ', apellidos) AS nombre
    FROM docentes
    ORDER BY apellidos, nombres
");

$cursos = $conexion->query("
    SELECT
        id,
        nombre_curso,
        modalidad
    FROM cursos
    ORDER BY nombre_curso
");

$asignaturas = $conexion->query("
    SELECT
        id,
        asignatura_nombre
    FROM asignaturas
    ORDER BY asignatura_nombre
");

//=====================================================
// DÍAS DE LA SEMANA
//=====================================================

$diasSemana = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes'
];

//=====================================================
// BLOQUES
//=====================================================

$bloques = $conexion->query("
    SELECT
        id,
        numero_bloque,
        hora_inicio,
        hora_termino
    FROM bloques
    ORDER BY numero_bloque
");

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Nuevo horario fijo</title>

    <link
        rel="stylesheet"
        href="../../assets/css/estilos.css">

    <link
        rel="stylesheet"
        href="../../assets/css/formularios.css">

    <link
        rel="stylesheet"
        href="../../assets/css/botones.css">

</head>

<body>

    <div class="contenedor-formulario">

        <h1>
            Nuevo horario fijo
        </h1>

        <form
            action="guardar.php"
            method="POST">

            <!--=================================================
            DÍA
        ==================================================-->

            <div class="grupo-formulario">

                <label for="dia_semana">
                    Día
                </label>

                <select
                    name="dia_semana"
                    id="dia_semana"
                    required>

                    <option value="">
                        Seleccione un día
                    </option>

                    <?php foreach ($diasSemana as $numero => $nombre): ?>

                        <option value="<?= $numero ?>">
                            <?= htmlspecialchars($nombre) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!--=================================================
            BLOQUE
        ==================================================-->

            <div class="grupo-formulario">

                <label for="bloque_id">
                    Bloque
                </label>

                <select
                    name="bloque_id"
                    id="bloque_id"
                    required>

                    <option value="">
                        Seleccione un bloque
                    </option>

                    <?php while ($bloque = $bloques->fetch_assoc()): ?>

                        <option value="<?= $bloque['id'] ?>">

                            Bloque <?= (int) $bloque['numero_bloque'] ?>

                            —
                            <?= substr($bloque['hora_inicio'], 0, 5) ?>

                            a

                            <?= substr($bloque['hora_termino'], 0, 5) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!--=================================================
            TIPO
        ==================================================-->

            <div class="grupo-formulario">

                <label for="tipo">
                    Tipo de reserva
                </label>

                <select
                    name="tipo"
                    id="tipo"
                    required>

                    <option value="">
                        Seleccione un tipo
                    </option>

                    <option value="completo">
                        Bloque completo
                    </option>

                    <option value="sub1">
                        Primer bloque
                    </option>

                    <option value="sub2">
                        Segundo bloque
                    </option>

                </select>

            </div>


            <!--=================================================
            MODALIDAD
        ==================================================-->

            <div class="grupo-formulario">

                <label for="modalidad">
                    Modalidad
                </label>

                <select
                    name="modalidad"
                    id="modalidad"
                    required>

                    <option value="">
                        Seleccione una modalidad
                    </option>

                    <option value="asignatura">
                        Asignatura
                    </option>

                    <option value="taller">
                        Taller
                    </option>

                </select>

            </div>


            <!--=================================================
            DOCENTE
        ==================================================-->

            <div class="grupo-formulario">

                <label for="docente_id">
                    Docente
                </label>

                <select
                    name="docente_id"
                    id="docente_id"
                    required>

                    <option value="">
                        Seleccione un docente
                    </option>

                    <?php while ($docente = $docentes->fetch_assoc()): ?>

                        <option value="<?= (int) $docente['id'] ?>">

                            <?= htmlspecialchars(
                                $docente['nombre']
                            ) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!--=================================================
            CURSO
        ==================================================-->

            <div class="grupo-formulario">

                <label for="curso_id">
                    Curso
                </label>

                <select
                    name="curso_id"
                    id="curso_id"
                    required>

                    <option value="">
                        Seleccione un curso
                    </option>

                    <?php while ($curso = $cursos->fetch_assoc()): ?>

                        <option
                            value="<?= (int) $curso['id'] ?>"
                            data-modalidad="<?= htmlspecialchars(
                                                $curso['modalidad'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>">

                            <?= htmlspecialchars(
                                $curso['nombre_curso']
                            ) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!--=================================================
            ASIGNATURA
        ==================================================-->

            <div class="grupo-formulario">

                <label for="asignatura_id">
                    Asignatura
                </label>

                <select
                    name="asignatura_id"
                    id="asignatura_id">

                    <option value="">
                        Seleccione una asignatura
                    </option>

                    <?php while ($asignatura = $asignaturas->fetch_assoc()): ?>

                        <option value="<?= (int) $asignatura['id'] ?>">

                            <?= htmlspecialchars(
                                $asignatura['asignatura_nombre']
                            ) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!--=================================================
            FECHA DE INICIO
        ==================================================-->

            <div class="grupo-formulario">

                <label for="fecha_inicio">
                    Fecha de inicio
                </label>

                <input
                    type="date"
                    name="fecha_inicio"
                    id="fecha_inicio"
                    required>

            </div>


            <!--=================================================
            FECHA DE TÉRMINO
        ==================================================-->

            <div class="grupo-formulario">

                <label for="fecha_fin">
                    Fecha de término
                </label>

                <input
                    type="date"
                    name="fecha_fin"
                    id="fecha_fin">

            </div>


            <!--=================================================
            BOTONES
        ==================================================-->

            <div class="botones">

                <a
                    href="index.php"
                    class="btn btn-secundario">
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-primario">
                    Guardar
                </button>

            </div>

        </form>

    </div>

    <script src="../../assets/js/horarios_fijos.js"></script>

</body>

</html>
