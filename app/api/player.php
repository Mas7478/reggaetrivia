<?php

require_once "config.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        if (isset($_GET["id"])) {

            $id = intval($_GET["id"]);

            $query = mysqli_query(
                $conn,
                "SELECT
                    id,
                    nama,
                    xp,
                    level,
                    correct,
                    wrong,
                    created_at
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
                "SELECT
                    id,
                    nama,
                    xp,
                    level,
                    correct,
                    wrong,
                    created_at
                FROM players
                ORDER BY level DESC,
                         xp DESC,
                         nama ASC"
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

        $nama = mysqli_real_escape_string(
            $conn,
            trim($input["nama"] ?? "")
        );

        if ($nama == "") {
            response(false, "Nama wajib diisi.");
        }

        // Cek apakah player sudah ada
        $cek = mysqli_query(
            $conn,
            "SELECT
                id,
                nama,
                xp,
                level,
                correct,
                wrong,
                created_at
            FROM players
            WHERE nama='$nama'
            LIMIT 1"
        );

        if (!$cek) {
            response(false, mysqli_error($conn));
        }

        if (mysqli_num_rows($cek) > 0) {

            response(
                true,
                "Player sudah ada.",
                mysqli_fetch_assoc($cek)
            );
        }

        $insert = mysqli_query(
            $conn,
            "INSERT INTO players
            (
                nama,
                xp,
                level,
                correct,
                wrong
            )
            VALUES
            (
                '$nama',
                0,
                1,
                0,
                0
            )"
        );

        if (!$insert) {
            response(false, mysqli_error($conn));
        }

        $id = mysqli_insert_id($conn);

        $query = mysqli_query(
            $conn,
            "SELECT
                id,
                nama,
                xp,
                level,
                correct,
                wrong,
                created_at
            FROM players
            WHERE id=$id
            LIMIT 1"
        );

        response(
            true,
            "Player berhasil dibuat.",
            mysqli_fetch_assoc($query)
        );

        break;

    case "PUT":

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            response(false, "Data tidak valid.");
        }

        $id = intval($input["id"] ?? 0);

        $nama = mysqli_real_escape_string(
            $conn,
            trim($input["nama"] ?? "")
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
