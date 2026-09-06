<?php

//=====================================================
// BITÁCORA - CONFIRMAR ELIMINACIÓN
//=====================================================
//
// Solo SuperAdmin puede acceder.
//
// Esta página NO elimina todavía el registro.
// Solamente solicita confirmación.
//
//=====================================================


//=====================================================
// 1. VALIDAR SESIÓN Y ROL
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

require_once '../../includes/permisos.php';

requiereRol('superadmin');


//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';


//=====================================================
// 3. VALIDAR ID
//=====================================================

$bitacoraId = (int) ($_GET['id'] ?? 0);

if ($bitacoraId <= 0) {

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

        COALESCE(
            r.fecha,
            hfo.fecha
        ) AS fecha,

        CONCAT(
            d.nombres,
            ' ',
            d.apellidos
        ) AS docente,

        c.nombre_curso AS curso,

        a.asignatura_nombre AS asignatura,


        COALESCE(
            r.tipo_reserva,
            hf.tipo
        ) AS tipo_uso,


        bl.hora_inicio,

        bl.hora_termino


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


    WHERE bita.id = ?

    LIMIT 1

";


$stmt =
    $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] =
        'No se pudo consultar el registro.';

    header('Location: index.php');

    exit();
}


$stmt->bind_param(
    "i",
    $bitacoraId
);


if (!$stmt->execute()) {

    $stmt->close();

    $_SESSION['error'] =
        'No se pudo consultar el registro.';

    header('Location: index.php');

    exit();
}


$resultado =
    $stmt->get_result();


$registro =
    $resultado->fetch_assoc();


$stmt->close();


if (!$registro) {

    $_SESSION['error'] =
        'El registro de bitácora no existe.';

    header('Location: index.php');

    exit();
}


//=====================================================
// 5. FORMATEAR FECHA
//=====================================================

$fechaMostrar = '—';


if (!empty($registro['fecha'])) {

    $fechaObj =
        DateTime::createFromFormat(
            'Y-m-d',
            $registro['fecha']
        );

    if ($fechaObj) {

        $fechaMostrar =
            $fechaObj->format('d/m/Y');
    }
}


//=====================================================
// 6. FORMATEAR HORARIO
//=====================================================

$horaMostrar = '—';


if (
    !empty($registro['fecha'])
    &&
    !empty($registro['hora_inicio'])
    &&
    !empty($registro['hora_termino'])
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
        -
        $inicio->getTimestamp();


    $media =
        clone $inicio;


    $media->modify(
        '+'
            . ($duracion / 2)
            . ' seconds'
    );


    switch ($registro['tipo_uso']
        ?? 'completo') {

        case 'sub1':

            $horaMostrar =
                $inicio->format('H:i')
                . ' - '
                . $media->format('H:i');

            break;


        case 'sub2':

            $horaMostrar =
                $media->format('H:i')
                . ' - '
                . $termino->format('H:i');

            break;


        case 'completo':

        default:

            $horaMostrar =
                $inicio->format('H:i')
                . ' - '
                . $termino->format('H:i');

            break;
    }
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
        Eliminar bitácora
    </title>


    <link
        rel="stylesheet"
        href="../../assets/css/estilos.css">

    <link
        rel="stylesheet"
        href="../../assets/css/botones.css">

    <link
        rel="stylesheet"
        href="../../assets/css/tablas.css">

    <link
        rel="stylesheet"
        href="../../assets/css/bitacora.css">

</head>


<body>


    <main class="contenedor contenedor-bitacora">


        <!--=================================================
        ENCABEZADO
    ==================================================-->

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Eliminar bitácora
                </h1>

                <p>
                    Registro de utilización de la Sala de Computación
                </p>

            </div>

        </div>


        <!--=================================================
        CONFIRMACIÓN
    ==================================================-->

        <section class="panel panel-eliminar-bitacora">

            <h2>
                ¿Está seguro de eliminar este registro?
            </h2>


            <p>
                Esta acción eliminará el registro de bitácora
                y las herramientas asociadas.
            </p>


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
                                Fecha
                            </th>

                            <td>
                                <?= htmlspecialchars(
                                    $fechaMostrar
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Hora
                            </th>

                            <td>
                                <?= htmlspecialchars(
                                    $horaMostrar
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Profesor
                            </th>

                            <td>
                                <?= htmlspecialchars(
                                    $registro['docente']
                                        ?? '—'
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Curso
                            </th>

                            <td>
                                <?= htmlspecialchars(
                                    $registro['curso']
                                        ?? '—'
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Asignatura
                            </th>

                            <td>
                                <?= htmlspecialchars(
                                    $registro['asignatura']
                                        ?? '—'
                                ) ?>
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!--=============================================
            ACCIONES
        ==============================================-->

            <div class="acciones acciones-eliminar-bitacora">

                <form
                    action="eliminar_confirmar.php"
                    method="post">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $bitacoraId ?>">


                    <button
                        type="submit"
                        class="btn btn-peligro">
                        Eliminar definitivamente
                    </button>

                </form>


                <a
                    href="ver.php?id=<?= $bitacoraId ?>"
                    class="btn btn-secundario">
                    Cancelar
                </a>

            </div>

        </section>


    </main>


</body>

</html>
