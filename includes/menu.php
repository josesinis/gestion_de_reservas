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


//=====================================================
// DETERMINAR SUBSECCIÓN DE ADMINISTRACIÓN
//=====================================================

$subseccionAdministracion =
    $subseccionAdministracion ?? '';

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
                href="/gestion_de_reservas/modules/bitacora/index.php"
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
                ADMINISTRACIÓN
            ==================================================-->

            <?php if (
                $rolUsuario === 'admin'
                ||
                $rolUsuario === 'superadmin'
            ): ?>

                <div class="menu-administracion">


                    <!--=================================================
                        BOTÓN ADMINISTRACIÓN
                    ==================================================-->

                    <a
                        href="#"
                        class="menu-enlace menu-enlace-administracion<?= claseMenuActivo(
                                                                            'administracion',
                                                                            $seccionActual
                                                                        ) ?>">

                        <i class="fa-solid fa-users-gear"></i>

                        <span>
                            Administración
                        </span>

                        <i class="fa-solid fa-chevron-down menu-flecha"></i>

                    </a>


                    <!--=================================================
                        SUBMENÚ
                    ==================================================-->

                    <div class="menu-submenu">


                        <a
                            href="/gestion_de_reservas/modules/usuarios/index.php"
                            class="menu-submenu-enlace<?= $subseccionAdministracion === 'usuarios'
                                                            ? ' menu-submenu-activo'
                                                            : '' ?>">

                            <i class="fa-solid fa-users"></i>

                            <span>
                                Usuarios
                            </span>

                        </a>


                        <a
                            href="/gestion_de_reservas/modules/docentes/index.php"
                            class="menu-submenu-enlace<?= $subseccionAdministracion === 'docentes'
                                                            ? ' menu-submenu-activo'
                                                            : '' ?>">

                            <i class="fa-solid fa-chalkboard-user"></i>

                            <span>
                                Docentes
                            </span>

                        </a>


                    </div>


                </div>

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
