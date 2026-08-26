<?php
//=====================================================
// HORARIOS FIJOS - LISTADO
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// OBTENER HORARIOS FIJOS
//=====================================================

$fechaInicio = date('Y-m-d');
$fechaFin = date('Y-m-d');

$horariosFijos = obtenerHorariosFijos(
    $conexion,
    $fechaInicio,
    $fechaFin
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
        content="width=device-width, initial-scale=1.0"
    >

    <title>Horarios fijos</title>

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
        href="../../assets/css/tablas.css"
    >

    <!-- CSS del módulo de reservas -->

    <link
        rel="stylesheet"
        href="../../assets/css/reservas.css"
    >

</head>

<body>

<div class="contenedor">

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
                class="btn btn-primary"
            >
                + Nuevo horario fijo
            </a>

        </div>

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

                    <th>Asignatura</th>

                    <th>Fecha inicio</th>

                    <th>Fecha término</th>

                    <th>Estado</th>

                </tr>

            </thead>

            <tbody>

                <?php if (empty($horariosFijos)): ?>

                    <tr>

                        <td
                            colspan="11"
                            style="text-align:center;"
                        >
                            No existen horarios fijos registrados.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($horariosFijos as $horario): ?>

                        <tr>

                            <!-- DÍA -->

                            <td>

                                <?= htmlspecialchars(
                                    $nombresDias[
                                        (int)$horario['dia_semana']
                                    ] ?? 'Desconocido'
                                ); ?>

                            </td>


                            <!-- BLOQUE -->

                            <td>

                                <?= (int)$horario['numero_bloque']; ?>

                            </td>


                            <!-- HORARIO -->

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


                            <!-- TIPO -->

                            <td>

                                <?= htmlspecialchars(
                                    textoTipoHorarioFijo(
                                        $horario['tipo']
                                    )
                                ); ?>

                            </td>


                            <!-- MODALIDAD -->

                            <td>

                                <?= htmlspecialchars(
                                    textoModalidadHorarioFijo(
                                        $horario['modalidad']
                                    )
                                ); ?>

                            </td>


                            <!-- DOCENTE -->

                            <td>

                                <?= htmlspecialchars(
                                    $horario['docente']
                                ); ?>

                            </td>


                            <!-- CURSO -->

                            <td>

                                <?= htmlspecialchars(
                                    $horario['curso']
                                ); ?>

                            </td>


                            <!-- ASIGNATURA -->

                            <td>

                                <?= htmlspecialchars(
                                    $horario['asignatura']
                                ); ?>

                            </td>


                            <!-- FECHA INICIO -->

                            <td>

                                <?= date(
                                    'd/m/Y',
                                    strtotime(
                                        $horario['fecha_inicio']
                                    )
                                ); ?>

                            </td>


                            <!-- FECHA TÉRMINO -->

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


                            <!-- ESTADO -->

                            <td>

                                <?php if (
                                    (int)$horario['activo'] === 1
                                ): ?>

                                    Activo

                                <?php else: ?>

                                    Inactivo

                                <?php endif; ?>

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
