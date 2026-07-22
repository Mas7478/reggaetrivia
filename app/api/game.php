<?php

require_once "config.php";


$keyword = isset($_GET["keyword"]) && trim($_GET["keyword"]) != ""
    ? trim($_GET["keyword"])
    : "reggae";


$url = "https://m-yt-music-api.vercel.app/search/musics?query=" . urlencode($keyword);

$context = stream_context_create([
    "http" => [
        "timeout" => 10
    ]
]);

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    response(false, "Gagal mengambil data dari YouTube Music API.");
}


$json = json_decode($response, true);

if (
    !$json ||
    !isset($json["content"]) ||
    !is_array($json["content"])
) {
    response(false, "Data API tidak valid.");
}


$results = array_filter($json["content"], function ($item) {
    return isset($item["resultType"]) &&
        $item["resultType"] === "song";
});

$results = array_values($results);

if (count($results) < 4) {
    response(false, "Jumlah lagu tidak mencukupi.");
}


shuffle($results);


$selected = array_slice($results, 0, 4);


$correct = rand(0, 3);


$thumbnail = "";

if (
    isset($selected[$correct]["thumbnails"]) &&
    is_array($selected[$correct]["thumbnails"])
) {

    $maxWidth = 0;

    foreach ($selected[$correct]["thumbnails"] as $thumb) {

        $width = intval($thumb["width"] ?? 0);

        if ($width > $maxWidth) {

            $maxWidth = $width;

            $thumbnail = $thumb["url"] ?? "";
        }
    }
}


if ($thumbnail == "") {

    $thumbnail = "https://via.placeholder.com/512x512?text=Reggae";
}


$question = [

    "youtube_id" => $selected[$correct]["id"] ?? "",

    "thumbnail" => $thumbnail,

    "artist" => $selected[$correct]["artists"][0]["name"] ?? "Unknown Artist",

    "answer" => $selected[$correct]["title"] ?? ""

];


$options = [];

foreach ($selected as $song) {

    if (isset($song["title"])) {

        $options[] = $song["title"];
    }
}


$options = array_values(array_unique($options));

if (count($options) < 4) {
    response(false, "Pilihan jawaban tidak mencukupi.");
}

shuffle($options);


response(
    true,
    "Soal berhasil dibuat.",
    [
        "question" => $question,
        "options" => $options
    ]
);
