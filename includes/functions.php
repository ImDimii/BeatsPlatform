<?php
/**
 * Funzioni di utilità per l'applicazione Beats
 */

/**
 * Formatta una data in formato italiano
 * 
 * @param string $date Data da formattare
 * @return string Data formattata
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Sanitizza una stringa per l'output HTML
 * 
 * @param string $string Stringa da sanitizzare
 * @return string Stringa sanitizzata
 */
function sanitizeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica se l'utente è autenticato
 * 
 * @return bool True se l'utente è autenticato, false altrimenti
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Reindirizza l'utente a una pagina
 * 
 * @param string $page Pagina di destinazione
 */
function redirect($page) {
    header("Location: $page");
    exit;
}

/**
 * Genera un token CSRF
 * 
 * @return string Token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 * 
 * @param string $token Token da verificare
 * @return bool True se il token è valido, false altrimenti
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Formatta il numero di riproduzioni
 * 
 * @param int $plays Numero di riproduzioni
 * @return string Numero di riproduzioni formattato
 */
function formatPlays($plays) {
    if ($plays >= 1000000) {
        return round($plays / 1000000, 1) . 'M';
    } elseif ($plays >= 1000) {
        return round($plays / 1000, 1) . 'K';
    }
    return $plays;
}

/**
 * Verifica se un file è un'immagine valida
 * 
 * @param string $filePath Percorso del file
 * @return bool True se il file è un'immagine valida, false altrimenti
 */
function isValidImage($filePath) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileInfo = getimagesize($filePath);
    return $fileInfo !== false && in_array($fileInfo['mime'], $allowedTypes);
}

/**
 * Verifica se un file è un file audio valido
 * 
 * @param string $filePath Percorso del file
 * @return bool True se il file è un file audio valido, false altrimenti
 */
function isValidAudio($filePath) {
    $allowedTypes = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);
    return in_array($mimeType, $allowedTypes);
}

/**
 * Genera un nome file univoco
 * 
 * @param string $originalName Nome file originale
 * @return string Nome file univoco
 */
function generateUniqueFileName($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
} 