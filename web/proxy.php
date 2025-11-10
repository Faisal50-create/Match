<?php
ini_set('default_charset', 'UTF-8');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$url = "https://netspor.co/script2.js";

$cacheFile = __DIR__ . '/cache.json';
$cacheTTL = 120;

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTTL)) {
    readfile($cacheFile);
    exit;
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => "Mozilla/5.0",
]);
$data = curl_exec($ch);
curl_close($ch);

if (!$data) {
    echo json_encode(["error" => "Unable to fetch data"]);
    exit;
}

if (preg_match('/const\s+karsilasmalar\s*=\s*(\[.*?\]);/s', $data, $match)) {
    $json = trim($match[1]);
    $json = preg_replace('/;+\s*$/', '', $json);
    $json = preg_replace('/\/\/.*$/m', '', $json);

    json_decode($json);
    if (json_last_error() === JSON_ERROR_NONE) {
        file_put_contents($cacheFile, $json);
        echo $json;
    } else {
        echo json_encode(["error" => "Invalid JSON"]);
    }
} else {
    echo json_encode(["error" => "No match data found"]);
}
?>