<?php
namespace App\Views;

class JsonView
{
    public static function render($data, int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }
}

// Explication : Cette classe nous évite de réécrire les header() et json_encode() partout. Elle standardise nos réponses.