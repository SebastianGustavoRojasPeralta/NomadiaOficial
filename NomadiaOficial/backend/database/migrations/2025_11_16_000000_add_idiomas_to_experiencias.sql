-- Agregar columnas de idiomas a la tabla experiencias
ALTER TABLE `experiencias` 
ADD COLUMN `idioma_principal` VARCHAR(50) DEFAULT NULL AFTER `cantidad`,
ADD COLUMN `idiomas_adicionales` VARCHAR(255) DEFAULT NULL AFTER `idioma_principal`;

-- Agregar columna para almacenar múltiples imágenes (JSON array)
ALTER TABLE `experiencias` 
ADD COLUMN `imagenes` TEXT DEFAULT NULL COMMENT 'JSON array de URLs de imágenes adicionales' AFTER `imagen`;
