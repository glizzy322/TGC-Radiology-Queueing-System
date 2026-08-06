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
