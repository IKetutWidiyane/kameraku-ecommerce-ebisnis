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
$stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: dashboard.php');
    exit;
}

$produk = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-900">Edit Produk</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium">Halo, <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                    <a href="logout.php" class="text-sm font-medium text-red-600 hover:text-red-700">Logout</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                <form action="proses_produk.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $produk['id'] ?>">
                    
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                        <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($produk['nama']) ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" required
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
                    </div>
                    
                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-700">Harga</label>
                        <input type="number" name="harga" id="harga" value="<?= htmlspecialchars($produk['harga']) ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                        <div class="mt-1 flex items-center">
                            <img src="uploads/<?= htmlspecialchars($produk['gambar']) ?>" alt="Current Image" class="h-16 w-16 object-cover rounded-md mr-4">
                            <input type="file" name="gambar" id="gambar" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Biarkan kosong jika tidak ingin mengubah gambar</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="dashboard.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>