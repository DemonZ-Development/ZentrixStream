<?php
// pages/character.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/_config.php');

$characterId = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$query = '
query ($id: Int) {
    Character(id: $id) {
        name { full native }
        image { large }
        description
        media(type: ANIME, sort: POPULARITY_DESC) {
            nodes {
                id
                title { english romaji }
                coverImage { large }
                format
            }
        }
    }
}';

$response = fetchAniList($query, ['id' => (int)$characterId]);
$character = $response['data']['Character'] ?? null;

if (!$character) {
    die("Character not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($character['name']['full']) ?> - <?= $websiteTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background-color: #0a0a0a; color: #fff; }</style>
</head>
<body class="flex flex-col min-h-screen">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/src/component/header.php'; ?>

    <main class="container mx-auto p-6 flex gap-8 flex-col md:flex-row">
        
        <div class="w-full md:w-64 flex-shrink-0 text-center">
            <img src="<?= $character['image']['large'] ?>" class="w-full rounded-lg shadow-2xl border-2 border-[#00f3ff]">
            <h1 class="text-2xl font-bold mt-4 text-[#00f3ff]"><?= htmlspecialchars($character['name']['full']) ?></h1>
            <p class="text-gray-400"><?= htmlspecialchars($character['name']['native']) ?></p>
        </div>

        <div class="flex-1">
            <div class="bg-gray-900 p-6 rounded-lg text-sm text-gray-300 leading-relaxed mb-8 max-h-96 overflow-y-auto custom-scrollbar">
                <?= nl2br(strip_tags($character['description'] ?? 'No description available.')) ?>
            </div>

            <h2 class="text-xl font-bold mb-4 border-b border-gray-800 pb-2">Animeography</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <?php foreach ($character['media']['nodes'] as $anime): ?>
                    <?php $title = $anime['title']['english'] ?? $anime['title']['romaji']; ?>
                    <a href="/details/<?= $anime['id'] ?>" class="bg-gray-800 rounded overflow-hidden hover:ring-2 ring-[#00f3ff] transition block">
                        <div class="aspect-[2/3] overflow-hidden">
                            <img src="<?= $anime['coverImage']['large'] ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="p-2 text-center">
                            <p class="text-xs font-bold truncate"><?= htmlspecialchars($title) ?></p>
                            <p class="text-[10px] text-gray-400"><?= $anime['format'] ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>
