-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2026 a las 15:41:54
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
  `asignatura_nombre` varchar(50) NOT NULL,
  `modalidad` enum('asignatura','taller') DEFAULT 'asignatura'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `asignaturas`
--

INSERT INTO `asignaturas` (`id`, `asignatura_nombre`, `modalidad`) VALUES
(1, 'Matemáticas', 'asignatura'),
(2, 'Lenguaje', 'asignatura'),
(3, 'Inglés', 'asignatura'),
(4, 'Música', 'asignatura'),
(5, 'Ciencias', 'asignatura'),
(6, 'Historia', 'asignatura'),
(7, 'Taller de IA', 'taller'),
(8, 'Tecnología', 'asignatura');

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
  `nombre_curso` varchar(10) NOT NULL DEFAULT '',
  `modalidad` enum('asignatura','taller') DEFAULT 'asignatura'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nombre_curso`, `modalidad`) VALUES
(1, '1° A', 'asignatura'),
(2, '1° B', 'asignatura'),
(3, '2° A', 'asignatura'),
(4, '2° B', 'asignatura'),
(5, '3° A', 'asignatura'),
(6, '3° B', 'asignatura'),
(7, '4° A', 'asignatura'),
(8, '4° B', 'asignatura'),
(9, '5° A', 'asignatura'),
(10, '5° B', 'asignatura'),
(11, '6° A', 'asignatura'),
(12, '6° B', 'asignatura'),
(13, '7° A', 'asignatura'),
(14, '7° B', 'asignatura'),
(15, '8° A', 'asignatura'),
(16, '8° B', 'asignatura'),
(17, 'Taller', 'taller');

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
(7, 7, 4),
(9, 8, 8);

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
(12, 5, 2, 'sub2', 8, 9, 8, 1, '2026-03-02', NULL, NULL, 'asignatura'),
(13, 4, 2, 'completo', 3, 17, 7, 1, '2026-04-07', '2026-12-15', NULL, 'taller'),
(14, 3, 2, 'sub2', 8, 9, 8, 1, '2026-03-09', '2026-12-15', NULL, 'asignatura');

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
(11, 12, '2026-08-28', 'reasignada', 8, 9, 8, 1, NULL, '2026-08-26 14:25:48', 35),
(22, 2, '2026-04-13', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(23, 2, '2026-04-20', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(24, 2, '2026-04-27', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(25, 2, '2026-05-04', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(26, 2, '2026-05-11', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(27, 2, '2026-05-18', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(28, 2, '2026-05-25', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(29, 2, '2026-06-01', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(30, 2, '2026-06-08', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(31, 2, '2026-06-15', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(32, 2, '2026-06-22', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(33, 2, '2026-06-29', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(34, 2, '2026-07-06', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(35, 2, '2026-07-13', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(36, 2, '2026-07-20', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(37, 2, '2026-07-27', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(38, 2, '2026-08-03', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(39, 2, '2026-08-10', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(40, 2, '2026-08-17', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(42, 2, '2026-08-31', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(43, 2, '2026-09-07', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(44, 2, '2026-09-14', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(45, 2, '2026-09-21', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(46, 2, '2026-09-28', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(47, 2, '2026-10-05', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(48, 2, '2026-10-12', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(49, 2, '2026-10-19', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(50, 2, '2026-10-26', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(51, 2, '2026-11-02', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(52, 2, '2026-11-09', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(53, 2, '2026-11-16', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(54, 2, '2026-11-23', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(55, 2, '2026-11-30', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(56, 2, '2026-12-07', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(57, 2, '2026-12-14', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(58, 3, '2026-04-13', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(59, 3, '2026-04-20', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(60, 3, '2026-04-27', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(61, 3, '2026-05-04', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(62, 3, '2026-05-11', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(63, 3, '2026-05-18', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(64, 3, '2026-05-25', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(65, 3, '2026-06-01', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(66, 3, '2026-06-08', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(67, 3, '2026-06-15', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(68, 3, '2026-06-22', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(69, 3, '2026-06-29', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(70, 3, '2026-07-06', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(71, 3, '2026-07-13', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(72, 3, '2026-07-20', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(73, 3, '2026-07-27', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(74, 3, '2026-08-03', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(75, 3, '2026-08-10', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(76, 3, '2026-08-17', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(78, 3, '2026-08-31', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(79, 3, '2026-09-07', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(80, 3, '2026-09-14', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(81, 3, '2026-09-21', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(82, 3, '2026-09-28', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(83, 3, '2026-10-05', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(84, 3, '2026-10-12', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(85, 3, '2026-10-19', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(86, 3, '2026-10-26', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(87, 3, '2026-11-02', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(88, 3, '2026-11-09', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(89, 3, '2026-11-16', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(90, 3, '2026-11-23', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(91, 3, '2026-11-30', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(92, 3, '2026-12-07', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(93, 3, '2026-12-14', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(94, 4, '2026-04-13', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(95, 4, '2026-04-20', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(96, 4, '2026-04-27', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(97, 4, '2026-05-04', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(98, 4, '2026-05-11', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(99, 4, '2026-05-18', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(100, 4, '2026-05-25', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(101, 4, '2026-06-01', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(102, 4, '2026-06-08', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(103, 4, '2026-06-15', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(104, 4, '2026-06-22', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(105, 4, '2026-06-29', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(106, 4, '2026-07-06', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(107, 4, '2026-07-13', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(108, 4, '2026-07-20', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(109, 4, '2026-07-27', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(110, 4, '2026-08-03', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(111, 4, '2026-08-10', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(112, 4, '2026-08-17', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(114, 4, '2026-08-31', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(115, 4, '2026-09-07', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(116, 4, '2026-09-14', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(117, 4, '2026-09-21', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(118, 4, '2026-09-28', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(119, 4, '2026-10-05', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(120, 4, '2026-10-12', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(121, 4, '2026-10-19', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(122, 4, '2026-10-26', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(123, 4, '2026-11-02', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(124, 4, '2026-11-09', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(125, 4, '2026-11-16', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(126, 4, '2026-11-23', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(127, 4, '2026-11-30', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(128, 4, '2026-12-07', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(129, 4, '2026-12-14', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(130, 5, '2026-04-13', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(131, 5, '2026-04-20', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(132, 5, '2026-04-27', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(133, 5, '2026-05-04', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(134, 5, '2026-05-11', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(135, 5, '2026-05-18', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(136, 5, '2026-05-25', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(137, 5, '2026-06-01', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(138, 5, '2026-06-08', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(139, 5, '2026-06-15', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(140, 5, '2026-06-22', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(141, 5, '2026-06-29', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(142, 5, '2026-07-06', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(143, 5, '2026-07-13', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(144, 5, '2026-07-20', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(145, 5, '2026-07-27', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(146, 5, '2026-08-03', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(147, 5, '2026-08-10', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(148, 5, '2026-08-17', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(150, 5, '2026-08-31', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(151, 5, '2026-09-07', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(152, 5, '2026-09-14', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(153, 5, '2026-09-21', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(154, 5, '2026-09-28', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(155, 5, '2026-10-05', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(156, 5, '2026-10-12', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(157, 5, '2026-10-19', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(158, 5, '2026-10-26', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(159, 5, '2026-11-02', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(160, 5, '2026-11-09', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(161, 5, '2026-11-16', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(162, 5, '2026-11-23', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(163, 5, '2026-11-30', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(164, 5, '2026-12-07', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(165, 5, '2026-12-14', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(166, 7, '2026-04-07', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(167, 7, '2026-04-14', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(168, 7, '2026-04-21', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(169, 7, '2026-04-28', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(170, 7, '2026-05-05', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(171, 7, '2026-05-12', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(172, 7, '2026-05-19', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(173, 7, '2026-05-26', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(174, 7, '2026-06-02', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(175, 7, '2026-06-09', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(176, 7, '2026-06-16', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(177, 7, '2026-06-23', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(178, 7, '2026-06-30', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(179, 7, '2026-07-07', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(180, 7, '2026-07-14', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(181, 7, '2026-07-21', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(182, 7, '2026-07-28', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(183, 7, '2026-08-04', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(184, 7, '2026-08-11', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(185, 7, '2026-08-18', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(187, 7, '2026-09-01', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(188, 7, '2026-09-08', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(189, 7, '2026-09-15', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(190, 7, '2026-09-22', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(191, 7, '2026-09-29', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(192, 7, '2026-10-06', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(193, 7, '2026-10-13', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(194, 7, '2026-10-20', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(195, 7, '2026-10-27', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(196, 7, '2026-11-03', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(197, 7, '2026-11-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(198, 7, '2026-11-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(199, 7, '2026-11-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(200, 7, '2026-12-01', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(201, 7, '2026-12-08', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(202, 8, '2026-04-07', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(203, 8, '2026-04-14', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(204, 8, '2026-04-21', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(205, 8, '2026-04-28', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(206, 8, '2026-05-05', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(207, 8, '2026-05-12', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(208, 8, '2026-05-19', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(209, 8, '2026-05-26', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(210, 8, '2026-06-02', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(211, 8, '2026-06-09', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(212, 8, '2026-06-16', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(213, 8, '2026-06-23', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(214, 8, '2026-06-30', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(215, 8, '2026-07-07', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(216, 8, '2026-07-14', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(217, 8, '2026-07-21', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(218, 8, '2026-07-28', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(219, 8, '2026-08-04', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(220, 8, '2026-08-11', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(221, 8, '2026-08-18', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(223, 8, '2026-09-01', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(224, 8, '2026-09-08', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(225, 8, '2026-09-15', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(226, 8, '2026-09-22', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(227, 8, '2026-09-29', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(228, 8, '2026-10-06', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(229, 8, '2026-10-13', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(230, 8, '2026-10-20', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(231, 8, '2026-10-27', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(232, 8, '2026-11-03', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(233, 8, '2026-11-10', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(234, 8, '2026-11-17', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(235, 8, '2026-11-24', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(236, 8, '2026-12-01', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(237, 8, '2026-12-08', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(238, 9, '2026-04-07', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(239, 9, '2026-04-14', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(240, 9, '2026-04-21', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(241, 9, '2026-04-28', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(242, 9, '2026-05-05', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(243, 9, '2026-05-12', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(244, 9, '2026-05-19', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(245, 9, '2026-05-26', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(246, 9, '2026-06-02', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(247, 9, '2026-06-09', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(248, 9, '2026-06-16', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(249, 9, '2026-06-23', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(250, 9, '2026-06-30', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(251, 9, '2026-07-07', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(252, 9, '2026-07-14', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(253, 9, '2026-07-21', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(254, 9, '2026-07-28', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(255, 9, '2026-08-04', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(256, 9, '2026-08-11', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(257, 9, '2026-08-18', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(259, 9, '2026-09-01', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(260, 9, '2026-09-08', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(261, 9, '2026-09-15', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(262, 9, '2026-09-22', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(263, 9, '2026-09-29', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(264, 9, '2026-10-06', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(265, 9, '2026-10-13', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(266, 9, '2026-10-20', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(267, 9, '2026-10-27', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(268, 9, '2026-11-03', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(269, 9, '2026-11-10', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(270, 9, '2026-11-17', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(271, 9, '2026-11-24', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(272, 9, '2026-12-01', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(273, 9, '2026-12-08', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(274, 1, '2026-04-07', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(275, 1, '2026-04-14', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(276, 1, '2026-04-21', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(277, 1, '2026-04-28', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(278, 1, '2026-05-05', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(279, 1, '2026-05-12', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(280, 1, '2026-05-19', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(281, 1, '2026-05-26', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(282, 1, '2026-06-02', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(283, 1, '2026-06-09', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(284, 1, '2026-06-16', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(285, 1, '2026-06-23', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(286, 1, '2026-06-30', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(287, 1, '2026-07-07', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(288, 1, '2026-07-14', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(289, 1, '2026-07-21', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(290, 1, '2026-07-28', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(291, 1, '2026-08-04', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(292, 1, '2026-08-11', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(293, 1, '2026-08-18', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(295, 1, '2026-09-01', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(296, 1, '2026-09-08', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(297, 1, '2026-09-15', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(298, 1, '2026-09-22', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(299, 1, '2026-09-29', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(300, 1, '2026-10-06', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(301, 1, '2026-10-13', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(302, 1, '2026-10-20', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(303, 1, '2026-10-27', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(304, 1, '2026-11-03', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(305, 1, '2026-11-10', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(306, 1, '2026-11-17', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(307, 1, '2026-11-24', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(308, 1, '2026-12-01', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(309, 1, '2026-12-08', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(310, 13, '2026-04-09', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(311, 13, '2026-04-16', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(312, 13, '2026-04-23', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(313, 13, '2026-04-30', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(314, 13, '2026-05-07', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(315, 13, '2026-05-14', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(316, 13, '2026-05-21', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(317, 13, '2026-05-28', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(318, 13, '2026-06-04', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(319, 13, '2026-06-11', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(320, 13, '2026-06-18', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(321, 13, '2026-06-25', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(322, 13, '2026-07-02', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(323, 13, '2026-07-09', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(324, 13, '2026-07-16', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(325, 13, '2026-07-23', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(326, 13, '2026-07-30', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(327, 13, '2026-08-06', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(328, 13, '2026-08-13', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(329, 13, '2026-08-20', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(330, 13, '2026-08-27', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(331, 13, '2026-09-03', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(332, 13, '2026-09-10', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(333, 13, '2026-09-17', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(334, 13, '2026-09-24', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(335, 13, '2026-10-01', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(336, 13, '2026-10-08', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(337, 13, '2026-10-15', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(338, 13, '2026-10-22', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(339, 13, '2026-10-29', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(340, 13, '2026-11-05', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(341, 13, '2026-11-12', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(342, 13, '2026-11-19', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(343, 13, '2026-11-26', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(344, 13, '2026-12-03', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(345, 13, '2026-12-10', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(346, 11, '2026-04-09', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(347, 11, '2026-04-16', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(348, 11, '2026-04-23', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(349, 11, '2026-04-30', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(350, 11, '2026-05-07', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(351, 11, '2026-05-14', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(352, 11, '2026-05-21', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(353, 11, '2026-05-28', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(354, 11, '2026-06-04', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(355, 11, '2026-06-11', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(356, 11, '2026-06-18', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(357, 11, '2026-06-25', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(358, 11, '2026-07-02', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(359, 11, '2026-07-09', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(360, 11, '2026-07-16', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(361, 11, '2026-07-23', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(362, 11, '2026-07-30', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(363, 11, '2026-08-06', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(364, 11, '2026-08-13', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(365, 11, '2026-08-20', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(367, 11, '2026-09-03', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(368, 11, '2026-09-10', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(369, 11, '2026-09-17', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(370, 11, '2026-09-24', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(371, 11, '2026-10-01', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(372, 11, '2026-10-08', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(373, 11, '2026-10-15', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(374, 11, '2026-10-22', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(375, 11, '2026-10-29', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(376, 11, '2026-11-05', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(377, 11, '2026-11-12', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(378, 11, '2026-11-19', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(379, 11, '2026-11-26', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(380, 11, '2026-12-03', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(381, 11, '2026-12-10', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(382, 12, '2026-04-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(383, 12, '2026-04-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(384, 12, '2026-04-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(385, 12, '2026-05-01', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(386, 12, '2026-05-08', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(387, 12, '2026-05-15', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(388, 12, '2026-05-22', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(389, 12, '2026-05-29', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(390, 12, '2026-06-05', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(391, 12, '2026-06-12', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(392, 12, '2026-06-19', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(393, 12, '2026-06-26', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(394, 12, '2026-07-03', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(395, 12, '2026-07-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(396, 12, '2026-07-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(397, 12, '2026-07-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(398, 12, '2026-07-31', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(399, 12, '2026-08-07', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(400, 12, '2026-08-14', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(401, 12, '2026-08-21', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(403, 12, '2026-09-04', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(404, 12, '2026-09-11', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(405, 12, '2026-09-18', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(406, 12, '2026-09-25', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(407, 12, '2026-10-02', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(408, 12, '2026-10-09', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(409, 12, '2026-10-16', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(410, 12, '2026-10-23', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(411, 12, '2026-10-30', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(412, 12, '2026-11-06', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(413, 12, '2026-11-13', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(414, 12, '2026-11-20', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(415, 12, '2026-11-27', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(416, 12, '2026-12-04', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(417, 12, '2026-12-11', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(418, 2, '2026-03-09', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(419, 2, '2026-03-16', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(420, 2, '2026-03-23', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(421, 2, '2026-03-30', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(422, 2, '2026-04-06', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(459, 3, '2026-03-09', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(460, 3, '2026-03-16', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(461, 3, '2026-03-23', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(462, 3, '2026-03-30', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(463, 3, '2026-04-06', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(500, 4, '2026-03-09', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(501, 4, '2026-03-16', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(502, 4, '2026-03-23', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(503, 4, '2026-03-30', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(504, 4, '2026-04-06', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(541, 5, '2026-03-09', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(542, 5, '2026-03-16', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(543, 5, '2026-03-23', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(544, 5, '2026-03-30', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(545, 5, '2026-04-06', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(582, 7, '2026-03-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(583, 7, '2026-03-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(584, 7, '2026-03-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(585, 7, '2026-03-31', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(622, 8, '2026-03-10', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(623, 8, '2026-03-17', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(624, 8, '2026-03-24', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(625, 8, '2026-03-31', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(662, 9, '2026-03-10', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(663, 9, '2026-03-17', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(664, 9, '2026-03-24', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(665, 9, '2026-03-31', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(738, 14, '2026-03-11', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(739, 14, '2026-03-18', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(740, 14, '2026-03-25', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(741, 14, '2026-04-01', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(742, 14, '2026-04-08', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(743, 14, '2026-04-15', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(744, 14, '2026-04-22', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(745, 14, '2026-04-29', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(746, 14, '2026-05-06', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(747, 14, '2026-05-13', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(748, 14, '2026-05-20', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(749, 14, '2026-05-27', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(750, 14, '2026-06-03', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(751, 14, '2026-06-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(752, 14, '2026-06-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(753, 14, '2026-06-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(754, 14, '2026-07-01', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(755, 14, '2026-07-08', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(756, 14, '2026-07-15', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(757, 14, '2026-07-22', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(758, 14, '2026-07-29', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(759, 14, '2026-08-05', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(760, 14, '2026-08-12', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(761, 14, '2026-08-19', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(762, 14, '2026-08-26', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(763, 14, '2026-09-02', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(764, 14, '2026-09-09', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(765, 14, '2026-09-16', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(766, 14, '2026-09-23', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(767, 14, '2026-09-30', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(768, 14, '2026-10-07', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(769, 14, '2026-10-14', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(770, 14, '2026-10-21', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(771, 14, '2026-10-28', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(772, 14, '2026-11-04', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(773, 14, '2026-11-11', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(774, 14, '2026-11-18', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(775, 14, '2026-11-25', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(776, 14, '2026-12-02', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(777, 14, '2026-12-09', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(850, 12, '2026-03-13', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(851, 12, '2026-03-20', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(852, 12, '2026-03-27', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(853, 12, '2026-04-03', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL);

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
(29, 1, 1, 15, 3, 1, '2026-08-27', '', '', 0, NULL, 0, 'cancelada', '2026-08-24 14:21:42', '2026-08-27 10:00:12', 'completo', NULL),
(30, 1, 1, 13, 3, 2, '2026-08-27', '', '', 0, NULL, 0, 'cancelada', '2026-08-24 14:22:00', '2026-08-27 11:50:12', 'completo', NULL),
(31, 1, 1, 16, 3, 3, '2026-08-27', '', '', 0, NULL, 0, 'cancelada', '2026-08-24 14:22:16', '2026-08-27 13:30:12', 'completo', NULL),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=890;

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
