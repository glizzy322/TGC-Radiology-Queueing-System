CREATE TABLE staff_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role ENUM('receptionist', 'radiology_staff', 'administrator') NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE procedures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE patient_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    priority_rank INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE queue_counters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    procedure_id INT NOT NULL,
    last_sequence_number INT NOT NULL DEFAULT 0,
    UNIQUE KEY unique_date_procedure (date, procedure_id),
    FOREIGN KEY (procedure_id) REFERENCES procedures(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE queue_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_code VARCHAR(100) NOT NULL UNIQUE,
    procedure_id INT NOT NULL,
    category_id INT NOT NULL,
    status ENUM('waiting', 'serving', 'completed', 'skipped', 'cancelled') NOT NULL DEFAULT 'waiting',
    serving_slot INT NULL,
    issue_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    serving_time TIMESTAMP NULL,
    completed_time TIMESTAMP NULL,
    issuer_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (procedure_id) REFERENCES procedures(id),
    FOREIGN KEY (category_id) REFERENCES patient_categories(id),
    FOREIGN KEY (issuer_id) REFERENCES staff_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE queue_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    actor_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES queue_tickets(id),
    FOREIGN KEY (actor_id) REFERENCES staff_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE advertisements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    duration_seconds INT NOT NULL DEFAULT 10,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES staff_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Seed Admin User (password: 'password' hashed using standard PHP password_hash for simplicity later, using placeholder here)
INSERT INTO staff_users (name, role, password) VALUES ('System Admin', 'administrator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Seed Procedures
INSERT INTO procedures (code, name) VALUES 
('XRAY', 'X-Ray'),
('UT', 'Ultrasound'),
('CT', 'CT Scan');

-- Seed Patient Categories (IPD has higher priority than OPD)
INSERT INTO patient_categories (code, priority_rank) VALUES 
('IPD', 1),
('OPD', 2);
-- Seed Receptionist
INSERT INTO staff_users (name, role, password) VALUES ('Receptionist User', 'receptionist', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Seed Radiology Staff (General - has access to all rooms)
INSERT INTO staff_users (name, role, password) VALUES ('Radiology User', 'radiology_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Seed Room-Specific Radiology Accounts (each locked to their assigned room)
INSERT INTO staff_users (name, role, password) VALUES ('X-Ray 1', 'radiology_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT INTO staff_users (name, role, password) VALUES ('X-Ray 2', 'radiology_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT INTO staff_users (name, role, password) VALUES ('Ultrasound 1', 'radiology_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT INTO staff_users (name, role, password) VALUES ('Ultrasound 2', 'radiology_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT INTO staff_users (name, role, password) VALUES ('CT Scan', 'radiology_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Note: The password hash above corresponds to the plaintext password 'password'.
-- Room accounts are restricted in the UI: e.g. 'X-Ray 1' can only Call/Complete on the X-Ray 1 slot.
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
