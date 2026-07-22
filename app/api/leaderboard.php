<?php

require_once "config.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        $limit = intval($_GET["limit"] ?? 100);

        if ($limit <= 0) {
            $limit = 100;
        }

        $query = mysqli_query(
            $conn,
            "SELECT
                id,
                nama,
                xp,
                level,
                correct,
                wrong,
                (
                    correct + wrong
                ) AS total_game,

                CASE
                    WHEN (correct + wrong)=0
                    THEN 0
                    ELSE ROUND((correct/(correct+wrong))*100,2)
                END AS accuracy

            FROM players

            ORDER BY

                level DESC,
                xp DESC,
                correct DESC,
                wrong ASC,
                nama ASC

            LIMIT $limit"
        );

        if (!$query) {
            response(false, mysqli_error($conn));
        }

        $ranking = 1;
        $data = [];

        while ($row = mysqli_fetch_assoc($query)) {

            $row["rank"] = $ranking++;

            $data[] = $row;
        }

        response(
            true,
            "Leaderboard berhasil diambil.",
            $data
        );

        break;

    default:

        response(false, "Method tidak didukung.");
}
