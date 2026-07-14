-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-07-2026 a las 01:36:29
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
(32, 'admin', 'admin', 'J200028696', 'lgfr03@gmail.com', '$2y$10$3swaaay7EI5InHyocZTCC.Q/D2X8cqHGuz5y/K6Z2j5BxCgKCIyma', 'Administrador', '', 'Urbanizacion La Maroma', 'Santa Barbara de Zulia Carretera via el Vigia Km4 ');

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
(390.29, 100.00, '2026-02-12 12:20:19', 108),
(725.75, 100.00, '2026-07-14 19:05:43', 109);

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
(222, 86, '2026-02-12 16:20:38', '2026-02-27', '02-2026', 100, 100, 'Pagado'),
(223, 87, '2026-02-12 16:20:38', '2026-02-27', '02-2026', 100, 101, ''),
(224, 88, '2026-02-12 16:22:32', '2026-02-27', '02-2026', 100, 102, ''),
(225, 86, '2026-07-14 23:05:56', '2026-07-30', '07-2026', 100, 100, ''),
(226, 87, '2026-07-14 23:05:56', '2026-07-30', '07-2026', 100, 101, ''),
(227, 88, '2026-07-14 23:05:56', '2026-07-30', '07-2026', 100, 102, ''),
(228, 89, '2026-07-14 23:05:57', '2026-07-30', '07-2026', 100, 103, 'Pagado');

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
(29, 'Plan de Alumbramiento', 'Servicios Públicos', 10000.5, '2026-02-12');

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
(2, '2026-07-14', '2026-07-14', 'Validado', 89, 72575, 'pago', 228),
(1821, '2026-02-12', '2026-02-12', 'Validado', 86, 39029, '1111111', 222),
(1822, '2026-02-12', '2026-02-12', 'Validado', 87, 39029, '01010101', 223),
(1823, '2026-02-12', '2026-02-12', 'Validado', 88, 39029, 'EFEFE', 224),
(1824, '2026-07-15', '2026-07-15', 'Validado', 88, 72575, '010203', 227),
(1825, '2026-07-15', '2026-07-15', 'Validado', 87, 72575, '0102044', 226),
(1826, '2026-07-15', '2026-07-15', 'Validado', 86, 72575, '2203145', 225);

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
(86, 'V26718375', 'Jose', 'Puerta', 'josepuerta01', '$2y$10$IDLdxZfqI/KJOhkd59lg/.x99GF6qBFcoLkiAH3v3Z7/i3ZZnIPDC', 'josepuerta@gmail.com', 'Propietario', '0'),
(87, 'V26854344', 'Jesus', 'Monsalve', 'jesus', '$2y$10$IgqeFsTQF9t0qI8LEtxNXOX6FmHA2IBETWkt71bKXVNGZVojtgfbi', 'jesusmosalve@gmail.com', 'Propietario', '0'),
(88, 'V7777956', 'Luis', 'Reverol', 'luisre', '$2y$10$z9Ys1fqj.4Z6vSqbCaADq.p.glWjY79uK6HdgeZstXk2Z7NPndOdi', 'luisreverol@gmail.com', 'Propietario', '0'),
(89, 'V31454874', 'Luis', 'Guillermo', 'lgfr_', '$2y$10$K6eeqbpxC8CM9WpRzDK0XuHI4zG5c9tU/qvCaPXbqO2Wa9GeElCfK', 'luisguillermoferrer2003@gmail.com', 'Propietario', '0');

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
(100, '100-21', 86),
(101, '25-21', 87),
(102, '2-36', 88),
(103, '2-35', 89);

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `factor`
--
ALTER TABLE `factor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT de la tabla `gastos_eventuales`
--
ALTER TABLE `gastos_eventuales`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1827;

--
-- AUTO_INCREMENT de la tabla `propietario`
--
ALTER TABLE `propietario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `residencias`
--
ALTER TABLE `residencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

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
