<?php

//=====================================================
// BITÁCORA - IMPRESIÓN
//=====================================================
//
// Genera una versión del listado preparada para
// impresión.
//
// Acceso:
// - usuario
// - admin
// - superadmin
//
// No modifica ningún registro.
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


//=====================================================
// 3. FUNCIONES
//=====================================================

/**
 * Formatea una fecha para mostrarla en pantalla.
 */
function formatearFechaImpresion(string $fecha): string
{
    if ($fecha === '') {
        return '';
    }

    $fechaObj = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    if (!$fechaObj) {
        return $fecha;
    }

    return $fechaObj->format('d/m/Y');
}


/**
 * Obtiene el horario efectivo según el tipo de uso.
 *
 * Cada bloque tiene dos subbloques de 45 minutos.
 */
function obtenerHorarioImpresion(
    string $fecha,
    string $horaInicio,
    string $horaTermino,
    string $tipo
): string {

    if (
        $fecha === ''
        ||
        $horaInicio === ''
        ||
        $horaTermino === ''
    ) {
        return '';
    }

    $inicio = new DateTime(
        $fecha . ' ' . $horaInicio
    );

    $termino = new DateTime(
        $fecha . ' ' . $horaTermino
    );

    $duracion =
        $termino->getTimestamp()
        -
        $inicio->getTimestamp();

    $media = clone $inicio;

    $media->modify(
        '+' . ($duracion / 2) . ' seconds'
    );

    switch ($tipo) {

        case 'sub1':

            return
                $inicio->format('H:i')
                . ' - '
                . $media->format('H:i');

        case 'sub2':

            return
                $media->format('H:i')
                . ' - '
                . $termino->format('H:i');

        case 'completo':

        default:

            return
                $inicio->format('H:i')
                . ' - '
                . $termino->format('H:i');
    }
}


//=====================================================
// 4. CONSULTAR BITÁCORA
//=====================================================

$registros = [];

$sql = "

    SELECT

        bita.id AS bitacora_id,

        bita.reserva_id,

        bita.horario_fijo_ocurrencia_id,


        /*---------------------------------------------
          OBJETIVO Y ACTIVIDAD
        ---------------------------------------------*/

        COALESCE(
            bita.objetivo_clase,
            r.objetivo_clase
        ) AS objetivo_clase,

        COALESCE(
            bita.actividad,
            r.actividad
        ) AS actividad,


        /*---------------------------------------------
          DATOS DE LA CLASE
        ---------------------------------------------*/

        COALESCE(
            r.fecha,
            hfo.fecha
        ) AS fecha,


        COALESCE(
            r.docente_id,
            hfo.docente_id
        ) AS docente_id,


        COALESCE(
            r.curso_id,
            hfo.curso_id
        ) AS curso_id,


        COALESCE(
            r.asignatura_id,
            hfo.asignatura_id
        ) AS asignatura_id,


        COALESCE(
            r.bloque_id,
            hf.bloque_id
        ) AS bloque_id,


        COALESCE(
            r.tipo_reserva,
            hf.tipo
        ) AS tipo_uso,


        /*---------------------------------------------
          DATOS DESCRIPTIVOS
        ---------------------------------------------*/

        CONCAT(
            d.nombres,
            ' ',
            d.apellidos
        ) AS docente,


        c.nombre_curso AS curso,


        a.asignatura_nombre AS asignatura,


        bl.hora_inicio,

        bl.hora_termino,


        /*---------------------------------------------
          RECURSOS
        ---------------------------------------------*/

        recursos.nombres_recursos


    FROM bitacoras bita


    LEFT JOIN reservas r

        ON r.id = bita.reserva_id


    LEFT JOIN horarios_fijos_ocurrencias hfo

        ON hfo.id =
            bita.horario_fijo_ocurrencia_id


    LEFT JOIN horarios_fijos hf

        ON hf.id =
            hfo.horario_fijo_id


    LEFT JOIN docentes d

        ON d.id = COALESCE(
            r.docente_id,
            hfo.docente_id
        )


    LEFT JOIN cursos c

        ON c.id = COALESCE(
            r.curso_id,
            hfo.curso_id
        )


    LEFT JOIN asignaturas a

        ON a.id = COALESCE(
            r.asignatura_id,
            hfo.asignatura_id
        )


    LEFT JOIN bloques bl

        ON bl.id = COALESCE(
            r.bloque_id,
            hf.bloque_id
        )


    LEFT JOIN (

        SELECT

            br.bitacora_id,

            GROUP_CONCAT(
                DISTINCT rec.nombre_recurso
                ORDER BY rec.nombre_recurso
                SEPARATOR ', '
            ) AS nombres_recursos

        FROM bitacora_recursos br

        INNER JOIN recursos rec

            ON rec.id = br.recurso_id

        GROUP BY
            br.bitacora_id

    ) AS recursos

        ON recursos.bitacora_id =
            bita.id


    ORDER BY

        COALESCE(
            r.fecha,
            hfo.fecha
        ) DESC,

        bl.hora_inicio DESC,

        bita.id DESC

";


$resultado =
    $conexion->query($sql);


if ($resultado) {

    while (
        $fila =
        $resultado->fetch_assoc()
    ) {

        $registros[] =
            $fila;
    }
}


$totalRegistros =
    count($registros);


//=====================================================
// 5. FECHA DE IMPRESIÓN
//=====================================================

$fechaImpresion =
    date('d/m/Y H:i');

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Bitácora de uso - Impresión
    </title>


    <style>
        /*=================================================
          CONFIGURACIÓN DE PÁGINA
        =================================================*/

        @page {

            size: A4 landscape;

            margin: 10mm;

        }


        /*=================================================
          ESTILOS GENERALES
        =================================================*/

        * {

            box-sizing: border-box;

        }


        body {

            margin: 0;

            padding: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            font-size: 10px;

            color: #222;

            background: #fff;

        }


        /*=================================================
          ENCABEZADO
        =================================================*/

        .encabezado {

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            margin-bottom: 12px;

            border-bottom: 2px solid #741b49;

            padding-bottom: 8px;

        }


        .encabezado h1 {

            margin: 0;

            font-size: 18px;

        }


        .encabezado p {

            margin: 3px 0 0;

            font-size: 11px;

        }


        .informacion-impresion {

            text-align: right;

            font-size: 9px;

        }


        .informacion-impresion p {

            margin: 2px 0;

        }


        /*=================================================
          RESUMEN
        =================================================*/

        .resumen {

            margin-bottom: 8px;

            font-size: 10px;

        }


        /*=================================================
          TABLA
        =================================================*/

        table {

            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;

        }


        th,
        td {

            border: 1px solid #777;

            padding: 5px 6px;

            vertical-align: top;

            text-align: left;

            overflow-wrap: break-word;

            word-wrap: break-word;

        }


        th {

            background: #eee;

            font-weight: bold;

            text-align: center;

        }


        /*=================================================
          ANCHOS
        =================================================*/

        th:nth-child(1),
        td:nth-child(1) {

            width: 7%;

        }


        th:nth-child(2),
        td:nth-child(2) {

            width: 8%;

        }


        th:nth-child(3),
        td:nth-child(3) {

            width: 15%;

        }


        th:nth-child(4),
        td:nth-child(4) {

            width: 7%;

        }


        th:nth-child(5),
        td:nth-child(5) {

            width: 9%;

        }


        th:nth-child(6),
        td:nth-child(6) {

            width: 16%;

        }


        th:nth-child(7),
        td:nth-child(7) {

            width: 18%;

        }


        th:nth-child(8),
        td:nth-child(8) {

            width: 20%;

        }


        /*=================================================
          PIE
        =================================================*/

        .pie {

            margin-top: 8px;

            font-size: 8px;

            text-align: right;

        }


        /*=================================================
          IMPRESIÓN
        =================================================*/

        @media print {

            body {

                -webkit-print-color-adjust: exact;

                print-color-adjust: exact;

            }

        }
    </style>

</head>


<body>


    <!--=================================================
        ENCABEZADO
    ==================================================-->

    <div class="encabezado">

        <div>

            <h1>
                Bitácora de uso
            </h1>

            <p>
                Registro de utilización de la Sala de Computación
            </p>

        </div>


        <div class="informacion-impresion">

            <p>
                Registros:
                <strong>
                    <?= $totalRegistros ?>
                </strong>
            </p>

            <p>
                Impreso:
                <?= htmlspecialchars(
                    $fechaImpresion
                ) ?>
            </p>

        </div>

    </div>


    <!--=================================================
        TABLA
    ==================================================-->

    <?php if (empty($registros)): ?>

        <p>
            No existen registros en la bitácora.
        </p>

    <?php else: ?>


        <table>

            <thead>

                <tr>

                    <th>
                        Día/Mes
                    </th>

                    <th>
                        Hora
                    </th>

                    <th>
                        Nombre Profesor
                    </th>

                    <th>
                        Curso
                    </th>

                    <th>
                        Asignatura
                    </th>

                    <th>
                        Objetivo de la clase
                    </th>

                    <th>
                        Actividad realizada
                    </th>

                    <th>
                        Herramientas utilizadas
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php foreach (
                    $registros
                    as $registro
                ): ?>


                    <?php

                    $fecha =
                        formatearFechaImpresion(
                            $registro['fecha'] ?? ''
                        );


                    $hora =
                        obtenerHorarioImpresion(
                            $registro['fecha'] ?? '',
                            $registro['hora_inicio'] ?? '',
                            $registro['hora_termino'] ?? '',
                            $registro['tipo_uso'] ?? 'completo'
                        );


                    $objetivo =
                        trim(
                            $registro['objetivo_clase'] ?? ''
                        );


                    $actividad =
                        trim(
                            $registro['actividad'] ?? ''
                        );


                    $recursos =
                        trim(
                            $registro['nombres_recursos'] ?? ''
                        );

                    ?>


                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                $fecha
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $hora
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $registro['docente'] ?? '—'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $registro['curso'] ?? '—'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $registro['asignatura'] ?? '—'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $objetivo !== ''
                                    ? $objetivo
                                    : '—'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $actividad !== ''
                                    ? $actividad
                                    : '—'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $recursos !== ''
                                    ? $recursos
                                    : '—'
                            ) ?>
                        </td>

                    </tr>


                <?php endforeach; ?>


            </tbody>

        </table>


    <?php endif; ?>


    <!--=================================================
        PIE
    ==================================================-->

    <div class="pie">

        Bitácora de uso — Sala de Computación

    </div>


    <script>
        window.addEventListener(
            'load',
            function() {

                window.print();

            }
        );
    </script>


</body>

</html>
