DROP DATABASE IF EXISTS proyectofisio;
CREATE DATABASE proyectofisio CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE proyectofisio;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =========================================================
-- TABLA: administrador
-- Se mantiene igual que en la versión original
-- =========================================================
CREATE TABLE administrador (
  id_admin INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  contraseña VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_admin),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
-- TABLA: paciente
-- Se mantiene igual que en la versión original
-- =========================================================
CREATE TABLE paciente (
  id_paciente INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  fecha_nacimiento DATE NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  contraseña VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_paciente),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
-- TABLA: servicio
-- Se mantiene la lógica original y solo se añaden:
-- - descripcion
-- - activo
-- =========================================================
CREATE TABLE servicio (
  id_servicio INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  duracion INT(11) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_servicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
-- TABLA: horario_laboral
-- Nueva tabla para definir el horario habitual del fisio
--
-- dia_semana:
-- 1 = lunes
-- 2 = martes
-- 3 = miércoles
-- 4 = jueves
-- 5 = viernes
-- 6 = sábado
-- 7 = domingo
--
-- Si un día no tiene filas activas, se entiende que no se trabaja.
-- Permite varios tramos por día.
-- =========================================================
CREATE TABLE horario_laboral (
  id_horario INT(11) NOT NULL AUTO_INCREMENT,
  dia_semana TINYINT NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_horario),
  CONSTRAINT chk_dia_semana CHECK (dia_semana BETWEEN 1 AND 7),
  CONSTRAINT chk_horario_valido CHECK (hora_inicio < hora_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
-- TABLA: bloqueo_agenda
-- Nueva tabla para bloqueos puntuales
--
-- Casos:
-- - día completo: hora_inicio y hora_fin NULL
-- - franja parcial: hora_inicio y hora_fin con valor
-- =========================================================
CREATE TABLE bloqueo_agenda (
  id_bloqueo INT(11) NOT NULL AUTO_INCREMENT,
  fecha DATE NOT NULL,
  hora_inicio TIME DEFAULT NULL,
  hora_fin TIME DEFAULT NULL,
  motivo VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id_bloqueo),
  KEY idx_bloqueo_fecha (fecha),
  CONSTRAINT chk_bloqueo_horas_validas CHECK (
    (hora_inicio IS NULL AND hora_fin IS NULL)
    OR
    (hora_inicio IS NOT NULL AND hora_fin IS NOT NULL AND hora_inicio < hora_fin)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
-- TABLA: cita
-- Evolución mínima de la original
--
-- Cambios respecto a la original:
-- - se sustituye "hora" por "hora_inicio" y "hora_fin"
-- - se mantiene "estado" como VARCHAR(50) para no romper lógica existente
-- - se mantiene id_paciente nullable
-- - se mantiene ON DELETE SET NULL
--
-- Ya no se deberían guardar citas "libres" en el nuevo sistema,
-- pero se deja estado flexible para facilitar transición del backend.
-- =========================================================
CREATE TABLE cita (
  id_cita INT(11) NOT NULL AUTO_INCREMENT,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  estado VARCHAR(50) NOT NULL,
  id_paciente INT(11) DEFAULT NULL,
  id_servicio INT(11) NOT NULL,
  PRIMARY KEY (id_cita),
  KEY fk_cita_paciente (id_paciente),
  KEY fk_cita_servicio (id_servicio),
  KEY idx_cita_fecha (fecha),
  CONSTRAINT chk_cita_horas CHECK (hora_inicio < hora_fin),
  CONSTRAINT fk_cita_paciente
    FOREIGN KEY (id_paciente) REFERENCES paciente (id_paciente)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT fk_cita_servicio
    FOREIGN KEY (id_servicio) REFERENCES servicio (id_servicio)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
-- DATOS INICIALES
-- Mantengo los que ya teníais, limpiando solo lo mínimo para que
-- encajen con la nueva estructura y no metan horas absurdas.
-- =========================================================

-- ADMINISTRADOR
INSERT INTO administrador (id_admin, nombre, email, contraseña) VALUES
(1, 'Luis', 'itorped@g.educaand.es', '$2y$10$.yHcGzeUqGGfBD8.76TuOeC1UAyWiozS9a.qSNl3N7O/bRIQy1ueO');

-- PACIENTES
INSERT INTO paciente (id_paciente, nombre, fecha_nacimiento, telefono, email, contraseña) VALUES
(1, 'Maria', '1990-01-01', '666666666', 'maria@gmail.com', '$2y$10$xhWD9r8sLwXhTkalhYngyOf8HsGNK7XR8QB/XMiX44QumunICtc8K'),
(2, 'maria gonzalez', '2026-04-07', '4444455555', 'paco@gmail.com', '$2y$10$UWeTlbn5MUZ3IAAXUEi1LO7B2BTXycC8OofnrSZp4AR20cuUn8jtO'),
(3, 'juan', '2028-01-09', '695785145', 'tomastoma@gmail.com', '$2y$10$NERI.WYf878.Tt6BR2ZEjeos/kkoHsbWNwJdYyz9d9UUK4h6cz1gG'),
(4, 'sandra', '2026-04-08', '55555555555555555555', 'sandra@gmail.com', '$2y$10$dMKGsRUWM2q8bWudjcMTke6lQUxQWuJ2mq0JGMdyToF/OlSaX0Su.');

-- SERVICIOS
INSERT INTO servicio (id_servicio, nombre, descripcion, duracion, precio, activo) VALUES
(1, 'Fisioterapia general', NULL, 60, 35.00, 1),
(2, 'Punción seca', NULL, 45, 40.00, 1),
(3, 'Masaje deportivo', NULL, 60, 45.00, 1),
(5, 'futbol', NULL, 50, 100.00, 1),
(6, 'natacion', NULL, 60, 55.00, 1),
(7, 's', NULL, 2, 5.00, 1),
(8, 'cr7(el mejor jugador de la historia del mundo mundial y punto)', NULL, 25, 25.00, 1);

-- HORARIO HABITUAL DE EJEMPLO
-- Lunes a viernes: mañana y tarde
-- Sábado y domingo no se trabajan porque no tienen filas
INSERT INTO horario_laboral (id_horario, dia_semana, hora_inicio, hora_fin, activo) VALUES
(1, 1, '09:00:00', '14:00:00', 1),
(2, 1, '16:00:00', '20:00:00', 1),
(3, 2, '09:00:00', '14:00:00', 1),
(4, 2, '16:00:00', '20:00:00', 1),
(5, 3, '09:00:00', '14:00:00', 1),
(6, 3, '16:00:00', '20:00:00', 1),
(7, 4, '09:00:00', '14:00:00', 1),
(8, 4, '16:00:00', '20:00:00', 1),
(9, 5, '09:00:00', '14:00:00', 1),
(10, 5, '16:00:00', '20:00:00', 1);

-- BLOQUEOS DE EJEMPLO
INSERT INTO bloqueo_agenda (id_bloqueo, fecha, hora_inicio, hora_fin, motivo) VALUES
(1, '2026-12-25', NULL, NULL, 'Navidad'),
(2, '2026-01-01', NULL, NULL, 'Año nuevo'),
(3, '2026-04-22', '11:00:00', '12:30:00', 'Cita médica');

-- CITAS
-- Aquí ya meto solo citas reales, no huecos "libres"
-- porque el sistema nuevo debe calcular la disponibilidad
INSERT INTO cita (id_cita, fecha, hora_inicio, hora_fin, estado, id_paciente, id_servicio) VALUES
(1, '2026-03-01', '10:00:00', '11:00:00', 'cancelada', NULL, 1),
(2, '2026-03-01', '11:00:00', '11:45:00', 'cancelada', NULL, 2),
(3, '2026-03-01', '12:00:00', '13:00:00', 'completada', 1, 3),
(4, '2026-04-09', '16:59:00', '17:59:00', 'cancelada', NULL, 1),
(5, '2026-04-10', '16:30:00', '17:30:00', 'cancelada', NULL, 3),
(6, '2026-04-07', '14:11:00', '14:56:00', 'cancelada', NULL, 2),
(8, '2026-04-10', '22:18:00', '22:43:00', 'cancelada', NULL, 8);

COMMIT;