<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : permisos.php
|--------------------------------------------------------------------------
| Descripción :
| Funciones para validar permisos según el rol del usuario.
|--------------------------------------------------------------------------
*/

function requiereRol(array|string $roles): void
{
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    if (
        !isset($_SESSION['rol']) ||
        !in_array($_SESSION['rol'], $roles, true)
    ) {

        http_response_code(403);

        exit('Acceso denegado.');

    }
}
