-- Active: 1745091062453@@127.0.0.1@3306@nomadia
-- Schema and seed for Nomadia (generated from migrations)

CREATE DATABASE IF NOT EXISTS `nomadia` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nomadia`;

-- users
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'traveler',
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- experiencias
CREATE TABLE IF NOT EXISTS `experiencias` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guia_id` BIGINT UNSIGNED NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NULL,
  `precio` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `categoria` VARCHAR(255) NULL,
  `duracion` INT NULL,
  `estado` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(1024) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`guia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- disponibilidades
CREATE TABLE IF NOT EXISTS `disponibilidades` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `experiencia_id` BIGINT UNSIGNED NOT NULL,
  `fecha` DATETIME NOT NULL,
  `cupos` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`experiencia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- reservas
CREATE TABLE IF NOT EXISTS `reservas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `experiencia_id` BIGINT UNSIGNED NOT NULL,
  `usuario_id` BIGINT UNSIGNED NOT NULL,
  `fecha_reserva` DATETIME NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`experiencia_id`),
  INDEX (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- calificaciones
CREATE TABLE IF NOT EXISTS `calificaciones` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `experiencia_id` BIGINT UNSIGNED NOT NULL,
  `usuario_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `comentario` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`experiencia_id`),
  INDEX (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reserva_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `method` VARCHAR(255) NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`reserva_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed sample experiencias
INSERT INTO `experiencias` (`guia_id`,`titulo`,`descripcion`,`precio`,`categoria`,`duracion`,`estado`,`image`,`created_at`,`updated_at`) VALUES
(NULL,'Tarabuco Market Immersion','Explore the colorful Tarabuco market with a local guide and learn about traditional crafts and culture.',75.00,'Cultural',180,'published',NULL,NOW(),NOW()),
(NULL,'La Paz Street Food Tour','Taste the best street food in La Paz while walking through lively neighborhoods.',45.00,'Food',120,'published',NULL,NOW(),NOW());

-- Optional: seed a test user (password is plaintext 'secret123' — you should hash it in production)
INSERT INTO `users` (`name`,`email`,`password`,`role`,`created_at`,`updated_at`) VALUES
('Test User','test@example.com','secret123','traveler',NOW(),NOW());
