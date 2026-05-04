<?php
// Database setup script
define('DB_HOST', 'localhost');
define('DB_NAME', 'pila_pets');
define('DB_USER', 'root');
define('DB_PASS', '0413');

try {
    // Connect without specifying database first
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Read and execute the SQL file
    $sql = file_get_contents('../database.sql');

    // Split SQL file into individual statements
    $statements = explode(';', $sql);

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }

    echo "Database and tables created successfully!\n";
    echo "Default admin account created:\n";
    echo "Email: admin@pila.pets\n";
    echo "Password: admin123!\n";

} catch (PDOException $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
}
?>