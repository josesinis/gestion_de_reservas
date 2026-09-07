<?php

//=====================================================
// USUARIOS - LISTADO
//=====================================================
//
// Muestra las cuentas de acceso al sistema.
//
// Permisos:
// - admin: puede consultar el listado.
// - superadmin: puede crear, editar y cambiar acceso.
//
// Los usuarios no se eliminan físicamente.
// El acceso se controla mediante el campo "acceso".
//
//=====================================================


//=====================================================
// 1. VALIDAR SESIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();


//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../includes/permisos.php';
require_once '../../config/database.php';


//=====================================================
// 3. VALIDAR ROL
//=====================================================

requiereRol([
    'admin',
    'superadmin'
]);

$rolUsuario = $_SESSION['rol'] ?? 'usuario';

//=====================================================
// 3.1 MENSAJES
//=====================================================

$mensajeExito = $_SESSION['exito'] ?? '';
$mensajeError = $_SESSION['error'] ?? '';

unset($_SESSION['exito']);
unset($_SESSION['error']);

//=====================================================
// 4. OBTENER USUARIOS
//=====================================================

$usuarios = [];

$sql = "

    SELECT

        id,
        nombres,
        apellidos,
        correo,
        usuario,
        rol,
        acceso,
        ultimo_acceso

    FROM usuarios

    ORDER BY
        apellidos ASC,
        nombres ASC

";

$resultado = $conexion->query($sql);

if ($resultado) {

    $usuarios = $resultado->fetch_all(
        MYSQLI_ASSOC
    );
}


//=====================================================
// 5. FUNCIONES DE PRESENTACIÓN
//=====================================================

function textoRolUsuario(string $rol): string
{
    return match ($rol) {

        'superadmin' => 'Superadmin',

        'admin' => 'Administrador',

        'usuario' => 'Usuario',

        default => $rol
    };
}


function textoAccesoUsuario(int $acceso): string
{
    return $acceso === 1
        ? 'Activo'
        : 'Bloqueado';
}


function formatearUltimoAcceso(
    ?string $fecha
): string {

    if (
        $fecha === null
        || $fecha === ''
    ) {

        return 'Nunca';
    }

    $timestamp = strtotime($fecha);

    if ($timestamp === false) {

        return '—';
    }

    return date(
        'd/m/Y H:i',
        $timestamp
    );
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Usuarios</title>


    <!--=================================================
         FONT AWESOME
    ==================================================-->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    <!--=================================================
         CSS GENERALES
    ==================================================-->

    <link
        rel="stylesheet"
        href="../../assets/css/estilos.css">

    <link
        rel="stylesheet"
        href="../../assets/css/botones.css">

    <link
        rel="stylesheet"
        href="../../assets/css/formularios.css">

    <link
        rel="stylesheet"
        href="../../assets/css/tablas.css">

</head>


<body>


    <?php

    $seccionActual = 'administracion';

    require_once '../../includes/menu.php';

    ?>


    <div class="contenedor contenedor-usuarios">


        <!--=================================================
         ENCABEZADO
    ==================================================-->

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Usuarios
                </h1>

                <p>
                    Administración de las cuentas de acceso al sistema
                </p>

            </div>


            <?php if ($rolUsuario === 'superadmin'): ?>

                <div>

                    <a
                        href="crear.php"
                        class="btn btn-primary">

                        <i class="fa-solid fa-user-plus"></i>

                        Nuevo usuario

                    </a>

                </div>

            <?php endif; ?>

        </div>

        <!--=================================================
     MENSAJES
==================================================-->

        <?php if ($mensajeExito !== ''): ?>

            <div class="mensaje mensaje-exito">

                <?= htmlspecialchars($mensajeExito) ?>

            </div>

        <?php endif; ?>


        <?php if ($mensajeError !== ''): ?>

            <div class="mensaje mensaje-error">

                <?= htmlspecialchars($mensajeError) ?>

            </div>

        <?php endif; ?>

        <!--=================================================
         RESUMEN
    ==================================================-->

        <div class="resumen-usuarios">

            Registros encontrados:

            <strong>
                <?= count($usuarios) ?>
            </strong>

        </div>


        <!--=================================================
         TABLA
    ==================================================-->

        <div class="tabla-contenedor">

            <table class="tabla">

                <thead>

                    <tr>

                        <th>
                            Nombre
                        </th>

                        <th>
                            Correo
                        </th>

                        <th>
                            Usuario
                        </th>

                        <th>
                            Rol
                        </th>

                        <th>
                            Acceso
                        </th>

                        <th>
                            Último acceso
                        </th>

                        <?php if ($rolUsuario === 'superadmin'): ?>

                            <th>
                                Acciones
                            </th>

                        <?php endif; ?>

                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($usuarios)): ?>

                        <tr>

                            <td
                                colspan="<?= $rolUsuario === 'superadmin' ? 7 : 6 ?>"
                                style="text-align: center;">

                                No existen usuarios registrados.

                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach ($usuarios as $usuario): ?>

                            <tr>


                                <!--=================================
                                 NOMBRE
                            ==================================-->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            trim(
                                                $usuario['nombres']
                                                    . ' '
                                                    . $usuario['apellidos']
                                            )
                                        ) ?>

                                    </strong>

                                </td>


                                <!--=================================
                                 CORREO
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        $usuario['correo']
                                    ) ?>

                                </td>


                                <!--=================================
                                 USUARIO
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        $usuario['usuario']
                                    ) ?>

                                </td>


                                <!--=================================
                                 ROL
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        textoRolUsuario(
                                            $usuario['rol']
                                        )
                                    ) ?>

                                </td>


                                <!--=================================
                                 ACCESO
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        textoAccesoUsuario(
                                            (int) $usuario['acceso']
                                        )
                                    ) ?>

                                </td>


                                <!--=================================
                                 ÚLTIMO ACCESO
                            ==================================-->

                                <td>

                                    <?= htmlspecialchars(
                                        formatearUltimoAcceso(
                                            $usuario['ultimo_acceso']
                                        )
                                    ) ?>

                                </td>


                                <!--=================================
                                 ACCIONES
                            ==================================-->

                                <?php if ($rolUsuario === 'superadmin'): ?>

                                    <td>


                                        <!-- EDITAR -->

                                        <a
                                            href="editar.php?id=<?= (int) $usuario['id'] ?>"
                                            class="btn btn-secondary">

                                            <i class="fa-solid fa-pen-to-square"></i>

                                            Editar

                                        </a>


                                        <!-- CAMBIAR ACCESO -->

                                        <?php if (
                                            (int) $usuario['acceso'] === 1
                                        ): ?>

                                            <a
                                                href="cambiar_acceso.php?id=<?= (int) $usuario['id'] ?>&accion=bloquear"
                                                class="btn btn-advertencia">

                                                <i class="fa-solid fa-user-lock"></i>

                                                Bloquear

                                            </a>

                                        <?php else: ?>

                                            <a
                                                href="cambiar_acceso.php?id=<?= (int) $usuario['id'] ?>&accion=activar"
                                                class="btn btn-exito">

                                                <i class="fa-solid fa-user-check"></i>

                                                Activar

                                            </a>

                                        <?php endif; ?>


                                    </td>

                                <?php endif; ?>


                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>


                </tbody>

            </table>

        </div>


    </div>


</body>

</html>
