<?php
// pages/watch.php
$env = @parse_ini_file(__DIR__ . '/../.env') ?: [];
$tmdbKey = $env['TMDB_API_KEY'] ?? '';

$tmdbId = $_GET['id'] ?? null;
$type = $_GET['type'] ?? 'movie'; // 'movie' or 'tv'
$season = $_GET['season'] ?? 1;
$episode = $_GET['episode'] ?? 1;

if (!$tmdbId) {
    die("Error: No Media ID provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watching - ZENTRIX STREAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-color: #0b0b0b;
            --accent: #ff0000;
            --text-main: #ffffff;
        }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .red-text { color: var(--accent); text-shadow: 0 0 10px rgba(255, 0, 0, 0.4); }
        .red-btn-active { 
            background-color: var(--accent); 
            color: #fff; 
            font-weight: bold; 
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.4);
        }
        .red-btn-inactive {
            background-color: #1a1a1a;
            color: #9ca3af;
            border: 1px solid #333;
        }
        .red-btn-inactive:hover {
            color: #fff;
            border-color: var(--accent);
        }
        .video-container {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.1);
        }
        iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        select {
            background-color: #1a1a1a;
            border: 1px solid #333;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            outline: none;
            transition: border-color 0.3s;
        }
        select:focus { border-color: var(--accent); }
        .poster-hover:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(255,0,0,0.3); border: 1px solid var(--accent); }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <header class="p-4 border-b border-gray-800 bg-[#0f0f0f] flex justify-between items-center">
        <a href="../index.php" class="text-xl font-bold tracking-widest red-text uppercase">ZENTRIX STREAM</a>
        <div class="flex items-center gap-4">
            <a href="continue-watching.php" class="text-xs font-bold bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded transition uppercase">History</a>
            <a href="../index.php" class="text-sm font-bold text-gray-400 hover:text-white transition">✕ CLOSE</a>
        </div>
    </header>

    <main class="flex-1 container mx-auto p-4 md:p-8">
        
        <div class="max-w-5xl mx-auto">
            <div class="video-container mb-6">
                <iframe id="video-frame" allowfullscreen allow="autoplay; fullscreen"></iframe>
            </div>

            <div class="bg-[#111] p-6 rounded-xl border border-gray-800 shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-6">
                    
                    <div class="flex flex-col gap-3">
                        <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Select Server</span>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="changeServer('vidsrc')" id="btn-vidsrc" class="px-5 py-2 rounded transition text-xs red-btn-active">VIDSRC</button>
                            <button onclick="changeServer('vidzen')" id="btn-vidzen" class="px-5 py-2 rounded transition text-xs red-btn-inactive">VIDZEN</button>
                            <button onclick="changeServer('vidplays')" id="btn-vidplays" class="px-5 py-2 rounded transition text-xs red-btn-inactive">VIDPLAYS</button>
                            <button onclick="changeServer('peachify')" id="btn-peachify" class="px-5 py-2 rounded transition text-xs red-btn-inactive">PEACHIFY</button>
                        </div>
                    </div>

                    <?php if($type === 'tv'): ?>
                    <div class="flex gap-4">
                        <div class="flex flex-col gap-2">
                            <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Season</span>
                            <select id="season-input" onchange="updateTVDetails()" class="w-24">
                                <?php for($i=1; $i<=20; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i == $season ? 'selected' : '' ?>>S<?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Episode</span>
                            <select id="episode-input" onchange="updateTVDetails()" class="w-24">
                                <?php for($i=1; $i<=50; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i == $episode ? 'selected' : '' ?>>E<?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <div class="mt-8 pt-6 border-t border-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                <span class="w-1 h-5 bg-red-600 rounded"></span>
                                Now Playing: <span class="red-text uppercase ml-1">ID <?= $tmdbId ?></span>
                            </h2>
                            <p class="text-gray-500 text-xs mt-1">If the current server is slow, please try switching to another source above.</p>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h3 class="text-md font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M15.574 17.759a1 1 0 01-1.576.814l-4-2.82a1 1 0 00-1.144 0l-4 2.82a1 1 0 01-1.576-.814l.758-4.712-3.41-3.324a1 1 0 01.554-1.707l4.733-.688 2.115-4.29a1 1 0 011.794 0l2.115 4.29 4.733.688a1 1 0 01.554 1.707l-3.41 3.324.758 4.712z"/></svg>
                            Trending Now
                        </h3>
                        <div id="trending-grid-watch" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                             </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const API_KEY = '<?= $tmdbKey ?>';
        const tmdbId = "<?= $tmdbId ?>";
        const mediaType = "<?= $type ?>";
        let currentSeason = "<?= $season ?>";
        let currentEpisode = "<?= $episode ?>";
        let currentServer = 'vidsrc';

        const STORAGE_KEY = 'watch_progress';
        let watchProgress = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');

        // Initialize Data
        document.addEventListener('DOMContentLoaded', () => {
            loadVideo();
            loadTrending();
        });

        // Dynamic Trending Loader (Fixed Poster Logic)
        async function loadTrending() {
            try {
                const res = await fetch(`https://api.themoviedb.org/3/trending/movie/day?api_key=${API_KEY}`);
                const data = await res.json();
                const items = data.results.slice(0, 5);
                const grid = document.getElementById('trending-grid-watch');
                
                grid.innerHTML = items.map(item => `
                    <a href="watch.php?id=${item.id}&type=movie" class="group relative block overflow-hidden rounded-lg bg-gray-900 transition hover:scale-105 poster-hover border border-gray-800">
                        <div class="aspect-[2/3]">
                            <img src="https://image.tmdb.org/t/p/w300${item.poster_path}" 
                                 alt="${item.title}" 
                                 class="w-full h-full object-cover opacity-80 group-hover:opacity-100"
                                 onerror="this.src='https://via.placeholder.com/300x450/111/ff0000?text=No+Image'">
                        </div>
                        <div class="absolute bottom-0 p-2 w-full bg-gradient-to-t from-black via-black/80 to-transparent pt-8">
                            <div class="text-[10px] font-bold truncate uppercase text-white">${item.title}</div>
                            <div class="text-[8px] text-red-500 font-bold">★ ${item.vote_average.toFixed(1)}</div>
                        </div>
                    </a>
                `).join('');
            } catch (err) {
                console.error("Failed to load trending posters:", err);
            }
        }

        window.addEventListener('message', (event) => {
            if (event.data?.type === 'MEDIA_DATA') {
                const mediaData = event.data.data;
                if (mediaData.id && (mediaData.type === 'movie' || mediaData.type === 'tv')) {
                    watchProgress[mediaData.id] = {
                        ...watchProgress[mediaData.id],
                        ...mediaData,
                        last_updated: Date.now()
                    };
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(watchProgress));
                }
            }
        });

        function changeServer(serverName) {
            currentServer = serverName;
            const servers = ['vidsrc', 'vidzen', 'vidplays', 'peachify'];
            servers.forEach(s => {
                const btn = document.getElementById(`btn-${s}`);
                if (s === serverName) {
                    btn.className = 'px-5 py-2 rounded transition text-xs red-btn-active';
                } else {
                    btn.className = 'px-5 py-2 rounded transition text-xs red-btn-inactive';
                }
            });
            loadVideo();
        }

        function updateTVDetails() {
            currentSeason = document.getElementById('season-input').value;
            currentEpisode = document.getElementById('episode-input').value;
            const newUrl = window.location.pathname + `?id=${tmdbId}&type=${mediaType}&season=${currentSeason}&episode=${currentEpisode}`;
            window.history.pushState({path:newUrl},'',newUrl);
            loadVideo();
        }

        function loadVideo() {
            let embedUrl = "";
            const redColor = "ff0000";

            if (mediaType === 'movie') {
                switch(currentServer) {
                    case 'vidsrc': embedUrl = `https://vidsrc.ru/movie/${tmdbId}?autoplay=true&colour=${redColor}`; break;
                    case 'vidzen': embedUrl = `https://vidzen.fun/movie/${tmdbId}`; break;
                    case 'vidplays': embedUrl = `https://vidplays.fun/embed/movie/${tmdbId}`; break;
                    case 'peachify': embedUrl = `https://peachify.top/embed/movie/${tmdbId}`; break;
                }
            } else {
                switch(currentServer) {
                    case 'vidsrc': embedUrl = `https://vidsrc.ru/tv/${tmdbId}/${currentSeason}/${currentEpisode}?autoplay=true&colour=${redColor}&autonextepisode=true`; break;
                    case 'vidzen': embedUrl = `https://vidzen.fun/tv/${tmdbId}/${currentSeason}/${currentEpisode}`; break;
                    case 'vidplays': embedUrl = `https://vidplays.fun/embed/tv/${tmdbId}/${currentSeason}/${currentEpisode}`; break;
                    case 'peachify': embedUrl = `https://peachify.top/embed/tv/${tmdbId}/${currentSeason}/${currentEpisode}`; break;
                }
            }
            document.getElementById('video-frame').src = embedUrl;
        }
    </script>
</body>
</html>