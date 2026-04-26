<?php
// TMDB API key is now handled server-side via api/tmdb.php proxy
// This prevents API key exposure in client-side JavaScript
require_once __DIR__ . '/_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zentrix Stream - Movies & TV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-color: #0b0b0b;
            --card-bg: #141414;
            --accent: #ff0000;
            --text-main: #ffffff;
            --text-dim: #999;
            --border: #222;
        }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .red-text { color: var(--accent); text-shadow: 0 0 10px rgba(255, 0, 0, 0.4); }
        .poster-hover:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(255,0,0,0.3); border: 1px solid var(--accent); }
        
        /* Modal Styles */
        #movie-modal { display: none; overflow-y: auto; background: rgba(0,0,0,0.95); position: fixed; inset: 0; z-index: 100; }
        .modal-body { max-width: 750px; margin: 2rem auto; background: #111; border-radius: 15px; overflow: hidden; border: 1px solid #333; position: relative; }
        .close-btn { position: absolute; right: 15px; top: 15px; background: var(--accent); color: #fff; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; font-weight: bold; z-index: 101; }
        .meta-table { width: 100%; font-size: 0.85rem; border-collapse: collapse; }
        .meta-table td { padding: 8px 0; border-bottom: 1px solid #222; }
        .meta-table .label { color: var(--text-dim); width: 130px; font-weight: bold; text-transform: uppercase; font-size: 0.75rem; }

        /* Carousel Styles */
        .spotlight-slide { position: absolute; inset: 0; opacity: 0; transition: opacity 0.7s ease-in-out; pointer-events: none; z-index: 1; }
        .spotlight-slide.active { opacity: 1; pointer-events: auto; z-index: 2; }
        .carousel-dot { width: 10px; height: 10px; border-radius: 50%; background: #444; cursor: pointer; transition: all 0.3s ease; }
        .carousel-dot.active { background: var(--accent); box-shadow: 0 0 10px var(--accent); }

        /* List Styling for Top Airing */
        .airing-item:hover { background: #1a1a1a; border-left: 4px solid var(--accent); }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
    </style>
</head>
<body class="bg-[#0b0b0b]">

    <div id="slidebar-overlay" class="fixed inset-0 bg-black/80 z-40 hidden" onclick="toggleSidebar()"></div>
    
    <div id="slidebar" class="fixed top-0 left-0 w-64 h-full bg-[#0b0b0b] border-r border-gray-800 z-50 transform -translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-6 flex justify-between items-center border-b border-gray-800">
            <h2 class="text-2xl font-bold tracking-widest red-text">MENU</h2>
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white text-2xl font-bold">&times;</button>
        </div>
        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-2">
            <a href="movie.php" class="flex items-center gap-3 px-4 py-3 rounded bg-red-600/10 text-red-500 font-bold border-l-4 border-red-600 transition">🏠 Home</a>
            
            <a href="pages/trending.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-red-500 transition">🔥 Trending</a>
            <a href="pages/popular-movies.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-red-500 transition">🎬 Popular Movies</a>
            <a href="pages/popular-tv.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-red-500 transition">📺 Popular TV</a>
            <a href="pages/upcoming.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-red-500 transition">🚀 Upcoming</a>
            <a href="pages/continue-watching.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-red-500 transition font-bold">🕒 Continue Watch</a>
        </nav>
    </div>

    <main class="w-full flex flex-col relative">
        <header class="p-6 border-b border-gray-800 bg-[#0f0f0f] z-20">
            <div class="flex flex-col items-center gap-4 text-center">
                <h1 class="text-xl font-bold tracking-[0.2em] red-text uppercase">ZENTRIX STREAM</h1>
                <div class="w-full max-w-2xl flex gap-4 items-center">
                    <button onclick="toggleSidebar()" class="text-red-600 hover:text-white transition shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <form onsubmit="event.preventDefault(); searchContent();" class="w-full flex gap-2">
                        <input type="text" id="searchInput" placeholder="Search Movies or TV Shows..." class="w-full bg-gray-900 border border-gray-700 rounded-full px-6 py-2 focus:outline-none focus:border-red-600 text-white">
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-full font-bold hover:bg-red-700 transition">Search</button>
                    </form>
                </div>
            </div>
        </header>

        <div id="content-area">
            <div id="spotlight-section" class="relative w-full h-[60vh] min-h-[450px] overflow-hidden">
                <div id="spotlight-slides" class="w-full h-full relative"></div>
                <div id="spotlight-dots" class="absolute right-6 top-1/2 transform -translate-y-1/2 flex flex-col gap-3 z-30"></div>
            </div>

            <div class="p-6 space-y-12">
                
                <section>
                    <h2 class="text-xl font-bold mb-6 uppercase tracking-wider text-red-600 flex items-center gap-2">
                        <span class="w-1 h-6 bg-red-600 rounded"></span> Trending Now
                    </h2>
                    <div id="trending-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
                </section>

                <div class="flex flex-col lg:flex-row gap-8">
                    <aside class="w-full lg:w-80">
                        <h2 class="text-xl font-bold mb-6 uppercase tracking-wider text-red-600">Top Airing</h2>
                        <div id="top-airing-list" class="bg-[#111] rounded-xl overflow-hidden border border-gray-800 divide-y divide-gray-800"></div>
                    </aside>

                    <section class="flex-1">
                        <h2 class="text-xl font-bold mb-6 uppercase tracking-wider text-red-600 flex items-center gap-2">
                            <span class="w-1 h-6 bg-red-600 rounded"></span> Latest Movies
                        </h2>
                        <div id="latest-movies-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>
                    </section>
                </div>
            </div>
        </div>

        <div id="movie-modal" onclick="if(event.target == this) closeModal()">
            <div class="modal-body shadow-2xl">
                <button class="close-btn shadow-lg hover:scale-110 transition" onclick="closeModal()">✕</button>
                <div id="modal-content"></div>
            </div>
        </div>
    </main>

    <script>
        // Use local proxy to hide API key from client-side
        const PROXY_URL = 'api/tmdb.php';
        let slideTimer;

        document.addEventListener('DOMContentLoaded', initData);

        async function initData() {
            try {
                const trendRes = await fetch(`${PROXY_URL}?endpoint=/trending/all/day`);
                const trendData = await trendRes.json();
                if (trendData.results) {
                    renderSpotlight(trendData.results.slice(0, 5));
                    renderGrid(trendData.results.slice(5, 17), 'trending-grid');
                }

                const airingRes = await fetch(`${PROXY_URL}?endpoint=/tv/on_the_air`);
                const airingData = await airingRes.json();
                if (airingData.results) {
                    renderAiringList(airingData.results.slice(0, 10));
                }

                const latestRes = await fetch(`${PROXY_URL}?endpoint=/movie/now_playing`);
                const latestData = await latestRes.json();
                if (latestData.results) {
                    renderGrid(latestData.results.slice(0, 12), 'latest-movies-grid');
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function toggleSidebar() {
            document.getElementById('slidebar').classList.toggle('-translate-x-full');
            document.getElementById('slidebar-overlay').classList.toggle('hidden');
        }

        function renderSpotlight(items) {
            const container = document.getElementById('spotlight-slides');
            const dots = document.getElementById('spotlight-dots');
            container.innerHTML = ''; dots.innerHTML = '';
            items.forEach((item, i) => {
                const backdropPath = item.backdrop_path || '';
                const title = item.title || item.name || '';
                const overview = item.overview || '';
                const mediaType = item.media_type || 'movie';
                const id = item.id || '';
                
                container.innerHTML += `
                    <div class="spotlight-slide ${i===0?'active':''}" data-index="${i}">
                        <img src="https://image.tmdb.org/t/p/original${escapeHtml(backdropPath)}" class="w-full h-full object-cover opacity-40">
                        <div class="absolute inset-0 bg-gradient-to-r from-[#0b0b0b] via-transparent to-transparent"></div>
                        <div class="absolute bottom-12 left-12 max-w-2xl z-10 p-4">
                            <h2 class="text-4xl md:text-6xl font-black mb-4 uppercase tracking-tighter">${escapeHtml(title)}</h2>
                            <p class="text-gray-300 line-clamp-2 mb-6 text-sm">${escapeHtml(overview)}</p>
                            <button onclick="openDetails('${escapeHtml(id)}', '${escapeHtml(mediaType)}')" class="bg-red-600 px-8 py-3 rounded-lg font-bold hover:bg-red-700 transition shadow-lg">DETAILS</button>
                        </div>
                    </div>`;
                dots.innerHTML += `<div class="carousel-dot ${i===0?'active':''}" onclick="showSlide(${i})"></div>`;
            });
            startAutoSlide();
        }

        function renderGrid(items, targetId) {
            const grid = document.getElementById(targetId);
            grid.innerHTML = items.map(item => {
                const posterPath = item.poster_path || '';
                const title = item.title || item.name || '';
                const mediaType = item.media_type || 'movie';
                const id = item.id || '';
                const voteAverage = item.vote_average ? item.vote_average.toFixed(1) : 'N/A';
                
                return `
                <div class="cursor-pointer transition-all duration-300 rounded-lg overflow-hidden poster-hover relative bg-gray-900" onclick="openDetails('${escapeHtml(id)}', '${escapeHtml(mediaType)}')">
                    <div class="aspect-[2/3] overflow-hidden">
                        <img src="https://image.tmdb.org/t/p/w500${escapeHtml(posterPath)}" class="w-full h-full object-cover" loading="lazy">
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black via-black/80 to-transparent pt-10">
                        <p class="text-xs font-bold truncate">${escapeHtml(title)}</p>
                        <div class="flex gap-2 mt-1">
                            <span class="text-[9px] bg-red-600 px-1 rounded font-bold">★ ${voteAverage}</span>
                        </div>
                    </div>
                </div>
            `;}).join('');
        }

        function renderAiringList(items) {
            const list = document.getElementById('top-airing-list');
            list.innerHTML = items.map((item, i) => {
                const posterPath = item.poster_path || '';
                const name = item.name || '';
                const id = item.id || '';
                const voteCount = item.vote_count || 0;
                
                return `
                <div class="airing-item p-3 flex items-center gap-4 cursor-pointer transition-all border-l-4 border-transparent" onclick="openDetails('${escapeHtml(id)}', 'tv')">
                    <span class="text-xl font-black text-gray-700 w-6">${(i+1).toString().padStart(2,'0')}</span>
                    <img src="https://image.tmdb.org/t/p/w200${escapeHtml(posterPath)}" class="w-12 h-16 object-cover rounded">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold truncate">${escapeHtml(name)}</h4>
                        <div class="flex items-center gap-2 mt-1 text-[10px]">
                            <span class="text-red-500 font-bold">VOTES: ${voteCount}</span>
                            <span class="text-gray-500">• TV</span>
                        </div>
                    </div>
                </div>
            `;}).join('');
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        async function openDetails(id, type) {
            const modal = document.getElementById('movie-modal');
            const content = document.getElementById('modal-content');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            content.innerHTML = '<div class="p-20 text-center text-red-600 font-bold animate-pulse uppercase">Fetching Data...</div>';

            try {
                const res = await fetch(`${PROXY_URL}?endpoint=/${type}/${id}`);
                const d = await res.json();

                if (d.error) {
                    content.innerHTML = '<div class="p-20 text-center text-red-600">Error loading data</div>';
                    return;
                }

                const backdropPath = d.backdrop_path || d.poster_path || '';
                const posterPath = d.poster_path || '';
                
                content.innerHTML = `
                    <div class="h-64 relative">
                        <img src="https://image.tmdb.org/t/p/original${escapeHtml(backdropPath)}" class="w-full h-full object-cover opacity-50">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#111] to-transparent"></div>
                        <img src="https://image.tmdb.org/t/p/w300${escapeHtml(posterPath)}" class="absolute bottom-[-30px] left-8 w-28 h-40 rounded shadow-2xl border-2 border-gray-800 object-cover z-20">
                    </div>
                    <div class="px-8 pb-8 pt-12 relative">
                        <h2 class="text-2xl font-black text-red-600 mb-2 uppercase tracking-tighter">${escapeHtml(d.title || d.name)}</h2>
                        
                        <div class="bg-gray-900/50 p-4 rounded-lg text-sm text-gray-300 leading-relaxed border-l-4 border-red-600 my-4 max-h-40 overflow-y-auto">
                            <strong>OVERVIEW:</strong><br>${escapeHtml(d.overview) || 'No description available.'}
                        </div>

                        <h4 class="border-b border-gray-800 pb-2 mb-2 font-bold text-gray-400 uppercase text-xs tracking-widest">Technical Data</h4>
                        <table class="meta-table mb-6">
                            <tr><td class="label">Release Date</td><td>${escapeHtml(d.release_date || d.first_air_date) || 'N/A'}</td></tr>
                            <tr><td class="label">Status</td><td>${escapeHtml(d.status) || 'N/A'}</td></tr>
                            <tr><td class="label">Genres</td><td>${d.genres ? d.genres.map(g => escapeHtml(g.name)).join(', ') : 'N/A'}</td></tr>
                            <tr><td class="label">Rating</td><td class="text-red-500 font-bold">★ ${d.vote_average ? d.vote_average.toFixed(1) : 'N/A'}</td></tr>
                        </table>

                        <button onclick="window.location.href='pages/watch.php?id=${escapeHtml(id)}&type=${escapeHtml(type)}'" class="w-full py-4 bg-red-600 text-white rounded-lg font-black text-lg hover:bg-red-700 shadow-lg shadow-red-900/40 transition-all uppercase">
                            ▶ WATCH NOW
                        </button>
                    </div>`;
            } catch (error) {
                content.innerHTML = '<div class="p-20 text-center text-red-600">Error loading data</div>';
            }
        }

        function closeModal() {
            document.getElementById('movie-modal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function showSlide(index) {
            const slides = document.querySelectorAll('.spotlight-slide');
            const dots = document.querySelectorAll('.carousel-dot');
            slides.forEach((s, i) => {
                s.classList.toggle('active', i === index);
                dots[i].classList.toggle('active', i === index);
            });
        }

        function startAutoSlide() {
            clearInterval(slideTimer);
            let current = 0;
            slideTimer = setInterval(() => {
                current = (current + 1) % 5;
                showSlide(current);
            }, 5000);
        }

        async function searchContent() {
            const query = document.getElementById('searchInput').value;
            if(!query) return;
            try {
                const res = await fetch(`${PROXY_URL}?endpoint=/search/multi&query=${encodeURIComponent(query)}`);
                const data = await res.json();
                if (data.results) {
                    document.getElementById('spotlight-section').classList.add('hidden');
                    renderGrid(data.results.filter(i => i.poster_path), 'trending-grid');
                }
            } catch (error) {
                console.error('Search error:', error);
            }
        }
    </script>
</body>
</html>