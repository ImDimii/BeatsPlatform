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
$user_name = trim($data['user_name']);

if ($track_id <= 0 || !in_array($reaction_type, ['like', 'dislike']) || empty($user_name)) {
    echo json_encode(['success' => false, 'message' => 'Dati non validi']);
    exit;
}

// Verifica se esiste già una reazione dell'utente per questa traccia
$check_sql = "SELECT id, reaction_type FROM reactions WHERE track_id = ? AND user_name = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "is", $track_id, $user_name);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);
$existing_reaction = mysqli_fetch_assoc($result);

if ($existing_reaction) {
    if ($existing_reaction['reaction_type'] === $reaction_type) {
        // Se la reazione è la stessa, la rimuoviamo
        $delete_sql = "DELETE FROM reactions WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($delete_stmt, "i", $existing_reaction['id']);
        $success = mysqli_stmt_execute($delete_stmt);
    } else {
        // Se la reazione è diversa, la aggiorniamo
        $update_sql = "UPDATE reactions SET reaction_type = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $reaction_type, $existing_reaction['id']);
        $success = mysqli_stmt_execute($update_stmt);
    }
} else {
    // Inseriamo una nuova reazione
    $insert_sql = "INSERT INTO reactions (track_id, user_name, reaction_type) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "iss", $track_id, $user_name, $reaction_type);
    $success = mysqli_stmt_execute($insert_stmt);
}

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio della reazione']);
} 