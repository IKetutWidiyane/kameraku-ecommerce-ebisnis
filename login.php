<?php
// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';

// Clear any previous error
unset($_SESSION['error']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password']; // Don't sanitize passwords

    // Debug: Log login attempt
    error_log("Login attempt for username: $username");

    try {
        // ADMIN LOGIN (Priority)
        $admin_stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
        $admin_stmt->bind_param("s", $username);
        $admin_stmt->execute();
        $admin_result = $admin_stmt->get_result();

        if ($admin_result->num_rows === 1) {
            $admin = $admin_result->fetch_assoc();
            
            // Verify MD5 hash (for backward compatibility)
            if (md5($password) === $admin['password']) {
                // Successful admin login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                // Debug log
                error_log("Admin login successful: {$admin['username']}");
                
                header('Location: admin/dashboard.php');
                exit;
            } else {
                error_log("Admin password mismatch");
                error_log("Input MD5: " . md5($password));
                error_log("DB Password: " . $admin['password']);
            }
        }

        // USER LOGIN (Fallback)
        $user_stmt = $conn->prepare("SELECT id, nama, email, password FROM user WHERE email = ?");
        $user_stmt->bind_param("s", $username);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows === 1) {
            $user = $user_result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Successful user login
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                
                // Debug log
                error_log("User login successful: {$user['email']}");
                
                header('Location: index.php');
                exit;
            }
        }

        // If we get here, login failed
        sleep(1); // Basic brute-force protection
        $_SESSION['error'] = "Invalid username/email or password";
        $_SESSION['login_attempt'] = $username; // Remember username for UX
        
        // Debug log
        error_log("Login failed for: $username");
        
        header('Location: login.php');
        exit;

    } catch (Exception $e) {
        error_log("Login system error: " . $e->getMessage());
        $_SESSION['error'] = "A system error occurred. Please try again later.";
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | KameraKU</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(to top, #5f2c82, #49a09d);
      background-image: url('assets/image/bg-log.jpg');
      background-size: cover;
      background-position: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .glass {
      backdrop-filter: blur(10px);
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .input-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
    }
    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #aaa;
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen px-4">

  <div class="text-center absolute top-10 w-full">
    <h2 class="text-4xl font-bold text-white drop-shadow-lg">Welcome to KameraKU</h2>
    <p class="text-lg text-purple-200 mt-2 drop-shadow">Tempat belanja online terpercaya dan terjangkau 🌟</p>
  </div>

  <div class="glass rounded-xl shadow-lg p-8 w-full max-w-sm text-white relative mt-24">
    <h1 class="text-3xl font-bold text-center mb-6">Login</h1>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <?= htmlspecialchars($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
      <div class="mb-4 relative">
        <i class="fas fa-user input-icon"></i>
        <input type="text" name="username" placeholder="Username or Email" required
          class="pl-10 w-full py-2 rounded-full bg-white bg-opacity-20 text-white placeholder-gray-300 border border-white focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
          value="<?= isset($_SESSION['login_attempt']) ? htmlspecialchars($_SESSION['login_attempt']) : '' ?>">
      </div>

      <div class="mb-4 relative">
        <i class="fas fa-lock input-icon"></i>
        <input type="password" name="password" id="password" placeholder="Password" required
          class="pl-10 pr-10 w-full py-2 rounded-full bg-white bg-opacity-20 text-white placeholder-gray-300 border border-white focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent">
        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
      </div>

      <div class="flex justify-between items-center text-sm mb-4">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="remember" class="accent-purple-500 rounded">
          Remember me
        </label>
        <a href="forgot_password.php" class="text-purple-300 hover:underline">Forgot password?</a>
      </div>

      <button type="submit" name="login"
        class="w-full py-2 bg-white text-purple-700 rounded-full font-semibold hover:bg-gray-100 transition flex items-center justify-center gap-2">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
    </form>

    <div class="relative my-6">
      <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-gray-300 border-opacity-50"></div>
      </div>
      <div class="relative flex justify-center text-sm">
        <span class="px-2 bg-transparent text-gray-300">Or continue with</span>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
      <button type="button" class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white py-2 rounded-full flex items-center justify-center gap-2 transition">
        <i class="fab fa-google text-red-400"></i> Google
      </button>
      <button type="button" class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white py-2 rounded-full flex items-center justify-center gap-2 transition">
        <i class="fab fa-facebook-f text-blue-400"></i> Facebook
      </button>
    </div>

    <p class="text-center text-sm mt-6 text-gray-200">
      Don't have an account? <a href="register.php" class="text-white font-semibold hover:underline">Register</a>
    </p>
  </div>

  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });

    // Basic client-side validation
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const username = this.elements['username'].value.trim();
      const password = this.elements['password'].value.trim();
      
      if (!username || !password) {
        e.preventDefault();
        alert('Please fill in all fields');
        return false;
      }
      return true;
    });
  </script>
</body>
</html>