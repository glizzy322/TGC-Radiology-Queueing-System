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
