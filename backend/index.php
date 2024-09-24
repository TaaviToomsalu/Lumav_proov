<?php
// index.php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *"); // Luba kõik domeenid

// Kontrolli API võtme olemasolu
require 'config.php';
if (!isset($_GET['api_key']) || $_GET['api_key'] !== API_KEY) {
    echo json_encode(["error" => "Invalid API key"]);
    exit;
}

// Funktsioon URL-i crawlinguks
function crawl($url) {
    $html = file_get_contents($url);
    // Siin toimub andmete töötlemine ja HTML struktuuri parseerimine
    // Näiteks: leia tootekategooriad, hinnad jne
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    
    $categories = []; // Siin saad lisada andmete kogumise loogika
    $xpath = new DOMXPath($dom);
    
    // Näide: leia kõik kategooriate pealkirjad, mis on h1 elemendid
    foreach ($xpath->query('//h1') as $element) {
        $categories[] = trim($element->textContent);
    }
    
    return $categories;
}

// Loo API vastus
$urls = file('urls.txt', FILE_IGNORE_NEW_LINES);
$results = [];

foreach ($urls as $url) {
    $results[] = [
        "url" => $url,
        "categories" => crawl($url)
    ];
}

// Tagasta tulemused JSON formaadis
echo json_encode($results);
