<?php
ini_set('default_charset', 'UTF-8');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$url = "https://netspor.co/script2.js";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36",
    "Referer: https://netspor.co/",
    "Accept: */*"
]);

$data = curl_exec($ch);
curl_close($ch);

if (!$data) {
    echo json_encode(["error" => "Unable to fetch data"]);
    exit;
}

// extract clean JSON array from the JS file
if (preg_match('/const\s+karsilasmalar\s*=\s*(\[.*?\]);/s', $data, $match)) {
    $json = trim($match[1]);

    // remove any trailing semicolon or comments
    $json = preg_replace('/;+\s*$/', '', $json);
    $json = preg_replace('/\/\/.*$/m', '', $json);

    // validate JSON
    json_decode($json);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $json;
    } else {
        echo json_encode(["error" => "Invalid JSON structure"]);
    }
} else {
    echo json_encode(["error" => "No match data found"]);
}
?>
