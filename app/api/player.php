<?php

require_once "config.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        if (isset($_GET["id"])) {

            $id = intval($_GET["id"]);

            $query = mysqli_query(
                $conn,
                "SELECT *
                 FROM players
                 WHERE id=$id
                 LIMIT 1"
            );

            if (mysqli_num_rows($query) == 0) {
                response(false, "Player tidak ditemukan.");
            }

            response(
                true,
                "Data player.",
                mysqli_fetch_assoc($query)
            );

        } else {

            $query = mysqli_query(
                $conn,
                "SELECT *
                 FROM players
                 ORDER BY id DESC"
            );

            $data = [];

            while ($row = mysqli_fetch_assoc($query)) {
                $data[] = $row;
            }

            response(true, "Daftar player.", $data);
        }

        break;

    case "POST":

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            response(false, "Data tidak valid.");
        }

        $nama = mysqli_real_escape_string(
            $conn,
            $nama = trim($input["nama"] ?? "");
            $nama = ucwords(strtolower($nama));
        );

        if ($nama == "") {
            response(false, "Nama wajib diisi.");
        }

        $insert = mysqli_query(
            $conn,
            "INSERT INTO players(nama)
             VALUES('$nama')"
        );

        if (!$insert) {
            response(false, mysqli_error($conn));
        }

        response(true, "Player berhasil dibuat.", [
            "player_id" => mysqli_insert_id($conn)
        ]);

        break;

    case "PUT":

        $input = json_decode(file_get_contents("php://input"), true);

        $id = intval($input["id"] ?? 0);

        $nama = mysqli_real_escape_string(
            $conn,
            $nama = trim($input["nama"] ?? "");
        );

        if ($id <= 0 || $nama == "") {
            response(false, "Data tidak lengkap.");
        }

        $update = mysqli_query(
            $conn,
            "UPDATE players
             SET nama='$nama'
             WHERE id=$id"
        );

        if (!$update) {
            response(false, mysqli_error($conn));
        }

        response(true, "Player berhasil diperbarui.");

        break;

    case "DELETE":

        $id = isset($_GET["id"])
            ? intval($_GET["id"])
            : 0;

        if ($id <= 0) {
            response(false, "ID tidak valid.");
        }

       // Hapus leaderboard
mysqli_query(
    $conn,
    "DELETE FROM leaderboard
    WHERE player_id=$id"
);

// Hapus history
mysqli_query(
    $conn,
    "DELETE FROM song_history
    WHERE player_id=$id"
);

// Hapus player
$delete = mysqli_query(
    $conn,
    "DELETE FROM players
    WHERE id=$id"
);

if (!$delete) {
    response(false, mysqli_error($conn));
}

response(true, "Player berhasil dihapus.");
        break;

    default:

        response(false, "Method tidak didukung.");
}
