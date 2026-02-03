<?php
header('Content-Type: application/json');
include "config.php"; // assumes $conn is defined here

// Get userid either from POST (Android) or SESSION (web)
$userid = $_POST['userid'] ?? ($_SESSION['userid'] ?? null);

$content     = $_POST['content'] ?? '';
$story_words = $_POST['story_words'] ?? 'No words';
$genre       = $_POST['genre'] ?? 'Unknown';
$tone        = $_POST['tone'] ?? 'Neutral';

// Validate userid
if (!$userid) {
    echo json_encode([
        "success" => false,
        "message" => "❌ User ID is required.",
        "story"   => null
    ]);
    exit;
}

// Prepare data for Python server
$data = [
    "name"  => $content,
    "genre" => $genre,
    "tone"  => $tone
];

$jsonData = json_encode($data);

// Python server URL
$url = 'http://127.0.0.1:8000';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
]);

$response = curl_exec($ch);
curl_close($ch);

// Handle curl error
if ($response === false) {
    echo json_encode([
        "success" => false,
        "message" => "❌ Failed to connect to the Python server.",
        "story"   => null
    ]);
    exit;
}

$responseData = json_decode($response, true);

// Validate Python response
if (!isset($responseData['reply'])) {
    echo json_encode([
        "success" => false,
        "message" => "❌ Invalid response from Python server.",
        "story"   => null
    ]);
    exit;
}

$reply  = $responseData['reply'];
$genre  = $responseData['genre'] ?? $genre;
$tone   = $responseData['tone'] ?? $tone;
$content = $responseData['content'] ?? $content;

// Save story to database
try {
    $stmt = $conn->prepare("INSERT INTO generate_story (userid, content, genre, story_words, tone) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("issss", $userid, $reply, $genre, $story_words, $tone);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();

    echo json_encode([
        "success" => true,
        "message" => "✅ Story generated successfully.",
        "story"   => $reply
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "❌ Database error: " . $e->getMessage(),
        "story"   => null
    ]);
}
