<?php
session_start();
require_once 'config/db.php';

// Simulate user login (replace with actual login system)
$_SESSION['user_id'] = 1;

// Get all products
$sql = "SELECT * FROM produk";
$result = $conn->query($sql);

// Get cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $count = $conn->query("SELECT SUM(quantity) as total FROM keranjang WHERE user_id = $user_id");
    $cart_count = $count->fetch_assoc()['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KameraKU - Toko Online Modern</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
  function startCountdown(duration, display) {
    let timer = duration, hours, minutes, seconds;
    setInterval(() => {
      hours = String(Math.floor(timer / 3600)).padStart(2, '0');
      minutes = String(Math.floor((timer % 3600) / 60)).padStart(2, '0');
      seconds = String(timer % 60).padStart(2, '0');
      display.textContent = `${hours}:${minutes}:${seconds}`;
      if (--timer < 0) timer = 0;
    }, 1000);
  }

  document.addEventListener("DOMContentLoaded", () => {
    const countdown = document.getElementById("countdown");
    const duration = 2 * 60 * 60; // 2 jam
    startCountdown(duration, countdown);
  });
</script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Ubah Konfigurasi Tailwind ke Warna Ungu -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#8b5cf6',   // ungu terang (violet-500)
                        secondary: '#6b21a8', // ungu gelap (purple-800)
                        accent: '#F59E0B',    // kuning/oranye tetap untuk badge dan lainnya
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white transition-colors duration-300">
    <!-- Dark Mode Toggle -->
    <button id="theme-toggle" class="fixed top-4 right-4 z-50 p-2 bg-white dark:bg-gray-800 rounded-full shadow-md">
        <i id="theme-icon" class="fas fa-moon text-gray-800 dark:text-yellow-300"></i>
    </button>

    <!-- Navbar -->
    <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-2 text-primary">
                <i class="fas fa-store text-2xl"></i>
                <span class="text-xl font-bold">KameraKU</span>
            </a>
            <form class="hidden md:block w-1/2 mx-6">
                    <div class="relative">
                    <input type="text" placeholder="Cari produk..." class="w-full py-2 px-4 rounded-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring focus:ring-primary">
                    <button class="absolute right-4 top-1/2 -translate-y-1/2 text-primary"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <div class="flex items-center gap-5">
                <a href="#" class="relative">
                    <i class="fas fa-heart text-xl"></i>
                    <span class="absolute -top-2 -right-2 bg-accent text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">3</span>
                </a>
                <a href="cart.php" class="relative">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span class="absolute -top-2 -right-2 bg-accent text-white text-xs w-5 h-5 flex items-center justify-center rounded-full"><?= $cart_count ?></span>
                </a>
                <div class="relative group">
    <button class="focus:outline-none">
        <i class="fas fa-user text-xl"></i>
    </button>
    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg opacity-0 group-hover:opacity-100 group-hover:visible invisible transition-all duration-200 z-50">
        <a href="akun.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Akun Saya</a>
        <a href="pesanan.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Detail Pesanan</a>
        <a href="histori_pembayaran.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Histori Pembayaran</a>
        <form action="logout.php" method="post">
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-red-400">Logout</button>
        </form>
    </div>
</div>

            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section 
    class="relative min-h-[80vh] flex items-center justify-center bg-cover bg-center bg-no-repeat" 
    style="background-image: url('assets/image/bg-log.jpg');"
>
    <div class="absolute inset-0 bg-gradient-to-b from-black/70 to-black/40"></div>
    <div class="relative z-10 text-center text-white px-6 max-w-3xl">
        <h1 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6 drop-shadow-md">
            Temukan Dunia Fotografi Terbaik
        </h1>
        <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
            KameraKU menghadirkan produk-produk kamera dan aksesoris dengan harga terbaik, kualitas terjamin, dan pengiriman cepat ke seluruh Indonesia.
        </p>
        <a href="#products" class="inline-block bg-primary hover:bg-secondary text-white font-semibold text-lg px-8 py-4 rounded-full shadow-lg transition duration-300 ease-in-out">
            🚀 Belanja Sekarang
        </a>
    </div>
</section>

<!-- Promo Hari Ini -->
<section class="bg-gradient-to-br from-purple-100 to-indigo-200 dark:from-purple-800 dark:to-indigo-900 py-8">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-6">
      <h2 class="text-xl font-bold text-gray-800 dark:text-white">🎁 Promo Hari Ini</h2>
      <p class="text-xs text-gray-600 dark:text-gray-300">10 penawaran menarik yang bisa kamu nikmati sekarang!</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
      <!-- Promo Cards -->
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-shipping-fast text-xl text-blue-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Gratis Ongkir</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Min. Rp150rb</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-gift text-xl text-pink-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Voucher 20%</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Pengguna Baru</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-coins text-xl text-yellow-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Poin Ganda</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Setiap Belanja</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-clock text-xl text-purple-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Flash Sale</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Diskon Per Jam</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-bolt text-xl text-red-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Cashback</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Up to Rp50rb</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-star text-xl text-yellow-400 mb-1"></i>
        <h3 class="font-semibold leading-tight">Rating 4.8+</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Produk Pilihan</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-tags text-xl text-teal-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Diskon Besar</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Hingga 70%</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-box text-xl text-gray-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Produk Baru</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Update Mingguan</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-percent text-xl text-indigo-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Kupon Harian</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Klaim Sekarang</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-md shadow p-2 text-center text-[12px] hover:shadow-md transition">
        <i class="fas fa-headset text-xl text-green-500 mb-1"></i>
        <h3 class="font-semibold leading-tight">Bantuan 24/7</h3>
        <p class="text-[10px] text-gray-500 dark:text-gray-400">Live Chat Aktif</p>
      </div>
    </div>
  </div>
</section>

<!-- Flash Sale Section -->
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-red-500">🔥 Flash Sale</h2>
        <a href="#" class="text-primary font-medium hover:underline">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        <?php
        $flash_sale = [
            ["nama" => "Kamera Mirrorless Canon M50", "gambar" => "canon_m50.jpg", "harga" => 8490000],
            ["nama" => "Lensa Fix 50mm f/1.8", "gambar" => "lensa_50mm.jpg", "harga" => 1350000],
            ["nama" => "Tripod Profesional 160cm", "gambar" => "tripod_pro.jpg", "harga" => 320000],
            ["nama" => "Lighting Softbox Kit", "gambar" => "softbox.jpg", "harga" => 750000],
            ["nama" => "Microphone Shotgun Rode", "gambar" => "mic_rode.jpg", "harga" => 1250000],
            ["nama" => "Gimbal Stabilizer Smartphone", "gambar" => "gimbal.jpg", "harga" => 980000],
            ["nama" => "Memory Card 128GB V90", "gambar" => "memory_card.jpg", "harga" => 380000],
            ["nama" => "Drone DJI Mini SE", "gambar" => "drone.jpg", "harga" => 4990000],
            ["nama" => "Reflector 5-in-1 110cm", "gambar" => "reflector.jpg", "harga" => 99000],
            ["nama" => "Monitor External FeelWorld", "gambar" => "monitor.webp", "harga" => 1590000],
        ];

        foreach ($flash_sale as $produk):
            $harga_asli = $produk['harga'] * 1.2; // simulasi harga sebelum diskon (20%)
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition relative overflow-hidden">
            <div class="relative">
                <img src="assets/image/<?= $produk['gambar'] ?>" alt="<?= $produk['nama'] ?>" class="w-full h-40 object-cover rounded-t">
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">Flash Sale</span>
                <span class="absolute top-2 right-2 bg-yellow-400 text-black text-xs font-semibold px-2 py-1 rounded">-20%</span>
            </div>
            <div class="p-4 space-y-2">
                <h3 class="font-semibold text-sm line-clamp-2"><?= $produk['nama'] ?></h3>
                <div>
                    <span class="text-primary font-bold text-lg">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span><br>
                    <span class="text-sm line-through text-gray-400">Rp <?= number_format($harga_asli, 0, ',', '.') ?></span>
                </div>
                <button class="mt-2 w-full bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary text-white text-sm py-2 rounded-lg font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-bolt text-yellow-300 animate-pulse"></i> Beli Sekarang
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>






    <!-- Products Section -->
    <section id="products" class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Produk Terbaru</h2>
            <a href="#" class="text-primary font-medium hover:underline">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow hover:shadow-lg transition">
                        <div class="relative">
                            <img src="uploads/<?= $row['gambar'] ?>" class="w-full h-48 object-cover" alt="<?= $row['nama'] ?>">
                            <button class="absolute top-2 right-2 bg-white dark:bg-gray-700 p-2 rounded-full shadow"><i class="far fa-heart text-gray-500 hover:text-red-500"></i></button>
                            <span class="absolute top-2 left-2 bg-accent text-white text-xs px-2 py-1 rounded">-15%</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold mb-1"><?= $row['nama'] ?></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2"><?= $row['deskripsi'] ?></p>
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-lg font-bold text-primary">Rp <?= number_format($row['harga'], 0, ',', '.') ?></span>
                                    <span class="text-xs text-gray-400 line-through ml-1">Rp <?= number_format($row['harga'] * 1.15, 0, ',', '.') ?></span>
                                </div>
                                <form method="post" action="cart.php">
                                    <input type="hidden" name="produk_id" value="<?= $row['id'] ?>">
                                    <button name="action" value="add" class="bg-primary hover:bg-purple-700 text-white p-2 rounded-full">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-10 text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-2"></i>
                    <p>Belum ada produk tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 dark:bg-gray-900 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-2">KameraKU</h3>
                <p class="text-gray-400">Menjual produk elektronik berkualitas dengan harga terbaik.</p>
            </div>
            <div>
                <h4 class="font-bold mb-2">Tautan</h4>
                <ul class="text-gray-400 space-y-1">
                    <li><a href="#" class="hover:underline">Beranda</a></li>
                    <li><a href="#" class="hover:underline">Produk</a></li>
                    <li><a href="#" class="hover:underline">Tentang Kami</a></li>
                    <li><a href="#" class="hover:underline">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-2">Layanan</h4>
                <ul class="text-gray-400 space-y-1">
                    <li><a href="#" class="hover:underline">Garansi</a></li>
                    <li><a href="#" class="hover:underline">Pengembalian</a></li>
                    <li><a href="#" class="hover:underline">Pembayaran</a></li>
                    <li><a href="#" class="hover:underline">Pengiriman</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-2">Kontak</h4>
                <ul class="text-gray-400 space-y-1">
                    <li><i class="fas fa-map-marker-alt mr-2"></i>Jakarta, Indonesia</li>
                    <li><i class="fas fa-phone-alt mr-2"></i>+62 123 4567 890</li>
                    <li><i class="fas fa-envelope mr-2"></i>info@kameraku.com</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-4 text-center text-gray-500 text-sm">
            &copy; 2023 KameraKU. All rights reserved.
        </div>
    </footer>

    <!-- Dark Mode Script -->
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        if (savedTheme === 'dark') {
            html.classList.add('dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            html.classList.remove('dark');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }

        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            } else {
                localStorage.setItem('theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            }
        });
    </script>
</body>
</html>
