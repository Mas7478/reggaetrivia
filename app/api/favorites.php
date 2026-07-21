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
                created_at
            FROM favorite_songs
            WHERE player_id = $player_id
            ORDER BY created_at DESC
        ");

        $data = [];

        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }

        response(true, "Playlist favorit berhasil diambil.", $data);

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

        // Cek duplikat
        $cek = mysqli_query(
            $conn,
            "SELECT id
             FROM favorite_songs
             WHERE player_id=$player_id
             AND youtube_id='$youtube_id'"
        );

        if (mysqli_num_rows($cek) > 0) {
            response(false, "Lagu sudah ada di favorit.");
        }

        $insert = mysqli_query(
            $conn,
            "INSERT INTO favorite_songs
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

        if ($insert) {
            response(true, "Lagu berhasil disimpan.", [
                "id" => mysqli_insert_id($conn)
            ]);
        } else {
            response(false, mysqli_error($conn));
        }

        break;

    case "PUT":

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            response(false, "Data tidak valid.");
        }

        $id = intval($input["id"] ?? 0);
        $judul = mysqli_real_escape_string($conn, trim($input["judul"] ?? ""));
        $artis = mysqli_real_escape_string($conn, trim($input["artis"] ?? ""));

        if ($id <= 0 || $judul == "" || $artis == "") {
            response(false, "Data tidak lengkap.");
        }

        $update = mysqli_query(
            $conn,
            "UPDATE favorite_songs
             SET judul='$judul', artis='$artis'
             WHERE id=$id"
        );

        if ($update) {
            response(true, "Lagu favorit berhasil diperbarui.");
        } else {
            response(false, mysqli_error($conn));
        }

        break;

    case "DELETE":

        $id = isset($_GET["id"])
            ? intval($_GET["id"])
            : 0;

        if ($id <= 0) {
            response(false, "ID tidak valid.");
        }

        $delete = mysqli_query(
            $conn,
            "DELETE FROM favorite_songs
             WHERE id=$id"
        );

        if ($delete) {
            response(true, "Lagu berhasil dihapus.");
        } else {
            response(false, mysqli_error($conn));
        }

        break;

    default:

        response(false, "Method tidak didukung.");
}
