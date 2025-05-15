<?php
require_once '../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['track_id']) || !isset($data['reaction_type']) || !isset($data['user_name'])) {
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit;
}

$track_id = (int)$data['track_id'];
$reaction_type = $data['reaction_type'];
$user_name = 'guest_' . time() . '_' . rand(1000, 9999); // Genera un nome utente unico

if ($track_id <= 0 || !in_array($reaction_type, ['like', 'dislike'])) {
    echo json_encode(['success' => false, 'message' => 'Dati non validi']);
    exit;
}

// Inseriamo direttamente la nuova reazione
$insert_sql = "INSERT INTO reactions (track_id, user_name, reaction_type) VALUES (?, ?, ?)";
$insert_stmt = mysqli_prepare($conn, $insert_sql);
mysqli_stmt_bind_param($insert_stmt, "iss", $track_id, $user_name, $reaction_type);
$success = mysqli_stmt_execute($insert_stmt);

if ($success) {
    // Recupera il conteggio aggiornato di like e dislike
    $count_sql = "SELECT 
        COUNT(CASE WHEN reaction_type = 'like' THEN 1 END) as likes,
        COUNT(CASE WHEN reaction_type = 'dislike' THEN 1 END) as dislikes
        FROM reactions 
        WHERE track_id = ?";
    $count_stmt = mysqli_prepare($conn, $count_sql);
    mysqli_stmt_bind_param($count_stmt, "i", $track_id);
    mysqli_stmt_execute($count_stmt);
    $counts = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt));
    
    echo json_encode([
        'success' => true,
        'likes' => (int)$counts['likes'],
        'dislikes' => (int)$counts['dislikes']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio della reazione']);
} 
