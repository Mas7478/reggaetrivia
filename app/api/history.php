<?php

require_once "config.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        $player_id = intval($_GET["player_id"] ?? 0);

        if ($player_id <= 0) {
            response(false, "Player tidak valid.");
        }

        $query = mysqli_query(
            $conn,
            "SELECT
                id,
                youtube_id,
                judul,
                artis,
                thumbnail,
                play_count,
                last_played
            FROM song_history
            WHERE player_id=$player_id
            ORDER BY
                last_played DESC,
                play_count DESC"
        );

        if (!$query) {
            response(false, mysqli_error($conn));
        }

        $data = [];

        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }

        response(true, "History berhasil diambil.", $data);

        break;

    case "POST":

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            response(false, "Data tidak valid.");
        }

        $player_id = intval($input["player_id"] ?? 0);

        $youtube_id = mysqli_real_escape_string(
            $conn,
            trim($input["youtube_id"] ?? "")
        );

        $judul = mysqli_real_escape_string(
            $conn,
            trim($input["judul"] ?? "")
        );

        $artis = mysqli_real_escape_string(
            $conn,
            trim($input["artis"] ?? "")
        );

        $thumbnail = mysqli_real_escape_string(
            $conn,
            trim($input["thumbnail"] ?? "")
        );

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
            "SELECT
                id,
                play_count
            FROM song_history
            WHERE
                player_id=$player_id
            AND
                youtube_id='$youtube_id'
            LIMIT 1"
        );

        if (!$cek) {
            response(false, mysqli_error($conn));
        }

        if (mysqli_num_rows($cek) > 0) {

            $row = mysqli_fetch_assoc($cek);

            $history_id = intval($row["id"]);

            $update = mysqli_query(
                $conn,
                "UPDATE song_history
                SET
                    play_count = play_count + 1,
                    judul='$judul',
                    artis='$artis',
                    thumbnail='$thumbnail',
                    last_played=NOW()
                WHERE id=$history_id"
            );

            if (!$update) {
                response(false, mysqli_error($conn));
            }

            response(true, "History diperbarui.", [
                "history_id" => $history_id,
                "updated" => true
            ]);
        }

        $insert = mysqli_query(
            $conn,
            "INSERT INTO song_history
            (
                player_id,
                youtube_id,
                judul,
                artis,
                thumbnail,
                play_count,
                last_played
            )
            VALUES
            (
                $player_id,
                '$youtube_id',
                '$judul',
                '$artis',
                '$thumbnail',
                1,
                NOW()
            )"
        );

        if (!$insert) {
            response(false, mysqli_error($conn));
        }

        response(true, "History berhasil disimpan.", [
            "history_id" => mysqli_insert_id($conn),
            "updated" => false
        ]);

        break;

    case "DELETE":

        $id = intval($_GET["id"] ?? 0);

        if ($id <= 0) {
            response(false, "ID tidak valid.");
        }

        $delete = mysqli_query(
            $conn,
            "DELETE FROM song_history
            WHERE id=$id"
        );

        if (!$delete) {
            response(false, mysqli_error($conn));
        }

        response(true, "History berhasil dihapus.");

        break;

    default:

        response(false, "Method tidak didukung.");
}
