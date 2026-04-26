<?php
$env = @parse_ini_file(__DIR__ . '/../.env') ?: [];
$tmdbKey = $env['TMDB_API_KEY'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming - Zentrix Stream</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --bg-color: #0b0b0b; --accent: #ff0000; --text-main: #ffffff; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Inter', sans-serif; }
        .red-text { color: var(--accent); text-shadow: 0 0 10px rgba(255, 0, 0, 0.4); }
        .poster-hover:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(255,0,0,0.3); border: 1px solid var(--accent); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/80 z-40 hidden" onclick="toggleSidebar()"></div>

    <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0b0b0b] border-r border-gray-800 z-50 transform -translate-x-full transition-transform duration-300 flex flex-col md:relative md:translate-x-0">
        <div class="p-6 flex justify-between items-center border-b border-gray-800">
            <h2 class="text-2xl font-bold red-text tracking-widest uppercase">MENU</h2>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-400 text-2xl font-bold">&times;</button>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <a href="../movie.php" class="block px-4 py-3 rounded text-gray-400 hover:bg-gray-800 hover:text-red-500 transition font-bold">🏠 Home</a>
            <a href="trending.php" class="block px-4 py-3 rounded text-gray-400 hover:bg-gray-800 hover:text-red-500 transition font-bold">🔥 Trending</a>
            <a href="popular-movies.php" class="block px-4 py-3 rounded text-gray-400 hover:bg-gray-800 hover:text-red-500 transition font-bold">🎬 Popular Movies</a>
            <a href="popular-tv.php" class="block px-4 py-3 rounded text-gray-400 hover:bg-gray-800 hover:text-red-500 transition font-bold">📺 Popular TV</a>
            <a href="upcoming.php" class="block px-4 py-3 rounded bg-red-600/10 text-red-500 border-l-4 border-red-600 font-bold">🚀 Upcoming</a>
            <a href="continue-watching.php" class="block px-4 py-3 rounded text-gray-400 hover:bg-gray-800 hover:text-red-500 transition font-bold">🕒 Continue Watch</a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="p-6 border-b border-gray-800 bg-[#0f0f0f] sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-xl font-black red-text uppercase tracking-widest">Upcoming Movies</h1>
            </div>
        </header>

        <div class="p-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4" id="grid"></div>
    </div>

    <script>
        const API_KEY = '<?= $tmdbKey ?>';
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        async function loadContent() {
            const res = await fetch(`https://api.themoviedb.org/3/movie/upcoming?api_key=${API_KEY}`);
            const data = await res.json();
            const grid = document.getElementById('grid');
            grid.innerHTML = data.results.map(item => `
                <div class="cursor-pointer transition-all duration-300 rounded-lg overflow-hidden poster-hover relative bg-gray-900" onclick="window.location.href='watch.php?id=${item.id}&type=movie'">
                    <div class="aspect-[2/3]">
                        <img src="https://image.tmdb.org/t/p/w500${item.poster_path}" class="w-full h-full object-cover" alt="${item.title}">
                    </div>
                    <div class="p-3 bg-gradient-to-t from-black to-transparent">
                        <p class="text-xs font-bold truncate">${item.title}</p>
                        <p class="text-[9px] text-gray-400">Release: ${item.release_date}</p>
                    </div>
                </div>
            `).join('');
        }
        loadContent();
    </script>
</body>
</html>