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

            if (!$query) {
                response(false, mysqli_error($conn));
            }

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
                ORDER BY id ASC"
            );

            if (!$query) {
                response(false, mysqli_error($conn));
            }

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

        $nama = trim($input["nama"] ?? "");
        $nama = ucwords(strtolower($nama));
        $nama = mysqli_real_escape_string($conn, $nama);

        if ($nama == "") {
            response(false, "Nama wajib diisi.");
        }

        // Cek nama player
        $cek = mysqli_query(
            $conn,
            "SELECT id
            FROM players
            WHERE LOWER(nama)=LOWER('$nama')
            LIMIT 1"
        );

        if (!$cek) {
            response(false, mysqli_error($conn));
        }

        if (mysqli_num_rows($cek) > 0) {
            response(false, "Nama player sudah digunakan.");
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

        if (!$input) {
            response(false, "Data tidak valid.");
        }

        $id = intval($input["id"] ?? 0);

        $nama = trim($input["nama"] ?? "");
        $nama = ucwords(strtolower($nama));
        $nama = mysqli_real_escape_string($conn, $nama);

        if ($id <= 0 || $nama == "") {
            response(false, "Data tidak lengkap.");
        }

        // Cek nama dipakai player lain
        $cek = mysqli_query(
            $conn,
            "SELECT id
            FROM players
            WHERE LOWER(nama)=LOWER('$nama')
            AND id<>$id
            LIMIT 1"
        );

        if (!$cek) {
            response(false, mysqli_error($conn));
        }

        if (mysqli_num_rows($cek) > 0) {
            response(false, "Nama player sudah digunakan.");
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

        mysqli_begin_transaction($conn);

        try {

            mysqli_query(
                $conn,
                "DELETE FROM leaderboard
                WHERE player_id=$id"
            );

            mysqli_query(
                $conn,
                "DELETE FROM song_history
                WHERE player_id=$id"
            );

            $delete = mysqli_query(
                $conn,
                "DELETE FROM players
                WHERE id=$id"
            );

            if (!$delete) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_commit($conn);

            response(true, "Player berhasil dihapus.");

        } catch (Exception $e) {

            mysqli_rollback($conn);

            response(false, $e->getMessage());
        }

        break;
    

    default:

        response(false, "Method tidak didukung.");
}
