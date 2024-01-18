-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2023 at 11:56 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clientes_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `articulos`
--

CREATE TABLE `articulos` (
  `codigo` varchar(8) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(60) NOT NULL,
  `categoria` varchar(15) NOT NULL,
  `precio` int(11) NOT NULL,
  `imagen` varchar(260) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articulos`
--

INSERT INTO `articulos` (`codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `imagen`) VALUES
('CAT00002', 'Taco cat Screen', 'Taco screen edición limitada', 'informáctica', 25, 'hemerotecaBD/taco-cat-pc-screen.png'),
('CAT00003', 'taco-cat-pc-case', 'taco-cat-pc-case', 'informática', 25, 'hemerotecaBD/taco-cat-pc-case.png'),
('CAT00004', 'altavoces de gato-taco', 'altavoces de gato-taco', 'informatica', 25, 'hemerotecaBD\\taco-cat-mouse-for-pc.png'),
('CAT00005', 'taco-cat-keyboard', 'taco-cat-keyboard', 'informatica', 52, 'hemerotecaBD/taco-cat-keyboard.jpg'),
('CAT00055', 'taco-cat-mouse-for-pc', 'taco-cat-mouse-for-pc', 'informatica', 52, 'hemerotecaBD/taco-cat-mouse-for-pc.jpg'),
('CAT00634', 'tacocat--a-cat-made-of-taco-body', 'tacocat--a-cat-made-of-taco-body', 'NFT', 32423, 'hemerotecaBD\\tacocat--a-cat-made-of-taco-body.png'),
('INF00001', 'pc- mouse', 'pc-mouse taco cat pallete', 'informática', 500, 'hemerotecaBD/pc-mouse.jpg'),
('INF00002', 'pc-screen', 'pc-screen tacocat pallete', 'informática', 25, 'hemerotecaBD/pc-screen.png'),
('INF00003', 'pc-keyboard-with-4-rows-of-keys', 'pc-keyboard-with-4-rows-of-keys taco cat pallete', 'informática', 50, 'hemerotecaBD/pc-keyboard-with-4-rows-of-keys.jpg'),
('INF00004', 'pc-tower-case', 'pc-tower-case taco cat pallete eddition', 'informática', 600, 'hemerotecaBD/pc-tower-case.png'),
('INF00006', 'pc-speakers', 'artilugio que le gusta hablarte de sus problemas diarios', 'informática', 500, 'hemerotecaBD/pc-speakers.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `dni` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localidad` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `psswrd` varchar(120) NOT NULL,
  `rol` enum('admin','user','editor') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`dni`, `nombre`, `direccion`, `localidad`, `provincia`, `telefono`, `email`, `psswrd`, `rol`) VALUES
('11111111H', 'Probando nombre', 'calle 13', 'Ciudad Z', 'Pronvicia fria', '777555444', 'jaja@memeo.com', '$2y$10$9bu7AuE/ngUKle3RlKQf.O2iR4oPblkCYyaTqqISVFeYDZbslfPva', 'user'),
('11224466Y', 'Hombre del espacio', 'marte', 'crater 5', 'crateres del sur', '555666454', 'marte@solito.com', '$2y$10$wR2AJkJu1dZtuWhSgOXIIuR6j5RS9TeVodNhY/lmQN4W0knSgDxKW', 'user'),
('12345122X', 'dasda', 'adsasd', 'ads', 'dsaads', '123123123', 'dasads@dasd.com', '$2y$10$wLJIMgbuZ/a45WOatgvZ8OrBL0Zx.Rjiro2uJwH.Ij7xHB6x.KX/C', 'user'),
('23432533H', 'Ana Maria', 'Wow ', 'ELda', 'Alicante', '223344551', 'adios@w.com', '$2y$10$yej36CgLfpym15h8ags1tOb2hOfrqjly3mzp0TOdxUvDJgXgQpMdu', 'user'),
('33333332F', 'Jamaicano', 'calle 1231', 'Ciudad W', 'Pronvicia calida', '111333444', 'calorcito@rico.com', '$2y$10$4v66NzH/xI3V0HPn5LMY6.U./4Q0aS2F9ewWIFcEzpQzlgqtPt.sm', 'user'),
('34910012Z', 'Orion', 'Gatolandia', 'encima del armario', 'Mi casa', '123321123', 'orion@doy.porsaco', '$2y$10$FvDlLmsyRER752kOb6W1DOkMeOTAaB5VuoXuN1h3m4AO/xq1Rmuxu', 'user'),
('44444444A', 'Perico martinez', 'Grillolandia', 'Insectolandia', 'Landialandia', '111444222', 'grillado@mucho.com', '$2y$10$AhfEri7Qv8pUZMw/6shcmuHW3uQlp5WGkXNCv5/fLxW/pr3UQ2yv6', 'user'),
('44886655Q', 'Cliente de nombre nuevo', 'Calle 3', 'Localidad marciana', 'Norte de Europa', '222333444', 'nuevo@cliente.com', '$2y$10$BI8vMZR3LyEKeB6ehdyZ0eP4s1VrxmSaQmVYP7JiL6NnNMfs28Foi', 'user'),
('55555555K', 'Editor JP', 'editorlandia', 'Cambios', 'Everywhere', '444222555', 'josepe@editor.com', '$2y$10$ATPwibuiR4lpV7t3zoKEkut7ZQigYxIu9YG.50v5khnMecarV24WW', 'editor'),
('74387306Q', 'Josepe', 'HQ', 'HQ city', 'HQ provincia', '666555444', 'josepe@admin.maestro', '$2y$10$UF69d88QzmeUepOYfGkE4u/S9KBiZFLcYFUpMMH2O8r66WQtHEcZG', 'admin'),
('75869765X', 'Yesica', 'campo', 'matola', 'alicante', '432432123', 'yes@ica.com', '$2y$10$l4GDv64x5F.i7mnJQHkiI.zlcdtzMmkl/zpMTvJumcvQJl.AS4kWm', 'user'),
('88131553S', 'X-man', 'Casa de Charles Xavier', 'Poblado', 'DC comics', '666222333', 'charles@xavier.com', '$2y$10$0MIg/tUMIEQGWxttXAwBqu43JVnhBVTcBHtBg.Xo/2T.7ywzJ.MU.', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`codigo`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`dni`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
