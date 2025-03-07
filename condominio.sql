-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-03-2025 a las 21:59:21
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
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(10) NOT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_registro` date DEFAULT current_timestamp(),
  `status` enum('Abierto','Validado','Anulado') DEFAULT NULL,
  `nro_residencia` int(10) DEFAULT NULL,
  `id_propietario` int(10) NOT NULL,
  `monto` double DEFAULT NULL,
  `referencia` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `fecha`, `fecha_registro`, `status`, `nro_residencia`, `id_propietario`, `monto`, `referencia`) VALUES
(16, '2025-02-25', '2025-02-25', '', 0, 3, 300, '13112r1rr22w'),
(17, '2025-02-20', '2025-02-25', '', 0, 3, 222, 'wewe'),
(18, '2025-02-10', '2025-02-25', 'Validado', 0, 3, 322, 'PAPAPAPA'),
(19, '2025-02-19', '2025-02-25', '', 0, 5, 123, '321'),
(20, '2025-02-26', '2025-02-25', '', 0, 3, 1233, '123'),
(21, '2025-02-27', '2025-02-25', 'Validado', 0, 3, 999, '112333'),
(23, '2025-02-20', '2025-02-25', '', 0, 3, 235, 'PAPAPAPA'),
(24, '2025-03-11', '2025-03-05', '', 0, 1, 899, 'SALDO');

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
(1, 'V31454874', 'Luis', 'Ferrer', 'lgfr_', '1234', 'lgfr03@gmail.com', 'Propietario', '4a6093480095dce941e98914aeeb0ba922c1729fc08ad0fc7dec9fc27adffbc4896007dea594a96518725ac152467e7eddf0'),
(3, 'V26718868', 'Michael Antonio', 'Ramirez Rincon', 'micha0507', '1234', 'michaelramirez.mr32@gmail.com', 'Propietario', ''),
(5, 'V12345678', 'Mario', 'Bross', 'mariobross', '1234', 'maryamvic1205@gmail.com', 'Propietario', '1'),
(7, 'V26718869', 'Ramiro', 'Perez', 'ramiperez', '1234', 'ramiperez@gmail.com', 'Propietario', '0'),
(9, 'V30534653', 'Adriana ', 'Borrero', 'adriborr04', '1234', 'darneilisadrianab@gmail.com', '', '0'),
(10, 'V123', 'Jose', 'Ramirez', 'joser', '123', 'michaelramirez.meer32@gmail.com', 'Propietario', '0'),
(11, 'V123', 'a', 'Pony', 'fmim', '12345678', 'gijij@gmail.com', 'Propietario', '0'),
(12, 'V123', 'a', 'Pony', 'fmim', '12345678', 'gijij@gmail.com', 'Propietario', '0'),
(13, 'V123', 'a', 'Pony', 'fmim', '12345678', 'gijij@gmail.com', 'Propietario', '0'),
(14, 'V123', 'a', 'Pony', 'fmim', '12345678', 'gijij@gmail.com', 'Propietario', '0'),
(15, 'V123', 'Manuel', 'Turizo', 'manuelt', '1235345345', 'turizomanuel@gmail.com', 'Propietario', '0'),
(16, 'v12345679', 'HUGO', 'CHAVEZ', 'hugoch', '12333123', 'ppp@gmail.com', 'Propietario', '0'),
(17, 'V123567', 'RAMIRO', 'PEREZ', 'RPEREZ', '12345678', 'rperez@gmail.com', 'Propietario', '0'),
(18, 'V123567', 'RAMIRO', 'PEREZ', 'RPEREZ', '12345678', 'rperez@gmail.com', 'Propietario', '0'),
(19, 'V123567', 'RAMIRO', 'PEREZ', 'RPEREZ', '12345678', 'rperez@gmail.com', 'Propietario', '0'),
(20, 'V26718868', 'LALO', 'PORRAS', 'lalop', '123123123', 'lalo@gmail.com', 'Propietario', '0'),
(21, 'V31454873', 'NORA', 'MADURO', 'noram', '123123123', 'noram@gmail.com', 'Propietario', '0'),
(22, 'v26718863', 'MARIO', 'CASAS', 'mariom', '32112333', 'mariom@gmail.com', 'Propietario', '0'),
(23, 'v26718866', 'MARIO', 'CASAS', 'mariom', '32112333', 'mariom@gmail.com', 'Propietario', '0'),
(24, 'v26718832', 'MARIO', 'CASAS', 'mariom', '32112333', 'mariom@gmail.com', 'Propietario', '0'),
(25, 'V1232', 'Manuel', 'Molina', 'manu', '123123123', 'manu@yahoo.com', 'Propietario', '0'),
(26, 'V1231231', 'Manuel', 'Molina', 'joser', '123321321', 'eqwemjmmj@gmail.com', 'Propietario', '0');

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
(3, 'K12', 5),
(4, 'K321', 7),
(5, 'Q123', 7),
(6, 'K54', 9),
(7, 'K55', 9),
(20, 'K34', 1),
(21, 'K35', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_admin`);

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
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `propietario`
--
ALTER TABLE `propietario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `residencias`
--
ALTER TABLE `residencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `id_propietario` FOREIGN KEY (`id_propietario`) REFERENCES `propietario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
