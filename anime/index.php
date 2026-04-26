<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zentrix Anime</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-color: #0b0b0b;
            --card-bg: #141414;
            --accent: #00f3ff;
            --text-main: #ffffff;
            --text-dim: #999;
            --border: #222;
        }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Inter', sans-serif; }
        .neon-text { color: var(--accent); text-shadow: 0 0 10px rgba(0, 243, 255, 0.3); }
        .poster-hover:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(0,243,255,0.2); border: 1px solid var(--accent); }
        
        /* Modal Styles - PRESERVED */
        #anime-modal { display: none; overflow-y: auto; background: rgba(0,0,0,0.95); }
        .modal-body { max-width: 700px; margin: 2rem auto; background: #111; border-radius: 15px; overflow: hidden; border: 1px solid #333; position: relative; }
        .close-btn { position: absolute; right: 15px; top: 15px; background: var(--accent); color: #000; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; font-weight: bold; z-index: 101; }
        .meta-table { width: 100%; font-size: 0.9rem; border-collapse: collapse; margin-top: 10px; }
        .meta-table td { padding: 12px; border-bottom: 1px solid #222; }
        .meta-table .label { color: var(--text-dim); width: 130px; font-weight: bold; }
        .badge { background: #333; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 5px; }
        
        /* Carousel Styles */
        .spotlight-slide { position: absolute; inset: 0; opacity: 0; transition: opacity 0.7s ease-in-out; pointer-events: none; z-index: 1; }
        .spotlight-slide.active { opacity: 1; pointer-events: auto; z-index: 2; }
        .carousel-dot { width: 10px; height: 10px; border-radius: 50%; background: #444; cursor: pointer; transition: all 0.3s ease; }
        .carousel-dot.active { background: var(--accent); box-shadow: 0 0 10px var(--accent); }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }

        .cc-badge { background-color: #a8e6cf; color: #000; }
        .mic-badge { background-color: #ffb7b2; color: #000; }
        .title-cyan { color: #00f3ff; }
    </style>
</head>
<body class="bg-[#0b0b0b]">

    <div id="slidebar-overlay" class="fixed inset-0 bg-black/80 z-40 hidden" onclick="toggleSidebar()"></div>
    
    <div id="slidebar" class="fixed top-0 left-0 w-64 h-full bg-[#0b0b0b] border-r border-gray-800 z-50 transform -translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-6 flex justify-between items-center border-b border-gray-800">
            <h2 class="text-2xl font-bold tracking-widest neon-text">MENU</h2>
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white text-2xl font-bold">&times;</button>
        </div>
        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-2">
            <a href="index.php" class="block px-4 py-2 rounded hover:bg-gray-800 text-[#00f3ff]">🏠 Home</a>
            <a href="pages/signup.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🔒 Signup/Login</a>
            <a href="pages/profile.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">👨 Profile</a>
            <a href="components/latest.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🆕 Latest</a>
            <a href="components/new-on.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">✨ New On</a>
            <a href="components/schedule.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">📅 Schedule</a>
            <a href="components/trending.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🔥 Trending</a>
            <a href="components/upcoming.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🚀 Upcoming</a>
            <a href="components/continue-watching.php" class="block px-4 py-2 rounded hover:bg-gray-800 hover:text-[#00f3ff] transition">🕒 Continue-Watch</a>
        </nav>
    </div>

    <main class="w-full flex flex-col relative">
        <header class="p-6 border-b border-gray-800 bg-[#0f0f0f] z-20">
            <div class="flex flex-col items-center gap-4">
                <h1 class="text-xl font-bold tracking-[0.2em] neon-text uppercase">ZENTRIX ANIME</h1>
                
                <div class="w-full max-w-2xl flex gap-4 items-center">
                    <button onclick="toggleSidebar()" class="text-[#00f3ff] hover:text-white transition focus:outline-none shrink-0" title="Open Menu">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <form onsubmit="event.preventDefault(); searchAnime();" class="w-full flex gap-2">
                        <input type="text" id="searchInput" placeholder="Search Anime..." class="w-full bg-gray-900 border border-gray-700 rounded-full px-6 py-2 focus:outline-none focus:border-[#00f3ff] text-white">
                        <button type="submit" class="bg-[#00f3ff] text-black px-6 py-2 rounded-full font-bold hover:bg-cyan-400 transition">Search</button>
                    </form>
                </div>
            </div>
        </header>

        <div id="content-area">
            <div id="spotlight-section" class="relative w-full h-[60vh] min-h-[450px] hidden overflow-hidden">
                <div id="spotlight-slides" class="w-full h-full relative"></div>
                <div id="spotlight-dots" class="absolute right-6 top-1/2 transform -translate-y-1/2 flex flex-col gap-3 z-30"></div>
            </div>

            <div class="p-6">
                <h2 id="section-title" class="text-xl font-bold mb-6 uppercase tracking-wider text-cyan">Trending</h2>
                <div id="grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6"></div>
            </div>

            <div id="home-extra-sections" class="px-6 pb-12 grid grid-cols-1 lg:grid-cols-3 gap-10 hidden">
                <div class="col-span-1">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="title-cyan text-xl font-bold">Top Airing</h2>
                    </div>
                    <div id="top-airing-list" class="flex flex-col space-y-4"></div>
                </div>

                <div class="col-span-1 lg:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="title-cyan text-xl font-bold">Latest Episode</h2>
                    </div>
                    <div id="latest-episodes-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                </div>
            </div>
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
        let slideTimer;
        let currentSpotlightIndex = 0;
        let totalSpotlights = 0;

        document.addEventListener('DOMContentLoaded', () => fetchAnime('TRENDING_DESC'));

        function toggleSidebar() {
            const slidebar = document.getElementById('slidebar');
            const overlay = document.getElementById('slidebar-overlay');
            if (slidebar.classList.contains('-translate-x-full')) {
                slidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                slidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        async function queryAnilist(query, variables) {
            const response = await fetch(ANILIST_API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ query, variables })
            });
            return response.json();
        }

        async function fetchAnime(sortOrder) {
            const query = `
            query ($sort: [MediaSort]) {
                Page(perPage: 15) {
                    media(sort: $sort, type: ANIME, isAdult: false) {
                        id 
                        title { english romaji } 
                        coverImage { extraLarge } 
                        bannerImage
                        description
                        format
                    }
                }
            }`;
            const data = await queryAnilist(query, { sort: [sortOrder] });
            const mediaList = data.data.Page.media;

            if (sortOrder === 'TRENDING_DESC' && mediaList.length > 0) {
                document.getElementById('section-title').innerText = 'Trending';
                const spotlights = mediaList.slice(0, 5);
                renderSpotlight(spotlights);
                renderGrid(mediaList.slice(5), true, 6); 
                
                document.getElementById('home-extra-sections').classList.remove('hidden');
                document.getElementById('home-extra-sections').classList.add('grid');
                fetchTopAiring();
                fetchLatestEpisodes();
            } else {
                document.getElementById('spotlight-section').style.display = 'none';
                document.getElementById('home-extra-sections').classList.add('hidden');
                document.getElementById('home-extra-sections').classList.remove('grid');
                clearInterval(slideTimer);
                let title = sortOrder === 'POPULARITY_DESC' ? 'Popular' : 'Newest';
                document.getElementById('section-title').innerText = title;
                renderGrid(mediaList, false, 1);
            }
        }

        async function searchAnime() {
            const search = document.getElementById('searchInput').value;
            if(!search) return;
            const query = `
            query ($search: String) {
                Page(perPage: 30) {
                    media(search: $search, type: ANIME, isAdult: false) {
                        id title { english romaji } coverImage { extraLarge } format
                    }
                }
            }`;
            document.getElementById('spotlight-section').style.display = 'none';
            document.getElementById('home-extra-sections').classList.add('hidden');
            document.getElementById('home-extra-sections').classList.remove('grid');
            clearInterval(slideTimer);
            document.getElementById('section-title').innerText = `Results for: ${search}`;
            const data = await queryAnilist(query, { search });
            renderGrid(data.data.Page.media, false, 1);
        }

        function renderSpotlight(spotlights) {
            const container = document.getElementById('spotlight-slides');
            const dotsContainer = document.getElementById('spotlight-dots');
            const section = document.getElementById('spotlight-section');
            
            container.innerHTML = '';
            dotsContainer.innerHTML = '';
            totalSpotlights = spotlights.length;

            spotlights.forEach((anime, index) => {
                const title = anime.title.english || anime.title.romaji;
                const banner = anime.bannerImage || anime.coverImage.extraLarge;
                let desc = anime.description ? anime.description.replace(/<[^>]*>?/gm, '').substring(0, 200) + '...' : 'No description available.';

                container.innerHTML += `
                    <div class="spotlight-slide ${index === 0 ? 'active' : ''}" id="slide-${index}">
                        <div class="absolute inset-0">
                            <img src="${banner}" alt="${title}" class="w-full h-full object-cover opacity-60">
                            <div class="absolute inset-0 bg-gradient-to-t from-[var(--bg-color)] via-[var(--bg-color)]/30 to-transparent"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-[var(--bg-color)] via-[var(--bg-color)]/70 to-transparent"></div>
                        </div>
                        
                        <div class="relative z-10 p-10 md:p-16 flex flex-col justify-end h-full max-w-3xl">
                            <p class="text-[var(--accent)] font-bold text-sm tracking-wide mb-3 uppercase">#${index + 1} Spotlight</p>
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight drop-shadow-lg">${title}</h1>
                            <p class="text-gray-300 mb-8 line-clamp-3 text-sm md:text-base max-w-2xl">${desc}</p>
                            
                            <div class="flex gap-4">
                                <button onclick="window.location.href='pages/watch.php?id=${anime.id}&ep=1'" class="bg-[var(--accent)] text-black px-8 py-3 rounded-full font-bold hover:bg-cyan-400 hover:shadow-[0_0_15px_rgba(0,243,255,0.6)] transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Watch Now
                                </button>
                                <button onclick="openDetails(${anime.id})" class="bg-gray-600/60 text-white px-8 py-3 rounded-full font-bold hover:bg-gray-600/90 transition backdrop-blur-sm border border-gray-500/50 flex items-center justify-center gap-2">
                                    Detail ❯
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                dotsContainer.innerHTML += `<div class="carousel-dot ${index === 0 ? 'active' : ''}" onclick="goToSlide(${index})"></div>`;
            });

            section.style.display = 'block';
            currentSpotlightIndex = 0;
            clearInterval(slideTimer);
            slideTimer = setInterval(nextSlide, 5000);
        }

        function nextSlide() { goToSlide((currentSpotlightIndex + 1) % totalSpotlights); }

        function goToSlide(index) {
            document.querySelectorAll('.spotlight-slide').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.carousel-dot').forEach(el => el.classList.remove('active'));
            document.getElementById(`slide-${index}`).classList.add('active');
            document.querySelectorAll('.carousel-dot')[index].classList.add('active');
            currentSpotlightIndex = index;
            clearInterval(slideTimer);
            slideTimer = setInterval(nextSlide, 5000);
        }

        function renderGrid(mediaList, showNumbers = false, startRank = 1) {
            const grid = document.getElementById('grid');
            grid.innerHTML = '';
            mediaList.forEach((anime, index) => {
                const title = anime.title.english || anime.title.romaji;
                const rankNumber = String(startRank + index).padStart(2, '0');
                const numberBadge = showNumbers ? `
                    <div class="absolute top-0 left-0 bg-white text-black font-bold text-xl px-3 py-1.5 z-10">
                        ${rankNumber}
                    </div>` : '';

                grid.innerHTML += `
                    <div class="cursor-pointer transition-all duration-300 rounded-lg overflow-hidden poster-hover relative bg-gray-900 group" 
                         onclick="openDetails(${anime.id})">
                        ${numberBadge}
                        <div class="aspect-[2/3] overflow-hidden">
                            <img src="${anime.coverImage.extraLarge}" alt="${title}" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black via-black/80 to-transparent pt-12">
                            <p class="text-xs font-bold text-[#00f3ff] mb-1 opacity-0 group-hover:opacity-100 transition-opacity">${anime.format || 'TV'}</p>
                            <p class="text-sm font-semibold truncate text-gray-100 drop-shadow-md">${title}</p>
                        </div>
                    </div>
                `;
            });
        }

        async function fetchTopAiring() {
            const query = `query { Page(perPage: 4) { media(sort: POPULARITY_DESC, type: ANIME, status: RELEASING, isAdult: false) { id title { english romaji } coverImage { large } format episodes } } }`;
            const data = await queryAnilist(query);
            renderTopAiring(data.data.Page.media);
        }

        function renderTopAiring(mediaList) {
            const container = document.getElementById('top-airing-list');
            container.innerHTML = '';
            mediaList.forEach(anime => {
                const title = anime.title.english || anime.title.romaji;
                const eps = anime.episodes || '?'; 
                container.innerHTML += `
                    <div class="flex gap-4 items-center border-b border-gray-800 pb-4 cursor-pointer hover:bg-gray-800/40 transition p-2 rounded-lg" onclick="openDetails(${anime.id})">
                        <img src="${anime.coverImage.large}" alt="${title}" class="w-16 h-24 object-cover rounded shadow-md">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-100 mb-2 truncate">${title}</h3>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="cc-badge px-2 py-0.5 rounded flex items-center gap-1 font-bold"><span class="text-[10px] font-black border border-black rounded-[2px] px-0.5 leading-none">CC</span> ${eps}</span>
                                <span class="text-gray-400 font-medium">• ${anime.format || 'TV'}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        async function fetchLatestEpisodes() {
            const query = `query { Page(perPage: 8) { media(sort: UPDATED_AT_DESC, type: ANIME, status: RELEASING, isAdult: false) { id title { english romaji } coverImage { large } format episodes duration } } }`;
            const data = await queryAnilist(query);
            renderLatestEpisodes(data.data.Page.media);
        }

        function renderLatestEpisodes(mediaList) {
            const container = document.getElementById('latest-episodes-grid');
            container.innerHTML = '';
            mediaList.forEach(anime => {
                const title = anime.title.english || anime.title.romaji;
                const eps = anime.episodes || '?';
                const duration = anime.duration ? `${anime.duration}m` : '24m';
                container.innerHTML += `
                    <div class="cursor-pointer group" onclick="openDetails(${anime.id})">
                        <div class="relative aspect-[3/4] overflow-hidden rounded-lg mb-2 shadow-lg">
                            <img src="${anime.coverImage.large}" alt="${title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute bottom-2 left-2 flex gap-1.5 text-xs z-10">
                                <span class="cc-badge px-1.5 py-0.5 rounded flex items-center gap-1 font-bold shadow-sm"><span class="text-[9px] font-black border border-black rounded-[2px] px-0.5 leading-none">CC</span> ${eps}</span>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent pointer-events-none"></div>
                        </div>
                        <h3 class="font-bold text-gray-200 text-sm truncate">${title}</h3>
                        <p class="text-gray-500 text-xs mt-1 font-medium">${anime.format || 'TV'} • ${duration}</p>
                    </div>
                `;
            });
        }

        async function openDetails(animeId) {
            const modal = document.getElementById('anime-modal');
            const content = document.getElementById('modal-content');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            content.innerHTML = '<div class="p-12 text-center text-[#00f3ff] font-bold">Loading Data...</div>';

            const query = `
            query ($id: Int) {
                Media(id: $id, type: ANIME) {
                    id title { english romaji } coverImage { extraLarge } bannerImage description episodes status seasonYear averageScore
                    studios(isMain: true) { nodes { name } }
                    characters(sort: ROLE, perPage: 3) { edges { node { name { full } } voiceActors(language: JAPANESE) { name { full } } } }
                }
            }`;

            try {
                const data = await queryAnilist(query, { id: animeId });
                const d = data.data.Media;
                const title = d.title.english || d.title.romaji;
                const banner = d.bannerImage || d.coverImage.extraLarge;
                const studio = d.studios.nodes.length > 0 ? d.studios.nodes[0].name : 'Unknown';
                let actorsHtml = d.characters.edges.map(c => {
                    let va = c.voiceActors.length > 0 ? c.voiceActors[0].name.full : 'Unknown VA';
                    return `${c.node.name.full} (<i>${va}</i>)`;
                }).join('<br>');

                content.innerHTML = `
                    <div class="h-64 relative">
                        <img src="${banner}" class="w-full h-full object-cover opacity-60">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#111] to-transparent"></div>
                        <img src="${d.coverImage.extraLarge}" class="absolute bottom-[-40px] left-8 w-32 h-48 rounded shadow-2xl border-2 border-gray-800 object-cover">
                    </div>
                    <div class="px-8 pb-8 pt-12 relative">
                        <h2 class="text-[#00f3ff] text-2xl font-bold drop-shadow-md mb-1">${title}</h2>
                        <p class="text-gray-400 text-sm mb-4">${d.title.romaji}</p>
                        <div class="flex gap-2 mb-6 flex-wrap">
                            <span class="badge bg-[#00f3ff] text-black font-bold">Score: ${d.averageScore || 'N/A'}%</span>
                            <span class="badge">Episodes: ${d.episodes || '?'}</span>
                            <span class="badge">Year: ${d.seasonYear || 'N/A'}</span>
                            <span class="badge">${d.status}</span>
                        </div>
                        <div class="bg-gray-900 p-4 rounded-lg text-sm text-gray-300 leading-relaxed border-l-4 border-[#00f3ff] mb-6 max-h-40 overflow-y-auto">
                            <strong>OVERVIEW:</strong><br>${d.description || 'No description available.'}
                        </div>
                        <h4 class="border-b border-gray-800 pb-2 mb-2 font-bold text-gray-200">TECHNICAL DATA</h4>
                        <table class="meta-table">
                            <tr><td class="label">Main Studio</td><td>${studio}</td></tr>
                            <tr><td class="label">Key Characters</td><td class="text-sm">${actorsHtml || 'N/A'}</td></tr>
                            <tr><td class="label">AniList ID</td><td>${d.id}</td></tr>
                        </table>
                        <button onclick="window.location.href='pages/watch.php?id=${d.id}&ep=1'\" class=\"w-full py-4 mt-6 bg-[#00f3ff] text-black rounded-lg font-bold text-lg hover:bg-cyan-400 transition-all\">
                            ▶ WATCH NOW
                        </button>
                    </div>
                `;
            } catch (err) {
                content.innerHTML = '<div class="p-12 text-center text-red-500">Failed to load AniList data.</div>';
            }
        }

        function closeModal() {
            document.getElementById('anime-modal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    </script>
</body>
</html>