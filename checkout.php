<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get cart items
$cart_items = $conn->query("
    SELECT k.*, p.nama, p.harga, p.gambar 
    FROM keranjang k 
    JOIN produk p ON k.produk_id = p.id 
    WHERE k.user_id = $user_id
");

if ($cart_items->num_rows === 0) {
    header("Location: cart.php");
    exit;
}

$total = 0;
while ($item = $cart_items->fetch_assoc()) {
    $total += $item['harga'] * $item['quantity'];
}
$cart_items->data_seek(0); // Reset pointer

// Handle checkout
if (isset($_POST['checkout'])) {
    $conn->query("INSERT INTO transaksi (user_id, total) VALUES ($user_id, $total)");
    $transaksi_id = $conn->insert_id;

    $cart_items_trans = $conn->query("SELECT * FROM keranjang WHERE user_id = $user_id");
    while ($item = $cart_items_trans->fetch_assoc()) {
        $produk_id = $item['produk_id'];
        $quantity = $item['quantity'];
        $harga = $conn->query("SELECT harga FROM produk WHERE id = $produk_id")->fetch_assoc()['harga'];

        $conn->query("INSERT INTO transaksi_detail (transaksi_id, produk_id, quantity, harga) 
                      VALUES ($transaksi_id, $produk_id, $quantity, $harga)");
    }

    $conn->query("DELETE FROM keranjang WHERE user_id = $user_id");
    header("Location: order_complete.php?id=$transaksi_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <title>Checkout - KameraKU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: '#8b5cf6',
                    secondary: '#6b21a8',
                    accent: '#F59E0B',
                }
            }
        }
    }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white transition-colors duration-300">

<!-- Dark Mode Toggle -->
<button id="theme-toggle" class="fixed top-4 right-4 z-50 p-2 bg-white dark:bg-gray-800 rounded-full shadow-md">
    <i id="theme-icon" class="fas fa-moon text-gray-800 dark:text-yellow-300"></i>
</button>

<!-- Header -->
<header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-40">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center space-x-2 text-primary">
            <i class="fas fa-store text-2xl"></i>
            <span class="text-xl font-bold">KameraKU</span>
        </a>
        <div class="flex items-center space-x-4">
            <a href="cart.php" class="relative text-gray-700 dark:text-gray-300 hover:text-secondary dark:hover:text-secondary">
                <i class="fas fa-shopping-cart text-xl"></i>
                <span class="absolute -top-1 -right-1 bg-accent text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    <?= $cart_items->num_rows ?? 0 ?>
                </span>
            </a>
            <a href="profile.php" class="text-gray-700 dark:text-gray-300 hover:text-secondary dark:hover:text-secondary">
                <i class="fas fa-user-circle text-xl"></i>
            </a>
        </div>
    </div>
</header>

<main class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-8">
        <a href="cart.php" class="text-primary hover:text-secondary mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold">Checkout</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
                <h2 class="text-xl font-bold border-b pb-2 border-gray-200 dark:border-gray-700 mb-6">Informasi Pengiriman</h2>
                <form>
                    <div class="space-y-4">
                        <input type="text" placeholder="Nama Lengkap" required class="w-full px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
                        <textarea placeholder="Alamat Lengkap" required class="w-full px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700"></textarea>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" placeholder="Kota" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
                            <input type="text" placeholder="Kode Pos" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <input type="tel" placeholder="No Telepon" required class="w-full px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-xl font-bold border-b pb-2 border-gray-200 dark:border-gray-700 mb-6">Metode Pembayaran</h2>
                <div class="space-y-3">
                    <div class="flex items-center p-3 border rounded-lg dark:border-gray-600">
                        <input type="radio" name="payment" class="h-4 w-4 text-primary" checked>
                        <label class="ml-3 text-sm font-medium">Transfer Bank</label>
                        <div class="flex space-x-2 ml-auto">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/3/30/BCA_logo.png" alt="BCA" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/id/0/09/Bank_Mandiri_logo.svg" alt="Mandiri" class="h-6">
                        </div>
                    </div>
                    <div class="flex items-center p-3 border rounded-lg dark:border-gray-600">
                        <input type="radio" name="payment" class="h-4 w-4 text-primary">
                        <label class="ml-3 text-sm font-medium">E-Wallet</label>
                        <div class="flex space-x-2 ml-auto">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/GoPay_logo.svg" alt="Gopay" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Logo_OVO_purple.svg" alt="OVO" class="h-6">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 sticky top-4">
                <h2 class="text-xl font-bold border-b pb-2 border-gray-200 dark:border-gray-700 mb-6">Pesanan Anda</h2>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 mb-6">
                    <?php while ($item = $cart_items->fetch_assoc()):
                        $subtotal = $item['harga'] * $item['quantity'];
                    ?>
                    <div class="py-4 flex items-center">
                        <img src="uploads/<?= $item['gambar'] ?>" class="w-16 h-16 object-cover rounded-lg border dark:border-gray-700 mr-4">
                        <div class="flex-1">
                            <h3 class="text-gray-800 dark:text-white"><?= $item['nama'] ?></h3>
                            <p class="text-sm text-gray-500"><?= $item['quantity'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                        </div>
                        <span class="font-medium">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                    <?php endwhile; ?>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span>Rp 15.000</span>
                    </div>
                    <div class="flex justify-between font-bold border-t pt-3 border-gray-200 dark:border-gray-700">
                        <span>Total</span>
                        <span class="text-secondary">Rp <?= number_format($total + 15000, 0, ',', '.') ?></span>
                    </div>
                </div>

                <form method="post">
                    <button type="submit" name="checkout" class="w-full bg-secondary hover:bg-primary text-white font-bold py-3 rounded-xl shadow-md">
                        <i class="fas fa-lock mr-2"></i> Bayar Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-10">
    <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">
        &copy; <?= date('Y') ?> KameraKU. All rights reserved.
    </div>
</footer>

<!-- Dark Mode Script -->
<script>
const themeToggle = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('theme-icon');
const html = document.documentElement;
const saved = localStorage.getItem('theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
if (saved === 'dark') { html.classList.add('dark'); themeIcon.classList.replace('fa-moon','fa-sun'); }
themeToggle.addEventListener('click', () => {
    html.classList.toggle('dark');
    if (html.classList.contains('dark')) {
        localStorage.setItem('theme','dark');
        themeIcon.classList.replace('fa-moon','fa-sun');
    } else {
        localStorage.setItem('theme','light');
        themeIcon.classList.replace('fa-sun','fa-moon');
    }
});
</script>
</body>
</html>
