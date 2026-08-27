//=====================================================
// HORARIOS FIJOS
//
// Controla la carga de:
// - Cursos según modalidad
// - Asignaturas según docente y modalidad
//=====================================================

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const docenteSelect =
            document.getElementById('docente_id');

        const modalidadSelect =
            document.getElementById('modalidad');

        const cursoSelect =
            document.getElementById('curso_id');

        const asignaturaSelect =
            document.getElementById('asignatura_id');


        //=================================================
        // VALIDAR ELEMENTOS
        //=================================================

        if (
            !docenteSelect ||
            !modalidadSelect ||
            !cursoSelect ||
            !asignaturaSelect
        ) {
            return;
        }


        //=================================================
        // GUARDAR CURSOS ORIGINALES
        //=================================================

        const opcionesCursos =
            Array.from(cursoSelect.options).map(
                opcion => opcion.cloneNode(true)
            );


        //=================================================
        // FILTRAR CURSOS SEGÚN MODALIDAD
        //=================================================

        function filtrarCursos() {

            const modalidad =
                modalidadSelect.value;


            //=============================================
            // SIN MODALIDAD
            //=============================================

            if (!modalidad) {

                cursoSelect.innerHTML = '';

                const opcion =
                    document.createElement('option');

                opcion.value = '';

                opcion.textContent =
                    'Seleccione una modalidad';

                cursoSelect.appendChild(
                    opcion
                );

                cursoSelect.disabled = true;

                return;
            }


            //=============================================
            // LIMPIAR CURSOS
            //=============================================

            cursoSelect.innerHTML = '';


            //=============================================
            // AGREGAR CURSOS CORRESPONDIENTES
            //=============================================

            opcionesCursos.forEach(
                opcion => {

                    // Mantener la opción inicial.

                    if (opcion.value === '') {

                        cursoSelect.appendChild(
                            opcion.cloneNode(true)
                        );

                        return;
                    }


                    // Agregar solamente los cursos
                    // de la modalidad seleccionada.

                    if (
                        opcion.dataset.modalidad === modalidad
                    ) {

                        cursoSelect.appendChild(
                            opcion.cloneNode(true)
                        );

                    }

                }
            );


            //=============================================
            // HABILITAR CURSO
            //=============================================

            cursoSelect.disabled = false;
        }


        //=================================================
        // CARGAR ASIGNATURAS
        //=================================================

        function cargarAsignaturas() {

            const docenteId =
                docenteSelect.value;

            const modalidad =
                modalidadSelect.value;


            //=============================================
            // VALIDAR DOCENTE Y MODALIDAD
            //=============================================

            if (!docenteId || !modalidad) {

                asignaturaSelect.innerHTML = '';

                const opcion =
                    document.createElement('option');

                opcion.value = '';

                opcion.textContent =
                    'Seleccione docente y modalidad';

                asignaturaSelect.appendChild(
                    opcion
                );

                asignaturaSelect.disabled = true;

                return;
            }


            //=============================================
            // MOSTRAR CARGANDO
            //=============================================

            asignaturaSelect.innerHTML = '';

            const cargando =
                document.createElement('option');

            cargando.value = '';

            cargando.textContent =
                'Cargando...';

            asignaturaSelect.appendChild(
                cargando
            );

            asignaturaSelect.disabled = true;


            //=============================================
            // CONSULTAR
            //=============================================

            fetch(
                '../reservas/asignaturas_docente.php?docente_id='
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


                //=========================================
                // PROCESAR RESULTADO
                //=========================================

                .then(asignaturas => {

                    asignaturaSelect.innerHTML = '';


                    const inicial =
                        document.createElement('option');

                    inicial.value = '';

                    inicial.textContent =
                        modalidad === 'taller'
                            ? 'Seleccione un taller'
                            : 'Seleccione una asignatura';

                    asignaturaSelect.appendChild(
                        inicial
                    );


                    //=====================================
                    // SIN RESULTADOS
                    //=====================================

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


                    //=====================================
                    // AGREGAR ASIGNATURAS
                    //=====================================

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


                    //=====================================
                    // HABILITAR ASIGNATURA
                    //=====================================

                    asignaturaSelect.disabled = false;

                })


                //=========================================
                // ERROR
                //=========================================

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


        //=================================================
        // CAMBIO DE DOCENTE
        //=================================================

        docenteSelect.addEventListener(
            'change',
            function () {

                cargarAsignaturas();

            }
        );


        //=================================================
        // CAMBIO DE MODALIDAD
        //=================================================

        modalidadSelect.addEventListener(
            'change',
            function () {

                // Primero filtramos los cursos.

                filtrarCursos();


                // Después cargamos las asignaturas.

                cargarAsignaturas();

            }
        );


        //=================================================
        // ESTADO INICIAL
        //=================================================

        filtrarCursos();

    }
);
