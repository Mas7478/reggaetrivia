<?php

header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "app" => "Reggae Trivia API",
    "endpoints" => [
        "GET  /api/game.php?keyword=..."‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ => "Ambil soal trivia dari YouTube Music API",
        "GET  /api/player.php?id=..."‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ => "Ambil satu / semua player",
        "POST /api/player.php" ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎  ‎ ‎ ‎ ‎ ‎ ‎ ‎=> "Buat player baru {nama}",
        "PUT  /api/player.php" ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎  ‎ ‎ ‎ ‎ ‎ ‎ ‎=> "Update nama player {id, nama}",
        "DELETE /api/player.php?id=..." ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎=> "Hapus player",
        "GET  /api/leaderboard.php" ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎ ‎  ‎ ‎=> "Ambil leaderboard",
        "POST /api/leaderboard.php"             => "Simpan skor {player_id, skor, total_soal, benar}",
        "GET  /api/favorites.php?player_id=..." => "Ambil playlist favorit player",
        "POST /api/favorites.php"               => "Simpan lagu ke favorit {player_id, youtube_id, judul, artis, thumbnail}",
        "PUT  /api/favorites.php"               => "Update judul/artis favorit {id, judul, artis}",
        "DELETE /api/favorites.php?id=..."      => "Hapus lagu favorit"
    ]
]);
