<?php
session_start();

/**
 * Verifica se l'utente è loggato come admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Effettua il login dell'utente
 * @param string $username
 * @param bool $isAdmin
 */
function login($username, $isAdmin = false) {
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
    $_SESSION['is_admin'] = $isAdmin;
}

/**
 * Effettua il logout dell'utente
 */
function logout() {
    $_SESSION = array();
    session_destroy();
}

/**
 * Verifica se l'utente è loggato
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
} 