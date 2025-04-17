<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/Track.php';

$track = new Track($db);

// Gestione delle richieste GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $result = $track->getTrack($_GET['id']);
        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Traccia non trovata']);
        }
    } else {
        $result = $track->getAllTracks();
        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Errore nel recupero delle tracce']);
        }
    }
}

// Gestione delle richieste POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action']) && $data['action'] === 'increment_plays' && isset($data['track_id'])) {
        $result = $track->incrementPlays($data['track_id']);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Errore nell\'incremento delle riproduzioni']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Parametri non validi']);
    }
}

// Gestione delle richieste non consentite
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
} 