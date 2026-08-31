<?php
//=====================================================
// HORARIOS FIJOS - LISTADO
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';


//=====================================================
// FILTRO DE ESTADO
//=====================================================

$estado = $_GET['estado'] ?? 'activos';

$estadosPermitidos = [
    'activos',
    'inactivos',
    'todos'
];

if (!in_array(
    $estado,
    $estadosPermitidos,
    true
)) {

    $estado = 'activos';
}


//=====================================================
// OBTENER HORARIOS FIJOS
//=====================================================
//
// El listado administrativo puede mostrar:
//
// - Activos
// - Inactivos
// - Todos
//
// Se utilizan fechas amplias para permitir consultar
// también horarios históricos.
//=====================================================

$horariosFijos = obtenerHorariosFijos(
    $conexion,
    '1900-01-01',
    '2999-12-31',
    $estado
);


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
// FORMATEAR TIPO
//=====================================================

function textoTipoHorarioFijo(string $tipo): string
{
    return match ($tipo) {

        'completo' => 'Bloque completo',

        'sub1' => 'Primer bloque',

        'sub2' => 'Segundo bloque',

        default => $tipo
    };
}


//=====================================================
// FORMATEAR MODALIDAD
//=====================================================

function textoModalidadHorarioFijo(string $modalidad): string
{
    return match ($modalidad) {

        'asignatura' => 'Asignatura',

        'taller' => 'Taller',

        default => $modalidad
    };
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Horarios fijos</title>


    <!--=================================================
        CSS GENERALES
    ==================================================-->

    <link
        rel="stylesheet"
        href="../../assets/css/estilos.css">

    <link
        rel="stylesheet"
        href="../../assets/css/botones.css">

    <link
        rel="stylesheet"
        href="../../assets/css/formularios.css">

    <link
        rel="stylesheet"
        href="../../assets/css/tablas.css">

    <link
        rel="stylesheet"
        href="../../assets/css/reservas.css">

</head>


<body>

    <div class="contenedor contenedor-horarios-fijos">


        <!--=================================================
    ENCABEZADO
==================================================-->

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Horarios fijos
                </h1>

                <p>
                    Planificación oficial de la Sala de Computación
                </p>

            </div>

            <div>

                <a
                    href="agregar.php"
                    class="btn btn-primary">
                    + Nuevo horario fijo
                </a>

            </div>

        </div>


        <!--=================================================
    FILTROS
==================================================-->

        <div class="filtros">

            <form
                method="GET"
                action="index.php"
                class="formulario-filtros">

                <div class="campo">

                    <label for="estado">
                        Mostrar
                    </label>

                    <select
                        name="estado"
                        id="estado">

                        <option
                            value="activos"
                            <?= $estado === 'activos'
                                ? 'selected'
                                : ''; ?>>
                            Horarios activos
                        </option>

                        <option
                            value="inactivos"
                            <?= $estado === 'inactivos'
                                ? 'selected'
                                : ''; ?>>
                            Horarios inactivos
                        </option>

                        <option
                            value="todos"
                            <?= $estado === 'todos'
                                ? 'selected'
                                : ''; ?>>
                            Todos los horarios
                        </option>

                    </select>

                </div>

                <div class="campo campo-boton">

                    <button
                        type="submit"
                        class="btn btn-secundario">
                        Filtrar
                    </button>

                </div>

            </form>

        </div>


        <!--=================================================
        TABLA
    ==================================================-->

        <div class="tabla-contenedor">

            <table class="tabla">

                <thead>

                    <tr>

                        <th>Día</th>

                        <th>Bloque</th>

                        <th>Horario</th>

                        <th>Tipo</th>

                        <th>Modalidad</th>

                        <th>Docente</th>

                        <th>Curso</th>

                        <th>Asignatura / Taller</th>

                        <th>Fecha inicio</th>

                        <th>Fecha término</th>

                        <th>Estado</th>

                        <th>Acciones</th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($horariosFijos)): ?>

                        <tr>

                            <td
                                colspan="12"
                                style="text-align: center;">
                                No existen horarios fijos
                                para el filtro seleccionado.
                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach (
                            $horariosFijos
                            as $horario
                        ): ?>

                            <tr>


                                <!--=================================
                                DÍA
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        $nombresDias[(int)$horario['dia_semana']] ?? 'Desconocido'
                                    ); ?>

                                </td>


                                <!--=================================
                                BLOQUE
                            ==================================-->

                                <td>

                                    <?= (int)
                                    $horario['numero_bloque']; ?>

                                </td>


                                <!--=================================
                                HORARIO
                            ==================================-->

                                <td>

                                    <?= substr(
                                        $horario['hora_inicio'],
                                        0,
                                        5
                                    ); ?>

                                    -

                                    <?= substr(
                                        $horario['hora_termino'],
                                        0,
                                        5
                                    ); ?>

                                </td>


                                <!--=================================
                                TIPO
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        textoTipoHorarioFijo(
                                            $horario['tipo']
                                        )
                                    ); ?>

                                </td>


                                <!--=================================
                                MODALIDAD
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        textoModalidadHorarioFijo(
                                            $horario['modalidad']
                                        )
                                    ); ?>

                                </td>


                                <!--=================================
                                DOCENTE
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        $horario['docente']
                                    ); ?>

                                </td>


                                <!--=================================
                                CURSO
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        $horario['curso']
                                    ); ?>

                                </td>


                                <!--=================================
                                ASIGNATURA / TALLER
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        $horario['asignatura']
                                    ); ?>

                                </td>


                                <!--=================================
                                FECHA INICIO
                            ==================================-->

                                <td>

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $horario['fecha_inicio']
                                        )
                                    ); ?>

                                </td>


                                <!--=================================
                                FECHA TÉRMINO
                            ==================================-->

                                <td>

                                    <?php if (
                                        $horario['fecha_fin'] !== null
                                        &&
                                        $horario['fecha_fin'] !== ''
                                    ): ?>

                                        <?= date(
                                            'd/m/Y',
                                            strtotime(
                                                $horario['fecha_fin']
                                            )
                                        ); ?>

                                    <?php else: ?>

                                        Sin término

                                    <?php endif; ?>

                                </td>


                                <!--=================================
                                ESTADO
                            ==================================-->

                                <td>

                                    <?php if (
                                        (int)$horario['activo'] === 1
                                    ): ?>

                                        <span
                                            class="estado estado-activo">
                                            Activo
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="estado estado-inactivo">
                                            Inactivo
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!--=================================
                                ACCIONES
                            ==================================-->

                                <td>

                                    <div class="acciones">

                                        <a
                                            href="editar.php?id=<?= (int)$horario['id']; ?>"
                                            class="btn btn-secundario">
                                            Editar
                                        </a>


                                        <?php if (
                                            (int)$horario['activo'] === 1
                                        ): ?>

                                            <a
                                                href="desactivar.php?id=<?= (int)$horario['id']; ?>"
                                                class="btn btn-advertencia">
                                                Desactivar
                                            </a>

                                        <?php else: ?>

                                            <a
                                                href="activar.php?id=<?= (int)$horario['id']; ?>"
                                                class="btn btn-exito">
                                                Activar
                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</body>

</html>
