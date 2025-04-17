<?php
// Inizializza la sessione
session_start();

// Distruggi tutte le variabili di sessione
$_SESSION = array();

// Distruggi il cookie di sessione se esiste
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Distruggi la sessione
session_destroy();

// Reindirizza alla pagina di login
header("Location: login.php");
exit;
?> 