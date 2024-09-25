<?php
// index.php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// API võtme kontroll
require 'config.php';
if (!isset($_GET['api_key']) || $_GET['api_key'] !== API_KEY) {
    echo json_encode(["error" => "Invalid API key"]);
    exit;
}

// Funktsioon HTML sisu hankimiseks ja kategorite välja tõmbamiseks
function crawl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $html = curl_exec($ch);
    if (curl_errno($ch)) {
        return false;
    }
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $categories = []; 
    foreach ($xpath->query("//li[contains(@class, 'cat-list__item')]") as $item) {
        $aElement = $xpath->query('.//a[contains(@class, "menu-click")]', $item)->item(0);
        if ($aElement) {
            $categoryName = trim($aElement->childNodes->item(0)->textContent);
            $countElement = $xpath->query('.//span[contains(@class, "count")]', $aElement)->item(0);
            if ($countElement) {
                preg_match('/\((\d+)\)/', $countElement->textContent, $matches);
                $countValue = $matches[1] ?? 0;
                $categories[] = [
                    'category' => $categoryName,
                    'count' => intval($countValue)
                ];
            }
        }
    }
    return $categories;
}

// API vastus
$urls = file('urls.txt', FILE_IGNORE_NEW_LINES);
$results = [];

foreach ($urls as $url) {
    $crawledData = crawl($url);
    $results[] = $crawledData !== false 
        ? ["url" => $url, "categories" => $crawledData] 
        : ["url" => $url, "error" => "Data collection failed"];
}

echo json_encode($results);
