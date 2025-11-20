-- Migración: Agregar campos de perfil público para guías
-- Fecha: 2025-11-17
-- Descripción: Agrega foto, bio, idiomas, certificaciones y ubicación para el perfil público del guía

ALTER TABLE users 
ADD COLUMN foto VARCHAR(255) NULL AFTER email,
ADD COLUMN bio TEXT NULL AFTER foto,
ADD COLUMN ubicacion VARCHAR(255) NULL AFTER bio,
ADD COLUMN idiomas_hablados VARCHAR(255) NULL AFTER ubicacion,
ADD COLUMN certificaciones TEXT NULL AFTER idiomas_hablados,
ADD COLUMN anos_experiencia INT NULL AFTER certificaciones,
ADD COLUMN total_tours INT DEFAULT 0 AFTER anos_experiencia;

-- Índice para búsqueda por ubicación
CREATE INDEX idx_users_ubicacion ON users(ubicacion);

-- Ejemplo de actualización para usuario existente (opcional)
-- UPDATE users SET 
--   bio = 'Passionate local guide with 5 years of experience showcasing the hidden gems of Chuquisaca.',
--   ubicacion = 'Sucre, Chuquisaca, Bolivia',
--   idiomas_hablados = 'Spanish, English, Quechua (Basic)',
--   certificaciones = 'First Aid Certified, Cultural Heritage Tour Guide (Certified)',
--   anos_experiencia = 5
-- WHERE id = 1;
