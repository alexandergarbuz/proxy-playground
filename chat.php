<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$api_key = 'sk-XXXXXXXXXXXXXXXXXXXXXXXX'; // make sure this is valid

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

if (!$prompt) {
    echo json_encode(['reply' => 'No prompt provided.']);
    exit;
}

// Log input for debugging
file_put_contents("log.txt", print_r($input, true), FILE_APPEND);

$data = [
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => $prompt]
    ],
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(['reply' => "cURL error: $error"]);
    exit;
}

$json = json_decode($response, true);
$reply = $json['choices'][0]['message']['content'] ?? 'No response from API';

echo json_encode(['reply' => $reply]);
