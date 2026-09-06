<?php

//=====================================================
// BITÁCORA - LISTADO
//=====================================================
//
// Muestra los usos confirmados de la Sala de Computación.
//
// Acceso a acciones:
// - usuario: solo listado (por ahora)
// - admin: ver + editar
// - superadmin: ver + editar + eliminar
//
// La impresión se agregará posteriormente.
//
//=====================================================


//=====================================================
// 1. VALIDAR SESIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

require_once '../../includes/permisos.php';


//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';


//=====================================================
// 3. OBTENER ROL
//=====================================================

$rolUsuario = $_SESSION['rol'] ?? 'usuario';


//=====================================================
// 4. FUNCIONES
//=====================================================

/**
 * Formatea una fecha para mostrarla en pantalla.
 */
function formatearFechaBitacora(string $fecha): string
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
function obtenerHorarioBitacora(
    string $fecha,
    string $horaInicio,
    string $horaTermino,
    string $tipo
): string {

    if ($horaInicio === '' || $horaTermino === '') {
        return '';
    }

    $inicio = new DateTime(
        $fecha . ' ' . $horaInicio
    );

    $termino = new DateTime(
        $fecha . ' ' . $horaTermino
    );

    // Duración total del bloque en segundos.
    $duracion =
        $termino->getTimestamp()
        - $inicio->getTimestamp();

    // Punto medio del bloque.
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
// 5. CONSULTAR BITÁCORA
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

            ON rec.id = br.recurso_id

        GROUP BY
            br.bitacora_id

    ) AS recursos

        ON recursos.bitacora_id =
            bita.id


    /*---------------------------------------------
      ORDEN
    ---------------------------------------------*/

    ORDER BY

        COALESCE(
            r.fecha,
            hfo.fecha
        ) DESC,

        bl.hora_inicio DESC,

        bita.id DESC

";


$resultado = $conexion->query($sql);


//=====================================================
// 6. COMPROBAR RESULTADO
//=====================================================

if ($resultado) {

    while ($fila = $resultado->fetch_assoc()) {

        $registros[] = $fila;
    }
}


//=====================================================
// 7. CONTADOR
//=====================================================

$totalRegistros = count($registros);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Bitácora de uso</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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

    <!-- CSS módulo Reservas -->

    <link
        rel="stylesheet"
        href="../../assets/css/reservas.css">

    <link
        rel="stylesheet"
        href="../../assets/css/bitacora.css">

</head>


<body>

    <?php

    $seccionActual = 'bitacora';

    require_once '../../includes/menu.php';

    ?>

    <div class="contenedor contenedor-bitacora">


        <!--=================================================
        ENCABEZADO
    ==================================================-->

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Bitácora de uso
                </h1>

                <p>
                    Registro de utilización de la Sala de Computación
                </p>

            </div>


            <div>

                <a
                    href="imprimir.php"
                    class="btn btn-primary">
                    <i class="fa-solid fa-print"></i>
                    Imprimir
                </a>

            </div>

        </div>


        <!--=================================================
        RESUMEN
    ==================================================-->

        <div class="resumen-bitacora">

            Registros encontrados:
            <strong>
                <?= $totalRegistros ?>
            </strong>

        </div>


        <!--=================================================
        TABLA
    ==================================================-->

        <div class="tabla-bitacora-contenedor">

            <table class="tabla tabla-bitacora">

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

                        <th>
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php if (empty($registros)): ?>

                        <tr>

                            <td
                                colspan="9"
                                style="text-align: center;">

                                No existen registros en la bitácora.

                            </td>

                        </tr>


                    <?php else: ?>


                        <?php foreach ($registros as $registro): ?>


                            <?php

                            $fecha =
                                formatearFechaBitacora(
                                    $registro['fecha'] ?? ''
                                );


                            $hora =
                                obtenerHorarioBitacora(
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


                                <!-- Día/Mes -->

                                <td>

                                    <?= htmlspecialchars($fecha) ?>

                                </td>


                                <!-- Hora -->

                                <td>

                                    <?= htmlspecialchars($hora) ?>

                                </td>


                                <!-- Profesor -->

                                <td>

                                    <?= htmlspecialchars(
                                        $registro['docente'] ?? ''
                                    ) ?>

                                </td>


                                <!-- Curso -->

                                <td>

                                    <?= htmlspecialchars(
                                        $registro['curso'] ?? ''
                                    ) ?>

                                </td>


                                <!-- Asignatura -->

                                <td>

                                    <?= htmlspecialchars(
                                        $registro['asignatura'] ?? ''
                                    ) ?>

                                </td>


                                <!-- Objetivo -->

                                <td>

                                    <?= htmlspecialchars(
                                        $objetivo !== ''
                                            ? $objetivo
                                            : '—'
                                    ) ?>

                                </td>


                                <!-- Actividad -->

                                <td>

                                    <?= htmlspecialchars(
                                        $actividad !== ''
                                            ? $actividad
                                            : '—'
                                    ) ?>

                                </td>


                                <!-- Recursos -->

                                <td>

                                    <?= htmlspecialchars(
                                        $recursos !== ''
                                            ? $recursos
                                            : '—'
                                    ) ?>

                                </td>


                                <!-- Acciones -->

                                <td class="acciones-bitacora">


                                    <?php if (
                                        $rolUsuario === 'admin'
                                        ||
                                        $rolUsuario === 'superadmin'
                                    ): ?>


                                        <a
                                            href="ver.php?id=<?= (int) $registro['bitacora_id'] ?>"
                                            class="btn btn-secundario btn-bitacora">
                                            Ver
                                        </a>


                                        <a
                                            href="editar.php?id=<?= (int) $registro['bitacora_id'] ?>"
                                            class="btn btn-primario btn-bitacora">
                                            Editar
                                        </a>


                                    <?php endif; ?>


                                    <?php if (
                                        $rolUsuario === 'superadmin'
                                    ): ?>


                                        <a
                                            href="eliminar.php?id=<?= (int) $registro['bitacora_id'] ?>"
                                            class="btn btn-peligro btn-bitacora">
                                            Eliminar
                                        </a>


                                    <?php endif; ?>


                                    <?php if (
                                        $rolUsuario === 'usuario'
                                    ): ?>

                                        <span>
                                            —
                                        </span>

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
