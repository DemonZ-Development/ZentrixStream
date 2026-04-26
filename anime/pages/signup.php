<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request. Please try again.";
    } else {
        // Input validation and sanitization
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $error = "All fields are required.";
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $error = "Username must be between 3 and 50 characters.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error = "Username can only contain letters, numbers, and underscores.";
        } else {
            // Securely hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Check if email or username already exists using prepared statement
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $check_stmt->bind_param("ss", $email, $username);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $error = "Username or Email already exists!";
            } else {
                // Insert new user using prepared statement
                $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("sss", $username, $email, $hashed_password);
                
                if ($insert_stmt->execute()) {
                    $success = "Account created! You can now login.";
                    // Regenerate CSRF token after successful submission
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                } else {
                    $error = "Error creating account. Please try again.";
                }
                $insert_stmt->close();
            }
            $check_stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ZENTRIX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background-color: #0f0f0f; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="text-white min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white/5 backdrop-blur-md border border-[#00f3ff]/20 rounded-xl p-8 shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
        <h2 class="text-3xl font-bold text-center mb-6 text-[#00f3ff] drop-shadow-[0_0_10px_rgba(0,243,255,0.4)] tracking-wide uppercase">Join Zentrix</h2>
        
        <?php if($error): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 text-sm p-3 rounded mb-4"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-green-500/10 border border-green-500 text-green-400 text-sm p-3 rounded mb-4"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="flex flex-col gap-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Username</label>
                <input type="text" name="username" required class="w-full bg-[#111] border border-gray-800 text-white px-4 py-2.5 rounded focus:outline-none focus:border-[#00f3ff] transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                <input type="email" name="email" required class="w-full bg-[#111] border border-gray-800 text-white px-4 py-2.5 rounded focus:outline-none focus:border-[#00f3ff] transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Password</label>
                <input type="password" name="password" required class="w-full bg-[#111] border border-gray-800 text-white px-4 py-2.5 rounded focus:outline-none focus:border-[#00f3ff] transition">
            </div>
            
            <button type="submit" class="mt-4 w-full py-3 bg-[#00f3ff] text-black font-bold uppercase tracking-wider rounded shadow-[0_0_10px_rgba(0,243,255,0.3)] hover:shadow-[0_0_20px_rgba(0,243,255,0.6)] transition active:scale-95">
                Create Account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Already have an account? <a href="login.php" class="text-[#00f3ff] hover:underline font-bold">Login</a>
        </p>
    </div>

</body>
</html>