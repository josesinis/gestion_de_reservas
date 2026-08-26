<?php

/**
 * ============================================================================
 * Sistema de Gestión Institucional
 * ----------------------------------------------------------------------------
 * Módulo   : Reservas
 * Archivo  : agenda_mockup.php
 * Versión  : 2.0.0
 * Fecha    : 29-07-2026
 * ----------------------------------------------------------------------------
 * Descripción:
 * Prototipo de la agenda semanal para la gestión de reservas de la
 * Sala de Computación.
 *
 * Cada bloque está compuesto por dos subbloques de 45 minutos.
 * ============================================================================
 */


require '../../config/database.php';
require '../../includes/reservas_funciones.php';

//=====================================================
// DETERMINAR SEMANA MOSTRADA
//=====================================================

$semanaActual = obtenerDiasSemana();

$fechaInicioActual = $semanaActual[0]['fecha'];

$fechaReferencia = $_GET['semana'] ?? null;


//-----------------------------------------------------
// VALIDAR SEMANA SOLICITADA
//-----------------------------------------------------

if ($fechaReferencia !== null) {

    $diasSemanaSolicitada = obtenerDiasSemana($fechaReferencia);

    $fechaInicioSolicitada =
        $diasSemanaSolicitada[0]['fecha'];

    // No permitir semanas anteriores
    if ($fechaInicioSolicitada < $fechaInicioActual) {

        header(
            'Location: agenda.php'
        );

        exit();
    }

    $diasSemana = $diasSemanaSolicitada;
} else {

    $diasSemana = $semanaActual;
}

//=====================================================
// BLOQUES MOSTRADOS
//=====================================================

$bloques = obtenerBloques($conexion);


$fechaInicio = $diasSemana[0]['fecha'];

$fechaFin = $diasSemana[4]['fecha'];

$reservas = obtenerReservas(
    $conexion,
    $fechaInicio,
    $fechaFin
);

$horariosFijosPorFecha = obtenerHorariosFijosPorFecha(
    $conexion,
    $fechaInicio,
    $fechaFin
);

$ocurrenciasHorariosFijosPorFecha =
    obtenerOcurrenciasHorariosFijosPorFecha(
        $conexion,
        $fechaInicio,
        $fechaFin
    );

?>



<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agenda - Mockup</title>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- CSS generales -->
    <link rel="stylesheet" href="../../assets/css/estilos.css">
    <link rel="stylesheet" href="../../assets/css/botones.css">
    <link rel="stylesheet" href="../../assets/css/formularios.css">
    <link rel="stylesheet" href="../../assets/css/tablas.css">

    <!-- CSS módulo Reservas -->
    <link rel="stylesheet" href="../../assets/css/reservas.css">

    <!-- CSS exclusivo del prototipo -->
    <link rel="stylesheet" href="../../assets/css/agenda_mockup.css">

</head>

<body>

    <div class="agenda-container">

        <!-- =====================================================
             HEADER DE LA AGENDA
        ====================================================== -->

        <header class="agenda-header">

            <div class="agenda-header-info">

                <div class="agenda-header-titulos">

                    <h1>
                        Gestión de Reservas
                    </h1>

                    <p class="agenda-subtitulo">
                        Sala de Computación
                    </p>

                </div>

            </div>

            <?php

            $numeroSemana = (int) (new DateTime(
                $diasSemana[0]['fecha']
            ))->format('W');

            $fechaSemanaMostrada = $diasSemana[0]['fecha'];

            $fechaSemanaAnterior = (new DateTime($fechaSemanaMostrada))
                ->modify('-7 days')
                ->format('Y-m-d');

            $fechaSemanaSiguiente = (new DateTime($fechaSemanaMostrada))
                ->modify('+7 days')
                ->format('Y-m-d');

            $puedeRetroceder = $fechaSemanaMostrada > $fechaInicioActual;

            ?>
            <!-- =================================================
                 NAVEGACIÓN SEMANAL
            ================================================== -->

            <div class="agenda-nav">

                <?php if ($puedeRetroceder): ?>

                    <a
                        href="agenda.php?semana=<?= urlencode($fechaSemanaAnterior); ?>"
                        class="agenda-btn-nav"
                        title="Semana anterior">

                        <i class="fa-solid fa-chevron-left"></i>

                        <span>
                            Semana anterior
                        </span>

                    </a>

                <?php else: ?>

                    <button
                        type="button"
                        class="agenda-btn-nav"
                        disabled
                        title="No se pueden consultar semanas anteriores">

                        <i class="fa-solid fa-chevron-left"></i>

                        <span>
                            Semana anterior
                        </span>

                    </button>

                <?php endif; ?>


                <div class="agenda-periodo">

                    <span class="agenda-semana">
                        Semana <?= $numeroSemana; ?>
                    </span>

                    <span class="agenda-fechas">

                        <?= formatearRangoSemana(
                            $diasSemana[0]['fecha'],
                            $diasSemana[4]['fecha']
                        ); ?>

                    </span>

                </div>


                <?php if ($fechaSemanaMostrada === $fechaInicioActual): ?>

                    <button
                        type="button"
                        class="agenda-btn-hoy"
                        disabled
                        title="Ya estás en la semana actual">

                        Hoy

                    </button>

                <?php else: ?>

                    <a
                        href="agenda.php"
                        class="agenda-btn-hoy"
                        title="Ir a la semana actual">

                        Hoy

                    </a>

                <?php endif; ?>


                <a
                    href="agenda.php?semana=<?= urlencode($fechaSemanaSiguiente); ?>"
                    class="agenda-btn-nav"
                    title="Semana siguiente">

                    <span>
                        Semana siguiente
                    </span>

                    <i class="fa-solid fa-chevron-right"></i>

                </a>

            </div>

        </header>


        <!-- =====================================================
             AGENDA SEMANAL
        ====================================================== -->

        <table class="agenda">

            <!-- =================================================
                 CABECERA
            ================================================== -->

            <thead>

                <tr>

                    <th>Bloque</th>

                    <th>Horario</th>

                    <?php foreach ($diasSemana as $dia): ?>

                        <th>

                            <div class="agenda-dia">

                                <span class="agenda-dia-nombre">
                                    <?= $dia['nombre']; ?>
                                </span>

                                <span class="agenda-dia-fecha">
                                    <?= $dia['fecha_corta']; ?>
                                </span>

                            </div>

                        </th>

                    <?php endforeach; ?>

                </tr>

            </thead>


            <!-- =================================================
                 CUERPO DE LA AGENDA
            ================================================== -->

            <tbody>

                <?php foreach ($bloques as $bloque): ?>

                    <tr>

                        <td>
                            <?= $bloque['numero_bloque']; ?>
                        </td>

                        <td>
                            <?= substr($bloque['hora_inicio'], 0, 5); ?>
                            -
                            <?= substr($bloque['hora_termino'], 0, 5); ?>
                        </td>

                        <?php foreach ($diasSemana as $dia): ?>

                            <?php

                            $tiposReservados = obtenerTiposReservados(
                                $conexion,
                                $dia['fecha'],
                                $bloque['id']
                            );

                            $reservaCompleta = $tiposReservados['completo'] ?? null;
                            $reservaSub1     = $tiposReservados['sub1'] ?? null;
                            $reservaSub2     = $tiposReservados['sub2'] ?? null;

                            $horariosFijosBloque =
                                $horariosFijosPorFecha[$dia['fecha']][$bloque['id']]
                                ?? [];

                            $ocurrenciasFijasBloque =
                                $ocurrenciasHorariosFijosPorFecha[$dia['fecha']][$bloque['id']]
                                ?? [];

                            $ocurrenciaFijaCompleta =
                                $ocurrenciasFijasBloque['completo']
                                ?? null;

                            $ocurrenciaFijaSub1 =
                                $ocurrenciasFijasBloque['sub1']
                                ?? null;

                            $ocurrenciaFijaSub2 =
                                $ocurrenciasFijasBloque['sub2']
                                ?? null;

                            $horarioFijoCompleto =
                                $horariosFijosBloque['completo']
                                ?? null;

                            $horarioFijoSub1 =
                                $horariosFijosBloque['sub1']
                                ?? null;

                            $horarioFijoSub2 =
                                $horariosFijosBloque['sub2']
                                ?? null;

                            //=====================================================
                            // OCULTAR HORARIO FIJO REASIGNADO
                            //
                            // Si la ocurrencia fue reasignada, el horario fijo
                            // original deja de mostrarse y la agenda mostrará
                            // la reserva que lo reemplazó.
                            //=====================================================

                            if (
                                $ocurrenciaFijaCompleta !== null &&
                                $ocurrenciaFijaCompleta['estado'] === 'reasignada'
                            ) {
                                $horarioFijoCompleto = null;
                            }

                            if (
                                $ocurrenciaFijaSub1 !== null &&
                                $ocurrenciaFijaSub1['estado'] === 'reasignada'
                            ) {
                                $horarioFijoSub1 = null;
                            }

                            if (
                                $ocurrenciaFijaSub2 !== null &&
                                $ocurrenciaFijaSub2['estado'] === 'reasignada'
                            ) {
                                $horarioFijoSub2 = null;
                            }

                            ?>

                            <td class="agenda-celda">

                                <?php

                                $bloqueLibre =
                                    !$reservaCompleta &&
                                    !$reservaSub1 &&
                                    !$reservaSub2 &&
                                    !$horarioFijoCompleto &&
                                    !$horarioFijoSub1 &&
                                    !$horarioFijoSub2;

                                /*
                                echo '<pre>';
                                var_dump($reservaCompleta, $reservaSub1, $reservaSub2);
                                echo '</pre>';*/

                                ?>



                                <?php if ($bloqueLibre): ?>

                                    <?php if (
                                        horarioPuedeReservarse(
                                            $dia['fecha'],
                                            $bloque,
                                            'completo'
                                        )
                                    ): ?>

                                        <button
                                            type="button"
                                            class="agenda-libre agenda-libre-completo"
                                            data-url="agregar.php?fecha=<?= urlencode($dia['fecha']) ?>&bloque=<?= $bloque['id'] ?>&tipo=completo">

                                            +

                                        </button>

                                    <?php elseif (
                                        horarioPuedeReservarse(
                                            $dia['fecha'],
                                            $bloque,
                                            'sub2'
                                        )
                                    ): ?>

                                        <button
                                            type="button"
                                            class="agenda-libre"
                                            data-url="agregar.php?fecha=<?= urlencode($dia['fecha']) ?>&bloque=<?= $bloque['id'] ?>&tipo=sub2">

                                            +

                                        </button>

                                    <?php endif; ?>

                                <?php elseif ($horarioFijoCompleto): ?>

                                    <?= renderizarTarjetaHorarioFijo(
                                        $horarioFijoCompleto,
                                        $ocurrenciaFijaCompleta
                                    ); ?>

                                <?php elseif ($reservaCompleta): ?>

                                    <?= renderizarTarjetaReserva($reservaCompleta); ?>

                                <?php else: ?>

                                    <div class="agenda-subbloque">

                                        <!-- SUBBLOQUE 1 -->

                                        <?php if ($horarioFijoSub1): ?>

                                            <?= renderizarTarjetaHorarioFijo(
                                                $horarioFijoSub1,
                                                $ocurrenciaFijaSub1
                                            ); ?>

                                        <?php elseif ($reservaSub1): ?>

                                            <?= renderizarTarjetaReserva(
                                                $reservaSub1
                                            ); ?>

                                        <?php else: ?>

                                            <?php if (
                                                horarioPuedeReservarse(
                                                    $dia['fecha'],
                                                    $bloque,
                                                    'sub1'
                                                )
                                            ): ?>

                                                <button
                                                    type="button"
                                                    class="agenda-libre"
                                                    data-url="agregar.php?fecha=<?= urlencode($dia['fecha']) ?>&bloque=<?= $bloque['id'] ?>&tipo=sub1">

                                                    +

                                                </button>

                                            <?php endif; ?>

                                        <?php endif; ?>

                                    </div>

                                    <!-- SUBBLOQUE 2 -->

                                    <div class="agenda-subbloque">

                                        <?php if ($horarioFijoSub2): ?>

                                            <?= renderizarTarjetaHorarioFijo(
                                                $horarioFijoSub2,
                                                $ocurrenciaFijaSub2
                                            ); ?>

                                        <?php elseif ($reservaSub2): ?>

                                            <?= renderizarTarjetaReserva(
                                                $reservaSub2
                                            ); ?>

                                        <?php else: ?>

                                            <?php if (
                                                horarioPuedeReservarse(
                                                    $dia['fecha'],
                                                    $bloque,
                                                    'sub2'
                                                )
                                            ): ?>

                                                <button
                                                    type="button"
                                                    class="agenda-libre"
                                                    data-url="agregar.php?fecha=<?= urlencode($dia['fecha']) ?>&bloque=<?= $bloque['id'] ?>&tipo=sub2">

                                                    +

                                                </button>

                                            <?php endif; ?>

                                        <?php endif; ?>

                                    </div>

    </div>

<?php endif; ?>

</td>

<?php endforeach; ?>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<!-- =====================================================
     FORMULARIO - NUEVA RESERVA
======================================================

        <section class="agenda-form-reserva">

            <div class="agenda-form-header">

                <div>
                    <h2>Nueva reserva</h2>

                    <p>
                        Complete los datos para registrar la reserva.
                    </p>
                </div>

            </div> -->


<!-- =================================================
         RESUMEN DEL HORARIO SELECCIONADO
    ==================================================

            <div class="agenda-resumen-seleccion">

                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Fecha
                    </span>

                    <strong id="resumen_fecha">
                        Lunes 08 de junio de 2026
                    </strong>

                </div>


                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Bloque
                    </span>

                    <strong id="resumen_bloque">
                        Bloque 2
                    </strong>

                </div>


                <div class="agenda-resumen-item">

                    <span class="agenda-resumen-label">
                        Horario seleccionado
                    </span>

                    <strong id="resumen_horario">
                        10:20 - 11:05
                    </strong>

                </div>

            </div>




        </section> -->

</div>

<script src="../../assets/js/reservas.js"></script>

</body>

</html>
