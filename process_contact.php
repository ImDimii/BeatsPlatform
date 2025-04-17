<?php
require_once 'includes/ContactMessage.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validazione
$errors = [];

if (empty($name)) {
    $errors['name'] = 'Il nome è obbligatorio';
}

if (empty($email)) {
    $errors['email'] = "L'email è obbligatoria";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "L'email non è valida";
}

if (empty($message)) {
    $errors['message'] = 'Il messaggio è obbligatorio';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$contactMessage = new ContactMessage();
$result = $contactMessage->create($name, $email, $message);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Messaggio inviato con successo']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Si è verificato un errore durante l'invio del messaggio"]);
} 