<?php
require_once '../config.php';
header('Content-Type: application/json');

// Gestione delle richieste GET (recupero commenti)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $track_id = isset($_GET['track_id']) ? (int)$_GET['track_id'] : 0;
    
    if ($track_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID traccia non valido']);
        exit;
    }
    
    $sql = "SELECT * FROM comments WHERE track_id = ? ORDER BY created_at DESC";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $track_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = [
            'id' => $row['id'],
            'user_name' => $row['user_name'],
            'comment' => $row['comment'],
            'created_at' => date('d/m/Y H:i', strtotime($row['created_at']))
        ];
    }
    
    echo json_encode(['success' => true, 'comments' => $comments]);
    exit;
}

// Gestione delle richieste POST (aggiunta commento)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['track_id']) || !isset($data['comment']) || !isset($data['user_name'])) {
        echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
        exit;
    }
    
    $track_id = (int)$data['track_id'];
    $comment = trim($data['comment']);
    $user_name = trim($data['user_name']);
    
    if ($track_id <= 0 || empty($comment) || empty($user_name)) {
        echo json_encode(['success' => false, 'message' => 'Dati non validi']);
        exit;
    }
    
    $sql = "INSERT INTO comments (track_id, user_name, comment) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $track_id, $user_name, $comment);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio del commento']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Metodo non supportato']); 