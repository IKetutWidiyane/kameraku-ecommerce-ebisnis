<?php
require_once 'auth.php';
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$produk_id = $_POST['produk_id'];
$transaksi_id = $_POST['transaksi_id'];
$rating = $_POST['rating'];
$komentar = clean_input($_POST['komentar']);

// Check if user already reviewed this product in this transaction
$check = $conn->query("
    SELECT id FROM ulasan 
    WHERE user_id = $user_id 
    AND produk_id = $produk_id
    AND transaksi_id = $transaksi_id
");

if ($check->num_rows === 0) {
    $sql = "INSERT INTO ulasan (user_id, produk_id, transaksi_id, rating, komentar)
            VALUES ($user_id, $produk_id, $transaksi_id, $rating, '$komentar')";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Ulasan berhasil disimpan";
    } else {
        $_SESSION['error'] = "Gagal menyimpan ulasan";
    }
}

header("Location: order_detail.php?id=$transaksi_id");
exit;