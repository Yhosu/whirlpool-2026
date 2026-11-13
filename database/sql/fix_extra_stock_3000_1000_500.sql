-- =============================================================================
-- Corrección del +1 extra en los premios de 3000, 1000 y 500 Bs
-- Elimina la última adición manual duplicada de cada uno de esos 3 premios
-- y descuenta 1 unidad del stock actual en `prize_branch`.
-- =============================================================================

START TRANSACTION;

SET @branch_may_id = (SELECT id FROM branchs WHERE channel = 'MAYORISTA' LIMIT 1);
SET @prize_3000_id = (SELECT id FROM prizes WHERE amount = 3000.00 LIMIT 1);
SET @prize_1000_id = (SELECT id FROM prizes WHERE amount = 1000.00 LIMIT 1);
SET @prize_500_id  = (SELECT id FROM prizes WHERE amount = 500.00 LIMIT 1);

-- 1. Eliminar el último movimiento manual de adición (+1) en `prize_stock_history` (sin tocar la carga inicial)
DELETE FROM `prize_stock_history`
WHERE `branch_id` = @branch_may_id
  AND `prize_id` = @prize_3000_id
  AND `type` = 'addition'
  AND `form_id` IS NULL
  AND `description` != 'Carga inicial de stock - CashBack Mayoristas (MAYORISTA)'
ORDER BY `created_at` DESC
LIMIT 1;

DELETE FROM `prize_stock_history`
WHERE `branch_id` = @branch_may_id
  AND `prize_id` = @prize_1000_id
  AND `type` = 'addition'
  AND `form_id` IS NULL
  AND `description` != 'Carga inicial de stock - CashBack Mayoristas (MAYORISTA)'
ORDER BY `created_at` DESC
LIMIT 1;

DELETE FROM `prize_stock_history`
WHERE `branch_id` = @branch_may_id
  AND `prize_id` = @prize_500_id
  AND `type` = 'addition'
  AND `form_id` IS NULL
  AND `description` != 'Carga inicial de stock - CashBack Mayoristas (MAYORISTA)'
ORDER BY `created_at` DESC
LIMIT 1;

-- 2. Restar 1 al stock actual en `prize_branch` para 3000, 1000 y 500 en MAYORISTA
UPDATE `prize_branch` SET `stock` = GREATEST(0, `stock` - 1), `updated_at` = NOW() WHERE `prize_id` = @prize_3000_id AND `branch_id` = @branch_may_id;
UPDATE `prize_branch` SET `stock` = GREATEST(0, `stock` - 1), `updated_at` = NOW() WHERE `prize_id` = @prize_1000_id AND `branch_id` = @branch_may_id;
UPDATE `prize_branch` SET `stock` = GREATEST(0, `stock` - 1), `updated_at` = NOW() WHERE `prize_id` = @prize_500_id  AND `branch_id` = @branch_may_id;

COMMIT;

