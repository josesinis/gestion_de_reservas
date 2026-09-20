//=====================================================
// ABRIR FORMULARIO DE RESERVA
//=====================================================

document.querySelectorAll('.agenda-libre').forEach(boton => {

    boton.addEventListener('click', function () {

        window.location.href = this.dataset.url;

    });

});


//=====================================================
// FILTRAR ASIGNATURAS SEGÚN DOCENTE Y MODALIDAD
//=====================================================

const docenteSelect =
    document.getElementById('docente_id');

const asignaturaSelect =
    document.getElementById('asignatura_id');

const modalidadSelect =
    document.getElementById('modalidad');


if (docenteSelect && asignaturaSelect) {

    docenteSelect.addEventListener(
        'change',
        cargarAsignaturas
    );

}


//=====================================================
// CARGAR ASIGNATURAS
//=====================================================

function cargarAsignaturas() {

    const docenteId =
        docenteSelect.value;

    const modalidad =
        modalidadSelect
            ? modalidadSelect.value
            : 'asignatura';


    //=================================================
    // LIMPIAR ASIGNATURAS
    //=================================================

    asignaturaSelect.innerHTML = '';


    //=================================================
    // VALIDAR DOCENTE
    //=================================================

    if (!docenteId) {

        const opcion =
            document.createElement('option');

        opcion.value = '';

        opcion.textContent =
            'Seleccionar docente primero';

        asignaturaSelect.appendChild(
            opcion
        );

        asignaturaSelect.disabled = true;

        return;
    }


    //=================================================
    // VALIDAR MODALIDAD
    //=================================================

    if (
        modalidad !== 'asignatura' &&
        modalidad !== 'taller'
    ) {

        const opcion =
            document.createElement('option');

        opcion.value = '';

        opcion.textContent =
            'Seleccionar modalidad primero';

        asignaturaSelect.appendChild(
            opcion
        );

        asignaturaSelect.disabled = true;

        return;
    }


    //=================================================
    // MOSTRAR CARGANDO
    //=================================================

    asignaturaSelect.disabled = true;

    const cargando =
        document.createElement('option');

    cargando.value = '';

    cargando.textContent =
        'Cargando...';

    asignaturaSelect.appendChild(
        cargando
    );


    //=================================================
    // CONSULTAR SERVIDOR
    //=================================================

    fetch(
        'asignaturas_docente.php?docente_id='
        + encodeURIComponent(docenteId)
        + '&modalidad='
        + encodeURIComponent(modalidad)
    )

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Error al consultar las asignaturas.'
                );

            }

            return response.json();

        })


        //=================================================
        // PROCESAR RESULTADO
        //=================================================

        .then(asignaturas => {

            asignaturaSelect.innerHTML = '';


            const inicial =
                document.createElement('option');

            inicial.value = '';

            inicial.textContent =
                modalidad === 'taller'
                    ? 'Seleccionar taller'
                    : 'Seleccionar asignatura';

            asignaturaSelect.appendChild(
                inicial
            );


            //=================================================
            // SIN RESULTADOS
            //=================================================

            if (asignaturas.length === 0) {

                const sinResultados =
                    document.createElement('option');

                sinResultados.value = '';

                sinResultados.textContent =
                    modalidad === 'taller'
                        ? 'El docente no tiene talleres asignados'
                        : 'El docente no tiene asignaturas asignadas';

                asignaturaSelect.appendChild(
                    sinResultados
                );

                asignaturaSelect.disabled = true;

                return;
            }


            //=================================================
            // CARGAR RESULTADOS
            //=================================================

            asignaturas.forEach(
                asignatura => {

                    const opcion =
                        document.createElement('option');

                    opcion.value =
                        asignatura.id;

                    opcion.textContent =
                        asignatura.asignatura_nombre;

                    asignaturaSelect.appendChild(
                        opcion
                    );

                }
            );


            asignaturaSelect.disabled = false;

        })


        //=================================================
        // ERROR
        //=================================================

        .catch(error => {

            console.error(error);

            asignaturaSelect.innerHTML = '';

            const opcion =
                document.createElement('option');

            opcion.value = '';

            opcion.textContent =
                'No fue posible cargar las opciones';

            asignaturaSelect.appendChild(
                opcion
            );

            asignaturaSelect.disabled = true;

        });

}


//=====================================================
// MOSTRAR U OCULTAR OPCIONES DE ENTREGA
//=====================================================

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const permiteEntrega =
            document.getElementById(
                'permite_entrega'
            );


        const opcionesEntrega =
            document.getElementById(
                'opciones_entrega'
            );


        const fechaEntregaOficial =
            document.getElementById(
                'fecha_entrega_oficial'
            );


        const tituloTrabajo =
            document.getElementById(
                'titulo_trabajo'
            );


        if (
            !permiteEntrega ||
            !opcionesEntrega
        ) {

            return;

        }


        function actualizarOpcionesEntrega() {

            if (permiteEntrega.checked) {

                opcionesEntrega.style.display =
                    'block';


                if (fechaEntregaOficial) {

                    fechaEntregaOficial.required =
                        true;

                }


                if (tituloTrabajo) {

                    tituloTrabajo.required =
                        true;

                }

            } else {

                opcionesEntrega.style.display =
                    'none';


                if (fechaEntregaOficial) {

                    fechaEntregaOficial.required =
                        false;

                    fechaEntregaOficial.value =
                        '';

                }


                if (tituloTrabajo) {

                    tituloTrabajo.required =
                        false;

                    tituloTrabajo.value =
                        '';

                }

            }

        }


        permiteEntrega.addEventListener(
            'change',
            actualizarOpcionesEntrega
        );


        //=================================================
        // ESTADO INICIAL
        //=================================================

        actualizarOpcionesEntrega();


        //=====================================================
        // TIPO DE ACTIVIDAD
        //=====================================================

        const tipoActividad =
            document.getElementById(
                'tipo_actividad'
            );


        const grupoTrabajoContinuacion =
            document.getElementById(
                'grupo_trabajo_continuacion'
            );


        const trabajoId =
            document.getElementById(
                'trabajo_id'
            );


        if (
            tipoActividad &&
            grupoTrabajoContinuacion &&
            trabajoId
        ) {


            function actualizarTipoActividad() {

                if (
                    tipoActividad.value ===
                    'continuacion'
                ) {


                    //=========================================
                    // MOSTRAR TRABAJO A CONTINUAR
                    //=========================================

                    grupoTrabajoContinuacion.style.display =
                        'block';

                    trabajoId.required =
                        true;


                    //=========================================
                    // DESHABILITAR ENTREGA DE TRABAJOS
                    //=========================================

                    permiteEntrega.checked =
                        false;

                    permiteEntrega.disabled =
                        true;


                    if (opcionesEntrega) {

                        opcionesEntrega.style.display =
                            'none';

                    }


                    if (fechaEntregaOficial) {

                        fechaEntregaOficial.required =
                            false;

                        fechaEntregaOficial.value =
                            '';

                    }


                    if (tituloTrabajo) {

                        tituloTrabajo.required =
                            false;

                        tituloTrabajo.value =
                            '';

                    }


                } else {


                    //=========================================
                    // OCULTAR TRABAJO A CONTINUAR
                    //=========================================

                    grupoTrabajoContinuacion.style.display =
                        'none';

                    trabajoId.required =
                        false;

                    trabajoId.value =
                        '';


                    //=========================================
                    // HABILITAR ENTREGA DE TRABAJOS
                    //=========================================

                    permiteEntrega.disabled =
                        false;


                    // Volver a aplicar el estado
                    // actual del checkbox.

                    actualizarOpcionesEntrega();

                }

            }


            tipoActividad.addEventListener(
                'change',
                actualizarTipoActividad
            );


            //=================================================
            // ESTADO INICIAL
            //=================================================

            actualizarTipoActividad();

        }


        //=====================================================
        // CONTINUACIÓN DE TRABAJO
        //=====================================================

        const trabajoSelect =
            document.getElementById(
                'trabajo_id'
            );


        const cursoSelect =
            document.getElementById(
                'curso_id'
            );


        if (
            trabajoSelect &&
            docenteSelect &&
            cursoSelect &&
            asignaturaSelect
        ) {


            trabajoSelect.addEventListener(
                'change',
                function () {


                    const opcion =
                        trabajoSelect.options[
                            trabajoSelect.selectedIndex
                        ];


                    if (
                        !opcion ||
                        !opcion.value
                    ) {

                        return;

                    }


                    const docenteId =
                        opcion.dataset.docenteId;


                    const cursoId =
                        opcion.dataset.cursoId;


                    const asignaturaId =
                        opcion.dataset.asignaturaId;


                    //=========================================
                    // SELECCIONAR DOCENTE
                    //=========================================

                    docenteSelect.value =
                        docenteId;


                    //=========================================
                    // SELECCIONAR CURSO
                    //=========================================

                    cursoSelect.value =
                        cursoId;


                    //=========================================
                    // CARGAR ASIGNATURAS DEL DOCENTE
                    //=========================================

                    cargarAsignaturas();


                    //=========================================
                    // ESPERAR A QUE SE CARGUE LA ASIGNATURA
                    //=========================================

                    let intentos = 0;


                    const esperarAsignatura =
                        setInterval(
                            function () {


                                const opcionAsignatura =
                                    asignaturaSelect.querySelector(
                                        'option[value="' +
                                        asignaturaId +
                                        '"]'
                                    );


                                if (
                                    opcionAsignatura
                                ) {

                                    asignaturaSelect.value =
                                        asignaturaId;

                                    clearInterval(
                                        esperarAsignatura
                                    );

                                }


                                intentos++;


                                if (
                                    intentos >= 50
                                ) {

                                    clearInterval(
                                        esperarAsignatura
                                    );

                                }


                            },
                            100
                        );

                }
            );

        }

    }
);
