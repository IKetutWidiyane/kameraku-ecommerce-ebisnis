<?php
require_once 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md p-8 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h2 class="text-3xl font-bold text-center text-gray-800 dark:text-white mb-6">Daftar Akun</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required 
                        class="w-full px-4 py-2 border rounded-xl bg-gray-50 dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" required 
                        class="w-full px-4 py-2 border rounded-xl bg-gray-50 dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" required 
                        class="w-full px-4 py-2 border rounded-xl bg-gray-50 dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2" 
                        class="w-full px-4 py-2 border rounded-xl bg-gray-50 dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-600"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 mb-1">No. Telepon</label>
                    <input type="text" name="no_telp" 
                        class="w-full px-4 py-2 border rounded-xl bg-gray-50 dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <button type="submit" name="register" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-xl transition duration-200">
                        Daftar Sekarang
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-gray-600 dark:text-gray-300 text-sm">
                Sudah punya akun? 
                <a href="login.php" class="text-indigo-600 hover:underline">Login disini</a>
            </p>
        </div>
    </div>
</body>
</html>
