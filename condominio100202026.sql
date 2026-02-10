-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-02-2026 a las 13:33:05
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
  `password_admin` varchar(256) NOT NULL,
  `rol_admin` enum('Administrador') NOT NULL,
  `reset_token` varchar(256) NOT NULL,
  `nombre_condominio` varchar(256) NOT NULL,
  `direccion_condominio` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_admin`, `usuario_admin`, `nombre_completo_admin`, `rif_admin`, `email_admin`, `password_admin`, `rol_admin`, `reset_token`, `nombre_condominio`, `direccion_condominio`) VALUES
(1, 'UrbLaMaroma', 'Luis Guillermo Ferrer Ramirez', 'V314548740', 'lgfr03@gmail.com', '$2y$10$wbd9MI8v6fA24cDr5Zo7sO98FL/1FDhDcFnJjf9J8VA7l6uVaE3gq', 'Administrador', '', 'Urbanizacion La Maroma', 'Carretera via el Vigia Km4'),
(27, 'admin', 'admin', 'G200028696', 'admin@gmail.com', '$2y$10$casz1wfAU6kU4fE7oZRIW.SFWpeF9LIePHolfdFDhQGlZweMIc6ye', 'Administrador', '', 'Urbanizacion La Maroma', 'Carretera via el Vigia Km4');

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
(372.11, 100.00, '2026-02-03 00:07:09', 104),
(382.63, 100.00, '2026-02-07 11:59:23', 106),
(382.63, 50.00, '2026-02-07 16:15:50', 107);

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
(200, 62, '2026-02-07 20:15:55', '2026-02-22', '02-2026', 50, 76, ''),
(201, 62, '2026-02-08 16:35:29', '2026-02-23', '02-2026', 50, 76, ''),
(202, 63, '2026-02-08 16:35:29', '2026-02-23', '02-2026', 50, 77, ''),
(203, 64, '2026-02-08 19:12:16', '2026-02-23', '02-2026', 50, 78, ''),
(204, 65, '2026-02-08 19:12:16', '2026-02-23', '02-2026', 50, 79, ''),
(205, 66, '2026-02-08 19:12:16', '2026-02-23', '02-2026', 50, 80, ''),
(206, 67, '2026-02-08 19:12:16', '2026-02-23', '02-2026', 50, 81, ''),
(207, 68, '2026-02-08 19:12:16', '2026-02-23', '02-2026', 50, 82, 'Pagado'),
(208, 69, '2026-02-08 19:12:16', '2026-02-23', '02-2026', 50, 83, ''),
(209, 70, '2026-02-08 19:12:16', '2026-02-23', '02-2026', 50, 84, ''),
(210, 71, '2026-02-08 19:12:16', '2026-02-23', '02-2026', 50, 85, ''),
(211, 75, '2026-02-08 19:22:44', '2026-02-23', '02-2026', 50, 89, ''),
(212, 76, '2026-02-08 19:44:26', '2026-02-23', '02-2026', 50, 90, ''),
(213, 77, '2026-02-08 20:50:29', '2026-02-23', '02-2026', 50, 91, ''),
(214, 78, '2026-02-08 20:54:11', '2026-02-23', '02-2026', 50, 92, ''),
(215, 79, '2026-02-09 00:10:34', '2026-02-24', '02-2026', 50, 93, ''),
(216, 80, '2026-02-10 07:05:11', '2026-02-25', '02-2026', 50, 94, ''),
(217, 81, '2026-02-10 07:10:26', '2026-02-25', '02-2026', 50, 95, ''),
(218, 82, '2026-02-10 07:44:12', '2026-02-25', '02-2026', 50, 96, ''),
(219, 83, '2026-02-10 11:58:02', '2026-02-25', '02-2026', 50, 97, 'Pendiente');

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
(19, 'Nomina Vigilantes', 'Nomina', 18500, '2026-02-08'),
(20, 'Plan de Alumbramiento', 'Mantenimiento', 2500, '2026-02-08'),
(21, 'Reparación porton ', 'Reparaciones', 13200, '2026-02-08'),
(22, 'Nomina Vigilantes', 'Nomina', 5000, '2026-02-08'),
(23, 'Nomina Vigilantes', 'Nomina', 12500.5, '2026-02-08'),
(24, 'Nomina Vigilantes', 'Nomina', 15000.5, '2026-02-08'),
(25, 'Nomina Vigilantes', 'Nomina', 15000, '2026-02-08'),
(26, 'Nomina Vigilantes', 'Nomina', 12500, '2026-02-10');

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
(82, '2026-02-07', '2026-02-07', 'Validado', 62, 19131.5, '1111111', 200),
(83, '2026-02-08', '2026-02-08', 'Validado', 63, 19131.5, 'pagado', 202),
(84, '2026-02-08', '2026-02-08', 'Validado', 62, 19131.5, 'pagado', 201),
(85, '2026-02-08', '2026-02-08', 'Validado', 75, 19131.5, 'pagado', 211),
(86, '2026-02-08', '2026-02-08', 'Validado', 67, 19131.5, '23170003', 206),
(87, '2026-02-08', '2026-02-08', 'Validado', 69, 19131.5, '123123', 208),
(88, '2026-02-08', '2026-02-08', 'Validado', 66, 19131.5, '1231234', 205),
(89, '2026-02-08', '2026-02-08', 'Validado', 70, 19131.5, '1231231', 209),
(90, '2026-02-08', '2026-02-08', 'Validado', 65, 19131.5, '123123', 204),
(91, '2026-02-08', '2026-02-09', 'Validado', 68, 19131.5, '123124', 207),
(92, '2026-02-08', '2026-02-09', 'Validado', 64, 19131.5, '123123', 203),
(93, '2026-02-08', '2026-02-09', 'Pendiente', 77, 19131.5, '123124', 213),
(94, '2026-02-08', '2026-02-09', 'Pendiente', 71, 19131.5, '123123', 210),
(97, '2026-02-10', '2026-02-10', 'Validado', 79, 19131.5, 'prueba1', 215),
(98, '2026-02-10', '2026-02-10', 'Validado', 71, 19131.5, 'prueba2', 210),
(99, '2026-02-10', '2026-02-10', 'Validado', 76, 19131.5, 'prueba3', 212),
(100, '2026-02-10', '2026-02-10', 'Validado', 77, 19131.5, 'prueba4', 213),
(101, '2026-02-10', '2026-02-10', 'Validado', 78, 19131.5, 'prueba5', 214),
(102, '2026-02-10', '2026-02-10', 'Validado', 80, 19131.5, 'prueba6', 216),
(103, '2026-02-10', '2026-02-10', 'Validado', 81, 19131.5, 'prueba7', 217),
(104, '2026-02-10', '2026-02-10', 'Validado', 82, 19131.5, 'prueba8', 218);

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
  `pass` varchar(256) NOT NULL,
  `email_propietario` varchar(256) NOT NULL,
  `rol` enum('Propietario') NOT NULL,
  `reset_token` varchar(256) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propietario`
--

INSERT INTO `propietario` (`id`, `rif`, `nombre`, `apellido`, `usuario`, `pass`, `email_propietario`, `rol`, `reset_token`) VALUES
(62, 'V26718868', 'Michael ', 'Ramirez', 'michael', '$2y$10$NAYl2wTtrWpgQ5qtpVJ1O.7pHKqdYmGibgGjjbnUxcxncTYmCPb7W', 'michael@gmail.com', 'Propietario', '0'),
(63, 'V31454874', 'Luis', 'Ferrer', 'lgfr_', '$2y$10$093abK7otS6hgxIjbCbSZemLFH8xJDbWRVPtzOYAt8H9/jxoUdZYC', 'lgfr03@gmail.com', 'Propietario', '0'),
(64, 'V31371985', 'Maryam Victoria', 'Miranda Pacheco', 'maryamvictoria', '$2y$10$OYgM4G.GnXx/U0D/97FVC.pgBnf8B0daTxn5XUMaDoQns0MpV/3Hi', 'maryamvic1205@gmail.com', 'Propietario', '0'),
(65, 'V31459447', 'Cesar', 'Ferrer', 'ciward', '$2y$10$FkD8EBskxZGJ88fxsEFHse29UOF2EbcfYDOWQy0bvEs1PUTm7Ghxa', 'cesareduardoferrer2003@gmail.com', 'Propietario', '0'),
(66, 'V6583137', 'Luis Guillermo', 'Ferrer Alaña', 'luchoferrala', '$2y$10$zhaagABkzPkGKq5LVSNB/eycymDe/vzdTEkX.z8i43EQN4BIxF8qG', 'ferreralana@gmail.com', 'Propietario', '0'),
(67, 'V7902201', 'Nora Margeris', 'Ramirez Peñaloza', 'noramirezp', '$2y$10$7wnpubsu9CNBdjCkK0SOO../nr.RdalMxtD3p4FIoz6cpn0hZpSf6', 'noramirezp18@gmail.com', 'Propietario', '0'),
(68, 'V28124173', 'Nohelia Victoria', 'Ferrer Ramirez', 'nohevfr', '$2y$10$eeWZQTs5f5uqROSoW1corOj5sv1sKpzDcEPHtmx7c9kYgdCSnxyfi', 'noheliavf@gmail.com', 'Propietario', '0'),
(69, 'V18963272', 'Yusmary', 'Martinez', 'yusmary02', '$2y$10$uF7t4fyxVAEv2SS8Z.FkDuykN3Ad/rSE5hexfg3h4I2oytptDWKR.', 'yusmarymartinez@gmail.com', 'Propietario', '0'),
(70, 'G200028696', 'Alcaldia Bolivariana', 'De Colon', 'alcaldiadecolon', '$2y$10$Rm9qq207gg1YRd8iB7JPOusk1kB3O8jS.kk/tLA1D.rWnQIQ2zpx2', 'alcaldia@alcaldiadecolon.gob.ve', 'Propietario', '0'),
(71, 'G200028699', 'Talento ', 'Humano', 'talentohumano', '$2y$10$ROroVkDeD/MB2Rb9v8YvBOBoTXeN5MGEM3uoRTWXho/dJxKib6jxa', 'talentohumano@alcaldiadecolon.gob.ve', 'Propietario', '0'),
(75, 'V5561002', 'Barbara', 'Elena', 'yudyelena', '$2y$10$F3t1AdE1KVqcX/eFhtQG5OtNuLv57jzg42U540VNKxXhVyzMAGxKm', 'yudy@gmail.com', 'Propietario', '0'),
(76, 'V15416985', 'Flor', 'Aparmo', 'floraparmo', '$2y$10$Ene6pUXADLtLgkJ/lJ2cyOVUgbAfUDm43IJv9Gsjw3AACD44BatjG', 'aparmo28@gmail.com', 'Propietario', '0'),
(77, 'V13123123', 'Juan', 'Jose', 'joseg', '$2y$10$1NUflWECGWkXNtW/ETtTweHml578zsQP2tSa2a1hRiktQm2gmPhRq', '12312123@gmail.com', 'Propietario', '0'),
(78, 'V1185923', 'Juan', 'Perez', 'juanperez', '$2y$10$Q79zYrv4BGAsLCrUqAhIW.VWMffaar68c7Vv8jrP.cm./fQP9qgKm', 'juan@gmail.com', 'Propietario', '0'),
(79, 'J26718868', 'Michael ', 'Ramirez', 'michael', '$2y$10$fSCRe6KNnS1i9NdReaMnU.XRVFO3Acja91AeR.iUDpyaYq.tviftG', 'michael12@gmail.com', 'Propietario', '0'),
(80, 'J31454874', 'Luis', 'Ferrer', 'lgfr_', '$2y$10$3yWZsIbeOsRgO64qdPJG5OSmf0WHBZbCImkRMJACK.ed0or3Jb0T6', 'lgfr03@gmail.com', 'Propietario', '0'),
(81, 'J31371985', 'Maryam', 'Victoria', 'maryamvictoria', '$2y$10$b7hQ4essNSw/1qAGGlJbW.wbwTTKYX1AstTbKFzeXP1vm.fMPVDby', 'maryamvic1205@gmail.com', 'Propietario', '0'),
(82, 'V12345678', 'Manolo', 'Salazar', 'manolin', '$2y$10$PXQRgOOZ4tiSdiOg7C.yzeBa4XyYcrNge7dUdl7ESBCm8WC3x2tge', '12312123@gmail.com', 'Propietario', '0'),
(83, 'VV12345678', 'Manolo ', 'Salazar', 'manolin', '$2y$10$FBCqWHM17RC8XvFuEVTJIecEQy5CrRNrojKAm9x0hQCUTGL/GuIGa', 'manolin@gmail.com', 'Propietario', '0');

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
(76, '1-111', 62),
(77, '2-25', 63),
(78, '10-102', 64),
(79, '2-35', 65),
(80, '2-381', 66),
(81, '2-132', 67),
(82, '12-212', 68),
(83, '123-22', 69),
(84, '2-331', 70),
(85, '100-21', 71),
(86, '100-21', 72),
(87, '100-21', 73),
(88, '100-21', 74),
(89, '12-3', 75),
(90, '12-233', 76),
(91, '12-2223', 77),
(92, '123-221', 78),
(93, '2-22', 79),
(94, '2-26', 80),
(95, '12-23', 81),
(96, '101', 82),
(97, '102', 83);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_admin`);

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `factor`
--
ALTER TABLE `factor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT de la tabla `gastos_eventuales`
--
ALTER TABLE `gastos_eventuales`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de la tabla `propietario`
--
ALTER TABLE `propietario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `residencias`
--
ALTER TABLE `residencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

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
