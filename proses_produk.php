<?php
require_once 'config/db.php';
redirect_if_not_logged_in();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan folder uploads ada
if (!file_exists('uploads')) {
    if (!mkdir('uploads', 0755, true)) {
        die("Gagal membuat folder uploads");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? clean_input($_POST['action']) : '';
    $nama = clean_input($_POST['nama']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $harga = clean_input($_POST['harga']);
    $gambar = '';

    // Handle file upload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_ext)) {
            die("Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.");
        }
        
        $target_dir = "uploads/";
        $gambar = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $gambar;

        if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            die("Gagal upload gambar.");
        }
    }

    if ($action === 'tambah') {
        if (empty($gambar)) {
            die("Gambar produk wajib diisi");
        }
        
        $stmt = $conn->prepare("INSERT INTO produk (nama, deskripsi, harga, gambar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $nama, $deskripsi, $harga, $gambar);
        
    } elseif ($action === 'edit') {
        $id = isset($_POST['id']) ? clean_input($_POST['id']) : 0;
        
        if (!empty($gambar)) {
            // Get old image
            $stmt_old = $conn->prepare("SELECT gambar FROM produk WHERE id = ?");
            $stmt_old->bind_param("i", $id);
            $stmt_old->execute();
            $result = $stmt_old->get_result();
            $old_img = $result->fetch_assoc()['gambar'];
            
            // Delete old image
            if ($old_img && file_exists("uploads/$old_img")) {
                unlink("uploads/$old_img");
            }
            
            $stmt = $conn->prepare("UPDATE produk SET nama=?, deskripsi=?, harga=?, gambar=? WHERE id=?");
            $stmt->bind_param("ssisi", $nama, $deskripsi, $harga, $gambar, $id);
        } else {
            $stmt = $conn->prepare("UPDATE produk SET nama=?, deskripsi=?, harga=? WHERE id=?");
            $stmt->bind_param("ssii", $nama, $deskripsi, $harga, $id);
        }
    }

    if (isset($stmt) && $stmt->execute()) {
        header('Location: admin/dashboard.php');
        exit;
    } else {
        die("Error: " . $conn->error);
    }
} else {
    header('Location: admin/dashboard.php');
    exit;
}
?>