-- Migration: create categorias table
-- Run this SQL in your MySQL database used by the project (e.g., via phpMyAdmin or mysql CLI)

CREATE TABLE IF NOT EXISTS `categorias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(191) NOT NULL,
  `slug` VARCHAR(191) NULL,
  `descripcion` TEXT NULL,
  `estado` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional seed rows
INSERT INTO `categorias` (`nombre`, `slug`, `descripcion`, `estado`) VALUES
('Aventura y Juegos','aventura-y-juegos','Actividades al aire libre y juegos de aventura','Activo'),
('Cultura y Patrimonio','cultura-y-patrimonio','Recorridos culturales y patrimoniales','Activo'),
('Turismo Gastronómico','turismo-gastronomico','Experiencias culinarias y gastronómicas','Activo');
