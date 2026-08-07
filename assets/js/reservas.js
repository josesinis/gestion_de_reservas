

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
