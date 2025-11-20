-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-11-2025 a las 02:32:07
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
-- Base de datos: `nomadia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_audit_logs`
--

CREATE TABLE `admin_audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(100) DEFAULT NULL,
  `target_id` bigint(20) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin_audit_logs`
--

INSERT INTO `admin_audit_logs` (`id`, `admin_id`, `action`, `target_type`, `target_id`, `details`, `created_at`) VALUES
(1, 5, 'approve_experiencia', 'experiencia', 3, '{\"approved_experiencia\":3}', '2025-11-14 00:20:34'),
(2, 8, 'create_user', 'user', 9, '{\"created_user\":9,\"email\":\"isa2@gmail.com\"}', '2025-11-14 00:47:15'),
(3, 8, 'create_user', 'user', 10, '{\"created_user\":10,\"email\":\"sebas777@gmail.com\"}', '2025-11-14 00:47:42'),
(4, 14, 'approve_experiencia', 'experiencia', 6, '{\"approved_experiencia\":6}', '2025-11-14 01:31:44'),
(5, 14, 'approve_experiencia', 'experiencia', 5, '{\"approved_experiencia\":5}', '2025-11-14 01:31:45'),
(6, 14, 'approve_experiencia', 'experiencia', 4, '{\"approved_experiencia\":4}', '2025-11-14 01:31:45'),
(7, 14, 'approve_experiencia', 'experiencia', 3, '{\"approved_experiencia\":3}', '2025-11-14 01:31:46'),
(8, 14, 'approve_experiencia', 'experiencia', 1, '{\"approved_experiencia\":1}', '2025-11-14 01:31:47'),
(9, 14, 'approve_experiencia', 'experiencia', 2, '{\"approved_experiencia\":2}', '2025-11-14 01:31:48'),
(10, 14, 'reject_experiencia', 'experiencia', 6, '{\"rejected_experiencia\":6}', '2025-11-14 01:35:55'),
(11, 14, 'reject_experiencia', 'experiencia', 5, '{\"rejected_experiencia\":5}', '2025-11-14 01:35:55'),
(12, 14, 'reject_experiencia', 'experiencia', 4, '{\"rejected_experiencia\":4}', '2025-11-14 01:35:56'),
(13, 14, 'reject_experiencia', 'experiencia', 3, '{\"rejected_experiencia\":3}', '2025-11-14 01:35:56'),
(14, 14, 'reject_experiencia', 'experiencia', 1, '{\"rejected_experiencia\":1}', '2025-11-14 01:35:57'),
(15, 14, 'reject_experiencia', 'experiencia', 2, '{\"rejected_experiencia\":2}', '2025-11-14 01:35:58'),
(16, 14, 'approve_experiencia', 'experiencia', 9, '{\"approved_experiencia\":9}', '2025-11-14 02:39:59'),
(17, 14, 'approve_experiencia', 'experiencia', 11, '{\"approved_experiencia\":11}', '2025-11-14 06:59:55'),
(18, 14, 'approve_experiencia', 'experiencia', 7, '{\"approved_experiencia\":7}', '2025-11-14 06:59:58'),
(19, 14, 'approve_experiencia', 'experiencia', 7, '{\"approved_experiencia\":7}', '2025-11-14 07:28:36'),
(20, 14, 'approve_experiencia', 'experiencia', 8, '{\"approved_experiencia\":8}', '2025-11-14 07:28:37'),
(21, 14, 'approve_experiencia', 'experiencia', 9, '{\"approved_experiencia\":9}', '2025-11-14 07:28:38'),
(22, 14, 'approve_experiencia', 'experiencia', 10, '{\"approved_experiencia\":10}', '2025-11-14 07:28:38'),
(23, 14, 'approve_experiencia', 'experiencia', 11, '{\"approved_experiencia\":11}', '2025-11-14 07:28:39'),
(24, 1, 'create_reserva', 'reserva', 51, '{\"usuario_id\":\"1\",\"experiencia_id\":12,\"cantidad\":1,\"total\":50}', '2025-11-14 08:04:46'),
(25, 1, 'create_pago', 'pago', 4, '{\"usuario_id\":\"1\",\"reserva_id\":51,\"amount\":50,\"method\":\"credit_card\",\"status\":\"completed\"}', '2025-11-14 08:04:46'),
(26, 1, 'update_experiencia', 'experiencia', 11, '{\"experiencia_id\":\"11\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\",\"imagen = ?\"]}', '2025-11-14 08:33:19'),
(27, 14, 'create_reserva', 'reserva', 52, '{\"usuario_id\":\"14\",\"experiencia_id\":11,\"cantidad\":2,\"total\":260}', '2025-11-14 08:34:26'),
(28, 14, 'create_pago', 'pago', 5, '{\"usuario_id\":\"14\",\"reserva_id\":52,\"amount\":260,\"method\":\"credit_card\",\"status\":\"completed\"}', '2025-11-14 08:34:26'),
(29, 1, 'update_experiencia', 'experiencia', 11, '{\"experiencia_id\":\"11\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\",\"imagen = ?\"]}', '2025-11-14 08:38:00'),
(30, 1, 'update_experiencia', 'experiencia', 11, '{\"experiencia_id\":\"11\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\",\"imagen = ?\"]}', '2025-11-14 08:48:10'),
(31, 1, 'update_experiencia', 'experiencia', 11, '{\"experiencia_id\":\"11\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\",\"imagen = ?\"]}', '2025-11-14 08:53:20'),
(32, 1, 'update_experiencia', 'experiencia', 11, '{\"experiencia_id\":\"11\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\",\"imagen = ?\"]}', '2025-11-14 09:09:07'),
(33, 14, 'approve_experiencia', 'experiencia', 14, '{\"approved_experiencia\":14}', '2025-11-14 09:33:45'),
(34, 14, 'approve_experiencia', 'experiencia', 13, '{\"approved_experiencia\":13}', '2025-11-14 09:33:51'),
(35, 14, 'approve_experiencia', 'experiencia', 12, '{\"approved_experiencia\":12}', '2025-11-14 09:37:51'),
(36, 14, 'approve_experiencia', 'experiencia', 8, '{\"approved_experiencia\":8}', '2025-11-14 09:38:00'),
(37, 14, 'create_reserva', 'reserva', 53, '{\"usuario_id\":\"14\",\"experiencia_id\":14,\"cantidad\":1,\"total\":70}', '2025-11-14 13:33:21'),
(38, 14, 'create_pago', 'pago', 6, '{\"usuario_id\":\"14\",\"reserva_id\":53,\"amount\":70,\"method\":\"mock\",\"status\":\"completed\"}', '2025-11-14 13:33:21'),
(39, 1, 'create_reserva', 'reserva', 54, '{\"usuario_id\":\"1\",\"experiencia_id\":13,\"cantidad\":1,\"total\":30}', '2025-11-14 13:36:19'),
(40, 1, 'create_reserva', 'reserva', 55, '{\"usuario_id\":\"1\",\"experiencia_id\":14,\"cantidad\":1,\"total\":70}', '2025-11-14 13:36:42'),
(41, 1, 'create_pago', 'pago', 7, '{\"usuario_id\":\"1\",\"reserva_id\":55,\"amount\":70,\"method\":\"mock\",\"status\":\"completed\"}', '2025-11-14 13:36:42'),
(42, 1, 'create_reserva', 'reserva', 56, '{\"usuario_id\":\"1\",\"experiencia_id\":14,\"cantidad\":1,\"total\":70}', '2025-11-14 14:10:44'),
(43, 1, 'create_pago', 'pago', 8, '{\"usuario_id\":\"1\",\"reserva_id\":56,\"amount\":70,\"method\":\"credit_card\",\"status\":\"completed\"}', '2025-11-14 14:10:44'),
(44, 14, 'approve_experiencia', 'experiencia', 15, '{\"approved_experiencia\":15}', '2025-11-17 03:13:49'),
(45, 1, 'update_experiencia', 'experiencia', 15, '{\"experiencia_id\":\"15\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\",\"imagen = ?\"]}', '2025-11-17 03:22:57'),
(46, 1, 'update_experiencia', 'experiencia', 15, '{\"experiencia_id\":\"15\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\",\"imagen = ?\"]}', '2025-11-17 03:33:41'),
(47, 14, 'approve_experiencia', 'experiencia', 16, '{\"approved_experiencia\":16}', '2025-11-17 03:40:44'),
(48, 14, 'approve_experiencia', 'experiencia', 17, '{\"approved_experiencia\":17}', '2025-11-17 04:04:26'),
(49, 14, 'update_role', 'user', 5, '{\"role\":\"guia\"}', '2025-11-17 07:28:02'),
(50, 14, 'update_role', 'user', 1, '{\"role\":\"guia\"}', '2025-11-17 07:28:14'),
(51, 1, 'update_experiencia', 'experiencia', 17, '{\"experiencia_id\":\"17\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\"]}', '2025-11-17 07:44:39'),
(52, 1, 'update_experiencia', 'experiencia', 14, '{\"experiencia_id\":\"14\",\"campos_actualizados\":[\"title = ?\",\"description = ?\",\"price = ?\",\"categoria = ?\",\"location = ?\",\"duration_minutes = ?\",\"cantidad = ?\",\"idioma_principal = ?\",\"idiomas_adicionales = ?\"]}', '2025-11-17 07:55:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `experiencia_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `experiencia_id`, `usuario_id`, `rating`, `comentario`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 5, 'Excelente', '2025-11-13 23:01:25', '2025-11-13 23:01:25'),
(2, 1, 5, 5, '', '2025-11-13 23:22:51', '2025-11-13 23:22:51'),
(3, 10, 14, 5, '', '2025-11-14 06:31:35', '2025-11-14 06:31:35'),
(4, 11, 14, 5, '', '2025-11-14 07:00:26', '2025-11-14 07:00:26'),
(5, 12, 1, 5, '', '2025-11-14 07:53:52', '2025-11-14 07:53:52'),
(6, 14, 1, 4, 'Buenisimo', '2025-11-14 13:52:07', '2025-11-14 13:52:07'),
(7, 15, 14, 4, 'Genial', '2025-11-17 03:20:56', '2025-11-17 03:20:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacions`
--

CREATE TABLE `calificacions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `experiencia_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `slug`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Aventura y Juegos', 'aventura-y-juegos', 'Actividades al aire libre y juegos de aventura', 'Activo', '2025-11-14 02:52:35', '2025-11-14 07:01:29'),
(2, 'Cultura y Patrimonio', 'cultura-y-patrimonio', 'Recorridos culturales y patrimoniales', 'Activo', '2025-11-14 02:52:35', '2025-11-14 02:52:35'),
(3, 'Turismo Gastronómico', 'turismo-gastronomico', 'Experiencias culinarias y gastronómicas', 'Activo', '2025-11-14 02:52:35', '2025-11-14 02:52:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disponibilidades`
--

CREATE TABLE `disponibilidades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `experiencia_id` bigint(20) UNSIGNED NOT NULL,
  `fecha` datetime NOT NULL,
  `cupos` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `disponibilidades`
--

INSERT INTO `disponibilidades` (`id`, `experiencia_id`, `fecha`, `cupos`, `created_at`, `updated_at`) VALUES
(1, 3, '2025-11-13 19:39:59', 5, '2025-11-13 23:39:59', '2025-11-13 23:39:59'),
(2, 12, '2025-11-20 04:52:00', 2, '2025-11-14 08:52:16', '2025-11-14 08:52:16'),
(3, 12, '2025-11-20 04:52:00', 2, '2025-11-14 08:52:18', '2025-11-14 08:52:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disponibilidads`
--

CREATE TABLE `disponibilidads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `experiencia_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `slots` int(11) NOT NULL DEFAULT 1,
  `price_override` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `experiencias`
--

CREATE TABLE `experiencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `categoria` varchar(255) DEFAULT NULL,
  `guia_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `imagen` varchar(2500) NOT NULL,
  `imagenes` text DEFAULT NULL COMMENT 'JSON array de URLs de imßgenes adicionales',
  `cantidad` int(11) NOT NULL,
  `idioma_principal` varchar(50) DEFAULT NULL,
  `idiomas_adicionales` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `experiencias`
--

INSERT INTO `experiencias` (`id`, `title`, `description`, `price`, `categoria`, `guia_id`, `location`, `duration_minutes`, `published`, `created_at`, `updated_at`, `imagen`, `imagenes`, `cantidad`, `idioma_principal`, `idiomas_adicionales`) VALUES
(7, 'Comida', 'Desayuno', 200.00, 'Turismo Gastron¾mico', 8, 'Comida', 60, 1, '2025-11-14 01:37:10', '2025-11-14 07:28:36', '/uploads/experiencias/fcb-1763084230.jpg', NULL, 0, NULL, NULL),
(8, 'Isa lelel', 'Molestias voluptate ', 92.00, 'Cultura y Patrimonio', 8, 'Aliqua Ea harum aut', 45, 1, '2025-11-14 01:41:26', '2025-11-14 09:38:00', '/uploads/experiencias/f8235ebc60707b4a91f27c5f21e054a5-1763084486.jpg', NULL, 0, NULL, NULL),
(9, 'Parque Cretasico', 'dino', 200.00, 'Aventura y Juegos', 15, 'Turistica', 60, 1, '2025-11-14 02:37:46', '2025-11-14 07:28:38', '/uploads/experiencias/fcb-1763087866.jpg', NULL, 0, NULL, NULL),
(10, 'Optio totam ipsa i', 'Alias cumque magna c', 96.00, 'Aventura y Juegos', 8, '3', 100, -1, '2025-11-14 03:14:04', '2025-11-14 07:28:38', '/uploads/experiencias/Imagen_de_WhatsApp_2023-05-03_a_las_10.49.50-1763090044.jpg', NULL, 79, NULL, NULL),
(11, 'Tour Central', 'Tour por la ciudad', 130.00, 'Turismo Gastronómico', 1, 'Sucre', 120, 0, '2025-11-14 06:58:34', '2025-11-14 07:28:39', '/uploads/experiencias/exp_6916f1b37bddc.avif', NULL, 10, NULL, NULL),
(12, 'Explora gastronomia', 'Sumergete en la gastronomia boliviana', 50.00, 'Turismo Gastronómico', 1, 'Sucre,Bolivia', 180, 1, '2025-11-14 07:51:02', '2025-11-14 09:37:51', '/uploads/experiencias/mondongo-1763106662.jpg', NULL, 10, NULL, NULL),
(13, 'Ir al parque cretacico ', 'Conoce las huellas', 30.00, 'Cultura y Patrimonio', 1, 'Sucre, Bolivia', 120, 1, '2025-11-14 09:10:56', '2025-11-14 09:33:51', '/uploads/experiencias/CRETACICO-1763111456.jpg', NULL, 15, NULL, NULL),
(14, 'Tour por la ciudad', 'Explora la ciudad', 70.00, 'Aventura y Juegos', 1, 'Sucre, Bolivia', 180, 1, '2025-11-14 09:13:11', '2025-11-14 09:33:45', '/uploads/experiencias/sucre-1763111591.avif', NULL, 8, 'Español', ''),
(17, 'Castillo de La Glorieta', 'Un castillo hermoso de la era victoriana con una historia de amor increible', 100.00, 'Cultura y Patrimonio', 1, 'Sucre, Bolivia', 180, 1, '2025-11-17 04:03:36', '2025-11-17 04:04:26', '/uploads/experiencias/Castillo_La_Glorieta__Sucre-1763352216.jpg', '[\\\"\\\\/uploads\\\\/experiencias\\\\/glorieta-1763352216-0.jpg\\\",\\\"\\\\/uploads\\\\/experiencias\\\\/museo-castillo-de-la-glorieta-4-1763352216-1.jpg\\\"]', 14, 'Español', 'Ingles');

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
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
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
(4, '2025_11_13_024812_create_experiencias_table', 1),
(5, '2025_11_13_024813_create_disponibilidads_table', 1),
(6, '2025_11_13_024814_create_reservas_table', 1),
(7, '2025_11_13_024815_create_calificacions_table', 1),
(8, '2025_11_13_024815_create_pagos_table', 1),
(9, '2025_11_13_030000_add_role_to_users_table', 2),
(10, '2025_11_13_031000_add_api_token_to_users_table', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reserva_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `reserva_id`, `amount`, `method`, `status`, `created_at`, `updated_at`) VALUES
(4, 51, 50.00, 'credit_card', 'completed', '2025-11-14 08:04:46', '2025-11-14 08:04:46'),
(5, 52, 260.00, 'credit_card', 'completed', '2025-11-14 08:34:26', '2025-11-14 08:34:26'),
(6, 53, 70.00, 'mock', 'completed', '2025-11-14 13:33:21', '2025-11-14 13:33:21'),
(7, 55, 70.00, 'mock', 'completed', '2025-11-14 13:36:42', '2025-11-14 13:36:42'),
(8, 56, 70.00, 'credit_card', 'completed', '2025-11-14 14:10:44', '2025-11-14 14:10:44');

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
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `experiencia_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_reserva` datetime NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `experiencia_id`, `usuario_id`, `fecha_reserva`, `cantidad`, `total`, `status`, `created_at`, `updated_at`) VALUES
(6, 9, 3, '2025-11-14 02:44:56', 1, 200.00, 'pending', '2025-11-14 02:44:56', '2025-11-14 02:44:56'),
(7, 9, 3, '2025-11-14 02:44:57', 1, 200.00, 'pending', '2025-11-14 02:44:57', '2025-11-14 02:44:57'),
(8, 9, 3, '2025-11-14 02:44:57', 1, 200.00, 'pending', '2025-11-14 02:44:57', '2025-11-14 02:44:57'),
(9, 9, 3, '2025-11-14 02:44:58', 1, 200.00, 'pending', '2025-11-14 02:44:58', '2025-11-14 02:44:58'),
(10, 9, 3, '2025-11-14 02:44:58', 1, 200.00, 'pending', '2025-11-14 02:44:58', '2025-11-14 02:44:58'),
(11, 9, 3, '2025-11-14 02:44:58', 1, 200.00, 'pending', '2025-11-14 02:44:58', '2025-11-14 02:44:58'),
(12, 8, 3, '2025-11-14 02:45:00', 1, 92.00, 'pending', '2025-11-14 02:45:00', '2025-11-14 02:45:00'),
(23, 7, 3, '2025-11-14 02:45:04', 1, 200.00, 'pending', '2025-11-14 02:45:04', '2025-11-14 02:45:04'),
(24, 7, 3, '2025-11-14 02:45:04', 1, 200.00, 'pending', '2025-11-14 02:45:04', '2025-11-14 02:45:04'),
(25, 7, 3, '2025-11-14 02:45:04', 1, 200.00, 'pending', '2025-11-14 02:45:04', '2025-11-14 02:45:04'),
(26, 10, 1, '2025-11-14 06:15:42', 1, 96.00, 'pending', '2025-11-14 06:15:42', '2025-11-14 06:15:42'),
(27, 10, 14, '2025-11-14 06:31:26', 1, 96.00, 'pending', '2025-11-14 06:31:26', '2025-11-14 06:31:26'),
(28, 10, 14, '2025-11-14 06:52:18', 1, 96.00, 'pending', '2025-11-14 06:52:18', '2025-11-14 06:52:18'),
(29, 11, 14, '2025-11-14 07:00:30', 1, 130.00, 'pending', '2025-11-14 07:00:30', '2025-11-14 07:00:30'),
(30, 11, 14, '2025-11-14 07:07:58', 1, 130.00, 'pending', '2025-11-14 07:07:58', '2025-11-14 07:07:58'),
(31, 11, 14, '2025-11-16 03:14:00', 2, 260.00, 'pending', '2025-11-14 07:15:44', '2025-11-14 07:15:44'),
(32, 11, 14, '2025-11-16 03:14:00', 2, 260.00, 'pending', '2025-11-14 07:15:47', '2025-11-14 07:15:47'),
(45, 12, 1, '2025-11-18 08:00:00', 1, 50.00, 'confirmed', '2025-11-14 08:00:14', '2025-11-14 08:00:14'),
(46, 12, 1, '2025-11-18 08:00:00', 1, 50.00, 'confirmed', '2025-11-14 08:00:22', '2025-11-14 08:00:22'),
(47, 12, 1, '2025-11-16 04:01:00', 1, 50.00, 'confirmed', '2025-11-14 08:01:52', '2025-11-14 08:01:52'),
(48, 12, 1, '2025-11-16 04:01:00', 1, 50.00, 'confirmed', '2025-11-14 08:01:56', '2025-11-14 08:01:56'),
(49, 12, 1, '2025-11-16 04:01:00', 1, 50.00, 'confirmed', '2025-11-14 08:01:59', '2025-11-14 08:01:59'),
(50, 12, 1, '2025-11-16 04:01:00', 1, 50.00, 'confirmed', '2025-11-14 08:02:30', '2025-11-14 08:02:30'),
(51, 12, 1, '2025-11-16 04:01:00', 1, 50.00, 'paid', '2025-11-14 08:04:46', '2025-11-14 08:04:46'),
(52, 11, 14, '2025-11-14 08:34:26', 2, 260.00, 'paid', '2025-11-14 08:34:26', '2025-11-14 08:34:26'),
(53, 14, 14, '2025-11-14 13:33:21', 1, 70.00, 'paid', '2025-11-14 13:33:21', '2025-11-14 13:33:21'),
(54, 13, 1, '0000-00-00 00:00:00', 1, 30.00, 'pending_payment', '2025-11-14 13:36:19', '2025-11-14 13:36:19'),
(55, 14, 1, '2025-11-14 13:36:42', 1, 70.00, 'paid', '2025-11-14 13:36:42', '2025-11-14 13:36:42'),
(56, 14, 1, '2025-11-15 13:13:00', 1, 70.00, 'paid', '2025-11-14 14:10:44', '2025-11-14 14:10:44');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `idiomas_hablados` varchar(255) DEFAULT NULL,
  `certificaciones` text DEFAULT NULL,
  `anos_experiencia` int(11) DEFAULT NULL,
  `total_tours` int(11) DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'traveler',
  `remember_token` varchar(100) DEFAULT NULL,
  `api_token` varchar(80) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `foto`, `bio`, `ubicacion`, `idiomas_hablados`, `certificaciones`, `anos_experiencia`, `total_tours`, `email_verified_at`, `password`, `role`, `remember_token`, `api_token`, `created_at`, `updated_at`) VALUES
(1, 'Carmen Garcia', 'test@example.com', '/uploads/guias/guia_1_1763365311.png', 'Passionate local guide with 5 years of experience showcasing the hidden gems of Chuquisaca.', 'Sucre, Chuquisaca, Bolivia', 'Spanish, English, Quechua (Basic)', 'First Aid Certified, Cultural Heritage Tour Guide', 5, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'guide', NULL, NULL, '2025-11-13 07:02:40', '2025-11-17 07:28:14'),
(3, 'sebas', 'sebas@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$/yuAwyIuK8QB0T6AdSBBx.JPA30MMZeg4LdmcfaFx48yd4m2Ok.lu', 'traveler', NULL, NULL, NULL, NULL),
(4, 'isa', 'isa@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$Lb7p5vdZercV0z03JGNZ6OyE5.DfKnxF.VH2Y5mfEhxZTBjC8azFm', 'traveler', NULL, NULL, NULL, NULL),
(7, 'sebastian', 'seb@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$dKbJZEz1WuAcI8TlSdgeZ.t.04HU.jCoMh5RSTfrCdsh/9s0FXdci', 'admin', NULL, NULL, NULL, NULL),
(8, 'Admin User', 'admin@example.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$tfLvUv2N/OVy5AyIZDXMQemokV84MUijmlafjRYpbvRW3MZO9A2Yq', 'guide', NULL, NULL, '2025-11-14 00:41:06', '2025-11-14 00:56:22'),
(9, 'Isa2', 'isa2@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$LNkS2GCI4svW/uOVrCGGs.aZf0JP8.MkJQwoxJyU8y1KBpfOobfpC', 'traveler', NULL, NULL, '2025-11-14 00:47:15', '2025-11-14 00:47:15'),
(10, 'Sebas777', 'sebas777@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$hEOi2wS8NKfwwkbAEJhNzeqjgVGkvDPQcr50KGutopWdXJKdbCW3.', 'admin', NULL, NULL, '2025-11-14 00:47:42', '2025-11-14 00:47:42'),
(11, 'Sebastian Rojas', 'hola@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$mEKIcxY3DyWf3GrPVL0GWe6F1XBrFDgly5N0JsraV8SCs0cgsqWY6', 'traveler', NULL, NULL, '2025-11-14 05:57:07', '2025-11-14 05:57:07'),
(12, 'Est porro totam eum', 'keniw@mailinator.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$HG5RrSCUpkB8ENkF4vq7OOKKty2Mp9N3bEjhzQm/KyG5jlmC7Qngy', 'guide', NULL, NULL, '2025-11-14 05:57:41', '2025-11-14 05:57:41'),
(13, 'Marcus', 'rashi@gmai.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$ow5EO4z/jmCLHSx8NQsgwOX9HeUDsQZbyNFepHY35hLTGPnUFz4se', 'traveler', NULL, NULL, '2025-11-14 06:03:17', '2025-11-14 06:03:17'),
(14, 'Test Admin', 'testadmin@example.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, '2025-11-14 01:05:33', '2025-11-14 01:05:33'),
(15, 'Helen', 'helen@gmail.com', NULL, 'Passionate local guide with 5 years of experience showcasing the hidden gems of Chuquisaca.', 'Sucre, Chuquisaca, Bolivia', 'Spanish, English, Quechua (Basic)', 'First Aid Certified, Cultural Heritage Tour Guide', 5, 0, NULL, '$2y$10$io9J1w.Hi/PaHezcX0GjY.xp4ZkRWSo/YYlj0WSnzLceQf8JONEUm', 'guide', NULL, NULL, '2025-11-14 07:37:02', '2025-11-14 07:37:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_audit_logs`
--
ALTER TABLE `admin_audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiencia_id` (`experiencia_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `calificacions`
--
ALTER TABLE `calificacions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calificacions_experiencia_id_foreign` (`experiencia_id`),
  ADD KEY `calificacions_usuario_id_foreign` (`usuario_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categorias_slug_unique` (`slug`);

--
-- Indices de la tabla `disponibilidades`
--
ALTER TABLE `disponibilidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiencia_id` (`experiencia_id`);

--
-- Indices de la tabla `disponibilidads`
--
ALTER TABLE `disponibilidads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disponibilidads_experiencia_id_foreign` (`experiencia_id`);

--
-- Indices de la tabla `experiencias`
--
ALTER TABLE `experiencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiencias_guia_id_foreign` (`guia_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pagos_reserva_id_foreign` (`reserva_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservas_experiencia_id_foreign` (`experiencia_id`),
  ADD KEY `reservas_usuario_id_foreign` (`usuario_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_api_token_unique` (`api_token`),
  ADD KEY `idx_users_ubicacion` (`ubicacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_audit_logs`
--
ALTER TABLE `admin_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `calificacions`
--
ALTER TABLE `calificacions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `disponibilidades`
--
ALTER TABLE `disponibilidades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `disponibilidads`
--
ALTER TABLE `disponibilidads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `experiencias`
--
ALTER TABLE `experiencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificacions`
--
ALTER TABLE `calificacions`
  ADD CONSTRAINT `calificacions_experiencia_id_foreign` FOREIGN KEY (`experiencia_id`) REFERENCES `experiencias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificacions_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `disponibilidads`
--
ALTER TABLE `disponibilidads`
  ADD CONSTRAINT `disponibilidads_experiencia_id_foreign` FOREIGN KEY (`experiencia_id`) REFERENCES `experiencias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `experiencias`
--
ALTER TABLE `experiencias`
  ADD CONSTRAINT `experiencias_guia_id_foreign` FOREIGN KEY (`guia_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_experiencia_id_foreign` FOREIGN KEY (`experiencia_id`) REFERENCES `experiencias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
