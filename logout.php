<?php
// logout.php (file tunggal untuk semua user)
session_start();

session_unset();
session_destroy();

if (strpos($_SERVER['HTTP_REFERER'], 'admin') !== false) {
    header("Location: http://localhost/ecommerce-php/login.php");
} else {
    header("Location: http://localhost/ecommerce-php/login.php");
}
exit;

?>