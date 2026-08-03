
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

//=====================================================
// ABRIR FORMULARIO DE RESERVA
//=====================================================

document.querySelectorAll('.agenda-libre').forEach(boton => {

    boton.addEventListener('click', function () {

        window.location.href = this.dataset.url;

    });

});
