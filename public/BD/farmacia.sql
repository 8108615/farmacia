-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-11-2026 a las 04:26:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `farmacia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajustes`
--

CREATE TABLE `ajustes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `divisa` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ajustes`
--

INSERT INTO `ajustes` (`id`, `nombre`, `descripcion`, `direccion`, `telefono`, `email`, `divisa`, `logo`, `web`, `created_at`, `updated_at`) VALUES
(1, 'Farmacia Demo', 'Configuracion inicial del sistema', 'Av. Principal 123 - Centro', '987654321', 'contacto@farmacia.com', 'Bs', 'ajustes/ChsdWiwBPXA9DFr2KthhYgtMMf6aWVVCRFP4oOQT.jpg', 'https://farmacia.test', '2026-06-09 07:46:21', '2026-06-09 07:46:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueos`
--

CREATE TABLE `arqueos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `caja_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_inicial` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_ingresos` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_egresos` decimal(12,2) NOT NULL DEFAULT 0.00,
  `monto_contado` decimal(12,2) NOT NULL DEFAULT 0.00,
  `diferencia` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado` enum('abierto','cerrado') NOT NULL DEFAULT 'abierto',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueo_detalles`
--

CREATE TABLE `arqueo_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `arqueo_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL DEFAULT 'ingreso',
  `concepto` varchar(255) NOT NULL,
  `monto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `referencia` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` enum('abierta','cerrada') NOT NULL DEFAULT 'cerrada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Higiene', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(2, 'Veterinaria', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(3, 'Hogar', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(4, 'Bebidas', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(5, 'Cuidado Personal', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(6, 'Suplementos', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(7, 'Dermocosmética', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(8, 'Bebés', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(9, 'Medicamentos', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(10, 'Aparatos Médicos', '2026-06-09 07:46:22', '2026-06-09 07:46:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ci_nit` varchar(50) NOT NULL,
  `nombres_apellidos` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `ci_nit`, `nombres_apellidos`, `email`, `telefono`) VALUES
(1, '685970505', 'Dr. Francisca Ortíz', 'oaguayo@example.com', NULL),
(2, '122041184', 'Dr. Malak Nieves Tercero', 'noelia.acuna@example.com', '929 416460'),
(3, '298494464', 'Inmaculada Bahena', 'zluis@example.net', NULL),
(4, '079798977', 'Srta. Nayara Almaráz', 'ualonzo@example.net', '+34 910-720828'),
(5, '543734975', 'D. Biel Vega', NULL, '+34 913 066271'),
(6, '366025766', 'Ing. Blanca Aranda Tercero', 'yago.solano@example.net', '+34 983313600'),
(7, '957230387', 'Ángela Saavedra', 'oriol.zapata@example.org', NULL),
(8, '590902481', 'Antonio Valencia', 'manzanares.yolanda@example.net', '+34 944 288147'),
(9, '031739096', 'Dr. Diego Cordero', NULL, NULL),
(10, '974947684', 'Alex Prado', 'ochoa.maria@example.com', '900 00 0195'),
(11, '735452503', 'Aina Rodríquez', 'mariapilar96@example.com', '+34 914-681956'),
(12, '558801991', 'Eva Ruíz Hijo', NULL, NULL),
(13, '714692512', 'Dr. Óscar Mena Tercero', 'izan07@example.net', '+34 930 73 9001'),
(14, '036862737', 'María Dolores Zamora', NULL, '941 815009'),
(15, '210406384', 'D. Samuel Prieto Tercero', NULL, '998-433943'),
(16, '449720097', 'Rocío Aponte Hijo', 'velez.izan@example.net', NULL),
(17, '611808713', 'Gabriela Ríos', 'arias.valentina@example.net', '936 17 8521'),
(18, '069609918', 'Alejandro Silva', 'alberto.garay@example.net', '+34 937-102369'),
(19, '339438542', 'Ing. Olivia Badillo Tercero', 'montero.lara@example.net', '+34 951-71-8474'),
(20, '702577646', 'Srta. Ana Arreola', 'roberto.luis@example.com', '+34 922 68 1409');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_id` bigint(20) UNSIGNED NOT NULL,
  `proveedor_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_compra` date NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `nota` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_detalles`
--

CREATE TABLE `compra_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `compra_id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `lote_id` bigint(20) UNSIGNED DEFAULT NULL,
  `precio_compra_unidad` decimal(12,2) NOT NULL,
  `precio_venta_unidad` decimal(12,2) NOT NULL,
  `porcentaje_ganancia_unidad` decimal(5,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_tmps`
--

CREATE TABLE `compra_tmps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `precio_compra_unidad` decimal(12,2) NOT NULL,
  `precio_venta_unidad` decimal(12,2) NOT NULL,
  `porcentaje_ganancia_unidad` decimal(5,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `estado` enum('activo','inactivo','pendiente','confirmado','cancelado') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compra_tmps`
--

INSERT INTO `compra_tmps` (`id`, `usuario_id`, `sucursal_id`, `producto_id`, `precio_compra_unidad`, `precio_venta_unidad`, `porcentaje_ganancia_unidad`, `cantidad`, `fecha_creacion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 11, 1, 5, 0.00, 0.00, 0.00, 0, '2026-11-10 06:25:09', 'activo', '2026-11-10 10:25:09', '2026-11-10 10:25:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_id` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `tipo_doc` enum('CI','DNI','RUC','PASAPORTE','OTRO') NOT NULL,
  `numero_doc` varchar(50) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `direccion` text NOT NULL,
  `profesion` varchar(150) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('M','F') NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `usuario_id`, `sucursal_id`, `nombres`, `apellidos`, `tipo_doc`, `numero_doc`, `telefono`, `direccion`, `profesion`, `fecha_nacimiento`, `genero`, `avatar`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Alba', 'Cisneros', 'CI', '3236345934', '+34 946 81 8715', 'Ronda Juan, 26, 5º A, 74153, Soler del Penedès', 'nulla', '1988-02-10', 'M', NULL, 'activo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(2, 2, 2, 'Malak', 'Rodrigo', 'CI', '3008104124', '+34 970 12 6917', 'Camino Hernádez, 173, 6º B, 99053, Villa Macías', 'odit', '2006-08-04', 'F', NULL, 'activo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(3, 3, 2, 'Santiago', 'Caldera', 'DNI', '2827406384', '936 53 4872', 'Plaza Badillo, 25, 3º F, 60958, A Amaya de la Sierra', 'numquam', '1999-11-07', 'M', NULL, 'activo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(4, 4, 4, 'Ana', 'Piñeiro', 'PASAPORTE', '4625830284', '923-86-3340', 'Rúa Nieves, 258, 3º E, 55474, Esquibel del Barco', 'sed', '1978-01-29', 'F', NULL, 'activo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(5, 5, 3, 'Sonia', 'Luján', 'CI', '2463514754', '941 42 8784', 'Travesía Pardo, 3, 30º B, 35463, As Ballesteros', 'optio', '1987-10-28', 'M', NULL, 'activo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(6, 6, 2, 'Samuel', 'Espinal', 'PASAPORTE', '6895834944', '963-751584', 'Rúa Nieves, 38, 9º C, 55430, Cortez de Arriba', 'natus', '2003-06-26', 'F', NULL, 'activo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(7, 7, 3, 'Antonio', 'Domínquez', 'RUC', '5458681417', '913122990', 'Plaza Quintana, 613, 84º A, 36391, Las Castillo', 'non', '2002-11-21', 'M', NULL, 'activo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(8, 8, 2, 'Patricia', 'Giménez', 'CI', '6114130168', '+34 942-247758', 'Camiño Biel, 968, 74º E, 28567, A Bustamante del Vallès', 'quo', '1971-09-04', 'F', NULL, 'activo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(9, 9, 1, 'Jon', 'Monroy', 'DNI', '1236801485', '+34 912 72 7389', 'Carrer Caldera, 79, 39º D, 75228, Luis de San Pedro', 'sint', '2004-02-27', 'M', NULL, 'inactivo', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(10, 10, 2, 'Unai', 'Solorio', 'PASAPORTE', '7566100941', '+34 955-24-7393', 'Rúa Andrés, 415, 2º F, 32313, Vall Ceballos de Lemos', 'voluptatem', '2005-05-15', 'F', NULL, 'inactivo', '2026-06-09 07:46:22', '2026-06-09 07:46:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forma_farmaceuticas`
--

CREATE TABLE `forma_farmaceuticas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `forma_farmaceuticas`
--

INSERT INTO `forma_farmaceuticas` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'POMADA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(2, 'UNGÜENTO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(3, 'GEL', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(4, 'CREMA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(5, 'AEROSOL', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(6, 'NEBULIZADOR', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(7, 'TABLETA SUBLINGUAL', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(8, 'CÁPSULA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(9, 'INHALADOR', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(10, 'JARABE', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(11, 'EMULSIÓN', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(12, 'SOLUCIÓN', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(13, 'COMPRIMIDO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(14, 'GRANULADO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(15, 'TABLETA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(16, 'SUPOSITORIO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(17, 'PASTILLA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(18, 'LOCIÓN', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(19, 'POLVO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(20, 'SUSPENSIÓN', '2026-06-09 07:46:23', '2026-06-09 07:46:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarios`
--

CREATE TABLE `inventarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lote_id` bigint(20) UNSIGNED NOT NULL,
  `ubicacion_fisica_id` bigint(20) UNSIGNED NOT NULL,
  `compra_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sucursal_id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `precio_compra_unidad` decimal(12,2) NOT NULL,
  `precio_venta_unidad` decimal(12,2) NOT NULL,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `stock_maximo` int(11) NOT NULL DEFAULT 0,
  `fecha_registro` date DEFAULT NULL,
  `estado` enum('compra','ajuste','traslado') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `laboratorios`
--

CREATE TABLE `laboratorios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `laboratorios`
--

INSERT INTO `laboratorios` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'ARELLANO-COLUNGA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(2, 'CALDERÓN-FONSECA Y FLIA.', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(3, 'NIETO Y LEAL', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(4, 'GESTORA SAMANIEGO-ESPINAL', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(5, 'LEÓN Y BARRIOS E HIJA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(6, 'BUENO DE CARMONA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(7, 'MEZA, CLEMENTE Y BARAJAS S. DE H.', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(8, 'ORNELAS Y PELÁEZ S. DE H.', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(9, 'VEGA Y CORTEZ', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(10, 'PERALTA DE MANZANO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(11, 'ASOCIACIÓN JURADO-MATOS', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(12, 'CARDONA, CASANOVA Y ESPINAL SRL', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(13, 'CORPORACIÓN LUEVANO SA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(14, 'ASOCIACIÓN IRIZARRY SRL', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(15, 'SOLIZ Y BLASCO E HIJA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(16, 'TOLEDO DE MANZANO Y ASOC.', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(17, 'GLOBAL CÓRDOBA-ARREOLA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(18, 'VIAJES SANTACRUZ S. DE H.', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(19, 'EXPÓSITO-BALLESTEROS SA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(20, 'DE JESÚS Y CALDERA Y ASOC.', '2026-06-09 07:46:23', '2026-06-09 07:46:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes`
--

CREATE TABLE `lotes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `proveedor_id` bigint(20) UNSIGNED NOT NULL,
  `numero_lote` varchar(50) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_fabricacion` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_27_061724_create_ajustes_table', 1),
(5, '2026_04_28_045308_create_permission_tables', 1),
(6, '2026_04_29_024020_create_sucursals_table', 1),
(7, '2026_04_29_031817_create_empleados_table', 1),
(8, '2026_04_29_081000_create_categorias_table', 1),
(9, '2026_04_30_030753_create_laboratorios_table', 1),
(10, '2026_05_06_120000_create_clientes_table', 1),
(11, '2026_05_07_045220_create_forma_farmaceuticas_table', 1),
(12, '2026_05_07_045312_create_presentacions_table', 1),
(13, '2026_05_13_120000_create_proveedores_table', 1),
(14, '2026_05_14_010000_create_productos_table', 1),
(15, '2026_05_14_011856_create_lotes_table', 1),
(16, '2026_05_16_001919_create_compras_table', 1),
(17, '2026_05_16_002933_create_compra_detalles_table', 1),
(18, '2026_05_16_003006_create_compra_tmps_table', 1),
(19, '2026_05_16_005931_create_ubicacion_fisicas_table', 1),
(20, '2026_05_16_012000_create_inventarios_table', 1),
(21, '2026_06_05_234514_create_cajas_table', 1),
(22, '2026_06_05_234607_create_arqueos_table', 1),
(23, '2026_06_05_235731_create_arqueo_detalles_table', 1),
(24, '2026_06_09_031943_create_venta_tmps_table', 1),
(25, '2026_06_09_032022_create_ventas_table', 1),
(26, '2026_06_09_032053_create_venta_detalles_table', 1),
(27, '2026_06_09_032121_create_venta_historials_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 11),
(5, 'App\\Models\\User', 1),
(5, 'App\\Models\\User', 2),
(5, 'App\\Models\\User', 3),
(5, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 5),
(5, 'App\\Models\\User', 6),
(5, 'App\\Models\\User', 7),
(5, 'App\\Models\\User', 8),
(5, 'App\\Models\\User', 9),
(5, 'App\\Models\\User', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacions`
--

CREATE TABLE `presentacions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `presentacions`
--

INSERT INTO `presentacions` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'TUBO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(2, 'POLVO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(3, 'EMULSIÓN', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(4, 'LOCION', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(5, 'SPRAY', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(6, 'SOLUCIÓN', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(7, 'AMPOLLA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(8, 'PARCHES', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(9, 'SUPOSITORIO', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(10, 'PAQUETE', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(11, 'CÁPSULA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(12, 'UNIÓN', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(13, 'SUSPENSIÓN', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(14, 'INHALADOR', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(15, 'BOLSITA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(16, 'CAJA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(17, 'NÁDULA', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(18, 'BLÍSTER', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(19, 'GEL', '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(20, 'JERINGA', '2026-06-09 07:46:23', '2026-06-09 07:46:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
  `laboratorio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `forma_farmaceutica_id` bigint(20) UNSIGNED DEFAULT NULL,
  `presentacion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `codigo_producto` varchar(50) NOT NULL,
  `codigo_barra` varchar(50) DEFAULT NULL,
  `nombre_comercial` varchar(255) NOT NULL,
  `nombre_generico` varchar(255) NOT NULL,
  `concentracion` varchar(100) DEFAULT NULL,
  `accion_terapeutica` varchar(255) DEFAULT NULL,
  `unidad_medida` varchar(50) DEFAULT NULL,
  `usa_receta` tinyint(1) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `laboratorio_id`, `forma_farmaceutica_id`, `presentacion_id`, `codigo_producto`, `codigo_barra`, `nombre_comercial`, `nombre_generico`, `concentracion`, `accion_terapeutica`, `unidad_medida`, `usa_receta`, `imagen`, `created_at`, `updated_at`) VALUES
(1, 5, NULL, 6, 20, 'PRD-30107', '4127469817025', 'AZITROMAX', 'AZITROMICINA', '5', 'INMUNOMODULADOR', NULL, 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(2, 4, NULL, 13, NULL, 'PRD-41172', '4565111009221', 'SALBUVENT', 'SALBUTAMOL', NULL, 'REHIDRATANTE', 'mg', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(3, 5, 1, NULL, 20, 'PRD-33937', NULL, 'ATORVAST', 'ATORVASTATINA', '10', 'ANTIALERGICO', 'g', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(4, 8, 18, 18, 3, 'PRD-19246', '2506267949785', 'VITAMAX D', 'VITAMINA D', '20', 'CORTICOIDE', 'ml', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(5, 6, NULL, 15, 4, 'PRD-47065', '8439792193817', 'ATORVAST', 'ATORVASTATINA', '500', 'HIPOLIPEMICO', 'UI', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(6, 1, 20, 11, 11, 'PRD-35215', '4468181062968', 'METFORMEX', 'METFORMINA', NULL, 'ANTIDIABETICO', 'mg', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(7, 8, 3, 17, 3, 'PRD-08183', '5513881677344', 'RENOXIN', 'LEVOTIROXINA', '500', 'ANALGESICO', 'g', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(8, 7, 16, 1, 11, 'PRD-20689', '7793844841422', 'IBUREL', 'IBUPROFENO', '2', 'ANTIPIRETICO', 'g', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(9, 9, 20, 2, 17, 'PRD-77395', NULL, 'AZITROMAX', 'AZITROMICINA', '10', NULL, 'g', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(10, 9, NULL, 2, 19, 'PRD-51552', '2772052981974', 'OMEPRAL', 'OMEPRAZOL', '50', 'ANTACIDO', 'ml', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(11, 6, 14, 8, 6, 'PRD-00873', '0112894572571', 'OMEPRAL', 'OMEPRAZOL', NULL, 'BRONCODILATADOR', 'ml', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(12, 1, 9, 15, NULL, 'PRD-45381', '6705734604758', 'IBUREL', 'IBUPROFENO', '500', 'ANTIBIOTICO', 'mcg', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(13, 9, 20, NULL, 6, 'PRD-46694', '8417135422136', 'AMOXILIN', 'AMOXICILINA', '5', 'SUPLEMENTO', 'mcg', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(14, 3, 4, 4, 11, 'PRD-37587', NULL, 'DICLOFEN', 'DICLOFENACO', '50', 'ANTISEPTICO', 'UI', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(15, 10, 20, 13, 1, 'PRD-61590', '2388205967225', 'AMOXILIN', 'AMOXICILINA', '1', NULL, 'UI', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(16, 1, 17, 19, 11, 'PRD-44455', '8980337345630', 'LORATEX', 'LORATADINA', '50', 'ANTIALERGICO', 'ml', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(17, 3, 17, 7, 9, 'PRD-75460', NULL, 'LORATEX', 'LORATADINA', '500', 'INMUNOMODULADOR', 'mg', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(18, 9, NULL, 10, 13, 'PRD-44393', '5093234333784', 'PROBIOLAC', 'LACTOBACILLUS', '0.5', 'ANTIDIABETICO', 'g', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(19, 5, 2, 1, 20, 'PRD-72466', '4983202983247', 'CLORINOL', 'CLORHEXIDINA', '20', NULL, 'mg', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(20, 7, 1, NULL, 10, 'PRD-27296', NULL, 'RENOXIN', 'LEVOTIROXINA', '20', 'ANTIALERGICO', 'ml', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(21, 1, NULL, 19, NULL, 'PRD-05641', '8678312870924', 'RENOXIN', 'LEVOTIROXINA', '5', 'ANTIDIABETICO', 'mg', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(22, 3, 18, 11, NULL, 'PRD-19340', '7281217668852', 'RANITID', 'RANITIDINA', '0.5', 'ANALGESICO', 'ml', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(23, 3, 13, 19, 18, 'PRD-19406', '8072847319522', 'ANEXAL', 'CICLOFERON', '1', 'REHIDRATANTE', 'mcg', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(24, 8, 11, 14, NULL, 'PRD-08615', NULL, 'CLORINOL', 'CLORHEXIDINA', NULL, 'ANTIGRIPAL', 'UI', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(25, 5, 2, 2, 8, 'PRD-15715', '9507970851675', 'PARACET PLUS', 'PARACETAMOL', '5', 'ANTIBIOTICO', 'UI', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(26, 5, 18, 6, NULL, 'PRD-97763', '8274636453409', 'RANITID', 'RANITIDINA', '50', 'ANTIPIRETICO', 'mg', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(27, 3, 12, 14, NULL, 'PRD-34473', '5606427792856', 'AMOXILIN', 'AMOXICILINA', NULL, 'ANALGESICO', 'g', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(28, 9, 17, NULL, NULL, 'PRD-62962', '1590482898915', 'OMEPRAL', 'OMEPRAZOL', '100', 'ANTIALERGICO', 'UI', 1, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(29, 8, 18, 4, 17, 'PRD-90092', '5377297213800', 'CLARITRON', 'CLARITROMICINA', '1', NULL, 'mcg', 0, NULL, '2026-06-09 07:46:23', '2026-06-09 07:46:23'),
(30, 10, NULL, 20, 3, 'PRD-92875', '8695278894709', 'CALCIOR', 'CARBONATO DE CALCIO', '50', 'VITAMINICO', 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(31, 1, 8, 4, 3, 'PRD-60506', '4821305154770', 'ANEXAL', 'CICLOFERON', '2', 'HIPOLIPEMICO', 'UI', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(32, 5, 4, NULL, 1, 'PRD-08495', '3115858035179', 'AZITROMAX', 'AZITROMICINA', '5', 'ANTIBIOTICO', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(33, 4, NULL, 7, 12, 'PRD-80932', '9139950912268', 'RENOXIN', 'LEVOTIROXINA', '1', 'VITAMINICO', 'UI', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(34, 8, 7, NULL, NULL, 'PRD-02861', NULL, 'PREDNISOL', 'PREDNISONA', '500', 'HORMONAL', 'mg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(35, 5, 1, 4, 9, 'PRD-88466', '3801084093640', 'AZITROMAX', 'AZITROMICINA', '5', 'HIPOLIPEMICO', 'mcg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(36, 1, 2, 13, 13, 'PRD-90347', '1416156289694', 'OMEPRAL', 'OMEPRAZOL', '0.5', 'CORTICOIDE', 'ml', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(37, 3, 13, 15, 7, 'PRD-86138', NULL, 'METFORMEX', 'METFORMINA', '2', 'CORTICOIDE', 'UI', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(38, 7, 18, 18, 11, 'PRD-98503', '8743034633968', 'CALCIOR', 'CARBONATO DE CALCIO', '500', 'HORMONAL', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(39, 10, 16, 19, 6, 'PRD-05093', NULL, 'RENOXIN', 'LEVOTIROXINA', '50', 'REHIDRATANTE', 'UI', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(40, 5, 11, 9, 15, 'PRD-94530', '6595050621316', 'DICLOFEN', 'DICLOFENACO', '10', 'REHIDRATANTE', 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(41, 8, 8, 20, 4, 'PRD-42689', '4811235686102', 'ANEXAL', 'CICLOFERON', '2', NULL, 'mg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(42, 7, 10, 14, 4, 'PRD-24502', '7509800471125', 'AMOXILIN', 'AMOXICILINA', '50', NULL, 'UI', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(43, 1, 13, 16, NULL, 'PRD-27566', '0825585225768', 'AZITROMAX', 'AZITROMICINA', '100', 'CORTICOIDE', 'mg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(44, 2, 13, 8, NULL, 'PRD-84831', '7381030420494', 'CLORINOL', 'CLORHEXIDINA', '50', NULL, 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(45, 5, 4, 3, 18, 'PRD-06936', '7437112122906', 'AZITROMAX', 'AZITROMICINA', '250', 'ANTIGRIPAL', 'ml', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(46, 5, 18, 3, NULL, 'PRD-08342', '4788389079505', 'DICLOFEN', 'DICLOFENACO', '5', 'ANTIDIABETICO', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(47, 5, 12, 18, 12, 'PRD-49312', '5053713423041', 'AZITROMAX', 'AZITROMICINA', '5', 'ANTISEPTICO', 'UI', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(48, 4, 1, 3, 15, 'PRD-83831', '6288779714790', 'RENOXIN', 'LEVOTIROXINA', NULL, 'HORMONAL', 'mg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(49, 6, 10, 2, 13, 'PRD-83381', '0291654529818', 'LORATEX', 'LORATADINA', '20', 'ANTIALERGICO', 'UI', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(50, 5, NULL, NULL, NULL, 'PRD-19700', '2508927704671', 'ATORVAST', 'ATORVASTATINA', '20', 'ANTIPIRETICO', 'mg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(51, 7, 7, 11, 11, 'PRD-03910', NULL, 'ANEXAL', 'CICLOFERON', '20', 'ANALGESICO', 'ml', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(52, 2, 20, 13, NULL, 'PRD-33735', '6691843538157', 'AMOXILIN', 'AMOXICILINA', '500', 'ANTIPIRETICO', 'mcg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(53, 6, 1, 19, 13, 'PRD-34370', '0237065522330', 'PARACET PLUS', 'PARACETAMOL', '100', 'ANTIBIOTICO', 'g', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(54, 4, 20, 14, 4, 'PRD-12819', '3754084979193', 'AZITROMAX', 'AZITROMICINA', '250', 'ANTISEPTICO', 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(55, 10, 19, 8, 13, 'PRD-46728', '7946381122818', 'CLORINOL', 'CLORHEXIDINA', '100', 'SUPLEMENTO', 'mg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(56, 8, 11, 18, 10, 'PRD-57986', '1318170170841', 'VITAMAX D', 'VITAMINA D', '0.5', 'HORMONAL', 'mg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(57, 10, 17, 18, 18, 'PRD-88595', NULL, 'ANEXAL', 'CICLOFERON', '2', 'ANTIINFLAMATORIO', 'mg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(58, 7, 3, 4, 20, 'PRD-50494', NULL, 'METFORMEX', 'METFORMINA', '5', 'HORMONAL', 'ml', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(59, 7, 1, NULL, 3, 'PRD-01315', '3177385362461', 'DICLOFEN', 'DICLOFENACO', '5', 'ANTIINFLAMATORIO', 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(60, 1, 11, 14, 1, 'PRD-84862', '6612427870510', 'VITAMAX D', 'VITAMINA D', '10', 'ANTIPIRETICO', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(61, 3, 18, 8, 6, 'PRD-85059', '7298350380673', 'PREDNISOL', 'PREDNISONA', '5', 'BRONCODILATADOR', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(62, 10, 15, 9, 7, 'PRD-65914', NULL, 'VITAMAX D', 'VITAMINA D', '1', 'ANTIINFLAMATORIO', NULL, 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(63, 2, 18, 20, 1, 'PRD-36643', '2783875144919', 'PREDNISOL', 'PREDNISONA', NULL, 'REHIDRATANTE', 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(64, 3, 8, NULL, 14, 'PRD-91357', '4514377625384', 'CLORINOL', 'CLORHEXIDINA', '50', 'BRONCODILATADOR', 'ml', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(65, 4, 16, 7, 8, 'PRD-99773', '9027936777983', 'ATORVAST', 'ATORVASTATINA', '500', NULL, 'g', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(66, 5, 10, 16, 16, 'PRD-48526', NULL, 'AZITROMAX', 'AZITROMICINA', '50', 'SUPLEMENTO', 'g', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(67, 6, 18, NULL, NULL, 'PRD-01759', '9630282811483', 'PARACET PLUS', 'PARACETAMOL', '10', 'ANTIBIOTICO', 'mcg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(68, 5, 10, 2, 11, 'PRD-62363', NULL, 'ANEXAL', 'CICLOFERON', '100', 'HIPOLIPEMICO', 'ml', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(69, 8, 19, 9, 14, 'PRD-04857', '8211292589297', 'CLARITRON', 'CLARITROMICINA', '5', 'ANTIBIOTICO', 'ml', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(70, 9, 5, NULL, 16, 'PRD-34720', NULL, 'METFORMEX', 'METFORMINA', '10', 'INMUNOMODULADOR', 'UI', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(71, 6, 18, 18, 20, 'PRD-75164', '8389962155989', 'AMOXILIN', 'AMOXICILINA', '20', 'ANTIPIRETICO', 'ml', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(72, 2, 6, 19, 6, 'PRD-26387', NULL, 'LIDOCARE', 'LIDOCAINA', '20', 'REHIDRATANTE', 'mg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(73, 9, 15, 18, 10, 'PRD-55917', '7109905655162', 'RANITID', 'RANITIDINA', '1', 'ANTIBIOTICO', 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(74, 3, NULL, 1, 2, 'PRD-92362', '6196234053654', 'OMEPRAL', 'OMEPRAZOL', '100', 'SUPLEMENTO', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(75, 9, 8, 20, 6, 'PRD-62708', '1673987510364', 'RANITID', 'RANITIDINA', NULL, 'ANTIDIABETICO', 'mcg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(76, 6, 12, 18, 9, 'PRD-31423', '2707564184309', 'CLORINOL', 'CLORHEXIDINA', '10', NULL, 'ml', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(77, 8, NULL, 12, 7, 'PRD-74184', '5616553716426', 'CALCIOR', 'CARBONATO DE CALCIO', '0.5', 'SUPLEMENTO', 'UI', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(78, 6, 4, 20, 11, 'PRD-74879', '0395476433082', 'RANITID', 'RANITIDINA', '250', 'ANTIALERGICO', 'mg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(79, 3, NULL, 16, 14, 'PRD-46631', NULL, 'RENOXIN', 'LEVOTIROXINA', '0.5', 'CORTICOIDE', 'ml', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(80, 1, NULL, 1, 17, 'PRD-75261', '5202470547955', 'SALBUVENT', 'SALBUTAMOL', '20', 'ANTIDIABETICO', 'ml', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(81, 8, 12, 15, 13, 'PRD-84580', '3730931161539', 'METFORMEX', 'METFORMINA', '2', 'ANTIDIABETICO', 'g', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(82, 2, 10, NULL, 18, 'PRD-70308', '4975455194773', 'CALCIOR', 'CARBONATO DE CALCIO', '0.5', NULL, 'UI', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(83, 10, NULL, NULL, 2, 'PRD-26840', NULL, 'SALBUVENT', 'SALBUTAMOL', '5', 'VITAMINICO', 'mg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(84, 1, 2, 18, 3, 'PRD-16720', '1173129979670', 'VITAMAX D', 'VITAMINA D', NULL, 'INMUNOMODULADOR', 'ml', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(85, 7, 15, 20, 2, 'PRD-42426', '4959587811975', 'AZITROMAX', 'AZITROMICINA', '10', 'VITAMINICO', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(86, 10, 7, 14, 16, 'PRD-98417', '5159870749036', 'CALCIOR', 'CARBONATO DE CALCIO', '2', 'ANTIPIRETICO', 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(87, 5, NULL, 14, 20, 'PRD-16396', '3023173132258', 'RENOXIN', 'LEVOTIROXINA', NULL, 'HIPOLIPEMICO', 'g', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(88, 10, 7, 9, 9, 'PRD-62774', '6987433998047', 'CLORINOL', 'CLORHEXIDINA', '250', 'ANTIDIABETICO', 'mg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(89, 8, NULL, 5, NULL, 'PRD-24800', NULL, 'RENOXIN', 'LEVOTIROXINA', '20', 'ANTIDIABETICO', 'ml', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(90, 2, 13, 7, NULL, 'PRD-27885', '7902348551367', 'METFORMEX', 'METFORMINA', '5', 'ANTIPIRETICO', 'mcg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(91, 1, NULL, 11, 1, 'PRD-98122', NULL, 'LIDOCARE', 'LIDOCAINA', '20', NULL, 'UI', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(92, 2, NULL, 9, NULL, 'PRD-84637', '4273158469520', 'CLORINOL', 'CLORHEXIDINA', '5', NULL, 'ml', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(93, 10, 16, 20, 14, 'PRD-78572', '1713323917278', 'VITAMAX D', 'VITAMINA D', '20', 'ANTIGRIPAL', 'ml', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(94, 7, 8, 14, 13, 'PRD-07889', '3628318006196', 'PARACET PLUS', 'PARACETAMOL', '20', NULL, 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(95, 3, 5, 16, 9, 'PRD-45974', NULL, 'ATORVAST', 'ATORVASTATINA', '5', 'CORTICOIDE', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(96, 3, 10, 3, 6, 'PRD-61339', NULL, 'AZITROMAX', 'AZITROMICINA', '10', 'ANTIGRIPAL', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(97, 4, 18, 18, 3, 'PRD-62105', NULL, 'ATORVAST', 'ATORVASTATINA', '2', 'ANTIALERGICO', 'mcg', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(98, 9, 2, 19, NULL, 'PRD-13460', '4156448312397', 'LORATEX', 'LORATADINA', '0.5', 'HORMONAL', 'g', 0, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(99, 3, 7, 7, 7, 'PRD-37876', '3970587509268', 'VITAMAX D', 'VITAMINA D', '0.5', 'ANTIINFLAMATORIO', 'ml', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24'),
(100, 4, 1, 13, 5, 'PRD-66912', NULL, 'CLORINOL', 'CLORHEXIDINA', '2', 'ANTIPIRETICO', 'mcg', 1, NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `empresa` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `telefono`, `email`, `direccion`, `empresa`, `notas`) VALUES
(1, 'Dña Julia Nevárez Tercero', '+34 914-92-4391', 'vbaez@example.net', 'Praza Pons, 7, 34º F, 27182, Villa Rendón del Mirador', 'Quiroz-Saldivar S. de H.', 'Quam et dolor autem deleniti.'),
(2, 'Marco Pereira', '915 60 4803', 'esteve.aurora@example.com', 'Ruela Nahia, 8, Bajo 0º, 81119, Los Corrales de Arriba', 'Berríos de Rubio', 'Doloribus et qui labore et explicabo voluptatibus.'),
(3, 'Srta. Yaiza Calderón', '946517458', 'helena83@example.org', 'Passeig Villagómez, 9, Bajos, 83136, A Hernádez', 'Grupo Quezada', NULL),
(4, 'Guillermo Armas', '+34 931-445949', 'qoliver@example.org', 'Travessera Carmona, 53, 2º 4º, 79303, Los Romo de San Pedro', 'Luis, Romero y Perales e Hijos', NULL),
(5, 'Dr. Eduardo Delapaz', '953826525', 'maria95@example.com', 'Avenida Naiara, 895, 3º, 54937, Delrío del Barco', 'Ramírez, Murillo y Segura y Asoc.', NULL),
(6, 'Sra. Nerea Haro Tercero', '935 499168', 'nora08@example.net', 'Camino Badillo, 5, Bajo 7º, 38815, O Lozada', 'Quezada de Aponte y Asoc.', NULL),
(7, 'Izan Terán', '916199036', 'martina84@example.com', 'Paseo Tejada, 9, 1º A, 08975, Vall Rodarte', 'Avilés y Clemente', NULL),
(8, 'Marco Gámez', '+34 980-91-0040', 'pilar.ruelas@example.org', 'Praza Zambrano, 584, Entre suelo 5º, 37654, A Anaya de Ulla', 'Gestora Malave', NULL),
(9, 'Nil Reséndez', '958 99 5666', 'castellanos.joel@example.org', 'Avinguda Perea, 851, 8º D, 59411, Villa Núñez', 'Villarreal-Abreu S. de H.', 'Sint esse molestiae blanditiis itaque dolorem temporibus et.'),
(10, 'Natalia Alaniz Hijo', '949 268540', 'adrian67@example.net', 'Travesía Valentina, 720, 0º 0º, 25418, Caraballo de las Torres', 'Bahena de Castellano', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'SUPER ADMIN', 'web', '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(2, 'ADMINISTRADOR', 'web', '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(3, 'CONTADOR', 'web', '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(4, 'VENDEDOR', 'web', '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(5, 'EMPLEADO', 'web', '2026-06-09 07:46:21', '2026-06-09 07:46:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('A7SXEIbhdBQtUREH7DXM5IKjCBiGfGKoQrcdd0cl', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:159.0) Gecko/20100101 Firefox/159.0', 'eyJfdG9rZW4iOiJ4Y2hwbEVLMUtVNkZaanpHY3BSWjFDTUF4NDNXUjZUek9JUW5haTZIIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2Zhcm1hY2lhLnRlc3RcL2FkbWluXC9vcmRlbmVzX2NvbXByYVwvY3JlYXRlIiwicm91dGUiOiJhZG1pbi5vcmRlbmVzX2NvbXByYS5jcmVhdGUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MTEsImF1dGgiOnsicGFzc3dvcmRfY29uZmlybWVkX2F0IjoxNzk0MjkwNDE2fX0=', 1794291909),
('atnrpQ8ZZvNLL0OYSEryWtcfCVKQA2sQbbyDUMEd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'eyJfdG9rZW4iOiI5a0NaWGhHY283amxBOHo2SXM0ejlUVmlHUklZaUpNT3NEWU9wdU5GIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvbG9naW4iLCJyb3V0ZSI6ImxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1781852176),
('BbiVyskEYJjaX4hgvYna86rQq6tQTNYK2GBTwLiR', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'eyJfdG9rZW4iOiJSZnNKV040MGlNNGhiaXdOSmtUWEhQOTR2ZmtmSVBTWjZZYjc3Q2NwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvaG9tZSIsInJvdXRlIjoiaG9tZSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxMSwiYXV0aCI6eyJwYXNzd29yZF9jb25maXJtZWRfYXQiOjE3ODExNDU1Mzl9fQ==', 1781145540),
('cyMuZWD0TcenNKq1H7ukzxU92pcYphbtqGDDxwJI', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'eyJfdG9rZW4iOiJqUzMxazU3N016bThpakdmOW8wOWlPZTlYTVdxM0pVdU9maEN2Q1FsIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvaG9tZSIsInJvdXRlIjoiaG9tZSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxMSwiYXV0aCI6eyJwYXNzd29yZF9jb25maXJtZWRfYXQiOjE3ODA5NzY4MDR9fQ==', 1780976804),
('laCs8MF3rAn3U7UL2fYyY2fFX19knpFwyceuGlNW', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'eyJfdG9rZW4iOiIwc1JUelJwaFRQTXVyMEk4RDRSM0Z1WDhXTmRsYXlqaThjSXlYNVpkIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvYWRtaW5cL3VzZXJzIiwicm91dGUiOiJhZG1pbi51c2Vycy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sInVybCI6W10sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxMSwiYXV0aCI6eyJwYXNzd29yZF9jb25maXJtZWRfYXQiOjE3ODIxODMwMzd9fQ==', 1782183043),
('MzH473E7SoKH2XautyIAtXFjqSmnXSYpbtXtu32h', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'eyJfdG9rZW4iOiI5UTRvc2Q4VTF4eTNYVGJ1a0xSYjRodWJEajNmNlZkZ05zSlgwRk81IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvYWRtaW5cL3JvbGVzIiwicm91dGUiOiJhZG1pbi5yb2xlcy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxMSwiYXV0aCI6eyJwYXNzd29yZF9jb25maXJtZWRfYXQiOjE3ODM5MjQ3MTV9fQ==', 1783926367),
('RYtUPeOEulqOxuwAuuAUVnBY2MdyJAbIHg9u7Jdr', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'eyJfdG9rZW4iOiJPYnBadlY4dUFIaUdVcnE5aUdTOG1Ra3ZnanNqUzcwSjRBZDhqRXZrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvYWRtaW5cL2NhdGVnb3JpYXMiLCJyb3V0ZSI6ImFkbWluLmNhdGVnb3JpYXMuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MTEsImF1dGgiOnsicGFzc3dvcmRfY29uZmlybWVkX2F0IjoxNzgyMTA2NTY0fX0=', 1782112112),
('RZDKDvjEkX02WcgLrTz0FtHTKKnfUIhI8IFyrHjJ', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'eyJfdG9rZW4iOiJzVzZjQ2RyNEhRbHA5NnF5UG92c2E2WG43d2FJRzd6V0VXM2k2bFRUIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvYWRtaW5cL3Byb2R1Y3Rvc1wvY3JlYXRlIiwicm91dGUiOiJhZG1pbi5wcm9kdWN0b3MuY3JlYXRlIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjExLCJhdXRoIjp7InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI6MTc4MjI3NjQ1OX19', 1782281782),
('SZ28zzaJ2URgz8I8ocuspFhEdX786EvpLknsWR0i', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'eyJfdG9rZW4iOiJDSkc5ZzlIVzFlU3JpZmZRbFZXMzNTeVIzNVk5REt6ZGJNakE5THhqIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvYWRtaW5cL2ludmVudGFyaW9zIiwicm91dGUiOiJhZG1pbi5pbnZlbnRhcmlvcy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxMSwiYXV0aCI6eyJwYXNzd29yZF9jb25maXJtZWRfYXQiOjE3ODE2NjI4Njd9fQ==', 1781664895),
('t6AIixxK8RCTfRA2AGscoXnF99uo3G4XPRZQrhc5', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'eyJfdG9rZW4iOiJCRnM4N0FKWEhrUXdBMTZRQXJPZllGaHFoajRQYnR2S3RGUkd4TXVKIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2Zhcm1hY2lhLnRlc3RcL2FkbWluXC9vcmRlbmVzX2NvbXByYVwvY3JlYXRlIiwicm91dGUiOiJhZG1pbi5vcmRlbmVzX2NvbXByYS5jcmVhdGUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJ1cmwiOltdLCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MTEsImF1dGgiOnsicGFzc3dvcmRfY29uZmlybWVkX2F0IjoxNzg0MTc5MDg3fX0=', 1784179114),
('Ya7ehIVGL5TzmJUK2ABK8e5HpH7swWgNfUF9utii', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'eyJfdG9rZW4iOiJoSXdhemM0YnVabTNmUEdMUGdLODlReEFvb2lWSHZrMFlhM1k0Y1ltIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZmFybWFjaWFcL3B1YmxpY1wvaG9tZSIsInJvdXRlIjoiaG9tZSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxMSwiYXV0aCI6eyJwYXNzd29yZF9jb25maXJtZWRfYXQiOjE3ODEwNTg1MTl9fQ==', 1781058519);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursals`
--

CREATE TABLE `sucursals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` text NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sucursals`
--

INSERT INTO `sucursals` (`id`, `nombre`, `direccion`, `telefono`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'CASA MATRIZ', 'Av. Cumavi calle 5 Nro 225', '59176658531', 1, '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(2, 'Orta y Arriaga e Hijos Filial', 'Avenida Ángela, 14, 99º B, 15745, Tirado de las Torres', '+34 966-654300', 1, '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(3, 'Corrales de Villareal SRL Sucursal', 'Rúa Pilar, 4, Entre suelo 7º, 69610, El Nazario', '+34 923288459', 1, '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(4, 'Asociación Olivares Sucursal', 'Travessera Martín, 3, 6º 4º, 08547, Acuña de Lemos', '+34 927315985', 1, '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(5, 'Rocha y Martín e Hija Oficina', 'Rúa José, 900, Bajos, 42963, O Oliver', '962-37-3033', 1, '2026-06-09 07:46:21', '2026-06-09 07:46:21'),
(6, 'Morales y Pons SA Sucursal', 'Praza Herrero, 113, 20º E, 56839, O Viera', '976 19 0308', 1, '2026-06-09 07:46:21', '2026-06-09 07:46:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion_fisicas`
--

CREATE TABLE `ubicacion_fisicas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ubicacion_fisicas`
--

INSERT INTO `ubicacion_fisicas` (`id`, `sucursal_id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 2, 'Pasillo 2 - Anaquel C', 'Estantería para productos frágiles y controlados.', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(2, 1, 'Cuarto de Empaque', 'Ubicación principal de inventario interno.', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(3, 3, 'Pasillo 1 - Anaquel A', 'Zona con control de temperatura y humedad.', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(4, 3, 'Sección Fría', NULL, '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(5, 6, 'Stock Auxiliar', 'Zona con control de temperatura y humedad.', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(6, 6, 'Estantería Alta', 'Estantería para productos frágiles y controlados.', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(7, 6, 'Almacén de Reservas', NULL, '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(8, 5, 'Depósito 2', NULL, '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(9, 6, 'Zona de Preparación', 'Cerca de la entrada principal, junto a recibo de mercadería.', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(10, 4, 'Bodega Central', 'Cerca de la entrada principal, junto a recibo de mercadería.', '2026-06-09 07:46:22', '2026-06-09 07:46:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Lic. Rubén Almaráz', 'isaac48@example.org', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'isXC50De05', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(2, 'África Andrés Tercero', 'aina.ruvalcaba@example.org', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'vjVC8fsDs6', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(3, 'Ainhoa Cotto', 'clara.rivera@example.com', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'AvasEzpTS4', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(4, 'Sra. Inés Archuleta Segundo', 'gquiroz@example.org', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'XSuukUNl4J', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(5, 'Bruno Razo', 'rafael.holguin@example.com', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'NLxFhUdpvr', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(6, 'María Dolores Rodríguez', 'olga.ocampo@example.com', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'i59DclF9MA', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(7, 'África Madrigal', 'muro.jaime@example.com', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', '2CQTlPmuIe', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(8, 'Jesús Carrasco', 'guillem37@example.com', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'hmQqfYEO8h', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(9, 'Rubén Soliz', 'eric.urias@example.org', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'bMULReLiic', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(10, 'Sra. Helena Holguín', 'rosario.franciscojavier@example.org', '2026-06-09 07:46:22', '$2y$12$zW5Yx5zifsr/iogYj.iqf.Cp5NHRvav20Qbsx33Jmaqr4QGoF/0i2', 'ss1ARXkfxK', '2026-06-09 07:46:22', '2026-06-09 07:46:22'),
(11, 'Erick Fernando Morales Gil', 'erick@gmail.com', NULL, '$2y$12$LxV0AhY5alXU0hRA28pmbOTL37mSaKioMJwK1J9awqlKjPqH4cH8O', NULL, '2026-06-09 07:46:24', '2026-06-09 07:46:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sucursal_id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_venta` date NOT NULL,
  `total_venta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','emitida','cancelada','devuelta') NOT NULL DEFAULT 'emitida',
  `tipo_pago` enum('efectivo','tarjeta','transferencia','mixto') NOT NULL DEFAULT 'efectivo',
  `notas` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venta_id` bigint(20) UNSIGNED NOT NULL,
  `inventario_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta_unidad` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_historials`
--

CREATE TABLE `venta_historials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venta_id` bigint(20) UNSIGNED NOT NULL,
  `cliente_nombre` varchar(255) DEFAULT NULL,
  `cliente_documento` varchar(50) DEFAULT NULL,
  `cliente_email` varchar(150) DEFAULT NULL,
  `cliente_celular` varchar(150) DEFAULT NULL,
  `sucursal_nombre` varchar(255) NOT NULL,
  `usuario_nombre` varchar(255) NOT NULL,
  `fecha_venta` date NOT NULL,
  `total_venta` decimal(12,2) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `tipo_pago20` varchar(255) NOT NULL,
  `notas` varchar(255) DEFAULT NULL,
  `detalles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`detalles`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_tmps`
--

CREATE TABLE `venta_tmps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `invevntario_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ajustes`
--
ALTER TABLE `ajustes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `arqueos`
--
ALTER TABLE `arqueos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arqueos_caja_id_foreign` (`caja_id`),
  ADD KEY `arqueos_usuario_id_foreign` (`usuario_id`),
  ADD KEY `arqueos_sucursal_id_foreign` (`sucursal_id`);

--
-- Indices de la tabla `arqueo_detalles`
--
ALTER TABLE `arqueo_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arqueo_detalles_arqueo_id_foreign` (`arqueo_id`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cajas_sucursal_id_foreign` (`sucursal_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categorias_nombre_unique` (`nombre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clientes_ci_nit_unique` (`ci_nit`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compras_proveedor_id_foreign` (`proveedor_id`),
  ADD KEY `compras_usuario_id_foreign` (`usuario_id`),
  ADD KEY `compras_sucursal_id_proveedor_id_usuario_id_index` (`sucursal_id`,`proveedor_id`,`usuario_id`);

--
-- Indices de la tabla `compra_detalles`
--
ALTER TABLE `compra_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compra_detalles_producto_id_foreign` (`producto_id`),
  ADD KEY `compra_detalles_lote_id_foreign` (`lote_id`),
  ADD KEY `compra_detalles_compra_id_producto_id_lote_id_index` (`compra_id`,`producto_id`,`lote_id`);

--
-- Indices de la tabla `compra_tmps`
--
ALTER TABLE `compra_tmps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compra_tmps_sucursal_id_foreign` (`sucursal_id`),
  ADD KEY `compra_tmps_producto_id_foreign` (`producto_id`),
  ADD KEY `compra_tmps_usuario_id_sucursal_id_producto_id_index` (`usuario_id`,`sucursal_id`,`producto_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `empleados_numero_doc_unique` (`numero_doc`),
  ADD KEY `empleados_usuario_id_foreign` (`usuario_id`),
  ADD KEY `empleados_sucursal_id_foreign` (`sucursal_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `forma_farmaceuticas`
--
ALTER TABLE `forma_farmaceuticas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `forma_farmaceuticas_nombre_unique` (`nombre`);

--
-- Indices de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventarios_lote_id_foreign` (`lote_id`),
  ADD KEY `inventarios_ubicacion_fisica_id_foreign` (`ubicacion_fisica_id`),
  ADD KEY `inventarios_compra_id_foreign` (`compra_id`),
  ADD KEY `inventarios_sucursal_id_producto_id_lote_id_compra_id_index` (`sucursal_id`,`producto_id`,`lote_id`,`compra_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `laboratorios_nombre_unique` (`nombre`);

--
-- Indices de la tabla `lotes`
--
ALTER TABLE `lotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lotes_producto_id_numero_lote_unique` (`producto_id`,`numero_lote`),
  ADD KEY `lotes_proveedor_id_foreign` (`proveedor_id`),
  ADD KEY `lotes_producto_id_proveedor_id_numero_lote_index` (`producto_id`,`proveedor_id`,`numero_lote`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `presentacions`
--
ALTER TABLE `presentacions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `presentacions_nombre_unique` (`nombre`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `productos_codigo_producto_unique` (`codigo_producto`),
  ADD UNIQUE KEY `productos_codigo_barra_unique` (`codigo_barra`),
  ADD KEY `productos_categoria_id_foreign` (`categoria_id`),
  ADD KEY `productos_laboratorio_id_foreign` (`laboratorio_id`),
  ADD KEY `productos_forma_farmaceutica_id_foreign` (`forma_farmaceutica_id`),
  ADD KEY `productos_presentacion_id_foreign` (`presentacion_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `sucursals`
--
ALTER TABLE `sucursals`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubicacion_fisicas`
--
ALTER TABLE `ubicacion_fisicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ubicacion_fisicas_sucursal_id_nombre_index` (`sucursal_id`,`nombre`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_sucursal_id_foreign` (`sucursal_id`),
  ADD KEY `ventas_cliente_id_foreign` (`cliente_id`),
  ADD KEY `ventas_usuario_id_foreign` (`usuario_id`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_detalles_venta_id_foreign` (`venta_id`),
  ADD KEY `venta_detalles_inventario_id_foreign` (`inventario_id`);

--
-- Indices de la tabla `venta_historials`
--
ALTER TABLE `venta_historials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_historials_venta_id_foreign` (`venta_id`);

--
-- Indices de la tabla `venta_tmps`
--
ALTER TABLE `venta_tmps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_tmps_usuario_id_foreign` (`usuario_id`),
  ADD KEY `venta_tmps_invevntario_id_foreign` (`invevntario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ajustes`
--
ALTER TABLE `ajustes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `arqueos`
--
ALTER TABLE `arqueos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `arqueo_detalles`
--
ALTER TABLE `arqueo_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra_detalles`
--
ALTER TABLE `compra_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra_tmps`
--
ALTER TABLE `compra_tmps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `forma_farmaceuticas`
--
ALTER TABLE `forma_farmaceuticas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `lotes`
--
ALTER TABLE `lotes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presentacions`
--
ALTER TABLE `presentacions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sucursals`
--
ALTER TABLE `sucursals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ubicacion_fisicas`
--
ALTER TABLE `ubicacion_fisicas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_historials`
--
ALTER TABLE `venta_historials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_tmps`
--
ALTER TABLE `venta_tmps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `arqueos`
--
ALTER TABLE `arqueos`
  ADD CONSTRAINT `arqueos_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `arqueos_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `arqueos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `arqueo_detalles`
--
ALTER TABLE `arqueo_detalles`
  ADD CONSTRAINT `arqueo_detalles_arqueo_id_foreign` FOREIGN KEY (`arqueo_id`) REFERENCES `arqueos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD CONSTRAINT `cajas_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compras_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compras_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compra_detalles`
--
ALTER TABLE `compra_detalles`
  ADD CONSTRAINT `compra_detalles_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compra_detalles_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `compra_detalles_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compra_tmps`
--
ALTER TABLE `compra_tmps`
  ADD CONSTRAINT `compra_tmps_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compra_tmps_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compra_tmps_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`),
  ADD CONSTRAINT `empleados_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD CONSTRAINT `inventarios_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventarios_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventarios_ubicacion_fisica_id_foreign` FOREIGN KEY (`ubicacion_fisica_id`) REFERENCES `ubicacion_fisicas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lotes`
--
ALTER TABLE `lotes`
  ADD CONSTRAINT `lotes_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lotes_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `productos_forma_farmaceutica_id_foreign` FOREIGN KEY (`forma_farmaceutica_id`) REFERENCES `forma_farmaceuticas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_laboratorio_id_foreign` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_presentacion_id_foreign` FOREIGN KEY (`presentacion_id`) REFERENCES `presentacions` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ubicacion_fisicas`
--
ALTER TABLE `ubicacion_fisicas`
  ADD CONSTRAINT `ubicacion_fisicas_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `venta_detalles_inventario_id_foreign` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_detalles_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `venta_historials`
--
ALTER TABLE `venta_historials`
  ADD CONSTRAINT `venta_historials_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `venta_tmps`
--
ALTER TABLE `venta_tmps`
  ADD CONSTRAINT `venta_tmps_invevntario_id_foreign` FOREIGN KEY (`invevntario_id`) REFERENCES `inventarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_tmps_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
