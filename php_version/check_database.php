<?php
// Check database setup script
define('DB_HOST', 'localhost');
define('DB_NAME', 'pila_pets');
define('DB_USER', 'root');
define('DB_PASS', '0413');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Check if admin exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE email = 'admin@pila.pets'");
    $adminCount = $stmt->fetch()['count'];

    if ($adminCount > 0) {
        echo "✓ Admin account exists!\n";
        echo "Login credentials:\n";
        echo "Email: admin@pila.pets\n";
        echo "Password: admin123!\n";
    } else {
        echo "✗ Admin account not found. Creating...\n";

        // Create admin account
        $hashedPassword = password_hash('admin123!', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, is_admin) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrator', 'admin@pila.pets', $hashedPassword, 1]);

        echo "✓ Admin account created!\n";
        echo "Email: admin@pila.pets\n";
        echo "Password: admin123!\n";
    }

    // Check table counts
    $tables = ['users', 'pets', 'medical_records', 'comments'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        echo "✓ $table table: $count records\n";
    }

} catch (PDOException $e) {
    echo "Database check failed: " . $e->getMessage() . "\n";
    echo "Make sure MySQL is running and the database exists.\n";
}
?>