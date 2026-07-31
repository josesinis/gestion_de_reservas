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


            <!-- =================================================
                 NAVEGACIÓN SEMANAL
            ================================================== -->

            <div class="agenda-nav">

                <button
                    type="button"
                    class="agenda-btn-nav"
                    title="Semana anterior">

                    <i class="fa-solid fa-chevron-left"></i>

                    <span>
                        Semana anterior
                    </span>

                </button>


                <div class="agenda-periodo">

                    <span class="agenda-semana">
                        Semana 24
                    </span>

                    <span class="agenda-fechas">
                        08 al 12 de junio de 2026
                    </span>

                </div>


                <button
                    type="button"
                    class="agenda-btn-hoy"
                    title="Ir a la semana actual">
                    Hoy
                </button>


                <button
                    type="button"
                    class="agenda-btn-nav"
                    title="Semana siguiente">

                    <span>
                        Semana siguiente
                    </span>

                    <i class="fa-solid fa-chevron-right"></i>

                </button>

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

                    <th class="agenda-columna-bloque">
                        Bloque
                    </th>

                    <th class="agenda-columna-horario">
                        Horario
                    </th>


                    <th>

                        <div class="agenda-dia">

                            <span class="agenda-dia-nombre">
                                Lunes
                            </span>

                            <span class="agenda-dia-fecha">
                                08/06
                            </span>

                        </div>

                    </th>


                    <th>

                        <div class="agenda-dia">

                            <span class="agenda-dia-nombre">
                                Martes
                            </span>

                            <span class="agenda-dia-fecha">
                                09/06
                            </span>

                        </div>

                    </th>


                    <th>

                        <div class="agenda-dia">

                            <span class="agenda-dia-nombre">
                                Miércoles
                            </span>

                            <span class="agenda-dia-fecha">
                                10/06
                            </span>

                        </div>

                    </th>


                    <th>

                        <div class="agenda-dia">

                            <span class="agenda-dia-nombre">
                                Jueves
                            </span>

                            <span class="agenda-dia-fecha">
                                11/06
                            </span>

                        </div>

                    </th>


                    <th>

                        <div class="agenda-dia">

                            <span class="agenda-dia-nombre">
                                Viernes
                            </span>

                            <span class="agenda-dia-fecha">
                                12/06
                            </span>

                        </div>

                    </th>

                </tr>

            </thead>


            <!-- =================================================
                 CUERPO DE LA AGENDA
            ================================================== -->

            <tbody>

                <!-- =============================================
                     BLOQUE 1
                ============================================== -->

                <tr class="agenda-subbloque agenda-subbloque-1">

                    <td
                        rowspan="2"
                        class="agenda-bloque-numero">
                        1
                    </td>

                    <td class="agenda-horario">
                        08:00 - 08:45
                    </td>

                    <td class="agenda-celda ocupada" rowspan="2">

                        <div class="agenda-reserva agenda-reserva-completa">

                            <strong class="agenda-reserva-asignatura">
                                Matemática
                            </strong>

                            <span class="agenda-reserva-detalle">
                                1° Medio A · Juan Pérez
                            </span>

                            <div class="agenda-reserva-acciones">

                                <button type="button" title="Ver reserva">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <button type="button" title="Editar reserva">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button type="button" title="Eliminar reserva">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </div>

                        </div>

                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                </tr>


                <tr class="agenda-subbloque agenda-subbloque-2 fin-bloque">

                    <td class="agenda-horario">
                        08:45 - 09:30
                    </td>

                    <!-- <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td> -->

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                </tr>


                <!-- =============================================
                     BLOQUE 2
                ============================================== -->

                <tr class="agenda-subbloque agenda-subbloque-1">

                    <td
                        rowspan="2"
                        class="agenda-bloque-numero">
                        2
                    </td>

                    <td class="agenda-horario">
                        09:45 - 10:30
                    </td>

                    <td class="agenda-celda ocupada">

                        <div class="agenda-reserva agenda-reserva-subbloque">

                            <strong class="agenda-reserva-asignatura">
                                Lenguaje
                            </strong>

                            <span class="agenda-reserva-detalle">
                                2° Medio B
                            </span>

                            <div class="agenda-reserva-acciones">

                                <button type="button" title="Ver reserva">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <button type="button" title="Editar reserva">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button type="button" title="Eliminar reserva">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </div>

                        </div>

                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                </tr>


                <tr class="agenda-subbloque agenda-subbloque-2 fin-bloque">

                    <td class="agenda-horario">
                        10:30 - 11:15
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                </tr>


                <!-- =============================================
                     BLOQUE 3
                ============================================== -->

                <tr class="agenda-subbloque agenda-subbloque-1">

                    <td
                        rowspan="2"
                        class="agenda-bloque-numero">
                        3
                    </td>

                    <td class="agenda-horario">
                        11:30 - 12:15
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                </tr>


                <tr class="agenda-subbloque agenda-subbloque-2 fin-bloque">

                    <td class="agenda-horario">
                        12:15 - 13:00
                    </td>

                    <td class="agenda-celda ocupada">

                        <div class="agenda-reserva agenda-reserva-subbloque">

                            <strong class="agenda-reserva-asignatura">
                                Historia
                            </strong>

                            <span class="agenda-reserva-detalle">
                                3° Medio A
                            </span>

                            <div class="agenda-reserva-acciones">

                                <button type="button" title="Ver reserva">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <button type="button" title="Editar reserva">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button type="button" title="Eliminar reserva">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </div>

                        </div>

                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                </tr>


                <!-- =============================================
                     BLOQUE 4
                ============================================== -->

                <tr class="agenda-subbloque agenda-subbloque-1">

                    <td
                        rowspan="2"
                        class="agenda-bloque-numero">
                        4
                    </td>

                    <td class="agenda-horario">
                        14:00 - 14:45
                    </td>

                    <td
                        class="agenda-celda libre"
                        data-fecha="2026-06-08"
                        data-bloque="4"
                        data-subbloque="sub1"
                        data-hora-inicio="14:15"
                        data-hora-fin="15:00"
                        data-hora-inicio-bloque="14:15"
                        data-hora-fin-bloque="15:45">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                </tr>


                <tr class="agenda-subbloque agenda-subbloque-2 fin-bloque">

                    <td class="agenda-horario">
                        14:45 - 15:30
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                    <td class="agenda-celda libre">
                        <i class="fa-solid fa-plus" title="Nueva reserva"></i>
                    </td>

                </tr>

            </tbody>

        </table>

        <!-- =====================================================
     FORMULARIO - NUEVA RESERVA
====================================================== -->

        <section class="agenda-form-reserva">

            <div class="agenda-form-header">

                <div>
                    <h2>Nueva reserva</h2>

                    <p>
                        Complete los datos para registrar la reserva.
                    </p>
                </div>

            </div>


            <!-- =================================================
         RESUMEN DEL HORARIO SELECCIONADO
    ================================================== -->

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


            <form class="agenda-form">

                <!-- =============================================
             TIPO DE RESERVA
        ============================================== -->

                <fieldset class="agenda-tipo-reserva" id="tipo_reserva_opciones">

                    <legend>Tipo de reserva</legend>

                    <label class="agenda-opcion-reserva">

                        <input
                            type="radio"
                            name="tipo_reserva"
                            value="sub1"
                            data-horario="10:20 - 11:05"
                            checked>

                        <span>
                            Primer subbloque
                        </span>

                        <small>
                            10:20 - 11:05
                        </small>

                    </label>


                    <label class="agenda-opcion-reserva">

                        <input
                            type="radio"
                            name="tipo_reserva"
                            value="completo"
                            data-horario="10:20 - 11:50">

                        <span>
                            Bloque completo
                        </span>

                        <small>
                            10:20 - 11:50
                        </small>

                    </label>

                </fieldset>


                <!-- =============================================
             DATOS DE LA RESERVA
        ============================================== -->

                <div class="agenda-form-grid">

                    <div class="grupo-formulario">

                        <label for="docente">
                            Docente
                        </label>

                        <select id="docente" name="docente">

                            <option value="">
                                Seleccionar docente
                            </option>

                            <option>
                                Juan Pérez
                            </option>

                            <option>
                                María González
                            </option>

                        </select>

                    </div>


                    <div class="grupo-formulario">

                        <label for="curso">
                            Curso
                        </label>

                        <select id="curso" name="curso">

                            <option value="">
                                Seleccionar curso
                            </option>

                            <option>
                                1° Medio A
                            </option>

                            <option>
                                2° Medio B
                            </option>

                            <option>
                                3° Medio A
                            </option>

                        </select>

                    </div>


                    <div class="grupo-formulario">

                        <label for="asignatura">
                            Asignatura
                        </label>

                        <select id="asignatura" name="asignatura">

                            <option value="">
                                Seleccionar asignatura
                            </option>

                            <option>
                                Matemática
                            </option>

                            <option>
                                Lenguaje
                            </option>

                            <option>
                                Historia
                            </option>

                        </select>

                    </div>

                </div>


                <!-- =============================================
             ACTIVIDAD
        ============================================== -->

                <div class="grupo-formulario agenda-form-actividad">

                    <label for="actividad">
                        Actividad
                    </label>

                    <textarea
                        id="actividad"
                        name="actividad"
                        rows="3"
                        placeholder="Describa brevemente la actividad a realizar"></textarea>

                </div>


                <!-- =============================================
             ENTREGA DE TRABAJOS
        ============================================== -->

                <!-- =============================================
     ENTREGA DE TRABAJOS
============================================== -->

                <div class="agenda-entrega">

                    <div class="grupo-checkbox">

                        <label>

                            <input
                                type="checkbox"
                                id="permite_entrega"
                                name="permite_entrega"
                                value="1">

                            Permitir entrega de trabajos

                        </label>

                    </div>


                    <div
                        class="agenda-entrega-opciones"
                        id="opciones_entrega">

                        <div class="grupo-formulario">

                            <label for="fecha_entrega_oficial">
                                Fecha oficial de entrega
                            </label>

                            <input
                                type="date"
                                id="fecha_entrega_oficial"
                                name="fecha_entrega_oficial"
                                min="2026-06-08">

                        </div>

                    </div>

                </div>


                <!-- =============================================
             ACCIONES
        ============================================== -->

                <div class="botones">

                    <button
                        type="button"
                        class="btn btn-secundario">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primario">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar reserva
                    </button>

                </div>

            </form>

        </section>

    </div>

    <script>
        const permiteEntrega = document.getElementById('permite_entrega');
        const opcionesEntrega = document.getElementById('opciones_entrega');
        const fechaEntregaOficial = document.getElementById('fecha_entrega_oficial');

        permiteEntrega.addEventListener('change', function() {

            if (this.checked) {

                opcionesEntrega.classList.add('activo');
                fechaEntregaOficial.required = true;

            } else {

                opcionesEntrega.classList.remove('activo');

                fechaEntregaOficial.required = false;
                fechaEntregaOficial.value = '';

            }

        });


        const celdasLibres = document.querySelectorAll('.agenda-celda.libre');

        const resumenFecha = document.getElementById('resumen_fecha');
        const resumenBloque = document.getElementById('resumen_bloque');
        const resumenHorario = document.getElementById('resumen_horario');

        const tipoReservaOpciones = document.getElementById(
            'tipo_reserva_opciones'
        );

        celdasLibres.forEach(celda => {

            celda.addEventListener('click', function() {

                const fecha = this.dataset.fecha;
                const bloque = this.dataset.bloque;
                const subbloque = this.dataset.subbloque;

                const horaInicio = this.dataset.horaInicio;
                const horaFin = this.dataset.horaFin;

                const horaInicioBloque = this.dataset.horaInicioBloque;
                const horaFinBloque = this.dataset.horaFinBloque;


                /*---------------------------------------------
          PRUEBA TEMPORAL
        ---------------------------------------------

                console.log('CELDA PULSADA');
                console.log('fecha:', fecha);
                console.log('bloque:', bloque);
                console.log('subbloque:', subbloque);
                console.log('horaInicio:', horaInicio);
                console.log('horaFin:', horaFin);
                console.log('horaInicioBloque:', horaInicioBloque);
                console.log('horaFinBloque:', horaFinBloque);*/


                /*---------------------------------------------
                  ACTUALIZAR RESUMEN
                ---------------------------------------------*/

                resumenFecha.textContent = fecha;

                resumenBloque.textContent = `Bloque ${bloque}`;

                resumenHorario.textContent =
                    `${horaInicio} - ${horaFin}`;

                /*console.log('Horario puesto en resumen:', resumenHorario.textContent);*/
                /*---------------------------------------------
                  TIPO DE RESERVA
                ---------------------------------------------*/

                let nombreSubbloque = '';

                if (subbloque === 'sub1') {
                    nombreSubbloque = 'Primer subbloque';
                }

                if (subbloque === 'sub2') {
                    nombreSubbloque = 'Segundo subbloque';
                }


                tipoReservaOpciones.innerHTML = `

            <legend>Tipo de reserva</legend>

            <label class="agenda-opcion-reserva">

                <input
                    type="radio"
                    name="tipo_reserva"
                    value="${subbloque}"
                    checked
                >

                <span>
                    ${nombreSubbloque}
                </span>

                <small>
                    ${horaInicio} - ${horaFin}
                </small>

            </label>


            <label class="agenda-opcion-reserva">

                <input
                    type="radio"
                    name="tipo_reserva"
                    value="completo"
                >

                <span>
                    Bloque completo
                </span>

                <small>
                    ${horaInicioBloque} - ${horaFinBloque}
                </small>

            </label>

        `;
                const radiosTipoReserva = tipoReservaOpciones.querySelectorAll(
                    'input[name="tipo_reserva"]'
                );

                radiosTipoReserva.forEach(radio => {

                    radio.addEventListener('change', function() {

                        if (this.value === 'completo') {

                            resumenHorario.textContent =
                                `${horaInicioBloque} - ${horaFinBloque}`;

                        } else {

                            resumenHorario.textContent =
                                `${horaInicio} - ${horaFin}`;

                        }

                    });

                });

            });

        });
    </script>

</body>

</html>
