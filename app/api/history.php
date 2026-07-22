<?php

require_once "config.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        $player_id = isset($_GET["player_id"])
            ? intval($_GET["player_id"])
            : 0;

        if ($player_id <= 0) {
            response(false, "Player tidak valid.");
        }

        $query = mysqli_query($conn, "
            SELECT
                id,
                youtube_id,
                judul,
                artis,
                thumbnail,
                shown_at
            FROM song_history
            WHERE player_id = $player_id
            ORDER BY shown_at DESC
            LIMIT 100
        ");

        if ($query === false) {
            response(false, "Database error: " . mysqli_error($conn));
        }

        $data = [];

        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }

        response(true, "Histori soal berhasil diambil.", $data);

        break;

    case "POST":

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            response(false, "Data tidak valid.");
        }

        $player_id = intval($input["player_id"] ?? 0);
        $youtube_id = mysqli_real_escape_string($conn, trim($input["youtube_id"] ?? ""));
        $judul = mysqli_real_escape_string($conn, trim($input["judul"] ?? ""));
        $artis = mysqli_real_escape_string($conn, trim($input["artis"] ?? ""));
        $thumbnail = mysqli_real_escape_string($conn, trim($input["thumbnail"] ?? ""));

        if (
            $player_id <= 0 ||
            $youtube_id == "" ||
            $judul == "" ||
            $artis == ""
        ) {
            response(false, "Data belum lengkap.");
        }

        $cek = mysqli_query(
    $conn,
    "SELECT id
    FROM song_history
    WHERE
        player_id=$player_id
    AND
        youtube_id='$youtube_id'
    LIMIT 1"
);

if (mysqli_num_rows($cek) > 0) {

    $row = mysqli_fetch_assoc($cek);

    $historyId = $row["id"];

    $update = mysqli_query(
        $conn,
        "UPDATE song_history
        SET
            shown_at=NOW()
        WHERE id=$historyId"
    );

    if (!$update) {
        response(false, mysqli_error($conn));
    }

    response(true, "History diperbarui.");

} else {

    $insert = mysqli_query(
        $conn,
        "INSERT INTO song_history
        (
            player_id,
            youtube_id,
            judul,
            artis,
            thumbnail
        )
        VALUES
        (
            $player_id,
            '$youtube_id',
            '$judul',
            '$artis',
            '$thumbnail'
        )"
    );

    if (!$insert) {
        response(false, mysqli_error($conn));
    }

    response(true, "History berhasil disimpan.", []).
}i soal disimpan.", [
            "id" => mysqli_insert_id($conn)
        ]);

        break;

    default:

        response(false, "Method tidak didukung.");
}
