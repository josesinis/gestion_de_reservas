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
