<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Fungsi login admin
function admin_login($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (md5($password) === $admin['password']) { // Untuk kompatibilitas dengan MD5 lama
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            return true;
        }
    }
    return false;
}

// Fungsi login user
function user_login($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT id, nama, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            return true;
        }
    }
    return false;
}