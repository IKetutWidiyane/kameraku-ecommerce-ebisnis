<?php
session_start();
require_once 'config/db.php';

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$transaksi_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get transaction details
$transaksi = $conn->query("
    SELECT t.*, COUNT(td.id) as item_count 
    FROM transaksi t
    LEFT JOIN transaksi_detail td ON t.id = td.transaksi_id
    WHERE t.id = $transaksi_id AND t.user_id = $user_id
")->fetch_assoc();

if (!$transaksi) {
    header("Location: index.php");
    exit;
}

// Get transaction items
$items = $conn->query("
    SELECT td.*, p.nama, p.gambar 
    FROM transaksi_detail td
    JOIN produk p ON td.produk_id = p.id
    WHERE td.transaksi_id = $transaksi_id
");
?>

<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Selesai - Toko Online</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        // Dark mode toggle script
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    document.documentElement.classList.toggle('dark');
                    localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
                });
            }
        });
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-primary-600 dark:text-primary-400">TokoOnline</a>
            <div class="flex items-center space-x-4">
                <a href="cart.php" class="relative p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </a>
                <button id="theme-toggle" class="p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:block"></i>
                </button>
                <a href="profile.php" class="p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                    <i class="fas fa-user-circle text-xl"></i>
                </a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <!-- Success Header -->
                <div class="bg-primary-600 dark:bg-primary-700 p-6 text-center">
                    <div class="w-20 h-20 bg-white dark:bg-gray-800 bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-white text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Pesanan Berhasil!</h1>
                    <p class="text-primary-100">
                        Terima kasih telah berbelanja. Pesanan Anda sedang diproses.
                    </p>
                </div>
                
                <!-- Order Summary -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Nomor Pesanan</h3>
                            <p class="text-gray-600 dark:text-gray-400">#<?= $transaksi_id ?></p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Tanggal</h3>
                            <p class="text-gray-600 dark:text-gray-400"><?= date('d M Y', strtotime($transaksi['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Total Pembayaran</h3>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                Rp <?= number_format($transaksi['total'], 0, ',', '.') ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Metode Pembayaran</h3>
                            <p class="text-gray-600 dark:text-gray-400">Transfer Bank</p>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Instructions -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Instruksi Pembayaran</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-start mb-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center mr-3">
                                <span class="text-primary-600 dark:text-primary-400 font-bold">1</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white">Transfer ke rekening BCA</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">1234567890 a.n. Toko Online</p>
                            </div>
                        </div>
                        <div class="flex items-start mb-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center mr-3">
                                <span class="text-primary-600 dark:text-primary-400 font-bold">2</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white">Total transfer</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Rp <?= number_format($transaksi['total'] + 15000, 0, ',', '.') ?></p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center mr-3">
                                <span class="text-primary-600 dark:text-primary-400 font-bold">3</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white">Konfirmasi pembayaran</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Upload bukti transfer di halaman pesanan Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Detail Pesanan</h3>
                    <div class="space-y-4">
                        <?php while ($item = $items->fetch_assoc()): ?>
                        <div class="flex items-center">
                            <img src="uploads/<?= $item['gambar'] ?>" alt="<?= $item['nama'] ?>" 
                                 class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700 mr-4">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-800 dark:text-white truncate"><?= $item['nama'] ?></h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    <?= $item['quantity'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                                </p>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-white">
                                Rp <?= number_format($item['harga'] * $item['quantity'], 0, ',', '.') ?>
                            </span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium">Rp <?= number_format($transaksi['total'], 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Ongkos Kirim</span>
                            <span class="font-medium">Rp 15.000</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-800 dark:text-white">Total</span>
                            <span class="text-primary-600 dark:text-primary-400">
                                Rp <?= number_format($transaksi['total'] + 15000, 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex flex-col sm:flex-row justify-between items-center">
                    <a href="index.php" class="w-full sm:w-auto mb-3 sm:mb-0 inline-flex items-center justify-center px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        <i class="fas fa-shopping-bag mr-2"></i> Lanjutkan Belanja
                    </a>
                    <a href="profile.php?tab=orders" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 transition">
                        <i class="fas fa-clipboard-list mr-2"></i> Lihat Pesanan
                    </a>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Butuh Bantuan?</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center mr-3">
                            <i class="fas fa-phone-alt text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white">Hubungi Kami</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">(021) 1234-5678</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center mr-3">
                            <i class="fas fa-envelope text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white">Email Kami</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">cs@tokoonline.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">TokoOnline</h3>
                    <p class="text-gray-600 dark:text-gray-400">Menjual produk berkualitas dengan harga terbaik.</p>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">Tentang Kami</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Layanan Pelanggan</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">Bantuan</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">Hubungi Kami</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">Status Pesanan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Ikuti Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-8 text-center text-gray-600 dark:text-gray-400">
                <p>&copy; <?= date('Y') ?> TokoOnline. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>