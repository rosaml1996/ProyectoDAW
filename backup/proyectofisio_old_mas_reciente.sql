-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2026 a las 23:48:02
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
CREATE DATABASE IF NOT EXISTS `proyectofisio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `proyectofisio`;

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
(1, 'Luis de Toro', 'itorped@g.educaand.es', '$2y$10$.yHcGzeUqGGfBD8.76TuOeC1UAyWiozS9a.qSNl3N7O/bRIQy1ueO');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(29, '2026-04-24', '09:30:00', '10:00:00', 'reservada', 11, 17),
(30, '2026-06-10', '19:00:00', '19:40:00', 'reservada', 11, 16),
(31, '2026-05-13', '17:00:00', '18:00:00', 'cancelada', 11, 20),
(32, '2026-04-24', '11:00:00', '11:30:00', 'reservada', 10, 2),
(33, '2026-07-14', '18:00:00', '18:30:00', 'reservada', 10, 2),
(34, '2026-04-24', '12:00:00', '12:40:00', 'reservada', 7, 16),
(35, '2026-04-30', '12:00:00', '13:00:00', 'reservada', 1, 20),
(36, '2026-04-24', '18:30:00', '18:45:00', 'reservada', 8, 26),
(37, '2026-05-06', '12:00:00', '13:00:00', 'reservada', 8, 20);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'María Jimémez Ruíz ', '1990-01-01', '632958436', 'maria@gmail.com', '$2y$10$xhWD9r8sLwXhTkalhYngyOf8HsGNK7XR8QB/XMiX44QumunICtc8K'),
(7, 'Rosa Moreno López', '1996-07-24', '672192920', 'rosa@email.com', '$2y$10$8EHN67TmpK.oW43qAzCq7u5m9Gpf1XXJRBjGxmmF0G2BlDndJnMg.'),
(8, 'Laura Sánchez Ruiz', '1993-03-15', '612345789', 'laura.sanchez@email.com', '$2y$10$exhb5YgNKzdw.pn02OUik.2YftBLtKCPanjK6wlP4KCKynVzjn7Qa'),
(9, 'Daniel Martín Pérez', '1987-11-08', '734856291', 'daniel.martin@email.com', '$2y$10$vYLNI1vljfoY.sZhtd5IWeNAVoDh6lEoTLxvG2ma4GGTmQ2dgVray'),
(10, 'Carmen López García', '1979-06-22', '856913472', 'carmen.lopez@email.com', '$2y$10$gvJa/1uLyq2..2d6gq75QuouXMH7Xde6kXlNhK8an8mTtvuPAlBV6'),
(11, 'Javier Torres Molina', '2001-01-30', '923748561', 'javier.torres@email.com', '$2y$10$.w.7OCOo1u.oQNoxynceTu01JLDVVYQ02oBaEi5.cG8m5.k41BjMq');

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
(2, 'Punción seca', 'La punción seca es una técnica invasiva de fisioterapia que utiliza agujas muy finas (similares a las de acupuntura, pero con un fundamento diferente) para tratar los puntos gatillo miofasciales.', 30, 40.00, 1),
(14, 'Terapias manuales avanzadas', 'Las terapias manuales avanzadas son técnicas de fisioterapia que utilizan las manos del terapeuta para tratar afecciones musculo-esqueléticas.', 45, 40.00, 1),
(15, 'Electroterapia y neuromodulación', 'Electroterapia: Uso de corrientes eléctricas externas para aliviar dolor, desinflamar y mejorar función muscular.\r\nNeuromodulación: Modificación directa de la actividad de nervios específicos (a menudo con agujas finas) para tratar dolor crónico o disfunciones.', 60, 55.00, 1),
(16, 'Magnetoterapia', 'La magnetoterapia es una técnica de fisioterapia que utiliza campos magnéticos (generalmente de baja frecuencia y baja intensidad) para tratar diversas patologías y afecciones en el cuerpo humano.', 40, 50.00, 1),
(17, 'Vendaje funcional y kinesiología', 'Vendaje Funcional: Inmovilización parcial y mecánica con cinta rígida para limitar movimientos dañinos.\r\n\r\n\r\nVendaje Neuromuscular (Kinesio Taping): Vendaje elástico que busca facilitar procesos fisiológicos (reducción de dolor/hinchazón, soporte muscular) sin restringir el movimiento.', 30, 30.00, 1),
(20, 'Fisioterapia deportiva', 'La fisioterapia deportiva es una especialidad centrada en la prevención, diagnóstico y tratamiento de lesiones relacionadas con la actividad física y el deporte.', 60, 50.00, 1),
(21, 'Fisioterapia geriátrica', 'La fisioterapia geriátrica se centra en el tratamiento de pacientes mayores, abordando problemas como la movilidad reducida, el dolor crónico y la recuperación de cirugías.', 45, 40.00, 1),
(22, 'Fisioterapia neurológica', 'La fisioterapia neurológica se centra en el tratamiento de pacientes con trastornos del sistema nervioso, como accidentes cerebrovasculares, esclerosis múltiple y lesiones medulares.', 60, 45.00, 1),
(23, 'Rehabilitación postoperatoria', 'Es un programa de fisioterapia individualizado que se inicia tras una cirugía.', 30, 25.00, 1),
(26, 'Ecografía neuromusculoesquelética', 'Es una técnica de diagnóstico por imagen que utiliza ondas de sonido de alta frecuencia para visualizar en tiempo real músculos, tendones, ligamentos, nervios, articulaciones y tejidos blandos del cuerpo.', 15, 25.00, 1);

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
  MODIFY `id_bloqueo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cita`
--
ALTER TABLE `cita`
  MODIFY `id_cita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `horario_laboral`
--
ALTER TABLE `horario_laboral`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `paciente`
--
ALTER TABLE `paciente`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
