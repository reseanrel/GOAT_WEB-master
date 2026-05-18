<?php
session_start();
$_SESSION['user_id'] = 0;
$_SESSION['is_admin'] = true;
$_SESSION['user_name'] = 'Administrator';
$_SESSION['user_email'] = 'admin@pila.pets';
$_SESSION['user_contact'] = '';
$_SESSION['user_address'] = 'Pila, Laguna';
$_SESSION['user_age'] = 30;
header('Location: /php_version/admin/dashboard.php');
exit();
?>
