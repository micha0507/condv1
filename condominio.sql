-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-01-2026 a las 13:35:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `condominio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id_admin` int(11) NOT NULL,
  `usuario_admin` varchar(50) NOT NULL,
  `nombre_completo_admin` varchar(50) NOT NULL,
  `rif_admin` varchar(50) NOT NULL,
  `email_admin` varchar(256) NOT NULL,
  `password_admin` varchar(50) NOT NULL,
  `rol_admin` enum('Administrador') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_admin`, `usuario_admin`, `nombre_completo_admin`, `rif_admin`, `email_admin`, `password_admin`, `rol_admin`) VALUES
(1, 'UrbTrinitarias', 'Luis Guillermo Ferrer Ramirez', 'V314548740', 'lgfr03@gmail.com', '1234', 'Administrador'),
(7, 'UrbLaMarona', 'Cesar Ferrer', 'V31459447', 'prueba1234@gmail.com', '1234', 'Administrador'),
(8, 'UrbOrquidea', 'Nora Ramirez', 'V7902201', 'noramirep18@gmail.com', '1234', 'Administrador'),
(9, 'UrbParqueSol', 'Maryam Miranda', 'V31371985', 'maryamvictoriaa@gmail.com', '1234', 'Administrador'),
(10, 'UrbMonteClaro', 'Nohelia Ferrer', 'V28124173', 'noheliavferrer@gmail.com', '1234', 'Administrador'),
(12, 'UrbLaVereda', 'Luis Ferrer Alaña', 'V6583137', 'luchoferrerala@gmail.com', '1234', 'Administrador'),
(13, 'UrbPrueba', 'Prueba', 'V0000000', 'prueba1234@gmail.com', '1234', 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_condominio`
--

CREATE TABLE `datos_condominio` (
  `id` int(11) NOT NULL,
  `rif` varchar(15) DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `direccion` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_condominio`
--

INSERT INTO `datos_condominio` (`id`, `rif`, `nombre`, `direccion`) VALUES
(1, 'J294380026', 'URBANIZACION LA MAROMA', 'KM 2 AV BOLIVAR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factor`
--

CREATE TABLE `factor` (
  `factor` decimal(10,2) DEFAULT NULL,
  `monto_mensual` decimal(10,2) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factor`
--

INSERT INTO `factor` (`factor`, `monto_mensual`, `fecha`, `id`) VALUES
(63.00, 5.00, '2025-04-12 18:09:33', 5),
(104.32, 5.00, '2025-04-12 18:13:16', 7),
(63.00, 5.00, '2025-04-12 18:55:19', 9),
(63.00, 5.00, '2025-04-12 18:56:06', 66),
(63.21, 5.00, '2025-04-12 18:56:37', 67),
(64.21, 5.00, '2025-04-12 18:58:20', 68),
(63.00, 5.00, '2025-04-18 12:14:24', 69),
(104.00, 5.00, '2025-04-18 12:41:08', 70),
(90.00, 5.00, '2025-04-18 12:42:17', 71),
(62.00, 5.00, '2025-04-18 12:46:51', 72),
(80.95, 5.00, '2025-04-19 11:34:31', 73),
(90.00, 5.00, '2025-04-19 18:28:39', 74),
(80.95, 5.00, '2025-04-20 18:52:14', 75),
(90.00, 5.00, '2025-04-20 18:52:43', 76),
(90.00, 5.00, '2025-04-26 18:24:12', 77),
(95.00, 5.00, '2025-05-26 16:04:53', 78),
(95.00, 5.00, '2025-05-26 16:17:41', 79),
(95.24, 5.00, '2025-05-26 16:18:01', 80),
(95.24, 5.00, '2025-05-26 16:21:21', 81),
(95.24, 5.00, '2025-05-26 16:21:43', 82),
(95.24, 5.00, '2025-05-26 16:22:28', 83),
(95.24, 5.00, '2025-05-26 16:22:30', 84),
(95.24, 5.00, '2025-05-26 16:22:33', 85),
(95.24, 5.00, '2025-05-26 16:22:40', 86),
(95.24, 5.00, '2025-05-27 16:39:23', 87),
(97.42, 5.00, '2025-06-04 11:17:04', 88),
(106.17, 5.00, '2025-06-25 14:59:51', 89),
(108.19, 5.00, '2025-06-30 18:56:02', 90),
(236.46, 5.00, '2025-11-16 16:15:57', 91),
(341.74, 5.00, '2026-01-15 21:05:18', 92);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(10) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `fecha_emision` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_vencimiento` date NOT NULL,
  `periodo` varchar(20) DEFAULT NULL,
  `monto` int(11) DEFAULT NULL,
  `id_residencia` int(11) NOT NULL,
  `status` enum('Pendiente','Pagado','','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id_factura`, `propietario_id`, `fecha_emision`, `fecha_vencimiento`, `periodo`, `monto`, `id_residencia`, `status`) VALUES
(1, 3, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 1, 'Pagado'),
(2, 3, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 2, 'Pagado'),
(3, 3, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 3, 'Pagado'),
(4, 36, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 4, 'Pagado'),
(5, 32, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 5, 'Pendiente'),
(6, 35, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 6, 'Pendiente'),
(7, 34, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 7, 'Pagado'),
(8, 3, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 29, 'Pagado'),
(9, 32, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 30, 'Pendiente'),
(10, 33, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 31, 'Pendiente'),
(11, 34, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 32, 'Pagado'),
(12, 35, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 33, 'Pendiente'),
(13, 36, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 34, 'Pagado'),
(14, 37, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 35, 'Pagado'),
(15, 38, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 36, 'Pendiente'),
(16, 39, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 37, 'Pendiente'),
(17, 40, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 38, 'Pendiente'),
(18, 41, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 39, 'Pendiente'),
(19, 42, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 40, 'Pendiente'),
(20, 43, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 41, 'Pendiente'),
(21, 44, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 42, 'Pendiente'),
(22, 45, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 43, 'Pendiente'),
(23, 46, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 44, 'Pendiente'),
(24, 47, '2025-04-27 00:15:55', '2025-05-12', '04-2025', 5, 45, 'Pendiente'),
(25, 3, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 1, ''),
(26, 3, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 2, ''),
(27, 3, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 3, ''),
(28, 36, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 4, 'Pagado'),
(29, 32, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 5, 'Pendiente'),
(30, 35, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 6, 'Pendiente'),
(31, 34, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 7, 'Pagado'),
(32, 3, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 29, ''),
(33, 32, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 30, 'Pendiente'),
(34, 33, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 31, 'Pendiente'),
(35, 34, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 32, ''),
(36, 35, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 33, 'Pendiente'),
(37, 36, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 34, 'Pagado'),
(38, 37, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 35, 'Pendiente'),
(39, 38, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 36, 'Pendiente'),
(40, 39, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 37, 'Pendiente'),
(41, 40, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 38, 'Pendiente'),
(42, 41, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 39, 'Pendiente'),
(43, 42, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 40, 'Pendiente'),
(44, 43, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 41, 'Pendiente'),
(45, 44, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 42, 'Pendiente'),
(46, 45, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 43, 'Pendiente'),
(47, 46, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 44, 'Pendiente'),
(48, 47, '2025-05-09 19:00:58', '2025-05-24', '05-2025', 5, 45, 'Pendiente'),
(49, 3, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 1, 'Pagado'),
(50, 3, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 2, ''),
(51, 3, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 3, ''),
(52, 36, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 4, 'Pagado'),
(53, 32, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 5, 'Pendiente'),
(54, 35, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 6, 'Pendiente'),
(55, 34, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 7, 'Pagado'),
(56, 3, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 29, ''),
(57, 32, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 30, 'Pendiente'),
(58, 33, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 31, 'Pendiente'),
(59, 34, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 32, 'Pagado'),
(60, 35, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 33, 'Pendiente'),
(61, 36, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 34, 'Pagado'),
(62, 37, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 35, 'Pendiente'),
(63, 38, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 36, 'Pendiente'),
(64, 39, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 37, 'Pendiente'),
(65, 40, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 38, 'Pendiente'),
(66, 41, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 39, 'Pendiente'),
(67, 42, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 40, 'Pendiente'),
(68, 43, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 41, 'Pendiente'),
(69, 44, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 42, 'Pendiente'),
(70, 45, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 43, 'Pendiente'),
(71, 46, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 44, 'Pendiente'),
(72, 47, '2025-05-27 00:00:08', '2025-06-11', '05-2025', 5, 45, 'Pendiente'),
(73, 3, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 1, 'Pagado'),
(74, 3, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 2, 'Pagado'),
(75, 3, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 3, 'Pagado'),
(76, 36, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 4, 'Pagado'),
(77, 32, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 5, 'Pendiente'),
(78, 35, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 6, 'Pendiente'),
(79, 34, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 7, 'Pagado'),
(80, 3, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 29, 'Pagado'),
(81, 32, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 30, 'Pendiente'),
(82, 33, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 31, 'Pendiente'),
(83, 34, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 32, 'Pendiente'),
(84, 35, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 33, 'Pendiente'),
(85, 36, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 34, ''),
(86, 37, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 35, 'Pendiente'),
(87, 38, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 36, 'Pendiente'),
(88, 39, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 37, 'Pendiente'),
(89, 40, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 38, 'Pendiente'),
(90, 41, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 39, 'Pendiente'),
(91, 42, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 40, 'Pendiente'),
(92, 43, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 41, 'Pendiente'),
(93, 44, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 42, 'Pendiente'),
(94, 45, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 43, 'Pendiente'),
(95, 46, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 44, 'Pendiente'),
(96, 47, '2025-06-24 16:20:24', '2025-07-09', '06-2025', 5, 45, 'Pendiente'),
(97, 3, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 1, 'Pagado'),
(98, 3, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 2, 'Pendiente'),
(99, 3, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 3, 'Pendiente'),
(100, 36, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 4, 'Pagado'),
(101, 32, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 5, 'Pendiente'),
(102, 35, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 6, 'Pendiente'),
(103, 34, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 7, 'Pendiente'),
(104, 3, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 29, 'Pendiente'),
(105, 32, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 30, 'Pendiente'),
(106, 33, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 31, 'Pendiente'),
(107, 34, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 32, 'Pendiente'),
(108, 35, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 33, 'Pendiente'),
(109, 36, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 34, 'Pagado'),
(110, 37, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 35, 'Pendiente'),
(111, 38, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 36, 'Pendiente'),
(112, 39, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 37, 'Pendiente'),
(113, 40, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 38, 'Pendiente'),
(114, 41, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 39, 'Pendiente'),
(115, 42, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 40, 'Pendiente'),
(116, 43, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 41, 'Pendiente'),
(117, 44, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 42, 'Pendiente'),
(118, 45, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 43, 'Pendiente'),
(119, 46, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 44, 'Pendiente'),
(120, 47, '2026-01-16 01:06:09', '2026-01-31', '01-2026', 5, 45, 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_eventuales`
--

CREATE TABLE `gastos_eventuales` (
  `id_gasto` int(11) NOT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `categoria` varchar(100) NOT NULL,
  `monto` float NOT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos_eventuales`
--

INSERT INTO `gastos_eventuales` (`id_gasto`, `concepto`, `categoria`, `monto`, `fecha`) VALUES
(2, 'Poda de areas verdes', 'Limpieza', 343, '2025-05-19'),
(3, 'Poda de areas verdes3', 'Limpieza', 400, '2025-05-20'),
(4, 'Poda de areas verdes3', 'Limpieza', 400, '2025-05-20'),
(5, 'Mantenimiento a bomba de agua ', 'Mantenimiento', 2000, '2025-05-26'),
(6, 'Pago jardinero', 'Limpieza', 150, '2025-06-21'),
(7, 'pago para aseo urbano', 'Limpieza', 200, '2025-06-24'),
(8, 'limpieza', 'Limpieza', 200, '2025-06-18'),
(9, 'Alumbrado ', 'Servicios Públicos', 500, '2025-11-16'),
(10, 'Alumbrado ', 'Mantenimiento', 4, '2026-01-13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(10) NOT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_registro` date DEFAULT current_timestamp(),
  `status` enum('Pendiente','Validado','Anulado') DEFAULT NULL,
  `id_propietario` int(10) NOT NULL,
  `monto` double DEFAULT NULL,
  `referencia` varchar(60) DEFAULT NULL,
  `factura_afectada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `fecha`, `fecha_registro`, `status`, `id_propietario`, `monto`, `referencia`, `factura_afectada`) VALUES
(39, '2025-06-04', '2025-06-04', 'Pendiente', 43, 476.2, '33111', 68),
(40, '2025-06-04', '2025-06-04', 'Pendiente', 34, 476.2, 'lllmlmljkljljljlkhfgtfyctrctctyrctycrtyrctyrcrtc', 59),
(41, '2025-06-04', '2025-06-04', 'Pendiente', 34, 487.1, '122121', 59),
(42, '2025-06-18', '2025-06-18', 'Validado', 36, 487.1, 'Jfidir', 52),
(43, '2025-06-24', '2025-06-24', 'Validado', 3, 5, 'vwvwv', 73),
(44, '2025-06-23', '2025-06-24', 'Validado', 3, 5, 'fefe', 74),
(45, '2025-06-30', '2025-06-24', 'Validado', 36, 5, 'fwfw', 61),
(46, '2025-06-23', '2025-06-24', 'Validado', 37, 5, 'vwvwv', 14),
(47, '2025-06-29', '2025-06-24', 'Validado', 3, 5, 'vwvwv', 75),
(48, '2025-06-24', '2025-06-24', 'Validado', 3, 5, 'vsvsvs', 80),
(49, '2025-06-24', '2025-06-24', 'Validado', 34, 5, 'Fghk', 55),
(50, '2025-06-17', '2025-06-24', 'Validado', 34, 5, 'Snsndn', 79),
(51, '2025-06-24', '2025-06-24', 'Validado', 34, 5, 'FSDFS', 55),
(52, '2025-06-25', '2025-06-24', 'Validado', 34, 5, 'ERWERW', 59),
(53, '2025-06-24', '2025-06-25', 'Validado', 36, 5, 'rwer', 76),
(54, '2025-06-25', '2025-06-25', 'Pendiente', 36, 487.1, 'gfg', 85),
(55, '2025-06-25', '2025-06-25', 'Pendiente', 36, 487.1, 'aaa', 85),
(56, '2025-06-25', '2025-06-25', 'Pendiente', 34, 5, 'rrffede', 83),
(57, '2025-06-30', '2025-06-30', 'Pendiente', 35, 540.95, '123', 36),
(58, '2025-06-30', '2025-06-30', 'Pendiente', 35, 540.95, '123', 54),
(59, '2025-06-30', '2025-06-30', 'Pendiente', 35, 540.95, '123', 60),
(60, '2025-06-30', '2025-06-30', 'Pendiente', 35, 540.95, '123', 78),
(61, '2025-06-30', '2025-06-30', 'Pendiente', 35, 540.95, '123', 84),
(62, '2025-11-16', '2025-11-17', 'Validado', 36, 1182.3, '31454874', 85),
(63, '2026-01-15', '2026-01-16', 'Validado', 36, 1708.7, '111', 100),
(64, '2026-01-15', '2026-01-16', 'Validado', 36, 1708.7, '1', 100),
(65, '2026-01-15', '2026-01-16', 'Validado', 36, 1708.7, '1', 109),
(66, '2026-01-15', '2026-01-16', 'Validado', 3, 1708.7, '1', 97);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

CREATE TABLE `post` (
  `id_post` int(10) NOT NULL,
  `contenido` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `post`
--

INSERT INTO `post` (`id_post`, `contenido`) VALUES
(11, '&lt;p&gt;michael&lt;/p&gt;'),
(12, '&lt;p&gt;michael&lt;/p&gt;'),
(15, '&lt;p&gt;Luis Ferrer&lt;/p&gt;'),
(16, '&lt;p&gt;El perro&lt;/p&gt;'),
(17, '&lt;p&gt;Michael &lt;strong&gt;Ramirez&lt;/strong&gt;&lt;/p&gt;'),
(18, '<p>Michael <strong>Ramirez</strong></p>'),
(19, '&lt;p&gt;¡Escribe aquí tu contenido!&lt;/p&gt;'),
(20, '&lt;p&gt;sdcvsdvsdvsd&lt;/p&gt;'),
(21, '&lt;p&gt;¡Escribe aquí tu contenido!&lt;/p&gt;'),
(22, '<p>¡Escribe aquí tu contenido!</p>'),
(23, '<p>El editor <strong>CKEditor 5</strong> debería interpretar este código HTML y mostrarte la publicación con el formato enriquecido (título como encabezado grande, párrafos, lista con viñetas y texto en negrita).</p><ol><li>&nbsp;Luego, cuando guardes esta publicación utilizando el formulario de tu editor, el código HTML se guardará en tu base de datos (si ya has realizado la modificación para no escapar el HTML en el proceso de guardado). Al mostrar esta publicación en tu sitio web, el navegador interpretará el HTML y mostrará el contenido con el formato correcto.</li></ol><p>Fuentes y contenido relacionado</p><p>&nbsp;</p>'),
(24, '<p><strong>Noticias y Actualizaciones del Condominio La Maroma - Marzo 2025</strong></p><p>Estimados residentes de La Maroma,</p><p>Esperamos que este boletín los encuentre bien. Queremos mantenerlos informados sobre las últimas noticias y actualizaciones importantes para nuestra comunidad.</p><p><strong>Asamblea General de Condominio - Recordatorio</strong></p><p>Les recordamos que la próxima Asamblea General de Condominio se llevará a cabo el sábado 12 de abril de 2025, a las 10:00 AM en el salón de fiestas. Su asistencia es crucial para discutir temas importantes relacionados con el mantenimiento, la seguridad y las mejoras de nuestro condominio. La agenda detallada será enviada por correo electrónico la próxima semana.</p><p><strong>Mantenimiento de Áreas Comunes</strong></p><p>El equipo de mantenimiento estará realizando trabajos de jardinería y mejoras en el área de la piscina durante la semana del 1 al 5 de abril de 2025. Les pedimos su comprensión y paciencia mientras se llevan a cabo estas labores para embellecer nuestras áreas comunes. Por favor, tengan precaución al transitar por estas zonas durante ese período.</p><p><strong>Recordatorio de Normas de Convivencia</strong></p><p>Queremos recordar a todos los residentes la importancia de respetar las normas de convivencia del condominio, especialmente en lo referente a los niveles de ruido después de las 10:00 PM y el correcto uso de los contenedores de basura. Mantener un ambiente de respeto y armonía es fundamental para el bienestar de todos.</p><p><strong>Cortes de Energía Programados - Información Importante</strong></p><p>Hemos sido notificados por la compañía eléctrica local sobre cortes de energía programados para el martes 8 de abril de 2025, entre las 2:00 PM y las 5:00 PM, debido a trabajos de mantenimiento en la red. Les recomendamos tomar las precauciones necesarias.</p><p><strong>Actividad Social: Tarde de Juegos de Mesa</strong></p><p>¡Los invitamos a participar en nuestra próxima tarde de juegos de mesa el sábado 20 de abril de 2025, a partir de las 3:00 PM en el área social! Será una excelente oportunidad para compartir y socializar con sus vecinos. Traigan sus juegos favoritos y ¡prepárense para divertirse!</p><p>Agradecemos su colaboración y les recordamos que la administración está a su disposición para cualquier consulta o inquietud.</p><p>Atentamente,</p><p>La Administración del Condominio La Maroma</p>'),
(25, '<p>¡Escribe aquí tu contenido!</p>'),
(26, '<p>Hola Vecinos</p>'),
(27, '<p>Hola <strong>Reimary</strong></p>'),
(28, '<p>¡Escribe aquí tu contenido!</p>'),
(29, '<p>Hola a todos , vecinos</p>'),
(30, '<p>¡Escribe aquí tu<strong> contenido!</strong></p>'),
(31, '<p>hola cara e nolas</p>'),
(32, '<p>Saludos a todos los propietarios, excelente dia.<br>Se informa que el dia de hoy se estará realizando mantenimiento a la bomba de agua, tomar previsiones.</p>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietario`
--

CREATE TABLE `propietario` (
  `id` int(11) NOT NULL,
  `rif` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `email_propietario` varchar(256) NOT NULL,
  `rol` enum('Propietario') NOT NULL,
  `reset_token` varchar(256) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propietario`
--

INSERT INTO `propietario` (`id`, `rif`, `nombre`, `apellido`, `usuario`, `pass`, `email_propietario`, `rol`, `reset_token`) VALUES
(3, 'V26718868', 'Michael', 'Ramirez', 'micha0507', '12345678', 'micha@gmail.com', 'Propietario', '0'),
(32, 'VJ29438002', 'JOSE ', 'FINOL', 'micha', '1234565656', 'jfinol@gmail.com', 'Propietario', '0'),
(33, 'G30534653', 'Adriana', 'Borrero', 'adriana', '321321321', 'jfinoal@gmail.com', 'Propietario', '0'),
(34, 'V31634833', 'JOSE GREGORIO', 'FINOL', 'jfinol', '12345678', 'jfinol@gmail.com', 'Propietario', '0'),
(35, 'V34234123', 'fsdf', 'sdfsd', 'fsdf2', 'fsdfsdf2', 'fsdfsd@gmail.com', 'Propietario', '0'),
(36, 'V31454874', 'Luis', 'Ferrer', 'lferrer', '12345678', '12345678@gmail.com', 'Propietario', '0'),
(37, 'V31596100', 'Reimary', 'Rojas', 'rrojas', '12345678', 'rrojas@gmail.com', 'Propietario', '0'),
(38, 'V-12345678', 'Ana', 'Pérez', 'anaperez', 'Segur0Clave', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(39, 'J-98765432', 'Carlos', 'Gómez', 'carlosg', 'Micontras3ña', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(40, 'E-56789012', 'Sofía', 'Rodríguez', 'sofiaro', 'P@$$wOrd123', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(41, 'G-23456789', 'Manuel', 'Vargas', 'manuelv', '12345Abc', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(42, 'V-87654321', 'Isabella', 'Torres', 'isatorres', 'Clav3Segura', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(43, 'J-34567890', 'Diego', 'Castro', 'diegoc', 'PasswOrd!', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(44, 'E-90123456', 'Valeria', 'López', 'valelopez', 'S3cur3Pass', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(45, 'G-45678901', 'Andrés', 'Fernández', 'andresf', 'P@$$wOrd456', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(46, 'V-65432109', 'Camila', 'Suárez', 'camilas', 'Cl@veSecreta', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(47, 'J-01234567', 'Ricardo', 'Jiménez', 'ricardoj', 'Contras3ña!', '[dirección de correo electrónico eliminada]', 'Propietario', '0'),
(48, 'V1', 'Juan', 'Ilario', 'ilariojuan', '12345678', 'tecnostarr22@gmail.com', 'Propietario', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `residencias`
--

CREATE TABLE `residencias` (
  `id` int(11) NOT NULL,
  `nro` varchar(10) NOT NULL,
  `id_propietario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `residencias`
--

INSERT INTO `residencias` (`id`, `nro`, `id_propietario`) VALUES
(1, 'K33', 3),
(2, 'K43', 3),
(3, 'K12', 3),
(4, 'K321', 36),
(5, 'Q123', 32),
(6, 'K54', 35),
(7, 'K55', 34),
(29, 'A-101', 3),
(30, 'B-205', 32),
(31, 'C-15', 33),
(32, 'D-402', 34),
(33, 'E-78', 35),
(34, 'F-111', 36),
(35, 'G-330', 37),
(36, 'H-50', 38),
(37, 'I-222', 39),
(38, 'J-100', 40),
(39, 'K-303', 41),
(40, 'L-21', 42),
(41, 'M-180', 43),
(42, 'N-505', 44),
(43, 'O-99', 45),
(44, 'P-250', 46),
(45, 'Q-77', 47);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indices de la tabla `datos_condominio`
--
ALTER TABLE `datos_condominio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `factor`
--
ALTER TABLE `factor`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `propietario` (`propietario_id`);

--
-- Indices de la tabla `gastos_eventuales`
--
ALTER TABLE `gastos_eventuales`
  ADD PRIMARY KEY (`id_gasto`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_propietario` (`id_propietario`),
  ADD KEY `factura_afectada` (`factura_afectada`);

--
-- Indices de la tabla `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id_post`);

--
-- Indices de la tabla `propietario`
--
ALTER TABLE `propietario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `residencias`
--
ALTER TABLE `residencias`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `datos_condominio`
--
ALTER TABLE `datos_condominio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `factor`
--
ALTER TABLE `factor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT de la tabla `gastos_eventuales`
--
ALTER TABLE `gastos_eventuales`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `post`
--
ALTER TABLE `post`
  MODIFY `id_post` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `propietario`
--
ALTER TABLE `propietario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `residencias`
--
ALTER TABLE `residencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `propietario` FOREIGN KEY (`propietario_id`) REFERENCES `propietario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `id_propietario` FOREIGN KEY (`id_propietario`) REFERENCES `propietario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
