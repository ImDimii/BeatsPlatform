<?php
require_once '../includes/auth.php';
require_once '../includes/Track.php';

// Verifica che l'utente sia admin
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Accesso non autorizzato']);
    exit;
}

// Verifica che sia stato fornito un ID valido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID non valido']);
    exit;
}

$track = new Track();
$trackData = $track->getTrack((int)$_GET['id']);

if ($trackData) {
    // Imposta l'header per indicare che la risposta è in JSON
    header('Content-Type: application/json');
    echo json_encode($trackData);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Traccia non trovata']);
}
?> 