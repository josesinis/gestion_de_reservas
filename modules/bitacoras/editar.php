<?php

//=====================================================
// BITÁCORA - EDITAR
//=====================================================
//
// Permite modificar únicamente la información propia
// del uso registrado.
//
// Acceso:
// - admin
// - superadmin
//
// No se permite modificar la información estructural
// de la clase.
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
// 4. OBTENER BITÁCORA
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

        bl.hora_termino


    FROM bitacoras bita


    /*---------------------------------------------
      RESERVA
    ---------------------------------------------*/

    LEFT JOIN reservas r

        ON r.id = bita.reserva_id


    /*---------------------------------------------
      OCURRENCIA HORARIO FIJO
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
      REGISTRO SOLICITADO
    ---------------------------------------------*/

    WHERE bita.id = ?

    LIMIT 1

";


$stmt = $conexion->prepare($sql);

if (!$stmt) {

    $_SESSION['error'] =
        'No se pudo consultar la bitácora.';

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
        'No se pudo consultar la bitácora.';

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
// 5. OBTENER RECURSOS
//=====================================================

$sqlRecursos = "

    SELECT
        id,
        nombre_recurso

    FROM recursos

    ORDER BY nombre_recurso

";


$resultadoRecursos =
    $conexion->query($sqlRecursos);


$recursos = [];


if ($resultadoRecursos) {

    while (
        $recurso =
            $resultadoRecursos->fetch_assoc()
    ) {

        $recursos[] =
            $recurso;
    }
}


//=====================================================
// 6. OBTENER RECURSOS ACTUALES
//=====================================================

$sqlRecursosActuales = "

    SELECT recurso_id

    FROM bitacora_recursos

    WHERE bitacora_id = ?

";


$stmtRecursosActuales =
    $conexion->prepare(
        $sqlRecursosActuales
    );


$recursosActuales = [];


if ($stmtRecursosActuales) {

    $stmtRecursosActuales->bind_param(
        "i",
        $bitacoraId
    );

    if (
        $stmtRecursosActuales->execute()
    ) {

        $resultadoActuales =
            $stmtRecursosActuales->get_result();

        while (
            $fila =
                $resultadoActuales->fetch_assoc()
        ) {

            $recursosActuales[] =
                (int) $fila['recurso_id'];
        }
    }

    $stmtRecursosActuales->close();
}


//=====================================================
// 7. PREPARAR DATOS
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


//=====================================================
// 8. FORMATEAR FECHA
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
// 9. CALCULAR HORARIO
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


    switch (
        $registro['tipo_uso'] ?? 'completo'
    ) {

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


//=====================================================
// 10. DETERMINAR ORIGEN
//=====================================================

if (
    !empty($registro['reserva_id'])
    &&
    !empty(
        $registro[
            'horario_fijo_ocurrencia_id'
        ]
    )
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
        Editar bitácora
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
        href="../../assets/css/formularios.css"
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
                Editar bitácora
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


                    <tr>

                        <th>
                            Origen
                        </th>

                        <td>
                            <?= htmlspecialchars(
                                $origen
                            ) ?>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!--=================================================
        FORMULARIO DE EDICIÓN
    ==================================================-->

    <section class="panel">

        <h2>
            Registro de la clase
        </h2>


        <form
            action="actualizar.php"
            method="post"
            autocomplete="off"
        >


            <input
                type="hidden"
                name="id"
                value="<?= $bitacoraId ?>"
            >


            <!--=========================================
                OBJETIVO
            ==========================================-->

            <div class="campo-formulario">

                <label for="objetivo_clase">
                    Objetivo de la clase
                </label>

                <textarea
                    id="objetivo_clase"
                    name="objetivo_clase"
                    rows="4"
                    maxlength="150"
                    required
                ><?= htmlspecialchars(
                    $objetivo
                ) ?></textarea>

            </div>


            <!--=========================================
                ACTIVIDAD
            ==========================================-->

            <div class="campo-formulario">

                <label for="actividad">
                    Actividad realizada
                </label>

                <textarea
                    id="actividad"
                    name="actividad"
                    rows="4"
                    maxlength="150"
                    required
                ><?= htmlspecialchars(
                    $actividad
                ) ?></textarea>

            </div>


            <!--=========================================
                RECURSOS
            ==========================================-->

            <div class="campo-formulario">

                <label>
                    Herramientas utilizadas
                </label>


                <?php if (!empty($recursos)): ?>

                    <div class="lista-recursos">

                        <?php foreach (
                            $recursos
                            as $recurso
                        ): ?>

                            <label>

                                <input
                                    type="checkbox"
                                    name="recursos[]"
                                    value="<?= (int) $recurso['id'] ?>"
                                    <?= in_array(
                                        (int) $recurso['id'],
                                        $recursosActuales,
                                        true
                                    )
                                        ? 'checked'
                                        : '' ?>
                                >

                                <?= htmlspecialchars(
                                    $recurso[
                                        'nombre_recurso'
                                    ]
                                ) ?>

                            </label>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <p>
                        No hay recursos registrados.
                    </p>

                <?php endif; ?>

            </div>


            <!--=========================================
                OBSERVACIONES
            ==========================================-->

            <div class="campo-formulario">

                <label for="observaciones">
                    Observaciones
                </label>

                <textarea
                    id="observaciones"
                    name="observaciones"
                    rows="4"
                ><?= htmlspecialchars(
                    $observaciones
                ) ?></textarea>

            </div>


            <!--=========================================
                ACCIONES
            ==========================================-->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-primario"
                >
                    Guardar cambios
                </button>


                <a
                    href="ver.php?id=<?= $bitacoraId ?>"
                    class="btn btn-secundario"
                >
                    Cancelar
                </a>

            </div>


        </form>

    </section>


</main>


</body>

</html>
