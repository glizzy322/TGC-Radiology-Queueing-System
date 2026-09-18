-- Run once on existing installations. Safe to repeat on MySQL 8.
SET @ddl = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'advertisements' AND column_name = 'is_live') = 0, 'ALTER TABLE advertisements ADD COLUMN is_live BOOLEAN NOT NULL DEFAULT FALSE AFTER is_active', 'SELECT 1');
PREPARE migration_statement FROM @ddl; EXECUTE migration_statement; DEALLOCATE PREPARE migration_statement;
