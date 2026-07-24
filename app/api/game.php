<?php

require_once "config.php";


$keyword = isset($_GET["keyword"]) && trim($_GET["keyword"]) != ""
    ? trim($_GET["keyword"])
    : "reggae";


$url = "https://m-yt-music-api.vercel.app/search/musics?query=" . urlencode($keyword);

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "User-Agent: Mozilla/5.0 (compatible; ReggaeTriviaBackend/1.0)"
    ]
]);

$response = curl_exec($ch);
$curlErrno = curl_errno($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($response === false || $curlErrno !== 0) {
    response(false, "Gagal mengambil data dari YouTube Music API. (" . $curlError . ")");
}

if ($httpCode < 200 || $httpCode >= 300) {
    response(false, "YouTube Music API mengembalikan status " . $httpCode . ".");
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
