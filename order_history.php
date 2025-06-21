<?php
require_once 'auth.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$transaksi = $conn->query("
    SELECT t.*, COUNT(td.id) as jumlah_produk 
    FROM transaksi t
    LEFT JOIN transaksi_detail td ON t.id = td.transaksi_id
    WHERE t.user_id = $user_id
    GROUP BY t.id
    ORDER BY t.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content sama seperti sebelumnya -->
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Header/Navbar sama seperti sebelumnya -->

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-8">Riwayat Transaksi</h1>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php while ($row = $transaksi->fetch_assoc()): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#<?= $row['id'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300"><?= $row['jumlah_produk'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $row['status'] === 'selesai' ? 'bg-green-100 text-green-800' : 
                                   ($row['status'] === 'dikirim' ? 'bg-blue-100 text-blue-800' : 
                                   'bg-yellow-100 text-yellow-800') ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="order_detail.php?id=<?= $row['id'] ?>" class="text-primary-500 hover:text-primary-600">Detail</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer sama seperti sebelumnya -->
</body>
</html>