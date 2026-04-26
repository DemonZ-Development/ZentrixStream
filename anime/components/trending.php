<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending - AniStream</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --bg-color: #0b0b0b; --accent: #00f3ff; --text-main: #ffffff; --text-dim: #999; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Inter', sans-serif; }
        .neon-text { color: var(--accent); text-shadow: 0 0 10px rgba(0, 243, 255, 0.3); }
        .poster-hover:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(0,243,255,0.2); border: 1px solid var(--accent); }
        #anime-modal { display: none; overflow-y: auto; background: rgba(0,0,0,0.95); }
        .modal-body { max-width: 700px; margin: 2rem auto; background: #111; border-radius: 15px; overflow: hidden; border: 1px solid #333; position: relative; }
        .close-btn { position: absolute; right: 15px; top: 15px; background: var(--accent); color: #000; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; font-weight: bold; z-index: 101; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <div id="slidebar-overlay" class="fixed inset-0 bg-black/80 z-40 hidden" onclick="toggleSidebar()"></div>
    
    <div id="slidebar" class="fixed top-0 left-0 w-64 h-full bg-[#0b0b0b] border-r border-gray-800 z-50 transform -translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-6 flex justify-between items-center border-b border-gray-800">
            <h2 class="text-2xl font-bold tracking-widest neon-text">MENU</h2>
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white text-2xl font-bold">&times;</button>
        </div>
        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-2">
            <a href="../index.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🏠 Home</a>
            <a href="latest.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🆕 Latest</a>
            <a href="new-on.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">✨ New On</a>
            <a href="schedule.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">📅 Schedule</a>
            <a href="trending.php" class="block px-4 py-2 rounded hover:bg-gray-800 text-[#00f3ff]">🔥 Trending</a>
            <a href="upcoming.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🚀 Upcoming</a>
        </nav>
    </div>

    <main class="flex-1 flex flex-col relative overflow-hidden">
        <header class="p-4 border-b border-gray-800 flex items-center bg-[#0f0f0f] z-20 relative">
            <button onclick="toggleSidebar()" class="text-[#00f3ff] hover:text-white transition focus:outline-none mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <h1 class="text-xl font-bold neon-text">TRENDING</h1>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <h2 class="text-2xl font-bold mb-6 uppercase tracking-wider text-[#00f3ff]">Hot This Week</h2>
            <div id="grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6"></div>
        </div>

        <div id="anime-modal" class="fixed inset-0 z-50 flex-col" onclick="if(event.target == this) closeModal()">
            <div class="modal-body shadow-2xl mx-4">
                <button class="close-btn shadow-lg hover:scale-110 transition" onclick="closeModal()">✕</button>
                <div id="modal-content"></div>
            </div>
        </div>
    </main>

    <script>
        const ANILIST_API = 'https://graphql.anilist.co';
        function toggleSidebar() { document.getElementById('slidebar').classList.toggle('-translate-x-full'); document.getElementById('slidebar-overlay').classList.toggle('hidden'); }
        function closeModal() { document.getElementById('anime-modal').style.display = 'none'; document.body.style.overflow = 'auto'; }

        document.addEventListener('DOMContentLoaded', async () => {
            const query = `query { Page(perPage: 30) { media(sort: TRENDING_DESC, type: ANIME, isAdult: false) { id title { english romaji } coverImage { extraLarge } format } } }`;
            const response = await fetch(ANILIST_API, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ query }) });
            const data = await response.json();
            const grid = document.getElementById('grid');
            data.data.Page.media.forEach(anime => {
                grid.innerHTML += `<div class="cursor-pointer transition-all duration-300 rounded-lg overflow-hidden poster-hover relative bg-gray-900" onclick="openDetails(${anime.id})">
                    <div class="aspect-[2/3] overflow-hidden"><img src="${anime.coverImage.extraLarge}" class="w-full h-full object-cover"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black via-black/80 to-transparent pt-12"><p class="text-sm font-semibold truncate text-gray-100">${anime.title.english || anime.title.romaji}</p></div>
                </div>`;
            });
        });

        async function openDetails(id) {
            const modal = document.getElementById('anime-modal');
            const content = document.getElementById('modal-content');
            modal.style.display = 'block'; document.body.style.overflow = 'hidden';
            const query = `query ($id: Int) { Media(id: $id, type: ANIME) { id title { english romaji } coverImage { extraLarge } bannerImage description episodes status seasonYear averageScore studios(isMain: true) { nodes { name } } } }`;
            const res = await fetch(ANILIST_API, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ query, variables: { id } }) });
            const d = (await res.json()).data.Media;
            content.innerHTML = `<div class="h-48 md:h-64 relative"><img src="${d.bannerImage || d.coverImage.extraLarge}" class="w-full h-full object-cover opacity-60"><div class="absolute inset-0 bg-gradient-to-t from-[#111] to-transparent"></div></div><div class="px-6 pb-8 pt-4"><h2 class="text-[#00f3ff] text-xl font-bold mb-4">${d.title.english || d.title.romaji}</h2><p class="text-gray-300 text-sm line-clamp-4 mb-4">${d.description || ''}</p><button onclick="window.location.href='../pages/watch.php?id=${d.id}&ep=1'" class="w-full py-3 bg-[#00f3ff] text-black rounded font-bold">▶ WATCH NOW</button></div>`;
        }
    </script>
</body>
</html>