-- Pila Pet Registration System
-- MySQL Schema for localhost

CREATE DATABASE IF NOT EXISTS pila_pets;
USE pila_pets;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    age INT NULL,
    contact_number VARCHAR(11) NULL,
    address TEXT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    archived TINYINT(1) DEFAULT 0,
    archived_at TIMESTAMP NULL
);

CREATE TABLE pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NULL,
    pet_type VARCHAR(100) NULL,
    age INT NULL,
    color VARCHAR(100) NULL,
    gender VARCHAR(20) NULL,
    owner_id INT NOT NULL,
    photo_url VARCHAR(255) NULL,
    available_for_adoption TINYINT(1) DEFAULT 0,
    lost TINYINT(1) DEFAULT 0,
    deceased TINYINT(1) DEFAULT 0,
    deceased_at TIMESTAMP NULL,
    archived TINYINT(1) DEFAULT 0,
    archived_at TIMESTAMP NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    rejection_reason TEXT NULL,
    registered_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    approved_by INT NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    record_type VARCHAR(255) NOT NULL,
    record_date DATE NOT NULL,
    next_due_date DATE NULL,
    provider VARCHAR(255) NULL,
    description TEXT NULL,
    photo_url VARCHAR(255) NULL,
    FOREIGN KEY (pet_id) REFERENCES pets(id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    user_id INT NULL,
    comment TEXT NOT NULL,
    is_admin_reply TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_id) REFERENCES pets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Default admin account (password: admin123!)
INSERT INTO users (full_name, email, password, is_admin)
VALUES ('Administrator', 'admin@pila.pets', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
