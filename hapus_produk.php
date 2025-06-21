<?php
require_once 'config/db.php';
redirect_if_not_logged_in();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

$id = clean_input($_GET['id']);

// Get image filename first
$stmt = $conn->prepare("SELECT gambar FROM produk WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $gambar = $row['gambar'];
    
    // Delete the product
    $stmt_delete = $conn->prepare("DELETE FROM produk WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    
    if ($stmt_delete->execute()) {
        // Delete the image file if exists
        if ($gambar && file_exists("uploads/$gambar")) {
            unlink("uploads/$gambar");
        }
    } else {
        die("Gagal menghapus produk: " . $conn->error);
    }
}

header('Location: admin/dashboard.php');
exit;
?>