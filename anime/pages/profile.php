<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the latest user info from the database
$sql = "SELECT username, email, created_at FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ZENTRIX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background-color: #0f0f0f; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="text-white min-h-screen p-4 flex flex-col items-center pt-20">

    <div class="w-full max-w-lg bg-white/5 backdrop-blur-md border border-[#00f3ff]/30 hover:border-[#00f3ff]/60 transition-all rounded-xl p-6 shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
        
        <div class="flex items-center gap-4 mb-6 border-b border-gray-800 pb-6">
            <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-[#00f3ff] to-blue-600 flex items-center justify-center text-black font-black text-2xl shadow-[0_0_15px_rgba(0,243,255,0.4)]">
                <?= strtoupper(substr($user['username'], 0, 1)) ?>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($user['username']) ?></h1>
                <p class="text-sm text-[#00f3ff]">Streamer Level 1</p>
            </div>
        </div>

        <div class="flex flex-col gap-3 mb-8">
            <div class="bg-[#111] p-3 rounded border border-gray-800 flex justify-between items-center">
                <span class="text-xs text-gray-500 uppercase font-bold">Email</span>
                <span class="text-sm font-medium"><?= htmlspecialchars($user['email']) ?></span>
            </div>
            <div class="bg-[#111] p-3 rounded border border-gray-800 flex justify-between items-center">
                <span class="text-xs text-gray-500 uppercase font-bold">Joined Date</span>
                <span class="text-sm font-medium"><?= date("M d, Y", strtotime($user['created_at'])) ?></span>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="index.php" class="flex-1 text-center py-2 bg-gray-800 hover:bg-gray-700 text-white font-bold rounded transition active:scale-95">
                Home
            </a>
            <a href="logout.php" class="flex-1 text-center py-2 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold rounded transition active:scale-95">
                Logout
            </a>
        </div>

    </div>

    <div class="flex flex-col gap-3">
    <a href="continue-watching.php" class="w-full text-center py-3 bg-[#00f3ff]/10 border border-[#00f3ff] text-[#00f3ff] font-bold rounded hover:bg-[#00f3ff] hover:text-black transition">
        View Watch History
    </a>
    <div class="flex gap-3">
        <a href="index.php" class="flex-1 text-center py-2 bg-gray-800 rounded font-bold">Home</a>
        <a href="logout.php" class="flex-1 text-center py-2 border border-red-500 text-red-500 rounded font-bold">Logout</a>
    </div>
</div>

    
</body>
</html>