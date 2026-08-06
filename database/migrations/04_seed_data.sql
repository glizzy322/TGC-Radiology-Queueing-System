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
