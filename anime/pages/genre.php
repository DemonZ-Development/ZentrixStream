<?php
// pages/genre.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/_config.php');

$category = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$genre = str_replace('-', ' ', ucfirst($category));
$page = max(1, (int)($_GET['page'] ?? 1));

// AniList GraphQL Query for Genres
$query = '
query ($genre: String, $page: Int) {
    Page(page: $page, perPage: 20) {
        pageInfo {
            total
            currentPage
            lastPage
            hasNextPage
        }
        media(genre: $genre, type: ANIME, isAdult: false, sort: TRENDING_DESC) {
            id
            title { english romaji }
            coverImage { extraLarge }
            episodes
            format
            duration
        }
    }
}';

$variables = [
    'genre' => $genre,
    'page' => $page
];

$response = fetchAniList($query, $variables);
$aniResults = $response['data']['Page']['media'] ?? [];
$pageInfo = $response['data']['Page']['pageInfo'] ?? ['currentPage' => 1, 'lastPage' => 1];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($genre) ?> Anime - <?= $websiteTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0f0f0f; color: #ffffff; }
        .neon-text { color: #00f3ff; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/src/component/header.php'; ?>

    <main class="container mx-auto p-6 flex-1">
        <h2 class="text-2xl font-bold mb-6 neon-text uppercase">Genre: <?= htmlspecialchars($genre) ?></h2>

        <?php if (!empty($aniResults)): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <?php foreach ($aniResults as $anime): ?>
                    <?php $title = $anime['title']['english'] ?? $anime['title']['romaji']; ?>
                    <div class="bg-gray-900 rounded-lg overflow-hidden relative group">
                        <a href="/details/<?= $anime['id'] ?>">
                            <div class="aspect-[2/3] overflow-hidden">
                                <img src="<?= $anime['coverImage']['extraLarge'] ?>" alt="<?= htmlspecialchars($title) ?>" class="w-full h-full object-cover group-hover:opacity-50 transition">
                            </div>
                            <div class="p-3">
                                <span class="text-xs font-bold text-[#00f3ff]"><?= $anime['format'] ?? 'TV' ?></span>
                                <h3 class="text-sm font-semibold truncate mt-1"><?= htmlspecialchars($title) ?></h3>
                                <p class="text-xs text-gray-500 mt-1"><?= $anime['episodes'] ?? '?' ?> EPS • <?= $anime['duration'] ?? '?' ?>m</p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-center mt-8 gap-2">
                <?php if ($pageInfo['currentPage'] > 1): ?>
                    <a href="?page=<?= $pageInfo['currentPage'] - 1 ?>" class="px-4 py-2 bg-gray-800 rounded hover:bg-[#00f3ff] hover:text-black">Prev</a>
                <?php endif; ?>
                
                <span class="px-4 py-2 bg-gray-900 rounded text-[#00f3ff]">Page <?= $pageInfo['currentPage'] ?> of <?= $pageInfo['lastPage'] ?></span>
                
                <?php if ($pageInfo['hasNextPage']): ?>
                    <a href="?page=<?= $pageInfo['currentPage'] + 1 ?>" class="px-4 py-2 bg-gray-800 rounded hover:bg-[#00f3ff] hover:text-black">Next</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-400">No anime found for this genre.</p>
        <?php endif; ?>
    </main>
</body>
</html>
