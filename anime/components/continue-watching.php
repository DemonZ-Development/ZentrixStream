<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db.php'; 

// Force Login Check: Redirect if no user_id is found in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$dbHistory = [];

// Fetch from your watch_history table using prepared statement
$stmt = $conn->prepare("SELECT * FROM watch_history WHERE user_id = ? ORDER BY watched_at DESC");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $dbHistory[] = $row;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime History - ZENTRIX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0f0f0f; font-family: 'Inter', sans-serif; }
        /* Custom scrollbar to match the Zentrix aesthetic */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #00f3ff; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#0f0f0f] text-white min-h-screen flex">

    <div class="lg:hidden fixed top-0 w-full bg-[#111] border-b border-[#00f3ff]/20 p-4 flex justify-between items-center z-50">
        <h1 class="text-xl font-black text-[#00f3ff] tracking-widest uppercase drop-shadow-[0_0_8px_rgba(0,243,255,0.5)]">ZENTRIX</h1>
        <button id="mobile-menu-btn" class="text-[#00f3ff] focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
    </div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 lg:static lg:w-64 bg-[#111] border-r border-[#00f3ff]/20 transition-transform duration-300 z-40 flex flex-col pt-20 lg:pt-0">
        <div class="hidden lg:flex items-center justify-center h-20 border-b border-[#00f3ff]/20">
            <h1 class="text-2xl font-black text-[#00f3ff] drop-shadow-[0_0_10px_rgba(0,243,255,0.4)] tracking-widest uppercase">ZENTRIX</h1>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="../index.php" class="flex items-center px-4 py-3 text-sm font-bold text-gray-400 hover:text-white hover:bg-[#00f3ff]/10 rounded-lg transition-colors uppercase tracking-wide">
                Home
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-sm font-bold text-gray-400 hover:text-white hover:bg-[#00f3ff]/10 rounded-lg transition-colors uppercase tracking-wide">
                Trending
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-sm font-bold text-gray-400 hover:text-white hover:bg-[#00f3ff]/10 rounded-lg transition-colors uppercase tracking-wide">
                Popular Movies
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-sm font-bold text-gray-400 hover:text-white hover:bg-[#00f3ff]/10 rounded-lg transition-colors uppercase tracking-wide">
                Popular TV
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-sm font-bold text-gray-400 hover:text-white hover:bg-[#00f3ff]/10 rounded-lg transition-colors uppercase tracking-wide">
                Upcoming
            </a>
            <a href="continue-watching.php" class="flex items-center px-4 py-3 text-sm font-bold text-[#00f3ff] bg-[#00f3ff]/10 border border-[#00f3ff]/30 rounded-lg transition-colors uppercase tracking-wide">
                Continue Watch
            </a>
            <a href="profile.php" class="flex items-center px-4 py-3 text-sm font-bold text-gray-400 hover:text-white hover:bg-[#00f3ff]/10 rounded-lg transition-colors uppercase tracking-wide">
                Profile
            </a>
        </nav>

        <div class="p-4 border-t border-[#00f3ff]/20">
            <a href="../pages/logout.php" class="flex items-center justify-center w-full px-4 py-2 text-sm font-bold text-white bg-red-600/80 hover:bg-red-600 rounded-lg transition-colors uppercase tracking-wide">
                Logout
            </a>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 hidden lg:hidden"></div>

    <main class="flex-1 p-4 lg:p-8 pt-24 lg:pt-8 overflow-y-auto">
        <header class="max-w-6xl mx-auto mb-8 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white tracking-wide uppercase">
                Watch <span class="text-[#00f3ff]">History</span>
            </h2>
        </header>

        <div id="list" class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <?php if (!empty($dbHistory)): ?>
                <?php foreach ($dbHistory as $item): ?>
                    <div class="bg-[#111] border border-[#00f3ff]/20 hover:border-[#00f3ff]/60 rounded-xl p-4 active:scale-95 transition-all duration-300">
                        <a href="../pages/watch.php?id=<?= htmlspecialchars($item['anime_id'], ENT_QUOTES, 'UTF-8') ?>&ep=<?= intval($item['episode']) ?>" class="block h-full">
                            <div class="text-[10px] text-gray-500 font-bold mb-1 uppercase tracking-widest">Cloud Sync</div>
                            <div class="text-sm font-bold text-white line-clamp-2 leading-tight mb-3"><?= htmlspecialchars($item['anime_title'], ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="text-[10px] text-black font-black inline-block bg-[#00f3ff] px-2.5 py-1 rounded-sm uppercase">
                                Episode <?= intval($item['episode']) ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-500 bg-[#111] rounded-xl border border-[#00f3ff]/10 shadow-[0_0_15px_rgba(0,0,0,0.5)]">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="font-bold text-sm tracking-widest uppercase">No Recent History Found</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Hamburger Menu & Sidebar Toggle Logic
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        mobileMenuBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
