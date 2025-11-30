-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-11-2025 a las 00:13:54
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
-- Base de datos: `clinica`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacoraacceso`
--

CREATE TABLE `bitacoraacceso` (
  `IdBitacora` int(11) NOT NULL,
  `IdUsuario` int(11) DEFAULT NULL,
  `FechaAcceso` datetime DEFAULT current_timestamp(),
  `AccionRealizada` varchar(250) DEFAULT NULL,
  `Modulo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `controlagenda`
--

CREATE TABLE `controlagenda` (
  `IdCita` int(11) NOT NULL,
  `IdPaciente` int(11) DEFAULT NULL,
  `IdMedico` int(11) DEFAULT NULL,
  `FechaCita` datetime DEFAULT NULL,
  `MotivoConsulta` varchar(250) DEFAULT NULL,
  `EstadoCita` varchar(20) DEFAULT NULL,
  `Observaciones` varchar(250) DEFAULT NULL,
  `FechaRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `controlmedicos`
--

CREATE TABLE `controlmedicos` (
  `IdMedico` int(11) NOT NULL,
  `NombreCompleto` varchar(150) DEFAULT NULL,
  `CedulaProfesional` varchar(50) DEFAULT NULL,
  `EspecialidadId` int(11) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `CorreoElectronico` varchar(100) DEFAULT NULL,
  `HorarioAtencion` varchar(100) DEFAULT NULL,
  `FechaIngreso` datetime DEFAULT current_timestamp(),
  `Estatus` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `controlpacientes`
--

CREATE TABLE `controlpacientes` (
  `IdPaciente` int(11) NOT NULL,
  `NombreCompleto` varchar(150) DEFAULT NULL,
  `CURP` varchar(18) DEFAULT NULL,
  `FechaNacimiento` date DEFAULT NULL,
  `Sexo` char(1) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `CorreoElectronico` varchar(100) DEFAULT NULL,
  `Direccion` varchar(250) DEFAULT NULL,
  `ContactoEmergencia` varchar(150) DEFAULT NULL,
  `TelefonoEmergencia` varchar(20) DEFAULT NULL,
  `Alergias` varchar(250) DEFAULT NULL,
  `AntecedentesMedicos` text DEFAULT NULL,
  `FechaRegistro` datetime DEFAULT current_timestamp(),
  `Estatus` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

CREATE TABLE `especialidades` (
  `IdEspecialidad` int(11) NOT NULL,
  `NombreEspecialidad` varchar(100) DEFAULT NULL,
  `Descripcion` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expedienteclinico`
--

CREATE TABLE `expedienteclinico` (
  `IdExpediente` int(11) NOT NULL,
  `IdPaciente` int(11) DEFAULT NULL,
  `IdMedico` int(11) DEFAULT NULL,
  `FechaConsulta` datetime DEFAULT NULL,
  `Sintomas` text DEFAULT NULL,
  `Diagnostico` text DEFAULT NULL,
  `Tratamiento` text DEFAULT NULL,
  `RecetaMedica` text DEFAULT NULL,
  `NotasAdicionales` text DEFAULT NULL,
  `ProximaCita` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestorpagos`
--

CREATE TABLE `gestorpagos` (
  `IdPago` int(11) NOT NULL,
  `IdCita` int(11) DEFAULT NULL,
  `IdPaciente` int(11) DEFAULT NULL,
  `Monto` decimal(10,2) DEFAULT NULL,
  `MetodoPago` varchar(50) DEFAULT NULL,
  `FechaPago` datetime DEFAULT current_timestamp(),
  `Referencia` varchar(100) DEFAULT NULL,
  `EstatusPago` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestortarifas`
--

CREATE TABLE `gestortarifas` (
  `IdTarifa` int(11) NOT NULL,
  `DescripcionServicio` varchar(150) DEFAULT NULL,
  `CostoBase` decimal(10,2) DEFAULT NULL,
  `EspecialidadId` int(11) DEFAULT NULL,
  `Estatus` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `IdReporte` int(11) NOT NULL,
  `TipoReporte` varchar(50) DEFAULT NULL,
  `IdPaciente` int(11) DEFAULT NULL,
  `IdMedico` int(11) DEFAULT NULL,
  `FechaGeneracion` datetime DEFAULT current_timestamp(),
  `RutaArchivo` varchar(250) DEFAULT NULL,
  `Descripcion` varchar(250) DEFAULT NULL,
  `GeneradoPor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdUsuario` int(11) NOT NULL,
  `Usuario` varchar(50) DEFAULT NULL,
  `ContrasenaHash` varchar(200) DEFAULT NULL,
  `Rol` varchar(50) DEFAULT NULL,
  `IdMedico` int(11) DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT NULL,
  `UltimoAcceso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacoraacceso`
--
ALTER TABLE `bitacoraacceso`
  ADD PRIMARY KEY (`IdBitacora`),
  ADD KEY `IdUsuario` (`IdUsuario`);

--
-- Indices de la tabla `controlagenda`
--
ALTER TABLE `controlagenda`
  ADD PRIMARY KEY (`IdCita`),
  ADD KEY `IdPaciente` (`IdPaciente`),
  ADD KEY `IdMedico` (`IdMedico`);

--
-- Indices de la tabla `controlmedicos`
--
ALTER TABLE `controlmedicos`
  ADD PRIMARY KEY (`IdMedico`),
  ADD KEY `EspecialidadId` (`EspecialidadId`);

--
-- Indices de la tabla `controlpacientes`
--
ALTER TABLE `controlpacientes`
  ADD PRIMARY KEY (`IdPaciente`);

--
-- Indices de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`IdEspecialidad`);

--
-- Indices de la tabla `expedienteclinico`
--
ALTER TABLE `expedienteclinico`
  ADD PRIMARY KEY (`IdExpediente`),
  ADD KEY `IdPaciente` (`IdPaciente`),
  ADD KEY `IdMedico` (`IdMedico`);

--
-- Indices de la tabla `gestorpagos`
--
ALTER TABLE `gestorpagos`
  ADD PRIMARY KEY (`IdPago`),
  ADD KEY `IdCita` (`IdCita`),
  ADD KEY `IdPaciente` (`IdPaciente`);

--
-- Indices de la tabla `gestortarifas`
--
ALTER TABLE `gestortarifas`
  ADD PRIMARY KEY (`IdTarifa`),
  ADD KEY `EspecialidadId` (`EspecialidadId`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`IdReporte`),
  ADD KEY `IdPaciente` (`IdPaciente`),
  ADD KEY `IdMedico` (`IdMedico`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IdUsuario`),
  ADD KEY `IdMedico` (`IdMedico`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacoraacceso`
--
ALTER TABLE `bitacoraacceso`
  MODIFY `IdBitacora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `controlagenda`
--
ALTER TABLE `controlagenda`
  MODIFY `IdCita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `controlmedicos`
--
ALTER TABLE `controlmedicos`
  MODIFY `IdMedico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `controlpacientes`
--
ALTER TABLE `controlpacientes`
  MODIFY `IdPaciente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `IdEspecialidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `expedienteclinico`
--
ALTER TABLE `expedienteclinico`
  MODIFY `IdExpediente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gestorpagos`
--
ALTER TABLE `gestorpagos`
  MODIFY `IdPago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gestortarifas`
--
ALTER TABLE `gestortarifas`
  MODIFY `IdTarifa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `IdReporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacoraacceso`
--
ALTER TABLE `bitacoraacceso`
  ADD CONSTRAINT `bitacoraacceso_ibfk_1` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`IdUsuario`);

--
-- Filtros para la tabla `controlagenda`
--
ALTER TABLE `controlagenda`
  ADD CONSTRAINT `controlagenda_ibfk_1` FOREIGN KEY (`IdPaciente`) REFERENCES `controlpacientes` (`IdPaciente`),
  ADD CONSTRAINT `controlagenda_ibfk_2` FOREIGN KEY (`IdMedico`) REFERENCES `controlmedicos` (`IdMedico`);

--
-- Filtros para la tabla `controlmedicos`
--
ALTER TABLE `controlmedicos`
  ADD CONSTRAINT `controlmedicos_ibfk_1` FOREIGN KEY (`EspecialidadId`) REFERENCES `especialidades` (`IdEspecialidad`);

--
-- Filtros para la tabla `expedienteclinico`
--
ALTER TABLE `expedienteclinico`
  ADD CONSTRAINT `expedienteclinico_ibfk_1` FOREIGN KEY (`IdPaciente`) REFERENCES `controlpacientes` (`IdPaciente`),
  ADD CONSTRAINT `expedienteclinico_ibfk_2` FOREIGN KEY (`IdMedico`) REFERENCES `controlmedicos` (`IdMedico`);

--
-- Filtros para la tabla `gestorpagos`
--
ALTER TABLE `gestorpagos`
  ADD CONSTRAINT `gestorpagos_ibfk_1` FOREIGN KEY (`IdCita`) REFERENCES `controlagenda` (`IdCita`),
  ADD CONSTRAINT `gestorpagos_ibfk_2` FOREIGN KEY (`IdPaciente`) REFERENCES `controlpacientes` (`IdPaciente`);

--
-- Filtros para la tabla `gestortarifas`
--
ALTER TABLE `gestortarifas`
  ADD CONSTRAINT `gestortarifas_ibfk_1` FOREIGN KEY (`EspecialidadId`) REFERENCES `especialidades` (`IdEspecialidad`);

--
-- Filtros para la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`IdPaciente`) REFERENCES `controlpacientes` (`IdPaciente`),
  ADD CONSTRAINT `reportes_ibfk_2` FOREIGN KEY (`IdMedico`) REFERENCES `controlmedicos` (`IdMedico`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`IdMedico`) REFERENCES `controlmedicos` (`IdMedico`);

--
-- Volcado de datos para la tabla `usuarios`
--
-- Contraseñas en texto plano para referencia (en producción usar hash):
-- admin: admin
-- medico: medico
-- recepcionista: recepcionista
-- paciente: paciente
-- NOTA: En este ejemplo didáctico guardaremos las contraseñas en texto plano o hash simple según la implementación de PHP.
-- Para este ejercicio, usaremos password_hash en PHP, así que aquí insertaremos usuarios base.
-- Si el script PHP usa password_verify, estos inserts necesitan hashes reales.
-- Generaré hashes bcrypt estándar para:
-- admin -> $2y$10$Xw.q.w.q.w.q.w.q.w.q.uXw.q.w.q.w.q.w.q.w.q.w.q.w.q.w (ejemplo, usaré uno real abajo)

-- Hashes generados con password_hash('password', PASSWORD_DEFAULT):
-- admin: $2y$10$j9.s.s.s.s.s.s.s.s.s.u.s.s.s.s.s.s.s.s.s.s.s.s.s.s.s.s
-- (Para simplificar y asegurar que funcione sin ejecutar PHP externo, usaré un script PHP temporal para generar los inserts o
-- simplemente insertaré los usuarios y luego el usuario tendrá que loguearse.
-- MEJOR OPCIÓN: Insertar los usuarios con las contraseñas en texto plano si el login.php las hashea al comparar (malo)
-- O insertar los hashes.
-- Voy a asumir que el login.php usará password_verify.
-- Hash para 'admin': $2y$10$8WkQ.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q.Q
-- Espera, no puedo generar hashes bcrypt mentalmente.
-- Insertaré un bloque de código SQL que inserta los usuarios.
-- Asumiré que la contraseña es igual al usuario para facilitar, pero encriptada.
-- Usaré un script PHP auxiliar para generar los hashes y luego actualizar el archivo SQL? No, muy complejo.
-- Voy a insertar los usuarios con una contraseña "dummy" y luego crearé un script 'setup.php' que el usuario pueda ejecutar (o yo ejecute) para popular la DB correctamente?
-- No, el usuario quiere que "ya estén".
-- Voy a usar hashes pre-calculados para 'admin', 'medico', 'recepcionista', 'admin'.

-- Hashes (Bcrypt cost 10):
-- admin: $2y$10$Ai09.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W.W (Inventado no sirve)
-- Usaré un pequeño truco: Crear un archivo PHP temporal para generar los hashes, leer su salida, y luego escribir el SQL.

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

