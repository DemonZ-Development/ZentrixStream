<?php
// pages/details.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/_config.php');

$animeId = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Fetch Anime Data from AniList
$query = '
query ($id: Int) {
    Media(id: $id, type: ANIME) {
        id title { english romaji native }
        coverImage { extraLarge } bannerImage
        description episodes status seasonYear averageScore
        genres
        studios(isMain: true) { nodes { name } }
        characters(sort: ROLE, perPage: 10) {
            edges {
                node { id name { full } image { large } }
                voiceActors(language: JAPANESE) { id name { full } image { large } }
            }
        }
    }
}';

$response = fetchAniList($query, ['id' => (int)$animeId]);
$animeData = $response['data']['Media'] ?? null;

if (!$animeData) {
    die("Anime not found on AniList.");
}

$title = $animeData['title']['english'] ?? $animeData['title']['romaji'];

// 2. Database Integration: Page Views
$pageID = "anime_" . $animeId;
if (!isset($_SESSION['viewed_pages'])) $_SESSION['viewed_pages'] = [];

if (!in_array($pageID, $_SESSION['viewed_pages'])) {
    $stmt = $conn->prepare("SELECT totalview FROM pageview WHERE pageID = ?");
    $stmt->bind_param("s", $pageID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO pageview (pageID, totalview, like_count, dislike_count, animeID) VALUES (?, 1, 1, 0, ?)");
        $insert->bind_param("ss", $pageID, $animeId);
        $insert->execute();
    } else {
        $update = $conn->prepare("UPDATE pageview SET totalview = totalview + 1 WHERE pageID = ?");
        $update->bind_param("s", $pageID);
        $update->execute();
    }
    $_SESSION['viewed_pages'][] = $pageID;
}

// 3. Database Integration: Check Watchlist Status
$isLoggedIn = isset($_SESSION['userID']);
$watchlistStatus = null;

if ($isLoggedIn) {
    // Note: Using anilist_id from your DB schema
    $stmt = $conn->prepare("SELECT type FROM watchlist WHERE user_id = ? AND anilist_id = ? LIMIT 1");
    $stmt->bind_param("ii", $_SESSION['userID'], $animeId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $watchlistStatus = (int)$row['type'];
    }
}

$watchlistLabels = [1 => 'Watching', 2 => 'On-Hold', 3 => 'Plan to Watch', 4 => 'Dropped', 5 => 'Completed'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($title) ?> - <?= $websiteTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background-color: #0a0a0a; color: #fff; }</style>
</head>
<body class="flex flex-col min-h-screen">

    <div class="relative h-96">
        <img src="<?= $animeData['bannerImage'] ?? $animeData['coverImage']['extraLarge'] ?>" class="w-full h-full object-cover opacity-40">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] to-transparent"></div>
    </div>

    <main class="container mx-auto p-6 -mt-32 relative z-10 flex gap-8 flex-col md:flex-row">
        <div class="w-full md:w-64 flex-shrink-0">
            <img src="<?= $animeData['coverImage']['extraLarge'] ?>" class="w-full rounded-lg shadow-2xl border border-gray-800">
            
            <a href="/watch/<?= $animeData['id'] ?>?ep=1" class="block w-full py-3 mt-4 text-center bg-[#00f3ff] text-black font-bold rounded hover:bg-cyan-400">
                ▶ Watch Now
            </a>

            <div class="mt-2 relative group cursor-pointer bg-gray-800 text-center py-3 rounded hover:bg-gray-700">
                <span class="font-bold text-[#00f3ff]">
                    <?= $watchlistStatus ? $watchlistLabels[$watchlistStatus] : '+ Add to List' ?>
                </span>
                <?php if ($isLoggedIn): ?>
                    <div class="hidden group-hover:block absolute top-full left-0 w-full bg-gray-900 border border-gray-700 rounded mt-1 z-20">
                        <?php foreach ($watchlistLabels as $id => $label): ?>
                            <div class="p-2 hover:bg-gray-800 text-sm" onclick="updateWatchlist(<?= $id ?>, <?= $animeId ?>)"><?= $label ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex-1">
            <h1 class="text-4xl font-bold text-[#00f3ff] mb-2"><?= htmlspecialchars($title) ?></h1>
            <p class="text-gray-400 mb-4"><?= htmlspecialchars($animeData['title']['native']) ?> • <?= $animeData['status'] ?></p>

            <div class="bg-gray-900 p-4 rounded border border-gray-800 mb-6 text-sm text-gray-300 leading-relaxed">
                <?= nl2br(strip_tags($animeData['description'])) ?>
            </div>

            <h2 class="text-xl font-bold mb-4 border-b border-gray-800 pb-2">Characters & Voice Actors</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($animeData['characters']['edges'] as $edge): ?>
                    <div class="flex items-center gap-4 bg-gray-900 p-2 rounded">
                        <a href="/character/<?= $edge['node']['id'] ?>" class="flex items-center gap-3 w-1/2">
                            <img src="<?= $edge['node']['image']['large'] ?>" class="w-12 h-12 rounded object-cover">
                            <span class="text-sm font-bold truncate text-[#00f3ff]"><?= htmlspecialchars($edge['node']['name']['full']) ?></span>
                        </a>
                        
                        <?php if (!empty($edge['voiceActors'])): ?>
                            <a href="/actors/<?= $edge['voiceActors'][0]['id'] ?>" class="flex items-center gap-3 w-1/2 justify-end text-right">
                                <span class="text-sm text-gray-400 truncate"><?= htmlspecialchars($edge['voiceActors'][0]['name']['full']) ?></span>
                                <img src="<?= $edge['voiceActors'][0]['image']['large'] ?>" class="w-12 h-12 rounded object-cover">
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>
