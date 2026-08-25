

//=====================================================
// ABRIR FORMULARIO DE RESERVA
//=====================================================

/*document.querySelectorAll('.agenda-libre').forEach(boton => {

    boton.addEventListener('click', function () {

        window.location.href = this.dataset.url;

    });

});*/

/*console.log("reservas.js cargado");*/

document.querySelectorAll('.agenda-libre').forEach(boton => {

    boton.addEventListener('click', function () {

       /* console.log("CLICK");
        console.log(this.dataset.url);*/

        window.location.href = this.dataset.url;

    });

});


//=====================================================
// FILTRAR ASIGNATURAS SEGÚN DOCENTE
//=====================================================

const docenteSelect = document.getElementById('docente_id');
const asignaturaSelect = document.getElementById('asignatura_id');

if (docenteSelect && asignaturaSelect) {

    docenteSelect.addEventListener('change', function () {

        const docenteId = this.value;

        asignaturaSelect.innerHTML = '';

        if (!docenteId) {

            const opcion = document.createElement('option');

            opcion.value = '';
            opcion.textContent = 'Seleccionar docente primero';

            asignaturaSelect.appendChild(opcion);

            asignaturaSelect.disabled = true;

            return;
        }

        asignaturaSelect.disabled = true;

        const cargando = document.createElement('option');

        cargando.value = '';
        cargando.textContent = 'Cargando asignaturas...';

        asignaturaSelect.appendChild(cargando);

        fetch(
            'asignaturas_docente.php?docente_id='
            + encodeURIComponent(docenteId)
        )
            .then(response => {

                if (!response.ok) {
                    throw new Error(
                        'Error al consultar las asignaturas.'
                    );
                }

                return response.json();
            })

            .then(asignaturas => {

                asignaturaSelect.innerHTML = '';

                const inicial = document.createElement('option');

                inicial.value = '';
                inicial.textContent =
                    'Seleccionar asignatura';

                asignaturaSelect.appendChild(inicial);

                if (asignaturas.length === 0) {

                    const sinAsignaturas =
                        document.createElement('option');

                    sinAsignaturas.value = '';

                    sinAsignaturas.textContent =
                        'El docente no tiene asignaturas asignadas';

                    asignaturaSelect.appendChild(
                        sinAsignaturas
                    );

                    return;
                }

                asignaturas.forEach(asignatura => {

                    const opcion =
                        document.createElement('option');

                    opcion.value = asignatura.id;

                    opcion.textContent =
                        asignatura.asignatura_nombre;

                    asignaturaSelect.appendChild(opcion);
                });

                asignaturaSelect.disabled = false;
            })

            .catch(error => {

                console.error(error);

                asignaturaSelect.innerHTML = '';

                const opcion =
                    document.createElement('option');

                opcion.value = '';

                opcion.textContent =
                    'No fue posible cargar las asignaturas';

                asignaturaSelect.appendChild(opcion);
            });
    });
}
