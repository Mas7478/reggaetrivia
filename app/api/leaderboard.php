<?php

require_once "config.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {


    case "GET":

        $query = mysqli_query(
            $conn,
            "SELECT
                leaderboard.id,
                leaderboard.player_id,
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

        if (!$input) {
            response(false, "Data tidak valid.");
        }

        $player_id = intval($input["player_id"] ?? 0);
        $skor      = intval($input["skor"] ?? 0);
        $total     = intval($input["total_soal"] ?? 0);
        $benar     = intval($input["benar"] ?? 0);

        if ($player_id <= 0) {
            response(false, "Player tidak valid.");
        }


        $player = mysqli_query(
            $conn,
            "SELECT id
            FROM players
            WHERE id=$player_id
            LIMIT 1"
        );

        if (!$player) {
            response(false, mysqli_error($conn));
        }

        if (mysqli_num_rows($player) == 0) {
            response(false, "Player tidak ditemukan.");
        }

    
        $cek = mysqli_query(
            $conn,
            "SELECT id, skor
            FROM leaderboard
            WHERE player_id=$player_id
            LIMIT 1"
        );

        if (!$cek) {
            response(false, mysqli_error($conn));
        }

        mysqli_begin_transaction($conn);

        try {

            if (mysqli_num_rows($cek) > 0) {

                $row = mysqli_fetch_assoc($cek);
            
                $leaderboardId = intval($row["id"]);
            
                $update = mysqli_query(
                    $conn,
                    "UPDATE leaderboard
                    SET
                        skor = GREATEST(0, skor + $skor),
                        total_soal = total_soal + $total,
                        benar = benar + $benar,
                        waktu_main = NOW()
                    WHERE id = $leaderboardId"
                );
            
                if (!$update) {
                    throw new Exception(mysqli_error($conn));
                }
            
                mysqli_commit($conn);
            
                response(true, "Leaderboard berhasil diperbarui.");
            }

    
            $insert = mysqli_query(
                $conn,
                "INSERT INTO leaderboard
                (
                    player_id,
                    skor,
                    total_soal,
                    benar,
                    waktu_main
                )
                VALUES
                (
                    $player_id,
                    $skor,
                    $total,
                    $benar,
                    NOW()
                )"
            );

            if (!$insert) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_commit($conn);

            response(true, "Leaderboard berhasil disimpan.");

        } catch (Exception $e) {

            mysqli_rollback($conn);

            response(false, $e->getMessage());
        }

        break;


    default:

        response(false, "Method tidak didukung.");
}
