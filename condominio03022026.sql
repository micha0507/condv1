-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-02-2026 a las 05:33:07
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
(1, 'UrbLaMaroma', 'Luis Guillermo Ferrer Ramirez', 'V314548740', 'lgfr03@gmail.com', '$2y$10$wbd9MI8v6fA24cDr5Zo7sO98FL/1FDhDcFnJjf9J8VA7l6uVaE3gq', 'Administrador', '', 'Urbanizacion La Maroma', ''),
(27, 'admin', 'admin', 'G200028696', 'admin@gmail.com', '$2y$10$casz1wfAU6kU4fE7oZRIW.SFWpeF9LIePHolfdFDhQGlZweMIc6ye', 'Administrador', '', 'Urbanizacion La Maroma', 'Carretera via el Vigia Km4');

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
(372.11, 100.00, '2026-02-03 00:07:09', 104);

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
(193, 60, '2026-02-03 04:07:11', '2026-02-18', '02-2026', 100, 71, '');

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
(18, 'Nomina Vigilantes', 'Nomina', 22600, '2026-02-03');

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
(79, '2026-02-03', '2026-02-03', 'Validado', 60, 37211, '23170003', 193);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

CREATE TABLE `post` (
  `id_post` int(10) NOT NULL,
  `contenido` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(60, 'V31454874', 'Luis', 'Ramirez', 'lgfr_', '$2y$10$XP3FTqtHSHHueeEVr6.DnuWL1dmLuEf9bbPNRl/qXGc4pYyL.YcgK', 'luisguillermoferrer2003@gmail.com', 'Propietario', '0');

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
(71, '2-35', 60);

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `factor`
--
ALTER TABLE `factor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT de la tabla `gastos_eventuales`
--
ALTER TABLE `gastos_eventuales`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de la tabla `post`
--
ALTER TABLE `post`
  MODIFY `id_post` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `propietario`
--
ALTER TABLE `propietario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `residencias`
--
ALTER TABLE `residencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

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
