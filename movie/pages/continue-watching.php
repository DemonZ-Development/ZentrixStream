<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - ZENTRIX</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b0b0b] text-white p-4">

    <h1 class="text-xl font-bold mb-6 text-red-600">CONTINUE WATCHING</h1>

    <div id="list" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>

    <div id="empty" class="hidden text-center py-20 text-gray-600">
        <p>No history found on this device.</p>
    </div>

    <script>
        const list = document.getElementById('list');
        const empty = document.getElementById('empty');
        const history = JSON.parse(localStorage.getItem('zentrix_history') || "[]");

        if (history.length === 0) {
            empty.classList.remove('hidden');
        } else {
            history.forEach(item => {
                const card = document.createElement('div');
                card.className = "bg-[#111] border border-gray-800 rounded-md p-3 active:scale-95 transition";
                
                const typeLabel = item.type === 'tv' ? `S${item.s} E${item.e}` : 'MOVIE';
                
                card.innerHTML = `
                    <a href="watch.php?id=${item.id}&type=${item.type}&season=${item.s}&episode=${item.e}">
                        <div class="text-[10px] text-gray-500 font-bold mb-1 uppercase tracking-tighter">ID: ${item.id}</div>
                        <div class="text-xs font-bold text-white truncate uppercase">Resume Play</div>
                        <div class="text-[10px] text-red-600 font-black mt-2 inline-block bg-red-600/10 px-2 py-0.5 rounded">${typeLabel}</div>
                    </a>
                `;
                list.appendChild(card);
            });
        }
    </script>
</body>
</html>