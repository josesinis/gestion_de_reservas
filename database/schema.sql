-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-09-2026 a las 14:07:08
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
  `objetivo_clase` varchar(150) DEFAULT NULL,
  `actividad` varchar(150) DEFAULT NULL,
  `horario_fijo_ocurrencia_id` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `bitacoras`
--

INSERT INTO `bitacoras` (`id`, `reserva_id`, `objetivo_clase`, `actividad`, `horario_fijo_ocurrencia_id`, `observaciones`) VALUES
(4, 39, NULL, NULL, NULL, ''),
(5, 37, NULL, NULL, NULL, ''),
(6, 40, NULL, NULL, NULL, ''),
(7, 42, NULL, NULL, NULL, ''),
(8, 41, NULL, NULL, NULL, ''),
(9, NULL, NULL, NULL, 2797, ''),
(10, 45, NULL, NULL, NULL, ''),
(11, NULL, 'Objetivo 1', 'Actividad 1', 3194, ''),
(12, 49, 'Comprender que es el acento diacrítico.', 'Buscar monosílabas, con y sin tilde.', NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_recursos`
--

CREATE TABLE `bitacora_recursos` (
  `id` int(11) NOT NULL,
  `bitacora_id` int(11) NOT NULL,
  `recurso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `bitacora_recursos`
--

INSERT INTO `bitacora_recursos` (`id`, `bitacora_id`, `recurso_id`) VALUES
(1, 5, 1),
(2, 5, 2),
(3, 5, 3),
(4, 6, 1),
(5, 6, 2),
(6, 6, 3),
(7, 7, 1),
(8, 7, 2),
(9, 7, 3),
(10, 8, 1),
(11, 8, 2),
(12, 8, 3),
(13, 9, 5),
(14, 9, 1),
(15, 9, 6),
(16, 9, 7),
(17, 9, 2),
(18, 10, 1),
(19, 10, 2),
(20, 10, 3),
(21, 11, 1),
(22, 11, 2),
(23, 11, 3),
(24, 12, 1),
(25, 12, 2);

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
(8, 'María Sandra', 'Aguayo Aravena', 'intirrayen@gmail.com'),
(9, 'Luis Andres', 'Inostroza Jara', 'profesor.inostroza@gmail.com');

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
(9, 8, 8),
(10, 9, 6);

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
(15, 1, 2, 'sub1', 8, 14, 8, 1, '2026-03-04', '2026-12-15', NULL, 'asignatura'),
(16, 1, 2, 'sub2', 8, 16, 8, 1, '2026-03-04', '2026-12-15', NULL, 'asignatura'),
(17, 1, 3, 'sub2', 8, 11, 8, 1, '2026-03-04', '2026-12-15', NULL, 'asignatura'),
(18, 1, 4, 'sub2', 8, 13, 8, 1, '2026-03-04', '2026-12-15', NULL, 'asignatura'),
(19, 2, 2, 'sub1', 8, 9, 8, 1, '2026-03-04', '2026-12-15', NULL, 'asignatura'),
(20, 2, 3, 'sub1', 8, 12, 8, 1, '2026-03-04', '2026-12-15', NULL, 'asignatura'),
(21, 2, 3, 'sub2', 8, 10, 8, 1, '2026-03-04', '2026-12-15', NULL, 'asignatura'),
(22, 2, 4, 'completo', 3, 17, 7, 1, '2026-04-07', '2026-12-15', NULL, 'taller'),
(23, 4, 4, 'completo', 3, 17, 7, 1, '2026-04-07', '2026-12-15', NULL, 'taller'),
(24, 5, 2, 'sub2', 8, 9, 8, 1, '2026-03-04', '2026-12-15', NULL, 'asignatura');

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
(1058, 15, '2026-03-09', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1059, 15, '2026-03-16', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1060, 15, '2026-03-23', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1061, 15, '2026-03-30', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1062, 15, '2026-04-06', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1063, 15, '2026-04-13', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1064, 15, '2026-04-20', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1065, 15, '2026-04-27', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1066, 15, '2026-05-04', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1067, 15, '2026-05-11', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1068, 15, '2026-05-18', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1069, 15, '2026-05-25', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1070, 15, '2026-06-01', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1071, 15, '2026-06-08', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1072, 15, '2026-06-15', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1073, 15, '2026-06-22', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1074, 15, '2026-06-29', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1075, 15, '2026-07-06', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1076, 15, '2026-07-13', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1077, 15, '2026-07-20', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1078, 15, '2026-07-27', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1079, 15, '2026-08-03', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1080, 15, '2026-08-10', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1081, 15, '2026-08-17', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1082, 15, '2026-08-24', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1083, 15, '2026-08-31', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1084, 15, '2026-09-07', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1085, 15, '2026-09-14', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1086, 15, '2026-09-21', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1087, 15, '2026-09-28', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1088, 15, '2026-10-05', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1089, 15, '2026-10-12', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1090, 15, '2026-10-19', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1091, 15, '2026-10-26', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1092, 15, '2026-11-02', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1093, 15, '2026-11-09', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1094, 15, '2026-11-16', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1095, 15, '2026-11-23', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1096, 15, '2026-11-30', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1097, 15, '2026-12-07', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1098, 15, '2026-12-14', 'pendiente', 8, 14, 8, NULL, NULL, NULL, NULL),
(1140, 16, '2026-03-09', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1141, 16, '2026-03-16', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1142, 16, '2026-03-23', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1143, 16, '2026-03-30', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1144, 16, '2026-04-06', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1145, 16, '2026-04-13', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1146, 16, '2026-04-20', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1147, 16, '2026-04-27', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1148, 16, '2026-05-04', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1149, 16, '2026-05-11', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1150, 16, '2026-05-18', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1151, 16, '2026-05-25', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1152, 16, '2026-06-01', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1153, 16, '2026-06-08', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1154, 16, '2026-06-15', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1155, 16, '2026-06-22', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1156, 16, '2026-06-29', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1157, 16, '2026-07-06', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1158, 16, '2026-07-13', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1159, 16, '2026-07-20', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1160, 16, '2026-07-27', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1161, 16, '2026-08-03', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1162, 16, '2026-08-10', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1163, 16, '2026-08-17', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1164, 16, '2026-08-24', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1165, 16, '2026-08-31', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1166, 16, '2026-09-07', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1167, 16, '2026-09-14', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1168, 16, '2026-09-21', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1169, 16, '2026-09-28', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1170, 16, '2026-10-05', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1171, 16, '2026-10-12', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1172, 16, '2026-10-19', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1173, 16, '2026-10-26', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1174, 16, '2026-11-02', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1175, 16, '2026-11-09', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1176, 16, '2026-11-16', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1177, 16, '2026-11-23', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1178, 16, '2026-11-30', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1179, 16, '2026-12-07', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1180, 16, '2026-12-14', 'pendiente', 8, 16, 8, NULL, NULL, NULL, NULL),
(1263, 17, '2026-03-09', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1264, 17, '2026-03-16', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1265, 17, '2026-03-23', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1266, 17, '2026-03-30', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1267, 17, '2026-04-06', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1268, 17, '2026-04-13', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1269, 17, '2026-04-20', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1270, 17, '2026-04-27', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1271, 17, '2026-05-04', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1272, 17, '2026-05-11', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1273, 17, '2026-05-18', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1274, 17, '2026-05-25', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1275, 17, '2026-06-01', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1276, 17, '2026-06-08', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1277, 17, '2026-06-15', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1278, 17, '2026-06-22', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1279, 17, '2026-06-29', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1280, 17, '2026-07-06', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1281, 17, '2026-07-13', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1282, 17, '2026-07-20', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1283, 17, '2026-07-27', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1284, 17, '2026-08-03', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1285, 17, '2026-08-10', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1286, 17, '2026-08-17', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1287, 17, '2026-08-24', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1288, 17, '2026-08-31', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1289, 17, '2026-09-07', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1290, 17, '2026-09-14', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1291, 17, '2026-09-21', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1292, 17, '2026-09-28', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1293, 17, '2026-10-05', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1294, 17, '2026-10-12', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1295, 17, '2026-10-19', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1296, 17, '2026-10-26', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1297, 17, '2026-11-02', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1298, 17, '2026-11-09', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1299, 17, '2026-11-16', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1300, 17, '2026-11-23', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1301, 17, '2026-11-30', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1302, 17, '2026-12-07', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1303, 17, '2026-12-14', 'pendiente', 8, 11, 8, NULL, NULL, NULL, NULL),
(1427, 18, '2026-03-09', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1428, 18, '2026-03-16', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1429, 18, '2026-03-23', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1430, 18, '2026-03-30', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1431, 18, '2026-04-06', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1432, 18, '2026-04-13', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1433, 18, '2026-04-20', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1434, 18, '2026-04-27', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1435, 18, '2026-05-04', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1436, 18, '2026-05-11', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1437, 18, '2026-05-18', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1438, 18, '2026-05-25', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1439, 18, '2026-06-01', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1440, 18, '2026-06-08', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1441, 18, '2026-06-15', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1442, 18, '2026-06-22', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1443, 18, '2026-06-29', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1444, 18, '2026-07-06', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1445, 18, '2026-07-13', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1446, 18, '2026-07-20', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1447, 18, '2026-07-27', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1448, 18, '2026-08-03', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1449, 18, '2026-08-10', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1450, 18, '2026-08-17', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1451, 18, '2026-08-24', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1452, 18, '2026-08-31', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1453, 18, '2026-09-07', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1454, 18, '2026-09-14', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1455, 18, '2026-09-21', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1456, 18, '2026-09-28', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1457, 18, '2026-10-05', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1458, 18, '2026-10-12', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1459, 18, '2026-10-19', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1460, 18, '2026-10-26', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1461, 18, '2026-11-02', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1462, 18, '2026-11-09', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1463, 18, '2026-11-16', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1464, 18, '2026-11-23', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1465, 18, '2026-11-30', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1466, 18, '2026-12-07', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1467, 18, '2026-12-14', 'pendiente', 8, 13, 8, NULL, NULL, NULL, NULL),
(1632, 19, '2026-03-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1633, 19, '2026-03-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1634, 19, '2026-03-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1635, 19, '2026-03-31', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1636, 19, '2026-04-07', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1637, 19, '2026-04-14', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1638, 19, '2026-04-21', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1639, 19, '2026-04-28', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1640, 19, '2026-05-05', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1641, 19, '2026-05-12', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1642, 19, '2026-05-19', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1643, 19, '2026-05-26', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1644, 19, '2026-06-02', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1645, 19, '2026-06-09', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1646, 19, '2026-06-16', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1647, 19, '2026-06-23', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1648, 19, '2026-06-30', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1649, 19, '2026-07-07', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1650, 19, '2026-07-14', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1651, 19, '2026-07-21', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1652, 19, '2026-07-28', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1653, 19, '2026-08-04', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1654, 19, '2026-08-11', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1655, 19, '2026-08-18', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1656, 19, '2026-08-25', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1657, 19, '2026-09-01', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1658, 19, '2026-09-08', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1659, 19, '2026-09-15', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1660, 19, '2026-09-22', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1661, 19, '2026-09-29', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1662, 19, '2026-10-06', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1663, 19, '2026-10-13', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1664, 19, '2026-10-20', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1665, 19, '2026-10-27', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1666, 19, '2026-11-03', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1667, 19, '2026-11-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1668, 19, '2026-11-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1669, 19, '2026-11-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1670, 19, '2026-12-01', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1671, 19, '2026-12-08', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(1876, 20, '2026-03-10', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1877, 20, '2026-03-17', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1878, 20, '2026-03-24', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1879, 20, '2026-03-31', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1880, 20, '2026-04-07', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1881, 20, '2026-04-14', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1882, 20, '2026-04-21', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1883, 20, '2026-04-28', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1884, 20, '2026-05-05', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1885, 20, '2026-05-12', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1886, 20, '2026-05-19', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1887, 20, '2026-05-26', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1888, 20, '2026-06-02', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1889, 20, '2026-06-09', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1890, 20, '2026-06-16', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1891, 20, '2026-06-23', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1892, 20, '2026-06-30', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1893, 20, '2026-07-07', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1894, 20, '2026-07-14', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1895, 20, '2026-07-21', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1896, 20, '2026-07-28', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1897, 20, '2026-08-04', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1898, 20, '2026-08-11', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1899, 20, '2026-08-18', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1900, 20, '2026-08-25', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1901, 20, '2026-09-01', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1902, 20, '2026-09-08', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1903, 20, '2026-09-15', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1904, 20, '2026-09-22', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1905, 20, '2026-09-29', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1906, 20, '2026-10-06', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1907, 20, '2026-10-13', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1908, 20, '2026-10-20', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1909, 20, '2026-10-27', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1910, 20, '2026-11-03', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1911, 20, '2026-11-10', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1912, 20, '2026-11-17', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1913, 20, '2026-11-24', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1914, 20, '2026-12-01', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(1915, 20, '2026-12-08', 'pendiente', 8, 12, 8, NULL, NULL, NULL, NULL),
(2160, 21, '2026-03-10', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2161, 21, '2026-03-17', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2162, 21, '2026-03-24', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2163, 21, '2026-03-31', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2164, 21, '2026-04-07', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2165, 21, '2026-04-14', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2166, 21, '2026-04-21', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2167, 21, '2026-04-28', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2168, 21, '2026-05-05', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2169, 21, '2026-05-12', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2170, 21, '2026-05-19', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2171, 21, '2026-05-26', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2172, 21, '2026-06-02', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2173, 21, '2026-06-09', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2174, 21, '2026-06-16', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2175, 21, '2026-06-23', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2176, 21, '2026-06-30', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2177, 21, '2026-07-07', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2178, 21, '2026-07-14', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2179, 21, '2026-07-21', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2180, 21, '2026-07-28', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2181, 21, '2026-08-04', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2182, 21, '2026-08-11', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2183, 21, '2026-08-18', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2184, 21, '2026-08-25', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2185, 21, '2026-09-01', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2186, 21, '2026-09-08', 'reasignada', 8, 10, 8, 1, NULL, '2026-09-02 14:29:40', 44),
(2187, 21, '2026-09-15', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2188, 21, '2026-09-22', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2189, 21, '2026-09-29', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2190, 21, '2026-10-06', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2191, 21, '2026-10-13', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2192, 21, '2026-10-20', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2193, 21, '2026-10-27', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2194, 21, '2026-11-03', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2195, 21, '2026-11-10', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2196, 21, '2026-11-17', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2197, 21, '2026-11-24', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2198, 21, '2026-12-01', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2199, 21, '2026-12-08', 'pendiente', 8, 10, 8, NULL, NULL, NULL, NULL),
(2452, 22, '2026-04-07', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2453, 22, '2026-04-14', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2454, 22, '2026-04-21', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2455, 22, '2026-04-28', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2456, 22, '2026-05-05', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2457, 22, '2026-05-12', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2458, 22, '2026-05-19', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2459, 22, '2026-05-26', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2460, 22, '2026-06-02', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2461, 22, '2026-06-09', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2462, 22, '2026-06-16', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2463, 22, '2026-06-23', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2464, 22, '2026-06-30', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2465, 22, '2026-07-07', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2466, 22, '2026-07-14', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2467, 22, '2026-07-21', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2468, 22, '2026-07-28', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2469, 22, '2026-08-04', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2470, 22, '2026-08-11', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2471, 22, '2026-08-18', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2472, 22, '2026-08-25', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2473, 22, '2026-09-01', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2474, 22, '2026-09-08', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2475, 22, '2026-09-15', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2476, 22, '2026-09-22', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2477, 22, '2026-09-29', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2478, 22, '2026-10-06', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2479, 22, '2026-10-13', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2480, 22, '2026-10-20', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2481, 22, '2026-10-27', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2482, 22, '2026-11-03', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2483, 22, '2026-11-10', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2484, 22, '2026-11-17', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2485, 22, '2026-11-24', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2486, 22, '2026-12-01', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2487, 22, '2026-12-08', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2776, 23, '2026-04-09', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2777, 23, '2026-04-16', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2778, 23, '2026-04-23', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2779, 23, '2026-04-30', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2780, 23, '2026-05-07', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2781, 23, '2026-05-14', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2782, 23, '2026-05-21', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2783, 23, '2026-05-28', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2784, 23, '2026-06-04', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2785, 23, '2026-06-11', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2786, 23, '2026-06-18', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2787, 23, '2026-06-25', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2788, 23, '2026-07-02', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2789, 23, '2026-07-09', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2790, 23, '2026-07-16', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2791, 23, '2026-07-23', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2792, 23, '2026-07-30', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2793, 23, '2026-08-06', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2794, 23, '2026-08-13', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2795, 23, '2026-08-20', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2796, 23, '2026-08-27', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2797, 23, '2026-09-03', 'utilizada', 3, 17, 7, 1, '', '2026-09-03 15:51:33', NULL),
(2798, 23, '2026-09-10', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2799, 23, '2026-09-17', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2800, 23, '2026-09-24', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2801, 23, '2026-10-01', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2802, 23, '2026-10-08', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2803, 23, '2026-10-15', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2804, 23, '2026-10-22', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2805, 23, '2026-10-29', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2806, 23, '2026-11-05', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2807, 23, '2026-11-12', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2808, 23, '2026-11-19', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2809, 23, '2026-11-26', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2810, 23, '2026-12-03', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(2811, 23, '2026-12-10', 'pendiente', 3, 17, 7, NULL, NULL, NULL, NULL),
(3168, 24, '2026-03-06', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3169, 24, '2026-03-13', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3170, 24, '2026-03-20', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3171, 24, '2026-03-27', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3172, 24, '2026-04-03', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3173, 24, '2026-04-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3174, 24, '2026-04-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3175, 24, '2026-04-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3176, 24, '2026-05-01', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3177, 24, '2026-05-08', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3178, 24, '2026-05-15', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3179, 24, '2026-05-22', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3180, 24, '2026-05-29', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3181, 24, '2026-06-05', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3182, 24, '2026-06-12', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3183, 24, '2026-06-19', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3184, 24, '2026-06-26', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3185, 24, '2026-07-03', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3186, 24, '2026-07-10', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3187, 24, '2026-07-17', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3188, 24, '2026-07-24', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3189, 24, '2026-07-31', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3190, 24, '2026-08-07', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3191, 24, '2026-08-14', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3192, 24, '2026-08-21', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3193, 24, '2026-08-28', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3194, 24, '2026-09-04', 'utilizada', 8, 9, 8, 1, '', '2026-09-04 13:03:44', NULL),
(3195, 24, '2026-09-11', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3196, 24, '2026-09-18', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3197, 24, '2026-09-25', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3198, 24, '2026-10-02', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3199, 24, '2026-10-09', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3200, 24, '2026-10-16', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3201, 24, '2026-10-23', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3202, 24, '2026-10-30', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3203, 24, '2026-11-06', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3204, 24, '2026-11-13', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3205, 24, '2026-11-20', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3206, 24, '2026-11-27', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3207, 24, '2026-12-04', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL),
(3208, 24, '2026-12-11', 'pendiente', 8, 9, 8, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos`
--

CREATE TABLE `recursos` (
  `id` int(11) NOT NULL,
  `nombre_recurso` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `recursos`
--

INSERT INTO `recursos` (`id`, `nombre_recurso`) VALUES
(1, 'Computador'),
(2, 'Internet'),
(3, 'Office PowerPoint'),
(4, 'Office Word'),
(5, 'ChatGPT'),
(6, 'Gemini'),
(7, 'Gmail');

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
(35, 4, 1, 15, 2, 2, '2026-08-28', '', '', 1, NULL, 0, 'cancelada', '2026-08-26 14:25:48', '2026-08-28 11:50:12', 'sub2', NULL),
(36, 1, 1, 12, 3, 2, '2026-09-02', '', '', 0, NULL, 0, 'cancelada', '2026-08-28 11:26:13', '2026-09-02 11:50:12', 'completo', NULL),
(37, 1, 1, 11, 3, 4, '2026-09-02', 'Buscar información de un animal a elección, describiéndolo físicamente y su hábitat.', 'Buscar información de su animal favorito.', 0, NULL, 0, 'utilizada', '2026-08-28 11:26:34', '2026-09-02 14:55:42', 'completo', NULL),
(38, 2, 1, 14, 5, 3, '2026-08-31', '', '', 0, NULL, 0, 'cancelada', '2026-08-31 11:12:49', '2026-08-31 12:45:12', 'sub1', NULL),
(39, 1, 1, 16, 3, 3, '2026-09-02', 'Planificar un viaje usando Going to', 'Buscar información del país elegido', 0, NULL, 0, 'utilizada', '2026-09-02 08:49:59', '2026-09-02 13:33:06', 'completo', NULL),
(40, 1, 1, 15, 3, 1, '2026-09-03', 'Planificar un viaje, usando Going To.', 'Buscar información de un país.', 0, NULL, 0, 'utilizada', '2026-09-02 08:50:54', '2026-09-03 12:10:51', 'completo', NULL),
(41, 1, 1, 16, 3, 3, '2026-09-03', 'Escribir una breve biografía de un deportista.', 'Buscar información de un deportista.', 0, NULL, 0, 'utilizada', '2026-09-02 08:51:07', '2026-09-03 12:13:31', 'completo', NULL),
(42, 1, 1, 13, 3, 2, '2026-09-03', 'Planificar un viaje, usando Going To.', 'Buscar información de un país.', 0, NULL, 0, 'utilizada', '2026-09-02 08:52:01', '2026-09-03 12:12:28', 'completo', NULL),
(43, 9, 1, 15, 6, 2, '2026-09-08', '', '', 0, NULL, 0, 'reservada', '2026-09-02 13:29:16', '2026-09-02 13:29:16', 'sub2', NULL),
(44, 9, 1, 11, 6, 3, '2026-09-08', '', '', 0, NULL, 0, 'reservada', '2026-09-02 14:29:40', '2026-09-02 14:29:40', 'sub2', NULL),
(45, 1, 1, 13, 3, 1, '2026-09-04', 'Escribir una breve biografía de un deportista.', 'Buscar información.', 0, NULL, 0, 'utilizada', '2026-09-03 11:26:45', '2026-09-04 10:03:49', 'completo', NULL),
(47, 1, 1, 12, 3, 2, '2026-09-09', '', '', 0, NULL, 0, 'reservada', '2026-09-04 09:58:00', '2026-09-04 09:58:00', 'completo', NULL),
(48, 1, 1, 11, 3, 4, '2026-09-09', '', '', 0, NULL, 0, 'reservada', '2026-09-04 09:58:18', '2026-09-04 09:58:18', 'completo', NULL),
(49, 4, 1, 14, 2, 3, '2026-09-04', 'Comprender que es el acento diacrítico.', 'Buscar monosílabas, con y sin tilde.', 0, NULL, 0, 'utilizada', '2026-09-04 10:54:08', '2026-09-04 13:56:16', 'completo', NULL);

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
(1, 'José A.', 'Fernández Concha', 'jfernandezconcha@gmail.com', 'Josesinis', '$2y$10$6/4FA8ny1xObuxbWtzI6J.6fY/2rc.KaITAU2aCXk4gTSFu/tEQ/2', 'superdmin', 1, '2026-09-04 10:53:44');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `bitacora_recursos`
--
ALTER TABLE `bitacora_recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `docentes_asignaturas`
--
ALTER TABLE `docentes_asignaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3209;

--
-- AUTO_INCREMENT de la tabla `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

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
