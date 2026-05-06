<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $sql = file_get_contents('create_adoption_tables.sql');
    // Split SQL into individual statements and execute them
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
            } catch (Exception $e) {
                // Skip errors for existing constraints or columns
                if (strpos($e->getMessage(), 'Duplicate key') === false &&
                    strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }
    echo 'Adoption tables created/updated successfully';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>