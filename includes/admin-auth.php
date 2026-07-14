<?php
/**
 * Admin authentication helpers.
 * Kept separate from customer auth (functions.php) so admin access
 * always requires its own explicit check (users.role = 'admin'),
 * even though both share the same `users` table and session.
 */

require_once __DIR__ . '/functions.php';

function is_admin() {
    return !empty($_SESSION['admin_id']);
}

function require_admin($redirect = 'login.php') {
    if (!is_admin()) {
        header('Location: ' . $redirect);
        exit;
    }
}
