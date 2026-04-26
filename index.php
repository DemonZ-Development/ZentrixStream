<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZENTRIX STREAM | Ultimate Entertainment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0f0f0f; 
            overflow-x: hidden;
        }
        
        /* Custom Glowing Effects */
        .glow-text-cyan { text-shadow: 0 0 20px rgba(0, 243, 255, 0.6); }
        .glow-text-red { text-shadow: 0 0 20px rgba(255, 0, 60, 0.6); }
        
        /* Ambient Background Blobs */
        .ambient-blob {
            position: absolute;
            filter: blur(100px);
            z-index: 0;
            opacity: 0.4;
            animation: pulse-slow 8s infinite alternate;
        }
        
        @keyframes pulse-slow {
            0% { transform: scale(1) translate(0, 0); opacity: 0.3; }
            100% { transform: scale(1.1) translate(20px, 20px); opacity: 0.5; }
        }
    </style>
</head>
<body class="text-white min-h-screen relative selection:bg-[#00f3ff] selection:text-black">

    <div class="ambient-blob bg-[#00f3ff] w-96 h-96 rounded-full top-[-10%] left-[-10%]"></div>
    <div class="ambient-blob bg-[#ff003c] w-96 h-96 rounded-full bottom-[20%] right-[-10%]"></div>

    <main class="relative z-10 flex flex-col items-center justify-center min-h-[85vh] px-4 text-center">
        
        <div class="mb-12 animate-[fadeIn_1s_ease-out]">
            <h1 class="text-5xl md:text-8xl font-black tracking-tighter uppercase mb-4">
                <span class="text-white">ZENTRIX</span> 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f3ff] to-[#ff003c]">STREAM</span>
            </h1>
            <p class="text-gray-400 text-sm md:text-lg font-bold tracking-widest uppercase max-w-2xl mx-auto">
                The ultimate dual-portal streaming experience. Choose your universe.
            </p>
        </div>

        <div class="flex flex-col md:flex-row gap-6 w-full max-w-3xl px-4">
            
            <a href="anime/index.php" class="group relative flex-1 bg-[#111] border border-[#00f3ff]/30 p-8 rounded-2xl hover:border-[#00f3ff] transition-all duration-500 overflow-hidden hover:-translate-y-2 hover:shadow-[0_10px_30px_rgba(0,243,255,0.2)]">
                <div class="absolute inset-0 bg-gradient-to-b from-[#00f3ff]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10 flex flex-col items-center">
                    <svg class="w-16 h-16 text-[#00f3ff] mb-4 transform group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-3xl font-black text-white tracking-widest uppercase glow-text-cyan group-hover:text-[#00f3ff] transition-colors">Anime</h2>
                    <p class="text-xs text-gray-500 mt-2 font-bold tracking-widest uppercase">Enter the AniList Portal</p>
                </div>
            </a>

            <a href="movie/index.php" class="group relative flex-1 bg-[#111] border border-[#ff003c]/30 p-8 rounded-2xl hover:border-[#ff003c] transition-all duration-500 overflow-hidden hover:-translate-y-2 hover:shadow-[0_10px_30px_rgba(255,0,60,0.2)]">
                <div class="absolute inset-0 bg-gradient-to-b from-[#ff003c]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10 flex flex-col items-center">
                    <svg class="w-16 h-16 text-[#ff003c] mb-4 transform group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                    </svg>
                    <h2 class="text-3xl font-black text-white tracking-widest uppercase glow-text-red group-hover:text-[#ff003c] transition-colors">Movies</h2>
                    <p class="text-xs text-gray-500 mt-2 font-bold tracking-widest uppercase">Enter the TMDB Portal</p>
                </div>
            </a>

        </div>
    </main>

    <section class="relative z-10 bg-[#111]/80 backdrop-blur-md border-t border-white/5 py-20 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold tracking-widest uppercase mb-2">How It <span class="text-[#00f3ff]">Works</span></h3>
                <div class="w-24 h-1 bg-gradient-to-r from-[#00f3ff] to-[#ff003c] mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-[#0f0f0f] p-8 rounded-xl border border-white/5 text-center hover:border-white/20 transition-colors">
                    <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6 text-xl font-black text-white">1</div>
                    <h4 class="text-lg font-bold text-white uppercase tracking-wide mb-3">Choose Your Portal</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Zentrix is divided into two dedicated servers. Select the Anime portal for the latest Japanese and Donghua releases, or the Movie portal for global cinema and TV shows.
                    </p>
                </div>

                <div class="bg-[#0f0f0f] p-8 rounded-xl border border-white/5 text-center hover:border-white/20 transition-colors">
                    <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6 text-xl font-black text-white">2</div>
                    <h4 class="text-lg font-bold text-white uppercase tracking-wide mb-3">Seamless Streaming</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Powered by high-speed proxy servers and top-tier APIs, ensuring you get metadata, posters, and uninterrupted video playback directly to your device, heavily optimized for mobile.
                    </p>
                </div>

                <div class="bg-[#0f0f0f] p-8 rounded-xl border border-white/5 text-center hover:border-white/20 transition-colors">
                    <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6 text-xl font-black text-white">3</div>
                    <h4 class="text-lg font-bold text-white uppercase tracking-wide mb-3">Cloud Sync History</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Create an account to automatically save your progress. Your "Continue Watching" history is stored in our database, so you never lose your spot across any device.
                    </p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
