<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agenda - Mockup</title>

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/estilos.css">
    <link rel="stylesheet" href="../../assets/css/botones.css">
    <link rel="stylesheet" href="../../assets/css/formularios.css">
    <link rel="stylesheet" href="../../assets/css/tablas.css">
    <link rel="stylesheet" href="../../assets/css/reservas.css">
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

                    <h1>Gestión de Reservas</h1>

                    <p class="agenda-subtitulo">
                        Sala de Computación
                    </p>

                </div>

            </div>

            <div class="agenda-nav">

                <button class="agenda-btn-nav">
                    <i class="fa-solid fa-chevron-left"></i>
                    Semana anterior
                </button>

                <div class="agenda-periodo">

                    <span class="agenda-semana">
                        Semana 24
                    </span>

                    <span class="agenda-fechas">
                        08 al 12 de junio de 2026
                    </span>

                </div>

                <button class="agenda-btn-hoy">
                    Hoy
                </button>

                <button class="agenda-btn-nav">
                    Semana siguiente
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

            </div>

        </header>

        <!-- =====================================================
             AGENDA SEMANAL
        ====================================================== -->

        <table class="agenda">

            <thead>

                <tr>

                    <th>

                        <div class="agenda-dia">

                            <span class="agenda-dia-nombre">
                                Bloque
                            </span>

                            <span class="agenda-dia-fecha">
                                Hora
                            </span>

                        </div>

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

            <tbody>

                <tr>

                    <td class="hora">

                        <div class="agenda-bloque">

                            <strong>Bloque 1</strong>

                            <span>08:00</span>

                            <span>08:45</span>

                        </div>

                    </td>

                    <td>

                        <div class="reserva">

                            <strong>Matemática</strong>

                            <span>Juan Pérez</span>

                            <span>1° Medio A</span>

                            <div class="acciones">

                                <i class="fa-solid fa-eye" title="Ver"></i>

                                <i class="fa-solid fa-pen" title="Editar"></i>

                                <i class="fa-solid fa-trash" title="Eliminar"></i>

                            </div>

                        </div>

                    </td>

                    <td class="libre">

                        <i class="fa-solid fa-plus"></i>

                    </td>

                    <td></td>

                    <td></td>

                    <td></td>

                </tr>

                <tr>

                    <td class="hora">

                        <div class="agenda-bloque">

                            <strong>Bloque 2</strong>

                            <span>08:45</span>

                            <span>09:30</span>

                        </div>

                    </td>

                    <td class="libre">

                        <i class="fa-solid fa-plus"></i>

                    </td>

                    <td></td>

                    <td></td>

                    <td></td>

                    <td></td>

                </tr>

                <tr>

                    <td class="hora">

                        <div class="agenda-bloque">

                            <strong>Bloque 3</strong>

                            <span>09:45</span>

                            <span>10:30</span>

                        </div>

                    </td>

                    <td></td>

                    <td></td>

                    <td></td>

                    <td></td>

                    <td></td>

                </tr>

                <tr>

                    <td class="hora">

                        <div class="agenda-bloque">

                            <strong>Bloque 4</strong>

                            <span>10:30</span>

                            <span>11:15</span>

                        </div>

                    </td>

                    <td></td>

                    <td></td>

                    <td></td>

                    <td></td>

                    <td></td>

                </tr>

            </tbody>

        </table>

    </div>

</body>

</html>
