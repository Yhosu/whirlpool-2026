-- ==========================================================================
-- SCRIPT DE INSTALACIÓN Y CARGA DE CUPONES DISMAC
-- WHIRLPOOL - TABLA CODES Y RELACIÓN EN FORMS
-- ==========================================================================

-- 1. CREACIÓN DE LA TABLA `codes`
CREATE TABLE IF NOT EXISTS `codes` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prize_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Premio asociado',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de serie / Cupón',
  `product_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código de producto Dismac (ej. TC1000S)',
  `value` decimal(10,2) DEFAULT NULL COMMENT 'Valor del cupón en Bs',
  `expiration_date` datetime DEFAULT NULL COMMENT 'Fecha de vencimiento del cupón',
  `days_valid` int NOT NULL DEFAULT '90' COMMENT 'Días de vigencia',
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Usuario creador en Dismac',
  `status` enum('available','used') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available' COMMENT 'Estado del código',
  `is_used` tinyint(1) NOT NULL DEFAULT '0' COMMENT '¿Ya fue utilizado?',
  `used_at` datetime DEFAULT NULL COMMENT 'Fecha y hora en que fue utilizado',
  `form_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Formulario ganador asociado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codes_code_unique` (`code`),
  KEY `codes_is_used_index` (`is_used`),
  KEY `codes_status_index` (`status`),
  KEY `codes_prize_id_index` (`prize_id`),
  KEY `codes_form_id_index` (`form_id`),
  CONSTRAINT `codes_prize_id_foreign` FOREIGN KEY (`prize_id`) REFERENCES `prizes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `codes_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. AGREGAR COLUMNA `code_id` A `forms` SI NO EXISTE
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'forms' AND COLUMN_NAME = 'code_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE forms ADD COLUMN code_id CHAR(36) NULL AFTER prize_id, ADD INDEX (code_id), ADD CONSTRAINT forms_code_id_foreign FOREIGN KEY (code_id) REFERENCES codes(id) ON DELETE SET NULL', 'SELECT "Column code_id already exists";');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. INSERCIÓN DE LOS 288 CUPONES DESDE LOS ARCHIVOS EXCEL
-- (Idempotente: Utiliza INSERT IGNORE para no duplicar si se ejecuta varias veces)

-- Archivo: TC1000s PROMOCION WHIRLPOOL 20261006.xlsx (15 cupones)
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'F5714PCDQW', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'W57149NT36', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'V57146UP6T', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'B571433B9W', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'C5714M42WH', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'D5714JPA5H', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'F5714VCAFT', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'T5714695HX', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), '65714CWR75', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'B5714UQ29A', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), '75714Q7GRM', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'U5714RP8GC', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'T571458RZC', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'N5714TAKJN', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 1000 LIMIT 1), 'B5714ZGAGJ', 'TC1000S', 1000, '2027-01-04 14:18:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());

-- Archivo: TC100s PROMOCION WHIRLPOOL 20261006.xlsx (170 cupones)
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Y5713Y34AP', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'M57137GT8V', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'C5713T95QZ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713U2AXU', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'M5713VA59V', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'P5713G2528', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'U5713ZB4KR', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713H2XXN', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '35713ZHUGV', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '55713TRT2Y', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'H5713X6KH3', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'C5713ABJXB', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'A5713ZG7HX', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'K5713T26CQ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '25713KM6MR', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713AM2BV', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'H5713GFUMM', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'K5713C7F8G', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X5713D9QYZ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X57133TW3B', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X57136GVG8', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '55713ZU94K', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '5571349NQD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '45713BU8JX', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'U5713DF8D2', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Q5713V7PTJ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X5713BQKB3', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'D5713T5URW', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'G5713D9RAD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '45713JAW4K', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '95713JWPHV', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '6571347AK4', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'R5713HBQ5F', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '8571366NT7', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '35713R9DNF', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Q571395BYD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'P571356KDH', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '25713D74H5', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'K5713QVDD2', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '35713992VF', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '45713AWVHX', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713R68ZW', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '75713B62WF', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'F5713MN9MM', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'G57135B5ND', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'A57135R59V', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '957136Z3W7', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713Y3CH9', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'B5713KYGD5', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'C5713744NV', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '25713Y3GJG', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713DG4KN', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'H5713KVG3W', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '75713AWWJU', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'R5713K7H9P', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713BYG4H', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'B5713T5JC9', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'A5713F69TJ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Y5713ZXFP7', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'R5713MKH5K', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X5713TGUUM', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713P8UJ8', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'C57138BK6T', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Z5713J6JZD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'T5713B6F6T', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '257139ZNJF', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N57134JWN2', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Q57138ZBUZ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713DANUK', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'M57133RARC', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'J5713Q37XJ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713V8ARH', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X571396XPY', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'H5713PA9JN', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'M5713RKMKY', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '75713VP353', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '95713BRD4G', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713VTWNG', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'M57135QYN9', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'R5713NC3MB', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'G5713CBYDY', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '35713ZNDAG', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'A5713ND33H', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X5713RRA3Z', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'H57132A99F', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'U5713NFCF2', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'G5713MVTHG', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'K5713N7D23', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '557136UX4U', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Y5713XHZJ8', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '95713Z7FCP', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Y5713HZTC7', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Y5713CH2GD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'W5713HTFJ8', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N57135YWQ9', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'U571333BXD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'J5713Q2H8C', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'C5713YPW59', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'T5713VC84H', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '75713BXB2U', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '657136CYJ2', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713G4G75', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'A571367TFM', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'H57134Y7VQ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '55713JRQVH', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '25713DNTYB', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'U5713HQJGD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'A5713JJAUY', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'B5713NYFKJ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '35713FQRC3', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'F5713ZJ3J7', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713RTPF4', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '35713F9ZTW', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'G57133RJQ3', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'A5713BT6KX', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'H5713AD7MQ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Q5713BGPFF', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '757136TDR2', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'T57133JJT6', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713W32VW', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'B5713QP72H', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'U5713AAGWW', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'A57132HMRT', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '957133TNKA', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713JW7CM', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'K5713JB6GZ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Z5713PA84J', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '65713V8PTH', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '35713GQCHZ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'G5713VDTH4', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'F5713RZGYX', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'U5713YQ26B', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'V5713JZ8MH', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713T683F', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'G5713GCKYJ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'F5713T6MVN', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X57132R3K9', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'F5713HMX2M', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'M5713XKDXR', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'M5713JUGHR', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'C5713WY7BT', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Z5713VQCJJ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Z5713WN6AB', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'V5713NTAVD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '2571385ZYD', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'B5713DFA42', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Y5713VYAG5', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'W5713T2UMW', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'B57137Z7XN', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Q5713TT6Q2', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'C5713GQMZC', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'T5713HZAZ5', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N5713KZZ5N', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'N57134MYH7', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'B5713GFK2Q', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '75713FNHCJ', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'J5713X4MT4', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '75713TYJZW', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'G5713UUNCR', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '557134R66H', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'H5713F6GJP', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'T57135MUD6', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '25713KHHB3', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'B5713ZUP9K', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'U5713AP8CV', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'C5713TH3ZF', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'P5713D63PK', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'X5713BDNFP', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), 'Y571376HG5', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 100 LIMIT 1), '85713WXYXT', 'TC100S', 100, '2027-01-04 14:17:25', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());

-- Archivo: TC2000 PROMOCION WHIRLPOOL 20261006.xlsx (7 cupones)
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 2000 LIMIT 1), 'U5708P2XJB9C', 'TC2000', 2000, '2027-01-04 14:13:06', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 2000 LIMIT 1), '35708QZQF46P', 'TC2000', 2000, '2027-01-04 14:13:06', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 2000 LIMIT 1), 'T5708MR89WBD', 'TC2000', 2000, '2027-01-04 14:13:06', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 2000 LIMIT 1), '35708QCYPKCZ', 'TC2000', 2000, '2027-01-04 14:13:06', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 2000 LIMIT 1), 'P5708YFU43CA', 'TC2000', 2000, '2027-01-04 14:13:06', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 2000 LIMIT 1), 'B5708B5K878D', 'TC2000', 2000, '2027-01-04 14:13:06', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 2000 LIMIT 1), 'G5708TFBDQRV', 'TC2000', 2000, '2027-01-04 14:13:06', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());

-- Archivo: TC250s PROMOCION WHIRLPOOL 20261006.xlsx (75 cupones)
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '95715QAWVX3Y', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'G5715TJU8RNC', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Y5715FBBHYVR', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'K5715782V7B5', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'X57158HZBJ2B', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'X5715CMD8W4C', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'V5715ANMZ5MW', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '85715Z6U3PV6', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Y5715YKY4264', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '75715BNKPFWX', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Y5715FUBRMH2', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'N5715CYVAPTZ', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '95715THUTPGB', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'H5715AKC2F4T', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Y5715KAAFKTJ', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'M57157TR55NQ', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '55715VR5N4XA', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'V5715JBRDCTU', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '45715AVWAX4Q', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'N5715A4C7HTM', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Z5715KV326TC', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'T5715VFYTRJM', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '75715AD4KK46', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'V57153YJHUN4', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Y5715BHYYZR6', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'R571544UMZGM', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'D57159KHDGFY', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Z5715VQB9XGZ', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'D5715AAKMKJQ', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'U5715PRQZW65', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'C5715TN9GF5Q', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'R5715FUHKJAA', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Z5715Q69P375', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '357155ZVBM3T', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '45715WHKDGBA', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '657157RMJH88', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'M5715QJKUG4W', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'T57157Y7J5KD', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'P5715CUD26XN', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'D5715R68CJRU', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '25715XFUKF3Z', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '4571547VTZQ2', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '75715QUYU67R', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '75715GT2A2F7', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'F5715QR2NT2R', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'M57153DUWN46', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'U5715C3J3486', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'F5715FJXDFAX', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Z5715YUY37G8', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Y5715N2DFZFC', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '55715PT9HCF8', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'W57159CH6GT8', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '5571589QJ8BA', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'D5715Q8VWMNJ', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Q5715QMGTK27', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'U5715X2NPT3V', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'P5715K7VNGX4', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Q5715DW5773Q', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'R57153NRPJT5', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '25715FRRCUVD', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'H5715PUJHTTK', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'B5715Z5KM8BQ', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'D5715ABRC5ZV', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'A5715FTDWKQG', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'B5715MWB7YQN', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'N5715XGN9ZFV', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Q5715RPMHC77', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'C5715JZDPVCF', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'B5715XR56FF7', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), '657153573QDV', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'D57152VNMJ2C', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'Y57158K8B4KT', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'F5715XF276C7', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'R57159KV663A', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 250 LIMIT 1), 'G57153BD4Y4Y', 'TC250S', 250, '2027-01-04 16:15:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());

-- Archivo: TC3000 PROMOCION WHIRLPOOL 20261006.xlsx (7 cupones)
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 3000 LIMIT 1), 'G5707B8RK2', 'TC3000', 3000, '2027-01-04 14:09:39', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 3000 LIMIT 1), 'W5707CFM88', 'TC3000', 3000, '2027-01-04 14:09:39', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 3000 LIMIT 1), 'A5707NXG9N', 'TC3000', 3000, '2027-01-04 14:09:39', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 3000 LIMIT 1), '257076FGTP', 'TC3000', 3000, '2027-01-04 14:09:39', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 3000 LIMIT 1), 'F5707RQBTQ', 'TC3000', 3000, '2027-01-04 14:09:39', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 3000 LIMIT 1), 'R5707YHFFB', 'TC3000', 3000, '2027-01-04 14:09:39', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 3000 LIMIT 1), 'Y5707TTHPC', 'TC3000', 3000, '2027-01-04 14:09:39', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());

-- Archivo: TC500s PROMOCION WHIRLPOOL 20261006.xlsx (14 cupones)
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'J57106GNMUVC', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), '55710F2RJVRQ', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'Y5710PB5R4AD', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'Q5710TVQ2QCH', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'G57109YXXGTA', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'U5710YA3GDNF', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), '65710RV9UMQA', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), '6571064AQAVR', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'V5710M4RHYYD', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'M5710NF2TP8N', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'R5710TY2Z29F', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), '957103QQKCVU', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'D5710K8CZNYB', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());
INSERT IGNORE INTO `codes` (`id`, `prize_id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`, `created_by`, `status`, `is_used`, `created_at`, `updated_at`)
VALUES (UUID(), (SELECT `id` FROM `prizes` WHERE `amount` = 500 LIMIT 1), 'B5710U98DHVF', 'TC500S', 500, '2027-01-04 14:13:56', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NOW(), NOW());

