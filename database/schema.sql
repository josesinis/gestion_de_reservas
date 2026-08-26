-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-08-2026 a las 15:55:49
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_gestion_de_reservas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `id` int(11) NOT NULL,
  `asignatura_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `asignaturas`
--

INSERT INTO `asignaturas` (`id`, `asignatura_nombre`) VALUES
(1, 'Matemáticas'),
(2, 'Lenguaje'),
(3, 'Inglés'),
(4, 'Música'),
(5, 'Ciencias'),
(6, 'Historia'),
(7, 'Taller de IA'),
(8, 'Tecnología');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacoras`
--

CREATE TABLE `bitacoras` (
  `id` int(11) NOT NULL,
  `reserva_id` int(11) DEFAULT NULL,
  `horario_fijo_ocurrencia_id` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `bitacoras`
--

INSERT INTO `bitacoras` (`id`, `reserva_id`, `horario_fijo_ocurrencia_id`, `observaciones`) VALUES
(1, 17, NULL, 'qwwerrerrt'),
(2, 20, NULL, ''),
(3, 35, 11, 'Reasignación de horario fijo. El horario original fue reasignado a la nueva reserva.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_recursos`
--

CREATE TABLE `bitacora_recursos` (
  `id` int(11) NOT NULL,
  `bitacora_id` int(11) NOT NULL,
  `recurso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloques`
--

CREATE TABLE `bloques` (
  `id` int(11) NOT NULL,
  `numero_bloque` int(11) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_termino` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `bloques`
--

INSERT INTO `bloques` (`id`, `numero_bloque`, `hora_inicio`, `hora_termino`) VALUES
(1, 1, '08:30:00', '10:00:00'),
(2, 2, '10:20:00', '11:50:00'),
(3, 3, '12:00:00', '13:30:00'),
(4, 4, '14:15:00', '15:45:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nombre_curso` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nombre_curso`) VALUES
(1, '1° A'),
(2, '1° B'),
(3, '2° A'),
(4, '2° B'),
(5, '3° A'),
(6, '3° B'),
(7, '4° A'),
(8, '4° B'),
(9, '5° A'),
(10, '5° B'),
(11, '6° A'),
(12, '6° B'),
(13, '7° A'),
(14, '7° B'),
(15, '8° A'),
(16, '8° B'),
(17, 'Taller');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `id` int(11) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`id`, `nombres`, `apellidos`, `correo`) VALUES
(1, 'Esmeralda Jacqueline', 'Cabrera Saavedra', 'kellycabrera1@gmail.com'),
(2, 'Evelyn Del Rosario', 'Cortés Saavedra', 'elastro6@hotmail.com'),
(3, 'Eduardo Cecilio', 'Muñoz Apablaza', 'emunozapablaza@gmail.com'),
(4, 'Hector Igor', 'Castillo Ulloa', 'h.castillo@edutome.cl'),
(5, 'Katherine Celestina', 'Fuentes Diaz', 'katherinecfuentes@gmail.com'),
(6, 'Matias', 'Lantaño Mardones', 'mlantano@ematematica.ucsc.cl'),
(7, 'Gastón', 'Flores Vargas', 'gaston.flores.vargas@edutome.cl'),
(8, 'María Sandra', 'Aguayo Aravena', 'intirrayen@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes_asignaturas`
--

CREATE TABLE `docentes_asignaturas` (
  `id` int(11) NOT NULL,
  `docente_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docentes_asignaturas`
--

INSERT INTO `docentes_asignaturas` (`id`, `docente_id`, `asignatura_id`) VALUES
(1, 1, 3),
(2, 2, 5),
(3, 3, 1),
(8, 3, 7),
(4, 4, 2),
(5, 5, 2),
(6, 6, 1),
(7, 7, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas`
--

CREATE TABLE `entregas` (
  `id` int(11) NOT NULL,
  `reserva_id` int(11) NOT NULL,
  `nombre_alumno` varchar(50) NOT NULL,
  `apellido_alumno` varchar(50) NOT NULL,
  `nombre_archivo` varchar(50) DEFAULT NULL,
  `fecha_hora_entrega` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_fijos`
--

CREATE TABLE `horarios_fijos` (
  `id` int(10) UNSIGNED NOT NULL,
  `dia_semana` tinyint(3) UNSIGNED NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `tipo` enum('completo','sub1','sub2') NOT NULL,
  `docente_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `modalidad` enum('asignatura','taller') DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `horarios_fijos`
--

INSERT INTO `horarios_fijos` (`id`, `dia_semana`, `bloque_id`, `tipo`, `docente_id`, `curso_id`, `asignatura_id`, `activo`, `fecha_inicio`, `fecha_fin`, `observaciones`, `modalidad`) VALUES
(1, 2, 4, 'completo', 3, 17, 7, 1, '2026-04-07', NULL, NULL, 'taller'),
(2, 1, 2, 'sub1', 8, 14, 8, 1, '2026-03-02', NULL, NULL, 'asignatura'),
(3, 1, 2, 'sub2', 8, 16, 8, 1, '2026-03-02', NULL, NULL, 'asignatura'),
(4, 1, 3, 'sub2', 8, 11, 8, 1, '2026-03-02', NULL, NULL, 'asignatura'),
(5, 1, 4, 'sub2', 8, 13, 8, 1, '2026-03-02', NULL, NULL, 'asignatura'),
(7, 2, 2, 'sub1', 8, 9, 8, 1, '2026-03-02', NULL, NULL, 'asignatura'),
(8, 2, 3, 'sub1', 8, 12, 8, 1, '2026-03-02', NULL, NULL, 'asignatura'),
(9, 2, 3, 'sub2', 8, 10, 8, 1, '2026-03-02', NULL, NULL, 'asignatura'),
(11, 4, 4, 'completo', 3, 17, 7, 1, '2026-04-07', NULL, NULL, 'taller'),
(12, 5, 2, 'sub2', 8, 9, 8, 1, '2026-03-02', NULL, NULL, 'asignatura');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_fijos_ocurrencias`
--

CREATE TABLE `horarios_fijos_ocurrencias` (
  `id` int(11) NOT NULL,
  `horario_fijo_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('pendiente','utilizada','no_utilizada','reasignada') NOT NULL DEFAULT 'pendiente',
  `docente_id` int(11) DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `asignatura_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_confirmacion` datetime DEFAULT NULL,
  `reserva_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios_fijos_ocurrencias`
--

INSERT INTO `horarios_fijos_ocurrencias` (`id`, `horario_fijo_id`, `fecha`, `estado`, `docente_id`, `curso_id`, `asignatura_id`, `usuario_id`, `observaciones`, `fecha_confirmacion`, `reserva_id`) VALUES
(1, 2, '2026-08-24', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(2, 3, '2026-08-24', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(3, 4, '2026-08-24', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(4, 5, '2026-08-24', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(5, 7, '2026-08-25', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(6, 8, '2026-08-25', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(7, 9, '2026-08-25', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(8, 1, '2026-08-25', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(10, 11, '2026-08-27', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(11, 12, '2026-08-28', 'reasignada', 8, 9, 8, 1, NULL, '2026-08-26 14:25:48', 35);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos`
--

CREATE TABLE `recursos` (
  `id` int(11) NOT NULL,
  `nombre_recurso` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `docente_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `curso_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `objetivo_clase` varchar(150) DEFAULT NULL,
  `actividad` varchar(150) DEFAULT '',
  `permite_entrega` tinyint(1) NOT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `cierre_manual` tinyint(1) DEFAULT 0,
  `estado` enum('reservada','utilizada','cancelada') NOT NULL DEFAULT 'reservada',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipo_reserva` enum('completo','sub1','sub2') NOT NULL DEFAULT 'completo',
  `fecha_entrega_oficial` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `docente_id`, `usuario_id`, `curso_id`, `asignatura_id`, `bloque_id`, `fecha`, `objetivo_clase`, `actividad`, `permite_entrega`, `fecha_cierre`, `cierre_manual`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `tipo_reserva`, `fecha_entrega_oficial`) VALUES
(3, 1, 1, 9, 3, 1, '2026-08-03', NULL, 'Generación de una presentación en PowerPoint \"My Favorite Food\"', 0, NULL, 0, 'cancelada', '2026-08-05 12:33:10', '2026-08-20 09:26:16', 'sub1', NULL),
(4, 1, 1, 16, 3, 1, '2026-08-10', NULL, 'gdfjgdfjgh', 0, NULL, 0, 'cancelada', '2026-08-10 09:02:35', '2026-08-20 09:26:16', 'sub1', NULL),
(5, 1, 1, 12, 3, 1, '2026-08-10', NULL, 'dsffasfd', 0, NULL, 0, 'cancelada', '2026-08-10 09:02:55', '2026-08-20 09:26:16', 'sub2', NULL),
(6, 1, 1, 9, 3, 1, '2026-08-11', NULL, 'HOLAaaaaaaaaa', 0, NULL, 0, 'cancelada', '2026-08-10 09:03:13', '2026-08-20 09:26:16', 'completo', NULL),
(7, 1, 1, 9, 3, 3, '2026-08-10', NULL, 'aaaaaaaaaaaaaaaaaaaa', 0, NULL, 0, 'cancelada', '2026-08-10 09:47:06', '2026-08-20 09:26:16', 'sub1', NULL),
(8, 1, 1, 12, 3, 4, '2026-08-10', NULL, 'dsffffffff        ddddddddddddd', 0, NULL, 0, 'cancelada', '2026-08-10 13:23:55', '2026-08-20 09:26:16', 'sub1', NULL),
(9, 1, 1, 13, 3, 4, '2026-08-10', NULL, 'bbbbbbbbbbbbbbbbbbb', 0, NULL, 0, 'cancelada', '2026-08-10 14:32:58', '2026-08-20 09:26:16', 'sub2', NULL),
(10, 1, 1, 15, 3, 2, '2026-08-10', '', '', 0, NULL, 0, 'cancelada', '2026-08-11 15:13:01', '2026-08-20 09:26:16', 'completo', NULL),
(11, 1, 1, 15, 3, 1, '2026-08-12', 'bbbbbbbbbbbbbbbbbbbb', 'aaaaaaaaaaaaaaaaaaa', 0, NULL, 0, 'cancelada', '2026-08-11 15:24:52', '2026-08-20 09:26:16', 'sub2', NULL),
(12, 1, 1, 13, 3, 4, '2026-08-11', 'cccccccccccc', 'aaaaaaaaaa', 0, NULL, 0, 'cancelada', '2026-08-11 15:41:46', '2026-08-20 09:26:16', 'sub2', NULL),
(13, 4, 1, 12, 2, 1, '2026-08-17', 'aaaaaaaaa', 'bbbbbbbbb', 0, NULL, 0, 'cancelada', '2026-08-17 09:46:23', '2026-08-20 09:26:16', 'sub1', NULL),
(14, 7, 1, 13, 4, 2, '2026-08-17', '', '', 0, NULL, 0, 'cancelada', '2026-08-17 09:59:48', '2026-08-20 09:26:16', 'completo', NULL),
(15, 4, 1, 13, 2, 3, '2026-08-17', '', '', 0, NULL, 0, 'cancelada', '2026-08-17 10:20:31', '2026-08-20 09:26:16', 'sub2', NULL),
(16, 1, 1, 14, 3, 4, '2026-08-17', '', '', 0, NULL, 0, 'cancelada', '2026-08-17 14:46:37', '2026-08-20 09:26:16', 'sub2', NULL),
(17, 1, 1, 16, 3, 1, '2026-08-18', 'qweqwewe', 'ewqeqweqwe', 0, NULL, 0, 'utilizada', '2026-08-17 15:06:39', '2026-08-18 14:50:07', 'sub1', NULL),
(18, 1, 1, 15, 3, 3, '2026-08-18', 'aaaa', 'bbbb', 0, NULL, 0, 'cancelada', '2026-08-18 11:45:40', '2026-08-18 11:45:51', 'sub2', NULL),
(19, 1, 1, 15, 3, 4, '2026-08-18', 'actividad', 'Objetivo', 0, NULL, 0, 'cancelada', '2026-08-18 14:51:43', '2026-08-20 09:26:16', 'sub2', NULL),
(20, 5, 1, 11, 2, 3, '2026-08-19', 'aaaa', 'bbbb', 0, NULL, 0, 'utilizada', '2026-08-19 12:35:10', '2026-08-19 12:56:43', 'sub2', NULL),
(21, 4, 1, 13, 3, 4, '2026-08-19', 'aaaaa', 'bbbbb', 0, NULL, 0, 'cancelada', '2026-08-19 12:58:38', '2026-08-19 12:59:07', 'sub1', NULL),
(22, 1, 1, 12, 3, 4, '2026-08-19', 'afasfsf', 'safdsfdsafsfd', 0, NULL, 0, 'cancelada', '2026-08-19 13:06:32', '2026-08-20 09:26:16', 'completo', NULL),
(23, 1, 1, 16, 3, 2, '2026-08-20', 'aasasasas', 'abvbvbvbv', 0, NULL, 0, 'cancelada', '2026-08-20 09:40:02', '2026-08-20 09:41:35', 'sub1', NULL),
(24, 4, 1, 14, 2, 2, '2026-08-20', 'aaaaaa', 'bbbbbb', 0, NULL, 0, 'cancelada', '2026-08-20 10:13:28', '2026-08-20 10:13:35', 'sub1', NULL),
(25, 1, 1, 15, 3, 2, '2026-08-23', 'aaaa', 'sssssss', 0, NULL, 0, 'cancelada', '2026-08-20 10:17:46', '2026-08-24 09:59:28', 'sub1', NULL),
(26, 7, 1, 14, 4, 2, '2026-08-24', 'mmmmmm', 'sssssssssss', 0, NULL, 0, 'cancelada', '2026-08-24 09:46:35', '2026-08-24 11:26:48', 'sub1', NULL),
(27, 4, 1, 11, 2, 4, '2026-08-24', 'lllllll', 'yyyyyyyy', 0, NULL, 0, 'cancelada', '2026-08-24 13:03:40', '2026-08-24 15:00:11', 'sub1', NULL),
(28, 2, 1, 16, 5, 1, '2026-08-26', '', '', 0, NULL, 0, 'cancelada', '2026-08-24 14:21:09', '2026-08-26 10:00:11', 'completo', NULL),
(29, 1, 1, 15, 3, 1, '2026-08-27', '', '', 0, NULL, 0, 'reservada', '2026-08-24 14:21:42', '2026-08-24 14:21:42', 'completo', NULL),
(30, 1, 1, 12, 3, 2, '2026-08-27', '', '', 0, NULL, 0, 'reservada', '2026-08-24 14:22:00', '2026-08-24 14:22:00', 'completo', NULL),
(31, 1, 1, 16, 3, 3, '2026-08-27', '', '', 0, NULL, 0, 'reservada', '2026-08-24 14:22:16', '2026-08-24 14:22:16', 'completo', NULL),
(32, 6, 1, 16, 1, 2, '2026-08-26', '', '', 0, NULL, 0, 'cancelada', '2026-08-25 12:57:16', '2026-08-26 11:50:12', 'completo', NULL),
(33, 6, 1, 14, 1, 3, '2026-08-26', '', '', 0, NULL, 0, 'cancelada', '2026-08-25 12:57:37', '2026-08-26 13:30:12', 'completo', NULL),
(34, 2, 1, 10, 5, 4, '2026-08-26', '', '', 0, NULL, 0, 'cancelada', '2026-08-26 09:06:41', '2026-08-26 15:45:12', 'completo', NULL),
(35, 4, 1, 15, 2, 2, '2026-08-28', '', '', 1, NULL, 0, 'reservada', '2026-08-26 14:25:48', '2026-08-26 14:25:48', 'sub2', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `usuario` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('usuario','admin','superdmin') NOT NULL,
  `acceso` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombres`, `apellidos`, `correo`, `usuario`, `password`, `rol`, `acceso`, `ultimo_acceso`) VALUES
(1, 'José A.', 'Fernández Concha', 'jfernandezconcha@gmail.com', 'Josesinis', 'm@31m301j@30', 'superdmin', 1, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `bitacoras`
--
ALTER TABLE `bitacoras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reserva_id` (`reserva_id`) USING BTREE,
  ADD UNIQUE KEY `uq_bitacora_ocurrencia` (`horario_fijo_ocurrencia_id`);

--
-- Indices de la tabla `bitacora_recursos`
--
ALTER TABLE `bitacora_recursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bitacora_id` (`bitacora_id`),
  ADD KEY `recurso_id` (`recurso_id`);

--
-- Indices de la tabla `bloques`
--
ALTER TABLE `bloques`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `docentes_asignaturas`
--
ALTER TABLE `docentes_asignaturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_docente_asignatura` (`docente_id`,`asignatura_id`) USING BTREE,
  ADD KEY `id_asignatura` (`asignatura_id`);

--
-- Indices de la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reserva_id` (`reserva_id`);

--
-- Indices de la tabla `horarios_fijos`
--
ALTER TABLE `horarios_fijos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_horarios_fijos_asignatura` (`asignatura_id`),
  ADD KEY `fk_horarios_fijos_bloque` (`bloque_id`),
  ADD KEY `fk_horarios_fijos_curso` (`curso_id`),
  ADD KEY `fk_horarios_fijos_docente` (`docente_id`);

--
-- Indices de la tabla `horarios_fijos_ocurrencias`
--
ALTER TABLE `horarios_fijos_ocurrencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_horario_fijo_fecha` (`horario_fijo_id`,`fecha`),
  ADD KEY `fk_ocurrencias_docente` (`docente_id`),
  ADD KEY `fk_ocurrencias_curso` (`curso_id`),
  ADD KEY `fk_ocurrencias_asignatura` (`asignatura_id`),
  ADD KEY `fk_ocurrencias_usuario` (`usuario_id`),
  ADD KEY `reservaId` (`reserva_id`);

--
-- Indices de la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `docente_id` (`docente_id`),
  ADD KEY `bloque_id` (`bloque_id`),
  ADD KEY `asignatura_id` (`asignatura_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `bitacoras`
--
ALTER TABLE `bitacoras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `bitacora_recursos`
--
ALTER TABLE `bitacora_recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bloques`
--
ALTER TABLE `bloques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `docentes`
--
ALTER TABLE `docentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `docentes_asignaturas`
--
ALTER TABLE `docentes_asignaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios_fijos`
--
ALTER TABLE `horarios_fijos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios_fijos_ocurrencias`
--
ALTER TABLE `horarios_fijos_ocurrencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacoras`
--
ALTER TABLE `bitacoras`
  ADD CONSTRAINT `fk_bitacoras_ocurrencia` FOREIGN KEY (`horario_fijo_ocurrencia_id`) REFERENCES `horarios_fijos_ocurrencias` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reserva_id` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bitacora_recursos`
--
ALTER TABLE `bitacora_recursos`
  ADD CONSTRAINT `bitacora_id` FOREIGN KEY (`bitacora_id`) REFERENCES `bitacoras` (`id`),
  ADD CONSTRAINT `recurso_id` FOREIGN KEY (`recurso_id`) REFERENCES `recursos` (`id`);

--
-- Filtros para la tabla `docentes_asignaturas`
--
ALTER TABLE `docentes_asignaturas`
  ADD CONSTRAINT `id_asignatura` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_docente` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD CONSTRAINT `id_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `horarios_fijos`
--
ALTER TABLE `horarios_fijos`
  ADD CONSTRAINT `fk_horarios_fijos_asignatura` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_horarios_fijos_bloque` FOREIGN KEY (`bloque_id`) REFERENCES `bloques` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_horarios_fijos_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_horarios_fijos_docente` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `horarios_fijos_ocurrencias`
--
ALTER TABLE `horarios_fijos_ocurrencias`
  ADD CONSTRAINT `fk_ocurrencias_asignatura` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ocurrencias_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ocurrencias_docente` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ocurrencias_horario_fijo` FOREIGN KEY (`horario_fijo_id`) REFERENCES `horarios_fijos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ocurrencias_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservaId` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `asignatura_id` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`),
  ADD CONSTRAINT `bloque_id` FOREIGN KEY (`bloque_id`) REFERENCES `bloques` (`id`),
  ADD CONSTRAINT `curso_id` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `docente_id` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`),
  ADD CONSTRAINT `usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
