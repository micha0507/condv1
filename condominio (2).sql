-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-03-2025 a las 02:22:21
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
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(10) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `monto` int(11) DEFAULT NULL,
  `status` enum('Pendiente','Pagado','','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(10) NOT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_registro` date DEFAULT current_timestamp(),
  `status` enum('Pendiente','Validado','Anulado') DEFAULT NULL,
  `nro_residencia` int(10) DEFAULT NULL,
  `id_propietario` int(10) NOT NULL,
  `monto` double DEFAULT NULL,
  `referencia` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `fecha`, `fecha_registro`, `status`, `nro_residencia`, `id_propietario`, `monto`, `referencia`) VALUES
(33, '2025-03-24', '2025-03-20', 'Validado', 1, 3, 344, '234df'),
(34, '2025-03-18', '2025-03-20', '', 1, 3, 565, '24234'),
(35, '2025-03-19', '2025-03-20', '', 1, 3, 234, 'rwer'),
(36, '2025-03-20', '2025-03-20', 'Validado', NULL, 3, 56565, 'qwe61'),
(37, '2025-03-20', '2025-03-20', 'Validado', NULL, 3, 333, 'abc'),
(47, '2025-03-18', '2025-03-19', 'Pendiente', 1, 3, 1561, 'dfff'),
(49, NULL, '2025-03-24', 'Pendiente', 2, 3, 321, 'VFV'),
(50, '2025-03-24', '2025-03-25', 'Pendiente', 4, 36, 123, '123456'),
(51, '2025-03-19', '2025-03-25', 'Pendiente', 4, 36, 333, 'gdftr'),
(52, '2025-03-19', '2025-03-25', 'Pendiente', 1, 3, 300, 'knjn'),
(53, '2025-03-18', '2025-03-25', 'Validado', 2, 3, 4555, 'dasd'),
(54, '2025-03-18', '2025-03-25', 'Pendiente', 1, 3, 342, 'wfsf4'),
(55, '2025-03-13', '2025-03-25', 'Validado', 1, 3, 12231, 'nv bnv'),
(56, '2025-03-19', '2025-03-25', 'Pendiente', 4, 36, 3220.13, '234df');

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
(36, 'V31454874', 'Luis', 'Ferrer', 'lferrer', '12345678', '12345678@gmail.com', 'Propietario', '0');

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
(5, 'Q123', 7),
(6, 'K54', 9),
(7, 'K55', 34),
(20, 'K34', 34),
(28, 'K100', 34);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `propietario` (`propietario_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_propietario` (`id_propietario`);

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
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `propietario`
--
ALTER TABLE `propietario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `residencias`
--
ALTER TABLE `residencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
