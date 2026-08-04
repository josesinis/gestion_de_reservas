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

/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE SESIÓN
|--------------------------------------------------------------------------
|
| Cuando el login esté implementado, se descomentará este bloque.
|
*/

/*
if (empty($_SESSION['usuario_id'])) {

    header('Location: /gestion_de_reservas/login.php');
    exit();

}
*/
