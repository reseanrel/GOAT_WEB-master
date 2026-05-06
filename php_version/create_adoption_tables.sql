-- Create adoption_applications table for formal adoption requests
CREATE TABLE IF NOT EXISTS adoption_applications (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    pet_id INTEGER NOT NULL,
    applicant_id INTEGER NOT NULL,
    pet_owner_id INTEGER NOT NULL,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'under_review', 'approved', 'rejected', 'withdrawn', 'completed') DEFAULT 'pending',
    applicant_full_name VARCHAR(255) NOT NULL,
    applicant_email VARCHAR(255) NOT NULL,
    applicant_phone VARCHAR(20),
    applicant_address TEXT,
    applicant_age INTEGER,
    household_members INTEGER DEFAULT 1,
    has_other_pets BOOLEAN DEFAULT FALSE,
    other_pets_details TEXT,
    housing_type ENUM('house', 'apartment', 'condo', 'other') DEFAULT 'house',
    has_yard BOOLEAN DEFAULT FALSE,
    adoption_reason TEXT,
    pet_experience TEXT,
    preferred_contact_method ENUM('email', 'phone', 'both') DEFAULT 'email',
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    reference_name VARCHAR(255),
    reference_phone VARCHAR(20),
    home_visit_allowed BOOLEAN DEFAULT TRUE,
    additional_notes TEXT,
    reviewed_by INTEGER NULL,
    reviewed_at TIMESTAMP NULL,
    review_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,

    INDEX idx_pet_id (pet_id),
    INDEX idx_applicant_id (applicant_id),
    INDEX idx_pet_owner_id (pet_owner_id),
    INDEX idx_status (status),
    INDEX idx_application_date (application_date)
);

-- Add adoption_completed column to pets table
ALTER TABLE pets ADD COLUMN IF NOT EXISTS adoption_completed BOOLEAN DEFAULT FALSE;
ALTER TABLE pets ADD COLUMN IF NOT EXISTS adopted_by INTEGER NULL;
ALTER TABLE pets ADD COLUMN IF NOT EXISTS adoption_date TIMESTAMP NULL;

-- Note: Foreign key constraint for adopted_by will be added manually if needed

-- Update enum to include 'completed' status if table exists
ALTER TABLE adoption_applications MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected', 'withdrawn', 'completed') DEFAULT 'pending';