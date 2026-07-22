<?php

require_once "config.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        $query = mysqli_query(
            $conn,
            "SELECT
                leaderboard.id,
                players.nama,
                leaderboard.skor,
                leaderboard.total_soal,
                leaderboard.benar,
                leaderboard.waktu_main
            FROM leaderboard
            INNER JOIN players
                ON leaderboard.player_id = players.id
            ORDER BY
                leaderboard.skor DESC,
                leaderboard.benar DESC,
                leaderboard.waktu_main ASC
            LIMIT 100"
        );

        if (!$query) {
            response(false, mysqli_error($conn));
        }

        $data = [];

        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }

        response(true, "Leaderboard berhasil diambil.", $data);

        break;

    case "POST":

        $input = json_decode(file_get_contents("php://input"), true);

        if (!is_array($input)) {
            response(false, "Data tidak valid.");
        }

        $player_id = intval($input["player_id"] ?? 0);
        $skor = intval($input["skor"] ?? 0);
        $total = intval($input["total_soal"] ?? 0);
        $benar = intval($input["benar"] ?? 0);

        if ($player_id <= 0) {
            response(false, "Player tidak ditemukan.");
        }

        // Cek apakah player sudah ada di leaderboard
$cek = mysqli_query(
    $conn,
    "SELECT id
    FROM leaderboard
    WHERE player_id=$player_id
    LIMIT 1"
);

if (mysqli_num_rows($cek) > 0) {

    $old = mysqli_fetch_assoc($cek);

if ($skor > $old["skor"]) {

    mysqli_query(
        $conn,
        "UPDATE leaderboard
        SET
            skor=$skor,
            total_soal=$total,
            benar=$benar,
            waktu_main=NOW()
        WHERE player_id=$player_id"
    );

}

$update = mysqli_query(
    $conn,
    "UPDATE leaderboard
    SET
        skor=$skor,
        total_soal=$total,
        benar=$benar,
        waktu_main=NOW()
    WHERE player_id=$player_id"
);

if (!$update) {
    response(false, mysqli_error($conn));
}

} else {

    $insert = mysqli_query(
        $conn,
        "INSERT INTO leaderboard
        (
            player_id,
            skor,
            total_soal,
            benar
        )
        VALUES
        (
            $player_id,
            $skor,
            $total,
            $benar
        )"
    );

    if (!$insert) {
        response(false, mysqli_error($conn));
    }
}
    
    response(true, "Leaderboard berhasil diperbarui.");

        if (!$insert) {
            response(false, mysqli_error($conn));
        }

        response(true, "Skor berhasil disimpan.");

        break;

    default:

        response(false, "Method tidak didukung.");
}
