<?php
session_start();
require_once 'config/db.php';

// Simulasi user login
$_SESSION['user_id'] = 1;

if (isset($_POST['action'])) {
    $produk_id = $_POST['produk_id'];
    $user_id = $_SESSION['user_id'];

    switch ($_POST['action']) {
        case 'add':
            $check = $conn->query("SELECT * FROM keranjang WHERE user_id = $user_id AND produk_id = $produk_id");
            if ($check->num_rows > 0) {
                $conn->query("UPDATE keranjang SET quantity = quantity + 1 WHERE user_id = $user_id AND produk_id = $produk_id");
            } else {
                $conn->query("INSERT INTO keranjang (user_id, produk_id) VALUES ($user_id, $produk_id)");
            }
            break;

        case 'update':
            $quantity = $_POST['quantity'];
            $conn->query("UPDATE keranjang SET quantity = $quantity WHERE user_id = $user_id AND produk_id = $produk_id");
            break;

        case 'remove':
            $conn->query("DELETE FROM keranjang WHERE user_id = $user_id AND produk_id = $produk_id");
            break;
    }

    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = $conn->query("
    SELECT k.*, p.nama, p.harga, p.gambar 
    FROM keranjang k 
    JOIN produk p ON k.produk_id = p.id 
    WHERE k.user_id = $user_id
");
$total = 0;
?>
<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - KameraKU</title>
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
        <div class="flex items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Keranjang Belanja</h1>
            <span class="ml-3 px-3 py-1 bg-primary/20 dark:bg-primary/40 text-primary dark:text-white text-sm font-medium rounded-full">
                <?= $cart_items->num_rows ?> item<?= $cart_items->num_rows != 1 ? 's' : '' ?>
            </span>
        </div>

        <?php if ($cart_items->num_rows > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-primary dark:border-secondary p-6">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php while ($item = $cart_items->fetch_assoc()):
                            $subtotal = $item['harga'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <div class="py-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                            <div class="relative">
                                <img src="uploads/<?= $item['gambar'] ?>" alt="<?= $item['nama'] ?>" class="w-24 h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="absolute -top-2 -right-2 bg-white dark:bg-gray-700 rounded-full p-1 shadow">
                                    <form method="post">
                                        <input type="hidden" name="produk_id" value="<?= $item['produk_id'] ?>">
                                        <button type="submit" name="action" value="remove" class="text-red-500 hover:text-red-600 p-1">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white truncate"><?= $item['nama'] ?></h3>
                                <p class="text-primary font-bold mt-1">Rp <?= number_format($item['harga'],0,',','.') ?></p>
                                <div class="mt-2 flex items-center">
                                    <form method="post" class="flex items-center">
                                        <input type="hidden" name="produk_id" value="<?= $item['produk_id'] ?>">
                                        <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown(); this.parentNode.submit()" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-l-lg border border-gray-200 dark:border-gray-600">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-12 px-2 py-1 border-t border-b border-gray-200 dark:bg-gray-700 dark:border-gray-600 text-center">
                                        <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp(); this.parentNode.submit()" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-r-lg border border-gray-200 dark:border-gray-600">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                        <button type="submit" name="action" value="update" class="ml-2 p-2 text-gray-500 hover:text-secondary dark:hover:text-secondary">
                                            <i class="fas fa-sync-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Subtotal</p>
                                <p class="font-semibold text-gray-800 dark:text-white">Rp <?= number_format($subtotal,0,',','.') ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-300 dark:border-gray-600 p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6 pb-2 border-b border-gray-200 dark:border-gray-700">Ringkasan Belanja</h2>
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">Subtotal</span><span class="font-medium">Rp <?= number_format($total,0,',','.') ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">Ongkos Kirim</span><span class="font-medium">Rp 15.000</span></div>
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between"><span class="font-bold">Total</span><span class="font-bold text-lg text-secondary dark:text-secondary">Rp <?= number_format($total+15000,0,',','.') ?></span></div>
                    </div>
                    <a href="checkout.php" class="block w-full bg-secondary hover:bg-primary text-white font-bold py-4 rounded-xl text-center text-lg shadow-md transition">
                        🛒 Lanjut ke Pembayaran
                    </a>
                    <div class="mt-4 flex justify-center"><a href="index.php" class="text-sm text-primary hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Lanjutkan Belanja</a></div>
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Metode Pembayaran</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/3/30/BCA_logo.png" alt="BCA" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/id/0/09/Bank_Mandiri_logo.svg" alt="Mandiri" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/id/3/38/Bank_BNI_logo.svg" alt="BNI" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/9/9e/BRI_logo.svg" alt="BRI" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/GoPay_logo.svg" alt="Gopay" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Logo_OVO_purple.svg" alt="OVO" class="h-6">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
            <div class="w-24 h-24 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-shopping-cart text-3xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Keranjang belanja kosong</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Tambahkan produk untuk berbelanja</p>
            <a href="index.php" class="inline-block bg-primary hover:bg-secondary text-white font-bold py-2 px-6 rounded-lg transition">
                Belanja Sekarang
            </a>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 dark:bg-gray-900 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">&copy; <?= date('Y') ?> KameraKU. All rights reserved.</div>
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
