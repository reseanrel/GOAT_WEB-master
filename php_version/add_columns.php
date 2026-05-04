<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Execute each ALTER statement individually
    $statements = [
        "ALTER TABLE pets ADD COLUMN photo VARCHAR(255) NULL",
        "ALTER TABLE pets ADD COLUMN for_adoption BOOLEAN DEFAULT FALSE",
        "ALTER TABLE pets ADD COLUMN lost BOOLEAN DEFAULT FALSE",
        "ALTER TABLE pets ADD COLUMN registered_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE pets ADD COLUMN status VARCHAR(20) DEFAULT 'pending'",
        "ALTER TABLE pets ADD COLUMN approved_at TIMESTAMP NULL",
        "ALTER TABLE pets ADD COLUMN approved_by INTEGER NULL",
        "ALTER TABLE pets ADD COLUMN archived BOOLEAN DEFAULT FALSE",
        "ALTER TABLE pets ADD COLUMN archived_at TIMESTAMP NULL"
    ];

    foreach ($statements as $statement) {
        try {
            echo "Executing: " . substr($statement, 0, 50) . "...\n";
            $conn->exec($statement);
        } catch (Exception $e) {
            // Ignore if column already exists
            if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                throw $e;
            } else {
                echo "Column already exists, skipping...\n";
            }
        }
    }

    echo "All columns added successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>