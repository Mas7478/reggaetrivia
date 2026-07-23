<?php

header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "app" => "Reggae Trivia API",
    "version" => "1.0.0",
    "description" => "REST API untuk aplikasi Reggae Trivia berbasis Flutter.",

    "endpoints" => [

        "GET    /api/game.php?keyword=reggae"
            => "Generate soal trivia dari YouTube Music API",

        "GET    /api/player.php"
            => "Daftar semua player",

        "GET    /api/player.php?id=1"
            => "Detail player",

        "POST   /api/player.php"
            => "Tambah player",

        "PUT    /api/player.php"
            => "Edit player",

        "DELETE /api/player.php?id=1"
            => "Hapus player beserta leaderboard dan history",

        "GET    /api/leaderboard.php"
            => "Ambil leaderboard",

        "POST   /api/leaderboard.php"
            => "Simpan / update skor leaderboard",

        "GET    /api/history.php?player_id=1"
            => "Ambil history lagu",

        "POST   /api/history.php"
            => "Simpan history lagu"
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
