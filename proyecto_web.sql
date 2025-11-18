-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-11-2025 a las 04:43:37
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
-- Base de datos: `proyecto_web`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_admin` int(11) NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `nivel` tinyint(4) DEFAULT NULL COMMENT '1 = Super admin, 2 = Basico',
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(6,2) DEFAULT NULL,
  `subtotal` decimal(8,2) DEFAULT NULL,
  `nota_detalle` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `numero_orden` varchar(20) DEFAULT NULL COMMENT 'Visible en mostrador',
  `matricula` varchar(20) DEFAULT NULL,
  `fecha_hora_pedido` datetime DEFAULT current_timestamp(),
  `estado_pedido` enum('pendiente','en_preparacion','listo','entregado','cancelado') DEFAULT 'pendiente',
  `total` decimal(8,2) DEFAULT NULL,
  `fecha_hora_pago` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `sancionado` tinyint(1) DEFAULT 0 COMMENT 'Si no recogio pedido previo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos`
--

CREATE TABLE `periodos` (
  `id_periodo` int(11) NOT NULL,
  `mes` tinyint(4) DEFAULT NULL COMMENT '1 = enero, 2 = febrero, etc.',
  `anio` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `precio` decimal(6,2) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `tipo` enum('comida','bebida','promo') NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `precio`, `disponible`, `tipo`, `imagen_url`, `id_categoria`) VALUES
(1, 'Hamburguesa con papas', 'Hamburguesa con queso, papas fritas y aderezo especial de la casa.', 65.00, 1, 'comida', 'imagenes/hamburguesa.png', NULL),
(2, 'Torta Cubana', 'Torta cubana con jamón, salchicha, milanesa, queso amarillo, quesillo y aguacate.', 60.00, 1, 'comida', 'imagenes/tortacubana.png', NULL),
(3, 'Enchiladas', 'Enchiladas rojas rellenas de pollo, acompañadas de lechuga, crema, queso fresco y cebolla.', 60.00, 1, 'comida', 'imagenes/enchilada.png', NULL),
(4, 'Hot Dog', 'Hot dog con salchicha jumbo, pan suave, mostaza, cátsup, mayonesa, cebolla frita y tocino.', 60.00, 1, 'comida', 'imagenes/hotdog.png', NULL),
(5, 'Chilaquiles', 'Chilaquiles verdes con pollo, crema, queso y cebolla morada, acompañados de frijoles refritos.', 70.00, 1, 'comida', 'imagenes/chilaquil.png', NULL),
(6, 'Burrito', 'Burrito de carne asada con frijoles refritos, arroz, queso, lechuga y salsa pico de gallo.', 61.00, 1, 'comida', 'imagenes/burrito.png', NULL),
(7, 'Hotcakes', 'Hotcakes esponjosos con miel de maple y mantequilla, servidos con fruta de temporada.', 60.00, 1, 'comida', 'imagenes/hotcake.png', NULL),
(8, 'Enfrijoladas', 'Enfrijoladas rellenas de queso fresco, bañadas en salsa de frijol, con crema y aguacate.', 60.00, 1, 'comida', 'imagenes/enfrijolada.png', NULL),
(9, 'Coditos con Crema', 'Pasta de coditos en crema con jamón y queso gratinado.', 60.00, 1, 'comida', 'imagenes/coditos.png', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reseñas`
--

CREATE TABLE `reseñas` (
  `id_reseña` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `calificación` tinyint(4) DEFAULT NULL CHECK (`calificación` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reseñas_productos`
--

CREATE TABLE `reseñas_productos` (
  `id_review` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `calificacion` int(11) DEFAULT NULL CHECK (`calificacion` between 1 and 5),
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `matricula` varchar(20) NOT NULL COMMENT 'Matricula unica del estudiante',
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL COMMENT 'Correo institucional',
  `contraseña` varchar(255) DEFAULT NULL,
  `bloqueado` tinyint(1) DEFAULT 0 COMMENT 'Si tiene sancion activa',
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`matricula`, `nombre`, `correo`, `contraseña`, `bloqueado`, `fecha_registro`) VALUES
('202400236', 'Edwin Diaz Tec', '202400236@upgroo.edu.mx', '123', 0, '2025-07-15 00:00:00'),
('202400272', 'Juan Morales', '202400272@upgroo.edu.mx', '123456', 0, '2025-07-13 13:03:15'),
('2025', 'juan', 'juanmolan66@gmail.com', '123', 0, '2025-07-25 00:00:00'),
('prueba', 'Juan', 'isawwi@gmail.com', '123', 0, '2025-11-18 00:00:00'),
('prueba123', 'Usuario de Prueba', 'prueba123@ejemplo.com', 'hashed_password', 0, '2025-07-13 00:35:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_mensuales`
--

CREATE TABLE `ventas_mensuales` (
  `id_venta` int(11) NOT NULL,
  `periodo_id` int(11) DEFAULT NULL,
  `total_ventas` decimal(12,2) DEFAULT NULL,
  `total_pedidos` int(11) DEFAULT NULL,
  `producto_top` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indices de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`);

--
-- Indices de la tabla `periodos`
--
ALTER TABLE `periodos`
  ADD PRIMARY KEY (`id_periodo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `reseñas`
--
ALTER TABLE `reseñas`
  ADD PRIMARY KEY (`id_reseña`);

--
-- Indices de la tabla `reseñas_productos`
--
ALTER TABLE `reseñas_productos`
  ADD PRIMARY KEY (`id_review`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`matricula`);

--
-- Indices de la tabla `ventas_mensuales`
--
ALTER TABLE `ventas_mensuales`
  ADD PRIMARY KEY (`id_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `periodos`
--
ALTER TABLE `periodos`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `reseñas`
--
ALTER TABLE `reseñas`
  MODIFY `id_reseña` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reseñas_productos`
--
ALTER TABLE `reseñas_productos`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas_mensuales`
--
ALTER TABLE `ventas_mensuales`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_productos` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
