<?php
require_once 'auth.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

$transaksi_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Handle upload bukti bayar
if (isset($_POST['upload_bukti'])) {
    $target_dir = "uploads/bukti_bayar/";
    $target_file = $target_dir . basename($_FILES["bukti_bayar"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Generate unique filename
    $filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES["bukti_bayar"]["tmp_name"], $target_file)) {
        $conn->query("UPDATE transaksi SET bukti_bayar = '$filename' WHERE id = $transaksi_id AND user_id = $user_id");
        $_SESSION['success'] = "Bukti pembayaran berhasil diupload";
    } else {
        $_SESSION['error'] = "Gagal upload bukti pembayaran";
    }
}

// Get transaction details
$transaksi = $conn->query("SELECT * FROM transaksi WHERE id = $transaksi_id AND user_id = $user_id")->fetch_assoc();
$items = $conn->query("
    SELECT td.*, p.nama, p.gambar 
    FROM transaksi_detail td
    JOIN produk p ON td.produk_id = p.id
    WHERE td.transaksi_id = $transaksi_id
");
$tracking = $conn->query("SELECT * FROM tracking WHERE transaksi_id = $transaksi_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content sama seperti sebelumnya -->
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Header/Navbar sama seperti sebelumnya -->

    <main class="container mx-auto px-4 py-8">
        <div class="flex items-center mb-8">
            <a href="order_history.php" class="text-primary-500 hover:text-primary-600 mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Detail Pesanan #<?= $transaksi_id ?></h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Produk Dipesan</h2>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php while ($item = $items->fetch_assoc()): ?>
                        <div class="py-4 flex items-center">
                            <img src="uploads/<?= $item['gambar'] ?>" alt="<?= $item['nama'] ?>" class="w-16 h-16 object-cover rounded-lg mr-4">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800 dark:text-white"><?= $item['nama'] ?></h3>
                                <p class="text-gray-600 dark:text-gray-400"><?= $item['quantity'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                            </div>
                            <span class="font-medium">Rp <?= number_format($item['harga'] * $item['quantity'], 0, ',', '.') ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Tracking -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Lacak Pengiriman</h2>
                    <div class="space-y-4">
                        <?php if ($tracking->num_rows > 0): ?>
                            <?php while ($track = $tracking->fetch_assoc()): ?>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white mr-4">
                                    <i class="fas fa-<?= 
                                        $track['status'] === 'diproses' ? 'cog' : 
                                        ($track['status'] === 'dikirim' ? 'truck' : 'check') 
                                    ?>"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white"><?= ucfirst($track['status']) ?></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400"><?= $track['keterangan'] ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1"><?= date('d M Y H:i', strtotime($track['created_at'])) ?></p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-600 dark:text-gray-400">Belum ada informasi pengiriman</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Status</span>
                            <span class="font-medium <?= 
                                $transaksi['status'] === 'selesai' ? 'text-green-500' : 
                                ($transaksi['status'] === 'dikirim' ? 'text-blue-500' : 'text-yellow-500')
                            ?>">
                                <?= ucfirst($transaksi['status']) ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
                            <span class="font-medium">Rp <?= number_format($transaksi['total'], 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Ongkir</span>
                            <span class="font-medium">Rp 15.000</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between">
                            <span class="font-bold text-lg">Total</span>
                            <span class="font-bold text-lg text-primary-500">Rp <?= number_format($transaksi['total'] + 15000, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Proof -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Bukti Pembayaran</h2>
                    <?php if ($transaksi['bukti_bayar']): ?>
                        <img src="uploads/bukti_bayar/<?= $transaksi['bukti_bayar'] ?>" alt="Bukti Bayar" class="mb-4 rounded-lg w-full">
                        <p class="text-green-500 text-sm">Bukti pembayaran sudah diupload</p>
                    <?php else: ?>
                        <form method="post" enctype="multipart/form-data" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Bukti Transfer</label>
                                <input type="file" name="bukti_bayar" required 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            </div>
                            <button type="submit" name="upload_bukti" 
                                    class="w-full bg-primary-500 hover:bg-primary-600 text-white font-bold py-2 px-4 rounded-lg transition">
                                Upload Bukti
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Review Products -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Beri Ulasan</h2>
                    <?php 
                    $items_for_review = $conn->query("
                        SELECT td.produk_id, p.nama, p.gambar 
                        FROM transaksi_detail td
                        JOIN produk p ON td.produk_id = p.id
                        WHERE td.transaksi_id = $transaksi_id
                        AND NOT EXISTS (
                            SELECT 1 FROM ulasan u 
                            WHERE u.produk_id = td.produk_id 
                            AND u.transaksi_id = $transaksi_id
                            AND u.user_id = $user_id
                        )
                    ");
                    
                    if ($items_for_review->num_rows > 0): ?>
                        <?php while ($item = $items_for_review->fetch_assoc()): ?>
                        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-center mb-4">
                                <img src="uploads/<?= $item['gambar'] ?>" alt="<?= $item['nama'] ?>" class="w-12 h-12 object-cover rounded-lg mr-4">
                                <h3 class="font-medium text-gray-800 dark:text-white"><?= $item['nama'] ?></h3>
                            </div>
                            <form method="post" action="submit_review.php">
                                <input type="hidden" name="produk_id" value="<?= $item['produk_id'] ?>">
                                <input type="hidden" name="transaksi_id" value="<?= $transaksi_id ?>">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rating</label>
                                    <div class="flex space-x-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <button type="button" onclick="setRating(this, <?= $item['produk_id'] ?>)" 
                                                    class="text-gray-400 hover:text-yellow-400 focus:outline-none"
                                                    data-rating="<?= $i ?>" data-produk="<?= $item['produk_id'] ?>">
                                                <i class="fas fa-star text-xl"></i>
                                            </button>
                                        <?php endfor; ?>
                                        <input type="hidden" name="rating" id="rating-<?= $item['produk_id'] ?>" value="0">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Komentar</label>
                                    <textarea name="komentar" rows="3" 
                                              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"></textarea>
                                </div>
                                <button type="submit" 
                                        class="bg-primary-500 hover:bg-primary-600 text-white font-bold py-2 px-4 rounded-lg transition">
                                    Submit Ulasan
                                </button>
                            </form>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-600 dark:text-gray-400">Semua produk sudah diulas atau pesanan belum selesai</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        function setRating(btn, produkId) {
            const rating = btn.getAttribute('data-rating');
            const stars = document.querySelectorAll(`[data-produk="${produkId}"]`);
            
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-400');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-400');
                }
            });
            
            document.getElementById(`rating-${produkId}`).value = rating;
        }
    </script>
</body>
</html>