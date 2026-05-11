<?php
// ========== RAHASIA: TOKEN BOT & CHAT ID (hanya di sini) ==========
define('BOT_TOKEN', '8259372749:AAG0TE16EgDQtk93g0OuSwBxVeYNKsTsAKQ');
define('CHAT_ID', '8405767170');
// ==================================================================

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'send_text') {
    $text = $input['text'] ?? '';
    
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?" . http_build_query([
        'chat_id' => CHAT_ID,
        'text' => $text,
        'disable_web_page_preview' => true
    ]);
    
    file_get_contents($url);
    echo json_encode(['status' => 'ok']);
    
} elseif ($action === 'send_photo') {
    $photoBase64 = $input['photo'] ?? '';
    $caption = $input['caption'] ?? '📸 Photo';
    
    $photoData = base64_decode($photoBase64);
    $tmpFile = tempnam(sys_get_temp_dir(), 'photo_') . '.jpg';
    file_put_contents($tmpFile, $photoData);
    
    $ch = curl_init("https://api.telegram.org/bot" . BOT_TOKEN . "/sendPhoto");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => [
            'chat_id' => CHAT_ID,
            'photo' => new CURLFile($tmpFile, 'image/jpeg', 'photo.jpg'),
            'caption' => $caption
        ]
    ]);
    curl_exec($ch);
    curl_close($ch);
    unlink($tmpFile);
    
    echo json_encode(['status' => 'ok']);
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
}
