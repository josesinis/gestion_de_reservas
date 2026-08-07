

//=====================================================
// ABRIR FORMULARIO DE RESERVA
//=====================================================

document.querySelectorAll('.agenda-libre').forEach(boton => {

    boton.addEventListener('click', function () {

        window.location.href = this.dataset.url;

    });

});
