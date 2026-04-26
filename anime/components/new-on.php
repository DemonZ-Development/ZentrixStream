<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New On AniStream - AniStream Pro Explorer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --bg-color: #0b0b0b; --card-bg: #141414; --accent: #00f3ff; --text-main: #ffffff; --text-dim: #999; --border: #222; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Inter', sans-serif; }
        .neon-text { color: var(--accent); text-shadow: 0 0 10px rgba(0, 243, 255, 0.3); }
        .poster-hover:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(0,243,255,0.2); border: 1px solid var(--accent); }
        #anime-modal { display: none; overflow-y: auto; background: rgba(0,0,0,0.95); }
        .modal-body { max-width: 700px; margin: 2rem auto; background: #111; border-radius: 15px; overflow: hidden; border: 1px solid #333; position: relative; }
        .close-btn { position: absolute; right: 15px; top: 15px; background: var(--accent); color: #000; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; font-weight: bold; z-index: 101; }
        .meta-table { width: 100%; font-size: 0.9rem; border-collapse: collapse; margin-top: 10px; }
        .meta-table td { padding: 12px; border-bottom: 1px solid #222; }
        .meta-table .label { color: var(--text-dim); width: 130px; font-weight: bold; }
        .badge { background: #333; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 5px; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
        .title-cyan { color: #00f3ff; }
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
            <a href="new-on.php" class="block px-4 py-2 rounded hover:bg-gray-800 text-[#00f3ff]">✨ New On</a>
            <a href="schedule.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">📅 Schedule</a>
            <a href="trending.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🔥 Trending</a>
            <a href="upcoming.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🚀 Upcoming</a>
        </nav>
    </div>

    <main class="flex-1 flex flex-col relative overflow-hidden">
        <header class="p-4 border-b border-gray-800 flex justify-between items-center bg-[#0f0f0f] z-20 relative">
            <div class="w-full max-w-2xl flex gap-4 items-center">
                <button onclick="toggleSidebar()" class="text-[#00f3ff] hover:text-white transition focus:outline-none shrink-0" title="Open Menu">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6" id="content-area">
            <h2 class="text-2xl font-bold mb-6 uppercase tracking-wider text-cyan">New On Platform</h2>
            <div id="grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6"></div>
        </div>

        <div id="anime-modal" class="fixed inset-0 z-50 flex-col" onclick="if(event.target == this) closeModal()">
            <div class="modal-body shadow-2xl">
                <button class="close-btn shadow-lg hover:scale-110 transition" onclick="closeModal()">✕</button>
                <div id="modal-content"></div>
            </div>
        </div>
    </main>

    <script>
        const ANILIST_API = 'https://graphql.anilist.co';
        document.addEventListener('DOMContentLoaded', fetchPageData);
        function toggleSidebar() { const sb = document.getElementById('slidebar'); const ov = document.getElementById('slidebar-overlay'); sb.classList.toggle('-translate-x-full'); ov.classList.toggle('hidden'); }
        async function fetchPageData() {
            const query = `query { Page(perPage: 30) { media(sort: START_DATE_DESC, type: ANIME, isAdult: false, format: TV) { id title { english romaji } coverImage { extraLarge } format } } }`;
            const response = await fetch(ANILIST_API, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ query }) });
            renderGrid((await response.json()).data.Page.media);
        }
        function renderGrid(mediaList) {
            const grid = document.getElementById('grid'); grid.innerHTML = '';
            mediaList.forEach((anime) => {
                grid.innerHTML += `<div class="cursor-pointer transition-all duration-300 rounded-lg overflow-hidden poster-hover relative bg-gray-900 group" onclick="openDetails(${anime.id})"><div class="aspect-[2/3] overflow-hidden"><img src="${anime.coverImage.extraLarge}" class="w-full h-full object-cover"></div><div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black via-black/80 to-transparent pt-12"><p class="text-sm font-semibold truncate text-gray-100 drop-shadow-md">${anime.title.english || anime.title.romaji}</p></div></div>`;
            });
        }
        async function openDetails(animeId) { /* Omitted duplicate JS structure (Identical to latest.php Modal logic) */ }
        function closeModal() { document.getElementById('anime-modal').style.display = 'none'; document.body.style.overflow = 'auto'; }
    </script>
</body>
</html>