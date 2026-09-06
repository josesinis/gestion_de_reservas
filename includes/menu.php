<?php

//=====================================================
// MENÚ PRINCIPAL
//=====================================================
//
// Navegación general del Sistema de Gestión Institucional.
//
// El menú utiliza la sesión actual para determinar:
// - usuario
// - admin
// - superadmin
//
// Además, identifica automáticamente la sección actual
// para destacarla visualmente.
//
//=====================================================


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


//=====================================================
// DATOS DE SESIÓN
//=====================================================

$rolUsuario = $_SESSION['rol'] ?? 'usuario';

$nombreUsuario =
    $_SESSION['usuario'] ??
    'Usuario';


//=====================================================
// SECCIÓN ACTUAL
//=====================================================

$seccionActual = $seccionActual ?? '';


//=====================================================
// FUNCIÓN PARA CLASE ACTIVA
//=====================================================

function claseMenuActivo(
    string $seccion,
    string $seccionActual
): string {

    return $seccion === $seccionActual
        ? ' menu-enlace-activo'
        : '';
}

?>

<nav class="menu-principal">

    <div class="menu-contenedor">


        <!--=================================================
            IDENTIDAD DEL SISTEMA
        ==================================================-->

        <div class="menu-identidad">

            <a
                href="/gestion_de_reservas/index.php"
                class="menu-logo">

                <span class="menu-logo-icono">
                    <i class="fa-solid fa-building-columns"></i>
                </span>

                <span class="menu-logo-texto">
                    Gestión Institucional
                </span>

            </a>

        </div>


        <!--=================================================
            OPCIONES PRINCIPALES
        ==================================================-->

        <div class="menu-opciones">


            <!--=================================================
                INICIO
            ==================================================-->

            <a
                href="/gestion_de_reservas/index.php"
                class="menu-enlace<?= claseMenuActivo(
                                        'inicio',
                                        $seccionActual
                                    ) ?>">

                <i class="fa-solid fa-house"></i>

                <span>
                    Inicio
                </span>

            </a>


            <!--=================================================
                RESERVAS
            ==================================================-->

            <a
                href="/gestion_de_reservas/modules/reservas/agenda.php"
                class="menu-enlace<?= claseMenuActivo(
                                        'reservas',
                                        $seccionActual
                                    ) ?>">

                <i class="fa-solid fa-calendar-days"></i>

                <span>
                    Reservas
                </span>

            </a>


            <!--=================================================
                BITÁCORA
            ==================================================-->

            <a
                href="/gestion_de_reservas/modules/bitacoras/index.php"
                class="menu-enlace<?= claseMenuActivo(
                                        'bitacora',
                                        $seccionActual
                                    ) ?>">

                <i class="fa-solid fa-clipboard-list"></i>

                <span>
                    Bitácora
                </span>

            </a>


            <!--=================================================
                BIENES
            ==================================================-->

            <a
                href="/gestion_de_reservas/modules/bienes/bienes_lista.php"
                class="menu-enlace<?= claseMenuActivo(
                                        'bienes',
                                        $seccionActual
                                    ) ?>">

                <i class="fa-solid fa-desktop"></i>

                <span>
                    Bienes
                </span>

            </a>


            <!--=================================================
                ADMINISTRACIÓN
            ==================================================-->

            <?php if (
                $rolUsuario === 'admin'
                ||
                $rolUsuario === 'superadmin'
            ): ?>

                <a
                    href="/gestion_de_reservas/modules/usuarios/index.php"
                    class="menu-enlace<?= claseMenuActivo(
                                            'administracion',
                                            $seccionActual
                                        ) ?>">

                    <i class="fa-solid fa-users-gear"></i>

                    <span>
                        Administración
                    </span>

                </a>

            <?php endif; ?>


        </div>


        <!--=================================================
            INFORMACIÓN DEL USUARIO
        ==================================================-->

        <div class="menu-usuario">


            <span class="menu-usuario-nombre">

                <i class="fa-solid fa-user"></i>

                <?= htmlspecialchars(
                    $nombreUsuario
                ) ?>

            </span>


            <a
                href="/gestion_de_reservas/logout.php"
                class="menu-salir"
                title="Cerrar sesión">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>
                    Salir
                </span>

            </a>


        </div>


    </div>

</nav>
