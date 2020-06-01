USE dbturnos;

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL,
  `fecha_turno` date NOT NULL,
  `hora_turno` time DEFAULT NULL,
  `nombre_paciente` varchar(300) COLLATE utf8_spanish2_ci NOT NULL,
  `email` varchar(300) COLLATE utf8_spanish2_ci NOT NULL,
  `telefono` varchar(300) COLLATE utf8_spanish2_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `talla_calzado` int(11) DEFAULT NULL,
  `altura` int(11) DEFAULT NULL,
  `color_pelo` varchar(30) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `imagen` mediumblob,
  `tipo_imagen` varchar(30) COLLATE utf8_spanish2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

ALTER TABLE `turnos` ADD PRIMARY KEY (`id`);

ALTER TABLE `turnos`MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;