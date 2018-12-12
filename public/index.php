<?php

// ini_set("display_errors", 1);

use Firebase\JWT\JWT;
use Slim\Middleware\JwtAuthentication;

require __DIR__ . "/../vendor/autoload.php";

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$secret = "secret123";
$allowed_algs = ["HS256", "HS384"];

$app->add(new JwtAuthentication([
    'secret' => $secret,

    'relaxed' => ["dev.jwt.com"],
    'path' => ["/jwt"],
    'passthrough' => ["/jwt/login", "/jwt/test"],

    'error' => function($request, $response, $args) {
        $json = [
            'status' => "error",
            'message' => $args['message']
        ];

        return $response
                ->withHeader('Content-Type', "application/json")
                ->withJson($json);
    },

    'algorithm' => $allowed_algs

    // optional parameters
    // 'path' => "",
    // 'passthrough' => "",
    // 'header' => "X-Token",
    // 'regexp' => "",
    // 'algorithm' => [],
    // 'attribute' => "jwt",
    // 'logger' => "",
    // 'error' => function
    // 'secure' => boolean
    // 'relaxed' => []
]));

$app->group('/jwt', function() {
    $this->get('/login', function($request, $response) {
        $secret = "secret123";
        $allowed_algs = ["HS256", "HS384"];
        $pick_alg = $allowed_algs[0];

        $iat = time();

        $payload = [
            'iat' => $iat,
            'exp' => $iat + 10,
            'data' => [
                'name' => "Rodrigo Galura Jr"
            ]
        ];

        $token = JWT::encode($payload, $secret, $pick_alg);

        return $response->withJson($token);
    });

    $this->get('/test', function($request, $response) {
        $secret = "secret123";
        $allowed_algs = ["HS256", "HS384"];

        try {
            $payload = JWT::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDQ1OTA4MDAsImV4cCI6MTU0NDU5MDgxMCwiZGF0YSI6eyJuYW1lIjoiUm9kcmlnbyBHYWx1cmEgSnIifX0.gAPO1-mHEJsIJrePg_p97prYkkZIk7lGbTiXsklBXM0", $secret, $allowed_algs);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    });
});

$app->run();