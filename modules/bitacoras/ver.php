<?php

//=====================================================
// BITÁCORA - DETALLE
//=====================================================
//
// Muestra el detalle de un uso confirmado de la
// Sala de Computación.
//
// El registro puede provenir de:
//
// 1. Una reserva normal.
// 2. Una ocurrencia de horario fijo.
// 3. Una reasignación.
//
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
// 3. OBTENER ID
//=====================================================

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {

    $_SESSION['error'] =
        'El registro de bitácora no es válido.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 4. CONSULTAR REGISTRO
//=====================================================

$sql = "

    SELECT

        bita.id AS bitacora_id,

        bita.reserva_id,

        bita.horario_fijo_ocurrencia_id,

        bita.objetivo_clase,

        bita.actividad,

        bita.observaciones,


        /*---------------------------------------------
          FECHA
        ---------------------------------------------*/

        COALESCE(
            r.fecha,
            hfo.fecha
        ) AS fecha,


        /*---------------------------------------------
          DATOS DE LA CLASE
        ---------------------------------------------*/

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
          DOCENTE
        ---------------------------------------------*/

        CONCAT(
            d.nombres,
            ' ',
            d.apellidos
        ) AS docente,


        /*---------------------------------------------
          CURSO
        ---------------------------------------------*/

        c.nombre_curso AS curso,


        /*---------------------------------------------
          ASIGNATURA
        ---------------------------------------------*/

        a.asignatura_nombre AS asignatura,


        /*---------------------------------------------
          BLOQUE
        ---------------------------------------------*/

        bl.numero_bloque,

        bl.hora_inicio,

        bl.hora_termino,


        /*---------------------------------------------
          FECHA DE CONFIRMACIÓN
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

        ON hf.id = hfo.horario_fijo_id


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

            ON rec.id = br.recurso_id

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

    $_SESSION['error'] =
        'No se pudo consultar el registro de bitácora.';

    header('Location: index.php');
    exit();
}


$stmt->bind_param(
    "i",
    $id
);


if (!$stmt->execute()) {

    $stmt->close();

    $_SESSION['error'] =
        'No se pudo consultar el registro de bitácora.';

    header('Location: index.php');
    exit();
}


$resultado = $stmt->get_result();

$registro = $resultado->fetch_assoc();

$stmt->close();


if (!$registro) {

    $_SESSION['error'] =
        'El registro de bitácora no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 5. PREPARAR FECHA
//=====================================================

$fecha = '';

if (!empty($registro['fecha'])) {

    $fechaObj = DateTime::createFromFormat(
        'Y-m-d',
        $registro['fecha']
    );

    if ($fechaObj) {

        $fecha =
            $fechaObj->format('d/m/Y');
    } else {

        $fecha =
            $registro['fecha'];
    }
}


//=====================================================
// 6. PREPARAR HORARIO
//=====================================================

$hora = '';

if (
    !empty($registro['hora_inicio']) &&
    !empty($registro['hora_termino']) &&
    !empty($registro['fecha'])
) {

    $inicio = new DateTime(
        $registro['fecha']
            . ' '
            . $registro['hora_inicio']
    );

    $termino = new DateTime(
        $registro['fecha']
            . ' '
            . $registro['hora_termino']
    );

    $duracion =
        $termino->getTimestamp()
        - $inicio->getTimestamp();

    $media = clone $inicio;

    $media->modify(
        '+' . ($duracion / 2) . ' seconds'
    );


    switch ($registro['tipo_uso']) {

        case 'sub1':

            $hora =
                $inicio->format('H:i')
                . ' - '
                . $media->format('H:i');

            break;


        case 'sub2':

            $hora =
                $media->format('H:i')
                . ' - '
                . $termino->format('H:i');

            break;


        case 'completo':

        default:

            $hora =
                $inicio->format('H:i')
                . ' - '
                . $termino->format('H:i');

            break;
    }
}


//=====================================================
// 7. PREPARAR DATOS DE LA CLASE
//=====================================================

$objetivo =
    trim(
        $registro['objetivo_clase'] ?? ''
    );


$actividad =
    trim(
        $registro['actividad'] ?? ''
    );


$observaciones =
    trim(
        $registro['observaciones'] ?? ''
    );


$recursos =
    trim(
        $registro['nombres_recursos'] ?? ''
    );


//=====================================================
// 8. DETERMINAR ORIGEN
//=====================================================

if (
    !empty($registro['reserva_id']) &&
    !empty($registro['horario_fijo_ocurrencia_id'])
) {

    $origen =
        'Reasignación';
} elseif (!empty($registro['reserva_id'])) {

    $origen =
        'Reserva';
} else {

    $origen =
        'Horario fijo';
}


//=====================================================
// 9. FECHA DE CONFIRMACIÓN
//=====================================================

$fechaConfirmacion = '';

if (!empty($registro['fecha_confirmacion'])) {

    $fechaConfirmacionObj =
        new DateTime(
            $registro['fecha_confirmacion']
        );

    $fechaConfirmacion =
        $fechaConfirmacionObj->format(
            'd/m/Y H:i'
        );
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Detalle de bitácora
    </title>


    <!-- CSS generales -->

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

    <!-- CSS Bitácora -->

    <link
        rel="stylesheet"
        href="../../assets/css/bitacora.css">

</head>


<body>


    <div class="contenedor contenedor-bitacora">


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


            <div>

                <a
                    href="index.php"
                    class="btn btn-secondary">
                    Volver a bitácora
                </a>

            </div>

        </div>


        <!--=================================================
        INFORMACIÓN DE LA CLASE
    ==================================================-->

        <div class="panel">

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
                                <?= htmlspecialchars(
                                    $hora !== ''
                                        ? $hora
                                        : '—'
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Profesor
                            </th>

                            <td>
                                <?= htmlspecialchars(
                                    $registro['docente'] ?? '—'
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Curso
                            </th>

                            <td>
                                <?= htmlspecialchars(
                                    $registro['curso'] ?? '—'
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Asignatura
                            </th>

                            <td>
                                <?= htmlspecialchars(
                                    $registro['asignatura'] ?? '—'
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

        </div>


        <!--=================================================
        REGISTRO DE LA CLASE
    ==================================================-->

        <div class="panel">

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

                                <?= nl2br(
                                    htmlspecialchars(
                                        $objetivo !== ''
                                            ? $objetivo
                                            : '—'
                                    )
                                ) ?>

                            </td>

                        </tr>


                        <tr>

                            <th>
                                Actividad realizada
                            </th>

                            <td>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $actividad !== ''
                                            ? $actividad
                                            : '—'
                                    )
                                ) ?>

                            </td>

                        </tr>


                        <tr>

                            <th>
                                Herramientas utilizadas
                            </th>

                            <td>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $recursos !== ''
                                            ? $recursos
                                            : '—'
                                    )
                                ) ?>

                            </td>

                        </tr>


                        <tr>

                            <th>
                                Observaciones
                            </th>

                            <td>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $observaciones !== ''
                                            ? $observaciones
                                            : '—'
                                    )
                                ) ?>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        <!--=================================================
        INFORMACIÓN DEL REGISTRO
    ==================================================-->

        <div class="panel">

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
                                    $fechaConfirmacion !== ''
                                        ? $fechaConfirmacion
                                        : '—'
                                ) ?>

                            </td>

                        </tr>


                        <tr>

                            <th>
                                Reserva asociada
                            </th>

                            <td>

                                <?php if (
                                    !empty($registro['reserva_id'])
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
                                    !empty($registro['horario_fijo_ocurrencia_id'])
                                ): ?>

                                    #<?= (int) $registro['horario_fijo_ocurrencia_id'] ?>

                                <?php else: ?>

                                    —

                                <?php endif; ?>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        <!--=================================================
        ACCIONES
    ==================================================-->

        <div class="acciones-formulario">



        </div>


    </div>


</body>

</html>
