<?php
// pages/watch.php
require_once __DIR__ . '/../_config.php';

// Sanitize inputs
$animeId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
$currentEp = isset($_GET['ep']) ? filter_var($_GET['ep'], FILTER_VALIDATE_INT) : 1;

if (!$animeId || $animeId < 1) {
    die("Error: Invalid Anime ID provided.");
}

if ($currentEp < 1) {
    $currentEp = 1;
}

// Escape for output
$animeIdEscaped = e($animeId);
$currentEpEscaped = e($currentEp);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watching - ZENTRIX STREAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0f0f0f; color: #fff; font-family: 'Inter', sans-serif; }
        .neon-text { color: #00f3ff; text-shadow: 0 0 10px rgba(0,243,255,0.4); }
        .neon-btn-active { 
            background-color: #00f3ff; 
            color: #000; 
            font-weight: bold; 
            box-shadow: 0 0 10px rgba(0,243,255,0.3);
        }
        .neon-btn-inactive {
            background-color: transparent;
            color: #9ca3af; 
            font-weight: bold;
        }
        .neon-btn-inactive:hover {
            color: #fff;
        }
        select option { background-color: #111; color: #fff; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <header class="p-4 border-b border-gray-800 bg-[#111] flex justify-between items-center">
        <a href="../index.php" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <span>←</span> Back to Explore
        </a>
        <h1 id="anime-title" class="text-lg font-bold neon-text truncate max-w-md">Loading Title...</h1>
        <div class="w-[100px]"></div> 
    </header>

    <main class="flex-1 w-full max-w-5xl mx-auto p-4 flex flex-col gap-6">
        <div class="w-full">
            <div class="aspect-video w-full bg-black rounded-lg overflow-hidden border border-[#00f3ff]/20 shadow-2xl relative">
                <iframe id="video-frame" class="w-full h-full absolute inset-0" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
            </div>
            
            <div class="mt-4 p-5 bg-white/5 backdrop-blur-md rounded-lg border border-gray-800 shadow-lg flex flex-col gap-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <h2 class="text-xl font-bold w-full md:w-auto text-center md:text-left">
                        Episode <span id="current-ep-display" class="neon-text"><?= htmlspecialchars($currentEp) ?></span>
                    </h2>
                    
                    <div class="flex gap-2 w-full md:w-auto justify-center">
                        <button onclick="prevEp()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded transition font-medium active:scale-95">⏮ Prev</button>
                        <select id="episode-select" onchange="changeEpisodeFromSelect()" class="bg-gray-900 border border-gray-700 text-white px-4 py-2 rounded outline-none focus:border-[#00f3ff] font-medium cursor-pointer">
                            <option value="" disabled>Loading Episodes...</option>
                        </select>
                        <button onclick="nextEp()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded transition font-medium active:scale-95">Next ⏭</button>
                    </div>
                </div>

                <hr class="border-gray-800">

                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2 bg-gray-900 p-1.5 rounded-lg border border-gray-800">
                        <span class="text-xs text-gray-500 uppercase tracking-wider px-2 font-bold hidden sm:block">Server</span>
                        <button id="btn-server-1" onclick="changeServer(1)" class="px-5 py-2 rounded transition neon-btn-active">Megaplay</button>
                        <button id="btn-server-2" onclick="changeServer(2)" class="px-5 py-2 rounded transition neon-btn-inactive">4Animo</button>
                    </div>

                    <div class="flex items-center gap-2 bg-gray-900 p-1.5 rounded-lg border border-gray-800">
                        <span class="text-xs text-gray-500 uppercase tracking-wider px-2 font-bold hidden sm:block">Audio</span>
                        <button id="btn-lang-sub" onclick="changeLang('sub')" class="px-6 py-2 rounded transition neon-btn-active">Sub</button>
                        <button id="btn-lang-dub" onclick="changeLang('dub')" class="px-6 py-2 rounded transition neon-btn-inactive">Dub</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const ANILIST_API = 'https://graphql.anilist.co';
        const animeId = <?= json_encode($animeId) ?>;
        
        let currentEp = parseInt(<?= json_encode($currentEp) ?>);
        let totalEpisodes = 1;
        let currentServer = 1;
        let currentLang = 'sub';

        document.addEventListener('DOMContentLoaded', () => {
            fetchAnimeDetails();
        });

        async function fetchAnimeDetails() {
            const query = `
            query ($id: Int) {
                Media(id: $id, type: ANIME) {
                    title { english romaji }
                    episodes
                }
            }`;

            try {
                const response = await fetch(ANILIST_API, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ query, variables: { id: parseInt(animeId) } })
                });
                const data = await response.json();
                const anime = data.data.Media;
                
                const animeTitle = anime.title.english || anime.title.romaji;
                document.getElementById('anime-title').innerText = animeTitle;
                totalEpisodes = anime.episodes || 24; 
                
                buildEpisodeSelect();
                loadVideo();
                updateHistory(animeTitle); 
            } catch (err) {
                console.error("Error fetching AniList details:", err);
            }
        }

        function buildEpisodeSelect() {
            const select = document.getElementById('episode-select');
            select.innerHTML = '';
            for (let i = 1; i <= totalEpisodes; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.innerText = `Episode ${i}`;
                if (i === currentEp) option.selected = true;
                select.appendChild(option);
            }
        }

        function changeEpisodeFromSelect() {
            const select = document.getElementById('episode-select');
            changeEpisode(parseInt(select.value));
        }

        function changeEpisode(epNum) {
            currentEp = epNum;
            document.getElementById('current-ep-display').innerText = currentEp;
            document.getElementById('episode-select').value = currentEp;
            
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + `?id=${animeId}&ep=${currentEp}`;
            window.history.pushState({path:newUrl}, '', newUrl);
            
            loadVideo();
            updateHistory(document.getElementById('anime-title').innerText);
        }

        function prevEp() { if (currentEp > 1) changeEpisode(currentEp - 1); }
        function nextEp() { if (currentEp < totalEpisodes) changeEpisode(currentEp + 1); }

        function changeLang(lang) {
            currentLang = lang;
            document.getElementById('btn-lang-sub').className = lang === 'sub' ? 'px-6 py-2 rounded transition neon-btn-active' : 'px-6 py-2 rounded transition neon-btn-inactive';
            document.getElementById('btn-lang-dub').className = lang === 'dub' ? 'px-6 py-2 rounded transition neon-btn-active' : 'px-6 py-2 rounded transition neon-btn-inactive';
            loadVideo();
        }

        function changeServer(serverNum) {
            currentServer = serverNum;
            document.getElementById('btn-server-1').className = serverNum === 1 ? 'px-5 py-2 rounded transition neon-btn-active' : 'px-5 py-2 rounded transition neon-btn-inactive';
            document.getElementById('btn-server-2').className = serverNum === 2 ? 'px-5 py-2 rounded transition neon-btn-active' : 'px-5 py-2 rounded transition neon-btn-inactive';
            loadVideo();
        }

        function loadVideo() {
            let embedUrl = "";
            if (currentServer === 1) {
                embedUrl = `https://megaplay.buzz/stream/ani/${animeId}/${currentEp}/${currentLang}`;
            } else if (currentServer === 2) {
                embedUrl = `https://cdn.4animo.xyz/api/embed/anilist/${animeId}/${currentEp}/${currentLang}?k=1`;
            }
            document.getElementById('video-frame').src = embedUrl;
        }

        // Updated function to match continue-watching.php expectations
        async function updateHistory(title) {
            // 1. Sync with LocalStorage using 'zentrix_history' key
            let history = JSON.parse(localStorage.getItem('zentrix_history') || "[]");
            
            // Filter out existing entries for this ID to avoid duplicates
            history = history.filter(item => parseInt(item.id) !== parseInt(animeId));
            
            // Add new entry with correct 'id' and 'ep' names
            history.unshift({
                id: animeId,
                title: title,
                ep: currentEp
            });
            
            if(history.length > 30) history.pop();
            localStorage.setItem('zentrix_history', JSON.stringify(history));

            // 2. Sync with MySQL Database for cross-device support
            const formData = new FormData();
            formData.append('anime_id', animeId);
            formData.append('title', title);
            formData.append('episode', currentEp);

            fetch('save_history.php', {
                method: 'POST',
                body: formData
            }).catch(err => console.error("Database sync failed:", err));
        }
    </script>
</body>
</html>
