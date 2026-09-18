-- Run once on existing installations after backing up the database.
-- Conditional DDL supports MySQL and installations with a manually added slot column.
SET @ddl = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'queue_tickets' AND column_name = 'serving_slot') = 0, 'ALTER TABLE queue_tickets ADD COLUMN serving_slot INT NULL AFTER status', 'SELECT 1');
PREPARE migration_statement FROM @ddl; EXECUTE migration_statement; DEALLOCATE PREPARE migration_statement;
SET @ddl = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'staff_users' AND column_name = 'assigned_procedure') = 0, 'ALTER TABLE staff_users ADD COLUMN assigned_procedure VARCHAR(20) NULL', 'SELECT 1');
PREPARE migration_statement FROM @ddl; EXECUTE migration_statement; DEALLOCATE PREPARE migration_statement;
SET @ddl = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'staff_users' AND column_name = 'assigned_slot') = 0, 'ALTER TABLE staff_users ADD COLUMN assigned_slot INT NULL', 'SELECT 1');
PREPARE migration_statement FROM @ddl; EXECUTE migration_statement; DEALLOCATE PREPARE migration_statement;
UPDATE staff_users SET assigned_procedure = 'xray', assigned_slot = 0 WHERE name = 'X-Ray 1';
UPDATE staff_users SET assigned_procedure = 'xray', assigned_slot = 1 WHERE name = 'X-Ray 2';
UPDATE staff_users SET assigned_procedure = 'ultrasound', assigned_slot = 0 WHERE name = 'Ultrasound 1';
UPDATE staff_users SET assigned_procedure = 'ultrasound', assigned_slot = 1 WHERE name = 'Ultrasound 2';
UPDATE staff_users SET assigned_procedure = 'ctscan', assigned_slot = 0 WHERE name = 'CT Scan';
