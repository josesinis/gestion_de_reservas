<?php
//=====================================================
// HORARIOS FIJOS - EDITAR
//
// Carga un horario fijo existente para su modificación.
//=====================================================

declare(strict_types=1);

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

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {

    $_SESSION['error'] =
        'El horario fijo seleccionado no es válido.';

    header('Location: index.php');

    exit();
}


//=====================================================
// OBTENER HORARIO
//=====================================================

$horario = obtenerHorarioFijoPorId(
    $conexion,
    $id
);

if (!$horario) {

    $_SESSION['error'] =
        'El horario fijo no existe.';

    header('Location: index.php');

    exit();
}


//=====================================================
// NOMBRES DE LOS DÍAS
//=====================================================

$nombresDias = [

    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes'

];


//=====================================================
// OBTENER BLOQUES
//=====================================================

$bloques = [];

$resultado = $conexion->query("
    SELECT
        id,
        numero_bloque,
        hora_inicio,
        hora_termino
    FROM bloques
    ORDER BY numero_bloque
");

if ($resultado) {

    $bloques = $resultado->fetch_all(
        MYSQLI_ASSOC
    );
}


//=====================================================
// OBTENER DOCENTES
//=====================================================

$docentes = [];

$resultado = $conexion->query("
    SELECT
        id,
        nombres,
        apellidos
    FROM docentes
    ORDER BY apellidos, nombres
");

if ($resultado) {

    $docentes = $resultado->fetch_all(
        MYSQLI_ASSOC
    );
}


//=====================================================
// OBTENER CURSOS
//=====================================================

$cursos = [];

$resultado = $conexion->query("
    SELECT
        id,
        nombre_curso,
        modalidad
    FROM cursos
    ORDER BY nombre_curso
");

if ($resultado) {

    $cursos = $resultado->fetch_all(
        MYSQLI_ASSOC
    );
}


//=====================================================
// OBTENER ASIGNATURAS
//=====================================================

$asignaturas = [];

$resultado = $conexion->query("
    SELECT
        id,
        asignatura_nombre,
        modalidad
    FROM asignaturas
    ORDER BY asignatura_nombre
");

if ($resultado) {

    $asignaturas = $resultado->fetch_all(
        MYSQLI_ASSOC
    );
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar horario fijo</title>

    <!-- CSS generales -->

    <link
        rel="stylesheet"
        href="../../assets/css/estilos.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/botones.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/formularios.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/reservas.css"
    >

</head>

<body>

<div class="contenedor-formulario">

    <h1>
        Editar horario fijo
    </h1>

    <form
        action="actualizar.php"
        method="POST"
    >

        <!-- ID DEL HORARIO FIJO -->

        <input
            type="hidden"
            name="id"
            value="<?= (int)$horario['id']; ?>"
        >


        <!--=================================================
            DÍA
        ==================================================-->

        <div class="grupo-formulario">

            <label for="dia_semana">
                Día
            </label>

            <select
                id="dia_semana"
                name="dia_semana"
                required
            >

                <option value="">
                    Seleccione un día
                </option>

                <?php foreach ($nombresDias as $numero => $nombre): ?>

                    <option
                        value="<?= $numero; ?>"
                        <?= (
                            (int)$horario['dia_semana'] === $numero
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars($nombre); ?>
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
                id="bloque_id"
                name="bloque_id"
                required
            >

                <option value="">
                    Seleccione un bloque
                </option>

                <?php foreach ($bloques as $bloque): ?>

                    <option
                        value="<?= (int)$bloque['id']; ?>"
                        <?= (
                            (int)$horario['bloque_id']
                            === (int)$bloque['id']
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars(
                            $bloque['numero_bloque']
                        ); ?>

                        -
                        <?= substr(
                            $bloque['hora_inicio'],
                            0,
                            5
                        ); ?>

                        -
                        <?= substr(
                            $bloque['hora_termino'],
                            0,
                            5
                        ); ?>

                    </option>

                <?php endforeach; ?>

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
                id="tipo"
                name="tipo"
                required
            >

                <option value="">
                    Seleccione un tipo
                </option>

                <option
                    value="completo"
                    <?= $horario['tipo'] === 'completo'
                        ? 'selected'
                        : ''
                    ?>
                >
                    Bloque completo
                </option>

                <option
                    value="sub1"
                    <?= $horario['tipo'] === 'sub1'
                        ? 'selected'
                        : ''
                    ?>
                >
                    Primer bloque
                </option>

                <option
                    value="sub2"
                    <?= $horario['tipo'] === 'sub2'
                        ? 'selected'
                        : ''
                    ?>
                >
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
                id="modalidad"
                name="modalidad"
                required
            >

                <option value="">
                    Seleccione una modalidad
                </option>

                <option
                    value="asignatura"
                    <?= $horario['modalidad'] === 'asignatura'
                        ? 'selected'
                        : ''
                    ?>
                >
                    Asignatura
                </option>

                <option
                    value="taller"
                    <?= $horario['modalidad'] === 'taller'
                        ? 'selected'
                        : ''
                    ?>
                >
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
                id="docente_id"
                name="docente_id"
                required
            >

                <option value="">
                    Seleccione un docente
                </option>

                <?php foreach ($docentes as $docente): ?>

                    <option
                        value="<?= (int)$docente['id']; ?>"
                        <?= (
                            (int)$horario['docente_id']
                            === (int)$docente['id']
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars(
                            $docente['nombres']
                            . ' '
                            . $docente['apellidos']
                        ); ?>
                    </option>

                <?php endforeach; ?>

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
                id="curso_id"
                name="curso_id"
                required
            >

                <option value="">
                    Seleccione un curso
                </option>

                <?php foreach ($cursos as $curso): ?>

                    <option
                        value="<?= (int)$curso['id']; ?>"
                        data-modalidad="<?= htmlspecialchars(
                            $curso['modalidad'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        <?= (
                            (int)$horario['curso_id']
                            === (int)$curso['id']
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars(
                            $curso['nombre_curso']
                        ); ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!--=================================================
            ASIGNATURA / TALLER
        ==================================================-->

        <div class="grupo-formulario">

            <label for="asignatura_id">
                Asignatura / Taller
            </label>

            <select
                id="asignatura_id"
                name="asignatura_id"
                required
            >

                <option value="">
                    Seleccione una asignatura
                </option>

                <?php foreach ($asignaturas as $asignatura): ?>

                    <option
                        value="<?= (int)$asignatura['id']; ?>"
                        data-modalidad="<?= htmlspecialchars(
                            $asignatura['modalidad'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        <?= (
                            (int)$horario['asignatura_id']
                            === (int)$asignatura['id']
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars(
                            $asignatura['asignatura_nombre']
                        ); ?>
                    </option>

                <?php endforeach; ?>

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
                id="fecha_inicio"
                name="fecha_inicio"
                value="<?= htmlspecialchars(
                    $horario['fecha_inicio']
                ); ?>"
                required
            >

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
                id="fecha_fin"
                name="fecha_fin"
                value="<?= (
                    $horario['fecha_fin'] !== null
                )
                    ? htmlspecialchars(
                        $horario['fecha_fin']
                    )
                    : ''
                ?>"
            >

        </div>


        <!--=================================================
            OBSERVACIONES
        ==================================================-->

        <div class="grupo-formulario">

            <label for="observaciones">
                Observaciones
            </label>

            <textarea
                id="observaciones"
                name="observaciones"
            ><?= htmlspecialchars(
                $horario['observaciones'] ?? ''
            ); ?></textarea>

        </div>


        <!--=================================================
            BOTONES
        ==================================================-->

        <div class="botones">

            <a
                href="index.php"
                class="btn btn-secundario"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="btn btn-primario"
            >
                Guardar cambios
            </button>

        </div>

    </form>

</div>


<!--=====================================================
    JAVASCRIPT
======================================================-->

<script
    src="../../assets/js/horarios_fijos.js"
></script>

</body>

</html>
