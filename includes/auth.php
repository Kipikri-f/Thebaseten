<?php
// =====================================================
// AUTH GUARD — include at top of protected pages
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Helper functions for role checking
function isAdmin() {
    return ($_SESSION['role'] ?? '') === 'admin';
}

function isMember() {
    return ($_SESSION['role'] ?? '') === 'member';
}

function isGuest() {
    return ($_SESSION['role'] ?? '') === 'guest';
}

function canEdit() {
    return isAdmin();
}

function getUserRole() {
    return $_SESSION['role'] ?? 'guest';
}
