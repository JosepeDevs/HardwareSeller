-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql107.infinityfree.com
-- Generation Time: Feb 02, 2024 at 05:32 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_35787488_clientes_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `articulos`
--

CREATE TABLE `articulos` (
  `codigo` varchar(8) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `categoria` int(11) NOT NULL,
  `precio` float NOT NULL,
  `imagen` varchar(260) NOT NULL,
  `descuento` float NOT NULL,
  `activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `articulos`
--

INSERT INTO `articulos` (`codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `imagen`, `descuento`, `activo`) VALUES
('CAT00001', 'one-piece-speakers23', 'taco-cat-mouse-for-pc', 2, 52, 'taco-cat-pc-case.png', 2, 1),
('CAT00002', 'tacocat--a-cat-made-of-taco-body', 'tacocat--a-cat-made-of-taco-body', 1, 31231, 'taco-cat-pc-screen.png', 2.8, 0),
('CAT00005', 'taco-cat-keyboard 222222', 'taco-cat-keyboard 33333', 1, 555.3, 'taco-cat-keyboard.jpg', 32.5, 1),
('INF00001', 'pc-tower-case', 'pc-tower-case', 21, 555, 'pc-tower-case.png', 5, 1),
('INF00002', 'pc-speakers', 'pc-speakers', 21, 52, 'pc-speakers.jpg', 55, 1),
('INF00004', 'pc-screen', 'pc-screen', 21, 555, 'pc-screen.png', 5, 1),
('INF00006', 'pc-keyboard-with-4-rows-of-keys', 'pc-keyboard-with-4-rows-of-keys', 21, 21, 'pc-keyboard-with-4-rows-of-keys.jpg', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `codCategoriaPadre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`codigo`, `nombre`, `activo`, `codCategoriaPadre`) VALUES
(0, 'sin categoria', 1, 0),
(1, 'NFTs', 1, 1),
(2, 'PC parts', 0, 2),
(21, 'Pc parts_Screens', 1, 2),
(22, 'PC PARTS>Ratones PC', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `dni` varchar(9) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `localidad` varchar(30) NOT NULL,
  `provincia` varchar(30) NOT NULL,
  `telefono` varchar(9) NOT NULL,
  `email` varchar(30) NOT NULL,
  `psswrd` varchar(60) NOT NULL,
  `rol` enum('user','editor','admin') NOT NULL DEFAULT 'user',
  `activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`dni`, `nombre`, `direccion`, `localidad`, `provincia`, `telefono`, `email`, `psswrd`, `rol`, `activo`) VALUES
('05988330G', 'perico', 'palotes 15', 'palotelandi', 'afableland', '444555111', 'josepe@editor.com', '$2y$10$uiVWYxPsmYtxgk2rYOYImeioeM3BwENu5kF5GvWzX0/C3UscrRcI.', 'editor', 1),
('07369849M', 'DSAASD', 'ASDADSDAS', 'ASDASDASD', 'ASDADSDAS', '987987987', '987@SDA.COM', '$2y$10$p3uoZq1y6NEQW2rk1Z7Mr.N6i8HsQ4gp9cf8qT1Bo2XdwuyYX3cSa', 'user', 0),
('15746319J', '0000000', 'asddasd', 'asdsd', 'adsdas', '123132132', '444@444.111', '$2y$10$VKjwtk0Xz3jETRK6ImKyAeZB9lUAv90jgedGPCq4iLlF1yHPAwr0S', 'user', 0),
('19221903K', '0aaa0a0a0a0', 'asaddas', 'asdasd', 'asdasdsad', '123123123', 'jjjjjj@aaaa.com', '$2y$10$VSebBC.aI3/10t4YIQ8kP.fwqFnIVag.1nhdf3zQPbaA5e0985q3a', 'user', 1),
('25278439J', '111111', 'adsasd', 'asdasdasd', '44441242412', '432432432', '21242112@asdsasads.com', '$2y$10$WFYcySLwtI0cUGGET8dwqutC0gLhiFO96Zr2mxj6xPw8h3D0G5y0S', 'user', 1),
('31011429T', 'asddasd', 'asdsad', 'dasads', 'asdads', '333222111', 'josepe@aasdadsadsdmin.maestro', '$2y$10$4f9fcZb5a9TI4weMEdvg4OX5d54Odtlvec9Gh80rvE.eNmbB4SFgW', 'user', 1),
('32461264F', 'asdsad', 'asdasd', 'asdasd', 'asdasdasd', '123123123', '2312rf123r12rf12@asdda.com', '$2y$10$ln/F7YFQe7209McwmY7Q9u.jXSMKKvd1Ov4ftyHqQnGndT0Y4MHdO', 'user', 1),
('36493042T', 'fsdf', 'fsddfs', 'dsffds', 'asdasdasd', '123123123', 'adsdasdas@asdasda.com', '$2y$10$fnMPxOzOpiDCLM1BEqubo.FLo09x9uK2Y0xilBm0rDVlxvraAG13O', 'user', 1),
('36559924K', 'adsasd', 'dsadasdas', 'asdsadasd', 'asdasdasd', '111222333', 'dasdasda@fasf32f23.com', '$2y$10$u4s1E/qSZNgpvyVjNoD6fe5OQ/HrIJbpFK8j5RBuz3AkemBpCdaAa', 'user', 1),
('36918058E', 'dasads', 'asdasd', 'asdasd', 'asdasd', '123123123', '3123123124asa@fsdfd.com00', '$2y$10$f0z9I9WEIj12VSjFR8of4.VrbDkCJmGdR7ts0YCZy8f1AMVmgmM2G', 'user', 1),
('37195120A', 'dasddsa', 'asddasdas', 'asdasdasd', 'sadasdasd', '123123123', '115619189151@fsaa.com', '$2y$10$18W7Mojl2aMsjrJvTy74sOD9xYi581wwuKD7S7u9ZfwQ9zeeiLVW.', 'user', 1),
('42140862D', 'asddasdas', 'dasasd', 'dasads', 'dasdasasd', '123123123', 'dassdaasdsda@fasfsad.csafas', '$2y$10$vrAytPm5Op8/TvyGe4pQ9ue/cVb41lxpKaZqJfcP8yLDH9B27C.h2', 'user', 1),
('45712011V', '312312', '312312', '132312', '321312231', '123123122', 'fsdamf@fas.com', '$2y$10$tBq1sEIFcKsDQfciXRIc3eS15X2RcjVU0hx9rcfFc36.dv9v31vaC', 'user', 1),
('48042580L', 'efsfsefd', 'sfdsfds', 'fdsfdsdf', 'fsdsfdfsd', '543765234', 'hola@adios.com', '$2y$10$whjIpjv3KG2x0s2rP4KmBegXuVOoDCK7Dm6HlvV4PYBCKQI.dwvrO', 'user', 1),
('48229490P', 'adsdasads', 'asddasdas', 'sadsdasdasda', 'dsaasdasdsda', '432432432', '12312312fsdf@fasfas.com', '$2y$10$7EyBddMtsATUH/u2xZIkheXDdZfC1920OzdRzOrglMPqNlanzhj0q', 'user', 1),
('53228026T', 'sdadassad', 'dsaads', 'sdaasd', 'asdasdasd', '123123123', 'asfffssaf@ads.com', '$2y$10$7Xj49NpCOnHVk9riAXmJTeeNIDtVH.G9yhPenRCxlclTCgnS6ZX56', 'user', 0),
('57522859C', 'rgdbd', 'fgdgfddg', 'fdgfgfddgfdgffdg', 'gdgfdgf', '543765123', 'adios@hasta.nunca', '$2y$10$cSfBY97JjmWAzVIa2z3o1O0zbySvJJWUspu8JcZ9z4Z.JNuHuQdo6', 'user', 1),
('61322766M', 'dasdas', 'dasdas', 'sdadasdas', 'sdaasdads', '432432423', '12312312gedsg43@asfafsa.com', '$2y$10$sEdq6bRo9AUH45a5WncOQOPDcROCP.9C7k0DTgDEcoljH6xD9ckfy', 'user', 1),
('62512617C', 'adsasd', 'asddas', 'adsasd', 'asdasd', '123123123', 'asdasdasdasdas@fsasdf.com', '$2y$10$MHVlMe3EdzL4UPEV/r3tbez2s/5/4sx6Qlh/H1uPihUugv30AykKK', 'user', 1),
('68642910P', 'dasd', 'dassadasd', 'asddas', 'asdasd', '123123123', '213@31asd.com', '$2y$10$rPN0l.o8PR9orUDTpnv5O.JMuQcmbTe8ltvuGa/ygMDgQpzquLa2e', 'user', 1),
('70429723C', 'asdasddas', 'asdasd', 'asdasd', 'asdasd', '123123123', 'sadsdas@adsda.com', '$2y$10$a22oChCO1m.XxbRFYnaq9u6iH7YFbKftKwIhlM58h1tlf.wfFizwW', 'user', 1),
('70590985Y', 'das', 'asdasdasd', 'asdads', 'dasads', '123123123', 'as12dda3sa@ads.com', '$2y$10$KS6gQKW/x8gCHm476HdUAeMx96/LT8pENOGIMcvOGi/gN9dQEVnVq', 'user', 1),
('74387306Q', 'josepe', '123', '123', '444444', '111222333', 'josepe@admin.maestro', '$2y$10$IHENC78sZxz21UEjeIU2PeVjaIqkcOpa1An7hsROnLfMWp.PJ.M4i', 'admin', 1),
('76114741J', '12332121', '32321312231213', '312231321', '33333333333333333333333333333', '333444111', 'sd15asd5asd5f6a1sf6a@afsda.com', '$2y$10$euuBl4ewKS997q.q9gzV7Oj2VmaOyOkteChNI.iSO2AYkwhGNY6cy', 'user', 1),
('83564890W', 'esfd', 'fsdsdf', 'sdfsfdfsd', 'fsdfsdfsd', '444555111', '123@123.com', '$2y$10$PGxDI6X04V1f1GFnXjFWaepm8fmESTbAY6GW9TRN08rV5PXCXkDjq', 'user', 1),
('85157840H', 'thhgjjgj', 'jyjtyj', 'ytjytyjt', 'ytjytjyjt', '444666222', 'original@nunca.mas', '$2y$10$i.4J/9PtFbXxA.w1httmxeOd3IbEht.9D363Oya04Hj9ttbJ1AyQW', 'user', 1),
('87966242M', 'dasasd', 'asdasdads', 'asdasddas', 'asdasd', '123123123', 'r23r23r23r23r23@sda.com', '$2y$10$h4LKx1c9Dy9f6En.g7AR6.iEgKb5x/VRbJ/7BW2WZ/Ci9ATn1r9YC', 'user', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contenidopedido`
--

CREATE TABLE `contenidopedido` (
  `numPedido` int(11) NOT NULL,
  `numLinea` int(11) NOT NULL,
  `codArticulo` varchar(8) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` float NOT NULL,
  `descuento` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contenidopedido`
--

INSERT INTO `contenidopedido` (`numPedido`, `numLinea`, `codArticulo`, `cantidad`, `precio`, `descuento`) VALUES
(1, 1, 'CAT00001', 1, 55, 5),
(1, 2, 'CAT00002', 3, 200, 10),
(2, 1, 'CAT00001', 1, 100, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `idPedido` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` float NOT NULL,
  `estado` smallint(6) NOT NULL,
  `codUsuario` varchar(9) NOT NULL,
  `activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`idPedido`, `fecha`, `total`, `estado`, `codUsuario`, `activo`) VALUES
(1, '2024-02-02', 55, 1, '74387306Q', 1),
(2, '2024-02-01', 60, 0, '74387306Q', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `fk_articulos_categorias` (`categoria`),
  ADD KEY `idx_codigo` (`codigo`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`codigo`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`dni`);

--
-- Indexes for table `contenidopedido`
--
ALTER TABLE `contenidopedido`
  ADD PRIMARY KEY (`numPedido`,`numLinea`),
  ADD KEY `idx_idArticulo` (`codArticulo`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`idPedido`),
  ADD KEY `codUsuario` (`codUsuario`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articulos`
--
ALTER TABLE `articulos`
  ADD CONSTRAINT `fk_articulos_categorias` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`codigo`);

--
-- Constraints for table `contenidopedido`
--
ALTER TABLE `contenidopedido`
  ADD CONSTRAINT `fk_contenidoPedido_articulos` FOREIGN KEY (`codArticulo`) REFERENCES `articulos` (`codigo`),
  ADD CONSTRAINT `fk_contenidoPedido_pedidos` FOREIGN KEY (`numPedido`) REFERENCES `pedidos` (`idPedido`);

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`codUsuario`) REFERENCES `clientes` (`dni`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
