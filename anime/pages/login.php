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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request. Please try again.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($email) || empty($password)) {
            $error = "Please enter both email and password.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                // Verify hashed password
                if (password_verify($password, $user['password'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    // Regenerate CSRF token
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    header("Location: profile.php");
                    exit();
                } else {
                    $error = "Incorrect password!";
                }
            } else {
                $error = "No account found with that email!";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ZENTRIX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background-color: #0f0f0f; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="text-white min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white/5 backdrop-blur-md border border-[#00f3ff]/20 rounded-xl p-8 shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
        <h2 class="text-3xl font-bold text-center mb-6 text-[#00f3ff] drop-shadow-[0_0_10px_rgba(0,243,255,0.4)] tracking-wide uppercase">Login</h2>
        
        <?php if($error): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 text-sm p-3 rounded mb-4"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="flex flex-col gap-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                <input type="email" name="email" required class="w-full bg-[#111] border border-gray-800 text-white px-4 py-2.5 rounded focus:outline-none focus:border-[#00f3ff] transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Password</label>
                <input type="password" name="password" required class="w-full bg-[#111] border border-gray-800 text-white px-4 py-2.5 rounded focus:outline-none focus:border-[#00f3ff] transition">
            </div>
            
            <button type="submit" class="mt-4 w-full py-3 bg-[#00f3ff] text-black font-bold uppercase tracking-wider rounded shadow-[0_0_10px_rgba(0,243,255,0.3)] hover:shadow-[0_0_20px_rgba(0,243,255,0.6)] transition active:scale-95">
                Login
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Don't have an account? <a href="signup.php" class="text-[#00f3ff] hover:underline font-bold">Sign Up</a>
        </p>
    </div>

</body>
</html>