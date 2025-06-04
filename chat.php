<?php
// /gpt/chat.php

header('Content-Type: application/json');

// ✅ Set your API key securely (DO NOT expose this to frontend)
$api_key = 'sk-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

if (!$prompt) {
    echo json_encode(['reply' => 'No prompt provided.']);
    exit;
}

// 🧠 Call ChatGPT via OpenAI API
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
curl_close($ch);

$json = json_decode($response, true);
$reply = $json['choices'][0]['message']['content'] ?? 'No response';

echo json_encode(['reply' => $reply]);
