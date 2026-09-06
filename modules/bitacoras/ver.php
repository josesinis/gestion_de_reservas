<?php

//=====================================================
// BITÁCORA - DETALLE
//=====================================================
//
// Muestra el detalle de un registro de bitácora.
//
// Acceso:
// - admin
// - superadmin
//
// El usuario normal solo podrá acceder al listado
// y posteriormente a la impresión.
//=====================================================


//=====================================================
// 1. VALIDAR SESIÓN Y ROL
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

require_once '../../includes/permisos.php';

requiereRol(['admin', 'superadmin']);


//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';


//=====================================================
// 3. VALIDAR ID
//=====================================================

$bitacoraId = (int) ($_GET['id'] ?? 0);

if ($bitacoraId <= 0) {

    header('Location: index.php');

    exit();
}


//=====================================================
// 4. CONSULTAR BITÁCORA
//=====================================================

$sql = "

    SELECT

        bita.id AS bitacora_id,

        bita.reserva_id,

        bita.horario_fijo_ocurrencia_id,

        COALESCE(
            bita.objetivo_clase,
            r.objetivo_clase
        ) AS objetivo_clase,

        COALESCE(
            bita.actividad,
            r.actividad
        ) AS actividad,

        bita.observaciones,


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


        bl.numero_bloque,

        bl.hora_inicio,

        bl.hora_termino,


        /*---------------------------------------------
          INFORMACIÓN DEL HORARIO FIJO
        ---------------------------------------------*/

        hfo.fecha_confirmacion,


        /*---------------------------------------------
          RECURSOS
        ---------------------------------------------*/

        recursos.nombres_recursos


    FROM bitacoras bita


    /*---------------------------------------------
      RESERVA
    ---------------------------------------------*/

    LEFT JOIN reservas r

        ON r.id = bita.reserva_id


    /*---------------------------------------------
      OCURRENCIA DE HORARIO FIJO
    ---------------------------------------------*/

    LEFT JOIN horarios_fijos_ocurrencias hfo

        ON hfo.id =
            bita.horario_fijo_ocurrencia_id


    /*---------------------------------------------
      HORARIO FIJO
    ---------------------------------------------*/

    LEFT JOIN horarios_fijos hf

        ON hf.id =
            hfo.horario_fijo_id


    /*---------------------------------------------
      DOCENTE
    ---------------------------------------------*/

    LEFT JOIN docentes d

        ON d.id = COALESCE(
            r.docente_id,
            hfo.docente_id
        )


    /*---------------------------------------------
      CURSO
    ---------------------------------------------*/

    LEFT JOIN cursos c

        ON c.id = COALESCE(
            r.curso_id,
            hfo.curso_id
        )


    /*---------------------------------------------
      ASIGNATURA
    ---------------------------------------------*/

    LEFT JOIN asignaturas a

        ON a.id = COALESCE(
            r.asignatura_id,
            hfo.asignatura_id
        )


    /*---------------------------------------------
      BLOQUE
    ---------------------------------------------*/

    LEFT JOIN bloques bl

        ON bl.id = COALESCE(
            r.bloque_id,
            hf.bloque_id
        )


    /*---------------------------------------------
      RECURSOS
    ---------------------------------------------*/

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

            ON rec.id =
                br.recurso_id

        GROUP BY
            br.bitacora_id

    ) AS recursos

        ON recursos.bitacora_id =
            bita.id


    /*---------------------------------------------
      REGISTRO SOLICITADO
    ---------------------------------------------*/

    WHERE bita.id = ?

    LIMIT 1

";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    header('Location: index.php');

    exit();
}

$stmt->bind_param(
    "i",
    $bitacoraId
);

$stmt->execute();

$resultado = $stmt->get_result();

$registro = $resultado->fetch_assoc();

$stmt->close();


//=====================================================
// 5. VALIDAR EXISTENCIA
//=====================================================

if (!$registro) {

    header('Location: index.php');

    exit();
}


//=====================================================
// 6. FUNCIONES DE PRESENTACIÓN
//=====================================================

function formatearFechaDetalle(
    ?string $fecha
): string {

    if (
        $fecha === null
        ||
        $fecha === ''
    ) {
        return '—';
    }

    $fechaObj =
        DateTime::createFromFormat(
            'Y-m-d',
            $fecha
        );

    if (!$fechaObj) {
        return $fecha;
    }

    return $fechaObj->format(
        'd/m/Y'
    );
}


function obtenerHorarioDetalle(
    ?string $fecha,
    ?string $horaInicio,
    ?string $horaTermino,
    ?string $tipo
): string {

    if (
        empty($fecha)
        ||
        empty($horaInicio)
        ||
        empty($horaTermino)
    ) {
        return '—';
    }

    $inicio = new DateTime(
        $fecha
        . ' '
        . $horaInicio
    );

    $termino = new DateTime(
        $fecha
        . ' '
        . $horaTermino
    );

    $duracion =
        $termino->getTimestamp()
        -
        $inicio->getTimestamp();

    $media = clone $inicio;

    $media->modify(
        '+'
        . ($duracion / 2)
        . ' seconds'
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
// 7. PREPARAR DATOS
//=====================================================

$fecha =
    formatearFechaDetalle(
        $registro['fecha'] ?? null
    );


$hora =
    obtenerHorarioDetalle(
        $registro['fecha'] ?? null,
        $registro['hora_inicio'] ?? null,
        $registro['hora_termino'] ?? null,
        $registro['tipo_uso'] ?? 'completo'
    );


$docente =
    trim(
        $registro['docente'] ?? ''
    );


$curso =
    trim(
        $registro['curso'] ?? ''
    );


$asignatura =
    trim(
        $registro['asignatura'] ?? ''
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


$observaciones =
    trim(
        $registro['observaciones'] ?? ''
    );


//=====================================================
// 8. DETERMINAR ORIGEN
//=====================================================

if (
    !empty($registro['reserva_id'])
    &&
    !empty($registro['horario_fijo_ocurrencia_id'])
) {

    $origen =
        'Reasignación';

} elseif (
    !empty($registro['reserva_id'])
) {

    $origen =
        'Reserva';

} else {

    $origen =
        'Horario fijo';
}


//=====================================================
// 9. FECHA DE CONFIRMACIÓN
//=====================================================

$fechaConfirmacion = '—';

if (
    !empty($registro['fecha_confirmacion'])
) {

    $fechaObj =
        DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $registro['fecha_confirmacion']
        );

    if ($fechaObj) {

        $fechaConfirmacion =
            $fechaObj->format(
                'd/m/Y H:i'
            );
    }
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

    <title>
        Detalle de bitácora
    </title>


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
        href="../../assets/css/tablas.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/bitacora.css"
    >

</head>


<body>


<main class="contenedor contenedor-bitacora">


    <!--=================================================
        ENCABEZADO
    ==================================================-->

    <div class="encabezado-pagina">

        <div>

            <h1>
                Detalle de bitácora
            </h1>

            <p>
                Registro de utilización de la Sala de Computación
            </p>

        </div>

    </div>


    <!--=================================================
        INFORMACIÓN DE LA CLASE
    ==================================================-->

    <section class="panel">

        <h2>
            Información de la clase
        </h2>


        <div class="tabla-detalle-bitacora">

            <table>

                <tbody>

                    <tr>

                        <th>
                            Fecha
                        </th>

                        <td>
                            <?= htmlspecialchars($fecha) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Hora
                        </th>

                        <td>
                            <?= htmlspecialchars($hora) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Profesor
                        </th>

                        <td>
                            <?= htmlspecialchars(
                                $docente !== ''
                                    ? $docente
                                    : '—'
                            ) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Curso
                        </th>

                        <td>
                            <?= htmlspecialchars(
                                $curso !== ''
                                    ? $curso
                                    : '—'
                            ) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Asignatura
                        </th>

                        <td>
                            <?= htmlspecialchars(
                                $asignatura !== ''
                                    ? $asignatura
                                    : '—'
                            ) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Origen
                        </th>

                        <td>
                            <?= htmlspecialchars($origen) ?>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!--=================================================
        REGISTRO DE LA CLASE
    ==================================================-->

    <section class="panel">

        <h2>
            Registro de la clase
        </h2>


        <div class="tabla-detalle-bitacora">

            <table>

                <tbody>

                    <tr>

                        <th>
                            Objetivo de la clase
                        </th>

                        <td>
                            <?= htmlspecialchars(
                                $objetivo !== ''
                                    ? $objetivo
                                    : '—'
                            ) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Actividad realizada
                        </th>

                        <td>
                            <?= htmlspecialchars(
                                $actividad !== ''
                                    ? $actividad
                                    : '—'
                            ) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Herramientas utilizadas
                        </th>

                        <td>
                            <?= htmlspecialchars(
                                $recursos !== ''
                                    ? $recursos
                                    : '—'
                            ) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Observaciones
                        </th>

                        <td>
                            <?php if ($observaciones !== ''): ?>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $observaciones
                                    )
                                ) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!--=================================================
        INFORMACIÓN DEL REGISTRO
    ==================================================-->

    <section class="panel">

        <h2>
            Información del registro
        </h2>


        <div class="tabla-detalle-bitacora">

            <table>

                <tbody>

                    <tr>

                        <th>
                            N.º de bitácora
                        </th>

                        <td>
                            <?= (int) $registro['bitacora_id'] ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Fecha de confirmación
                        </th>

                        <td>
                            <?= htmlspecialchars(
                                $fechaConfirmacion
                            ) ?>
                        </td>

                    </tr>


                    <tr>

                        <th>
                            Reserva asociada
                        </th>

                        <td>

                            <?php if (
                                !empty(
                                    $registro['reserva_id']
                                )
                            ): ?>

                                #<?= (int) $registro['reserva_id'] ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            Ocurrencia de horario fijo
                        </th>

                        <td>

                            <?php if (
                                !empty(
                                    $registro[
                                        'horario_fijo_ocurrencia_id'
                                    ]
                                )
                            ): ?>

                                #<?= (int) $registro[
                                    'horario_fijo_ocurrencia_id'
                                ] ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!--=================================================
        VOLVER
    ==================================================-->

    <div class="acciones">

        <a
            href="index.php"
            class="btn btn-secondary"
        >
            Volver a bitácora
        </a>

    </div>


</main>


</body>

</html>
