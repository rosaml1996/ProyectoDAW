-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2026 a las 21:33:26
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
-- Base de datos: `proyectofisio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

DROP TABLE IF EXISTS `administrador`;
CREATE TABLE `administrador` (
  `id_admin` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `administrador`:
--

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_admin`, `nombre`, `email`, `contraseña`) VALUES
(1, 'Luis', 'itorped@g.educaand.es', '$2y$10$.yHcGzeUqGGfBD8.76TuOeC1UAyWiozS9a.qSNl3N7O/bRIQy1ueO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloqueo_agenda`
--

DROP TABLE IF EXISTS `bloqueo_agenda`;
CREATE TABLE `bloqueo_agenda` (
  `id_bloqueo` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ;

--
-- RELACIONES PARA LA TABLA `bloqueo_agenda`:
--

--
-- Volcado de datos para la tabla `bloqueo_agenda`
--

INSERT INTO `bloqueo_agenda` (`id_bloqueo`, `fecha`, `hora_inicio`, `hora_fin`, `motivo`) VALUES
(1, '2026-12-25', NULL, NULL, 'Navidad'),
(2, '2026-01-01', NULL, NULL, 'Año nuevo'),
(3, '2026-05-07', '11:00:00', '12:30:00', 'Cita médica'),
(4, '2026-05-01', NULL, NULL, 'Festivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cita`
--

DROP TABLE IF EXISTS `cita`;
CREATE TABLE `cita` (
  `id_cita` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` varchar(50) NOT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `id_servicio` int(11) NOT NULL
) ;

--
-- RELACIONES PARA LA TABLA `cita`:
--   `id_paciente`
--       `paciente` -> `id_paciente`
--   `id_servicio`
--       `servicio` -> `id_servicio`
--

--
-- Volcado de datos para la tabla `cita`
--

INSERT INTO `cita` (`id_cita`, `fecha`, `hora_inicio`, `hora_fin`, `estado`, `id_paciente`, `id_servicio`) VALUES
(3, '2026-03-01', '12:00:00', '13:00:00', 'completada', 1, 3),
(22, '2026-04-30', '10:00:00', '11:00:00', 'cancelada', 7, 1),
(23, '2026-05-20', '18:00:00', '19:00:00', 'cancelada', 7, 6),
(24, '2026-05-28', '18:30:00', '19:30:00', 'cancelada', 7, 6),
(25, '2026-04-23', '19:00:00', '20:00:00', 'completada', 7, 1),
(26, '2026-04-24', '10:30:00', '11:30:00', 'reservada', 7, 1),
(27, '2026-05-28', '18:00:00', '19:00:00', 'reservada', 7, 1),
(28, '2026-05-28', '11:00:00', '12:00:00', 'cancelada', 7, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario_laboral`
--

DROP TABLE IF EXISTS `horario_laboral`;
CREATE TABLE `horario_laboral` (
  `id_horario` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ;

--
-- RELACIONES PARA LA TABLA `horario_laboral`:
--

--
-- Volcado de datos para la tabla `horario_laboral`
--

INSERT INTO `horario_laboral` (`id_horario`, `dia_semana`, `hora_inicio`, `hora_fin`, `activo`) VALUES
(1, 1, '09:00:00', '13:00:00', 1),
(2, 1, '17:00:00', '21:00:00', 1),
(4, 2, '17:00:00', '21:00:00', 1),
(5, 3, '09:00:00', '13:00:00', 1),
(6, 3, '17:00:00', '21:00:00', 1),
(7, 4, '09:00:00', '13:00:00', 1),
(8, 4, '17:00:00', '21:00:00', 1),
(9, 5, '09:00:00', '13:00:00', 1),
(13, 2, '09:00:00', '13:00:00', 1),
(14, 5, '17:00:00', '21:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paciente`
--

DROP TABLE IF EXISTS `paciente`;
CREATE TABLE `paciente` (
  `id_paciente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `paciente`:
--

--
-- Volcado de datos para la tabla `paciente`
--

INSERT INTO `paciente` (`id_paciente`, `nombre`, `fecha_nacimiento`, `telefono`, `email`, `contraseña`) VALUES
(1, 'Maria', '1990-01-01', '632958436', 'maria@gmail.com', '$2y$10$xhWD9r8sLwXhTkalhYngyOf8HsGNK7XR8QB/XMiX44QumunICtc8K'),
(2, 'maria gonzalez', '2026-04-07', '632514589', 'paco@gmail.com', '$2y$10$UWeTlbn5MUZ3IAAXUEi1LO7B2BTXycC8OofnrSZp4AR20cuUn8jtO'),
(3, 'juan', '2028-01-09', '695785145', 'tomastoma@gmail.com', '$2y$10$NERI.WYf878.Tt6BR2ZEjeos/kkoHsbWNwJdYyz9d9UUK4h6cz1gG'),
(4, 'sandra', '2026-04-08', '957486231', 'sandra@gmail.com', '$2y$10$dMKGsRUWM2q8bWudjcMTke6lQUxQWuJ2mq0JGMdyToF/OlSaX0Su.'),
(7, 'Rosa Moreno López', '1996-07-24', '672192920', 'rosa@email.com', '$2y$10$8EHN67TmpK.oW43qAzCq7u5m9Gpf1XXJRBjGxmmF0G2BlDndJnMg.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

DROP TABLE IF EXISTS `servicio`;
CREATE TABLE `servicio` (
  `id_servicio` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `servicio`:
--

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id_servicio`, `nombre`, `descripcion`, `duracion`, `precio`, `activo`) VALUES
(1, 'Fisioterapia general', 'El cliente puede elegir en el momento lo que necesite con un máximo de 60 minutos de servicio.', 60, 35.00, 1),
(2, 'Punción seca', NULL, 45, 40.00, 1),
(3, 'Masaje deportivo', NULL, 60, 45.00, 1),
(5, 'futbol', NULL, 50, 100.00, 0),
(6, 'natacion', NULL, 60, 55.00, 1),
(8, 'cr7(el mejor jugador de la historia del mundo mundial y punto)', NULL, 25, 25.00, 0),
(10, 'sfdsf', 'sdfsf', 45, 0.90, 1),
(11, 'dfgdg', 'fdgdg', 4545, 0.89, 1),
(12, 'etgrete', 'retet', 4545, 0.50, 1),
(13, 'prueba 1', 'sefewf', 43535, 0.80, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `bloqueo_agenda`
--
ALTER TABLE `bloqueo_agenda`
  ADD PRIMARY KEY (`id_bloqueo`),
  ADD KEY `idx_bloqueo_fecha` (`fecha`);

--
-- Indices de la tabla `cita`
--
ALTER TABLE `cita`
  ADD PRIMARY KEY (`id_cita`),
  ADD KEY `fk_cita_paciente` (`id_paciente`),
  ADD KEY `fk_cita_servicio` (`id_servicio`),
  ADD KEY `idx_cita_fecha` (`fecha`);

--
-- Indices de la tabla `horario_laboral`
--
ALTER TABLE `horario_laboral`
  ADD PRIMARY KEY (`id_horario`);

--
-- Indices de la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`id_paciente`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id_servicio`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `bloqueo_agenda`
--
ALTER TABLE `bloqueo_agenda`
  MODIFY `id_bloqueo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cita`
--
ALTER TABLE `cita`
  MODIFY `id_cita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horario_laboral`
--
ALTER TABLE `horario_laboral`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paciente`
--
ALTER TABLE `paciente`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cita`
--
ALTER TABLE `cita`
  ADD CONSTRAINT `fk_cita_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cita_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
