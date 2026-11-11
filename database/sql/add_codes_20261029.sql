-- =============================================================================
-- Adición de 66 nuevos cupones Whirlpool - Canal ECOMMERCE (Central Dismac)
-- Resumen:
--   - TC2000  (2000.00 Bs):  3 cupones (Lote 2026-10-29)
--   - TC1000S (1000.00 Bs):  3 cupones (Lote 2026-10-29)
--   - TC500S  ( 500.00 Bs): 10 cupones (Lote 2026-10-29)
--   - TC250S  ( 250.00 Bs): 10 cupones (Lote 2026-10-29)
--   - TC100S  ( 100.00 Bs): 40 cupones (Lote 2026-10-30)
--   TOTAL:                  66 cupones nuevos
-- Nota: No modifica la tabla `prizes` ni el stock de `MAYORISTA` (mercado informal).
-- =============================================================================

START TRANSACTION;

-- 1. Obtener dinámicamente los UUIDs de Premios existentes y Sucursal ECOMMERCE
SET @branch_eco_id = (SELECT id FROM branchs WHERE channel = 'ECOMMERCE' LIMIT 1);
SET @prize_2000_id = (SELECT id FROM prizes WHERE amount = 2000.00 LIMIT 1);
SET @prize_1000_id = (SELECT id FROM prizes WHERE amount = 1000.00 LIMIT 1);
SET @prize_500_id  = (SELECT id FROM prizes WHERE amount = 500.00 LIMIT 1);
SET @prize_250_id  = (SELECT id FROM prizes WHERE amount = 250.00 LIMIT 1);
SET @prize_100_id  = (SELECT id FROM prizes WHERE amount = 100.00 LIMIT 1);

-- 2. Insertar únicamente los 66 nuevos códigos en la tabla `codes`
INSERT IGNORE INTO `codes` (
    `id`,
    `prize_id`,
    `code`,
    `product_code`,
    `value`,
    `expiration_date`,
    `days_valid`,
    `created_by`,
    `status`,
    `is_used`,
    `used_at`,
    `form_id`,
    `created_at`,
    `updated_at`
) VALUES
-- TC2000 (3 códigos - 2000.00 Bs)
(UUID(), @prize_2000_id, '55818YUR3QK',  'TC2000',  2000.00, '2027-01-27 17:26:59', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_2000_id, '45818KG5C49',  'TC2000',  2000.00, '2027-01-27 17:26:59', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_2000_id, 'H5818ZZWRZA',  'TC2000',  2000.00, '2027-01-27 17:26:59', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),

-- TC1000S (3 códigos - 1000.00 Bs)
(UUID(), @prize_1000_id, 'R581954QT4',   'TC1000S', 1000.00, '2027-01-27 17:27:20', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_1000_id, '85819X9YWG',   'TC1000S', 1000.00, '2027-01-27 17:27:20', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_1000_id, 'A58198N2D7',   'TC1000S', 1000.00, '2027-01-27 17:27:20', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),

-- TC500S (10 códigos - 500.00 Bs)
(UUID(), @prize_500_id,  'M5820F7WCRPR', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  'B58209256H7V', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  '95820P8GU8QC', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  'U5820FNCJBZD', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  'Y5820AHVP9FW', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  'Q58204T7A2CX', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  'F5820DAMKP7W', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  '658208FZ9TWB', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  'K5820FURHPR6', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_500_id,  'Y5820FTCTAQN', 'TC500S',   500.00, '2027-01-27 17:27:43', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),

-- TC250S (10 códigos - 250.00 Bs)
(UUID(), @prize_250_id,  'V5821DJY8BG',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  '85821MNATQC',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  'H582137763C',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  'V5821P34DBP',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  '35821VMGM62',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  '55821ZTG94B',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  '35821KPFMD5',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  '958215T5YTC',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  'T5821FY4G84',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_250_id,  '45821K4Z6GU',  'TC250S',   250.00, '2027-01-27 17:28:03', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),

-- TC100S (40 códigos - 100.00 Bs)
(UUID(), @prize_100_id,  'B5823KNMTQQ4', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '358237T3Y5U6', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'X5823U345WW6', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'B5823JCMU7BY', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'V5823QKJAWTW', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'H5823MRNVA7G', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'X582366JF9HZ', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'X5823JRDRKC5', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '65823UWYCNV9', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'Z5823J3JWXFD', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'V58232FZXP88', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'N5823CZPMXGJ', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'D5823KKYV26W', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'G5823GNAWVGF', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '2582363NMQJJ', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '45823TXH8PHJ', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'T5823GZJRHZN', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'D5823VQHRHYC', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '958237V9NRDX', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '6582325GA8VZ', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'X5823F3DJRZC', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'Q58239TTUDUC', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'K58239VMPVX7', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '65823D49UX3G', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'K5823CTW8853', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '35823JA8ZM86', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'Y5823M58YJX6', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'T5823M5AMH2V', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'B5823MWH69RB', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'F5823R53YPM4', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'J5823JBJB2WN', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '75823HY6R78Q', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '95823G3HWPUU', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'N5823BGNTG63', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '658236K265XV', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'J58235VBVT72', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'A5823T3Z7VCN', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'Q582387KJ3TJ', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  'V5823V8G3NC4', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW()),
(UUID(), @prize_100_id,  '858233Z7D3HM', 'TC100S',   100.00, '2027-01-28 15:29:17', 90, 'Edgar Alejandro Tejada Rojas', 'available', 0, NULL, NULL, NOW(), NOW());

-- 3. Registrar el movimiento de adición en `prize_stock_history` (solo canal ECOMMERCE)
INSERT INTO `prize_stock_history` (`id`, `prize_id`, `branch_id`, `quantity`, `type`, `form_id`, `user_id`, `description`, `created_at`, `updated_at`) VALUES
(UUID(), @prize_2000_id, @branch_eco_id,  3, 'addition', NULL, NULL, 'Adición de stock nuevos cupones lote 20261029 - Central Dismac (ECOMMERCE)', NOW(), NOW()),
(UUID(), @prize_1000_id, @branch_eco_id,  3, 'addition', NULL, NULL, 'Adición de stock nuevos cupones lote 20261029 - Central Dismac (ECOMMERCE)', NOW(), NOW()),
(UUID(), @prize_500_id,  @branch_eco_id, 10, 'addition', NULL, NULL, 'Adición de stock nuevos cupones lote 20261029 - Central Dismac (ECOMMERCE)', NOW(), NOW()),
(UUID(), @prize_250_id,  @branch_eco_id, 10, 'addition', NULL, NULL, 'Adición de stock nuevos cupones lote 20261029 - Central Dismac (ECOMMERCE)', NOW(), NOW()),
(UUID(), @prize_100_id,  @branch_eco_id, 40, 'addition', NULL, NULL, 'Adición de stock nuevos cupones lote 20261030 - Central Dismac (ECOMMERCE)', NOW(), NOW());

-- 4. Incrementar el stock actual en `prize_branch` únicamente para Central Dismac (ECOMMERCE)
UPDATE `prize_branch` SET `stock` = `stock` + 3,  `updated_at` = NOW() WHERE `prize_id` = @prize_2000_id AND `branch_id` = @branch_eco_id;
UPDATE `prize_branch` SET `stock` = `stock` + 3,  `updated_at` = NOW() WHERE `prize_id` = @prize_1000_id AND `branch_id` = @branch_eco_id;
UPDATE `prize_branch` SET `stock` = `stock` + 10, `updated_at` = NOW() WHERE `prize_id` = @prize_500_id  AND `branch_id` = @branch_eco_id;
UPDATE `prize_branch` SET `stock` = `stock` + 10, `updated_at` = NOW() WHERE `prize_id` = @prize_250_id  AND `branch_id` = @branch_eco_id;
UPDATE `prize_branch` SET `stock` = `stock` + 40, `updated_at` = NOW() WHERE `prize_id` = @prize_100_id  AND `branch_id` = @branch_eco_id;

COMMIT;
