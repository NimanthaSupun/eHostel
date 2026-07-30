<?php
/**
 * Session / authorization helpers for eHostel
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_role() {
    return $_SESSION['role'] ?? null;
}

function require_login($base = '') {
    if (!is_logged_in()) {
        header("Location: " . $base . "login.php");
        exit;
    }
}

function require_admin($base = '') {
    require_login($base);
    if (current_role() !== 'admin') {
        header("Location: " . $base . "unauthorized.php");
        exit;
    }
}

function require_student($base = '') {
    require_login($base);
    if (current_role() !== 'student') {
        header("Location: " . $base . "unauthorized.php");
        exit;
    }
}

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
