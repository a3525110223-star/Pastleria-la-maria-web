-- Esquema MariaDB/MySQL para Pastelería La María.
-- En instalaciones Laravel se recomienda aplicar database/migrations en lugar de
-- ejecutar este archivo. El usuario administrador se crea de forma segura con
-- SEED_ADMIN_PASSWORD mediante `php artisan db:seed`.

CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rol` (
    `id_rol` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(50) NOT NULL,
    `descripcion` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id_rol`),
    UNIQUE KEY `rol_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rol_usuario` (
    `id_rol_usuario` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_usuario` BIGINT UNSIGNED NOT NULL,
    `id_rol` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id_rol_usuario`),
    UNIQUE KEY `rol_usuario_usuario_rol_unique` (`id_usuario`, `id_rol`),
    CONSTRAINT `rol_usuario_id_usuario_foreign`
        FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `rol_usuario_id_rol_foreign`
        FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `empresa` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre_comercial` VARCHAR(100) NOT NULL,
    `razon_social` VARCHAR(150) NULL DEFAULT NULL,
    `rfc` VARCHAR(13) NULL DEFAULT NULL,
    `telefono` VARCHAR(20) NULL DEFAULT NULL,
    `numero_whatsapp` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `ubicacion` VARCHAR(255) NOT NULL,
    `horario_normal` VARCHAR(255) NOT NULL,
    `horario_especial` VARCHAR(255) NULL DEFAULT NULL,
    `descripcion` TEXT NULL,
    `logotipo` VARCHAR(255) NULL DEFAULT NULL,
    `sitio_web` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rol` (`nombre`, `descripcion`, `created_at`, `updated_at`)
VALUES
    ('Administrador', 'Acceso total al sistema.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('Empleado', 'Acceso operativo a Productos y Categorías.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('Cliente', 'Rol para clientes de la pastelería.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);
