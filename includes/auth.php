<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : auth.php
|--------------------------------------------------------------------------
| Descripción :
| Control centralizado de autenticación.
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


//=====================================================
// VALIDAR SESIÓN
//=====================================================

function requiereLogin(): void
{
    if (empty($_SESSION['usuario_id'])) {

        $_SESSION['error'] =
            'Debe iniciar sesión para acceder al sistema.';

        header('Location: /gestion_de_reservas/login.php');
        exit();
    }
}
