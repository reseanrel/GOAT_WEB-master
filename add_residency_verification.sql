-- Add residency verification fields for Pila, Laguna users
-- Run this once on your database

ALTER TABLE users 
  ADD COLUMN residency_status ENUM('unverified','pending','verified','rejected') DEFAULT 'unverified' AFTER archived,
  ADD COLUMN residency_document VARCHAR(255) NULL AFTER residency_status,
  ADD COLUMN residency_rejection_reason TEXT NULL AFTER residency_document,
  ADD COLUMN residency_verified_at DATETIME NULL AFTER residency_rejection_reason,
  ADD COLUMN residency_verified_by INT NULL AFTER residency_verified_at,
  ADD CONSTRAINT fk_residency_verified_by FOREIGN KEY (residency_verified_by) REFERENCES users(id) ON DELETE SET NULL;

-- Optional: backfill existing users (they will need to verify)
-- UPDATE users SET residency_status = 'unverified' WHERE residency_status IS NULL;
