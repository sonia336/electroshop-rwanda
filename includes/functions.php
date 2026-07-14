<?php
/**
 * Shared helper functions used across the application.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- Security helpers ----------

/** Escape output to prevent XSS */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Generate (or reuse) a CSRF token for this session */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Verify a submitted CSRF token */
function csrf_verify($token) {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

/** Basic input sanitizer for strings */
function clean($value) {
    return trim(strip_tags($value ?? ''));
}

// ---------- Auth helpers ----------

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function require_login($redirect = 'login.php') {
    if (!is_logged_in()) {
        header('Location: ' . $redirect);
        exit;
    }
}

// ---------- Cart helpers ----------
// Cart is stored in the PHP session as [product_id => quantity]

function get_cart() {
    return $_SESSION['cart'] ?? [];
}

function cart_count() {
    $cart = get_cart();
    return array_sum($cart);
}

function add_to_cart($product_id, $qty = 1) {
    $product_id = (int) $product_id;
    $qty = max(1, (int) $qty);
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 0;
    }
    $_SESSION['cart'][$product_id] += $qty;
}

function update_cart_item($product_id, $qty) {
    $product_id = (int) $product_id;
    $qty = (int) $qty;
    if ($qty <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}

function remove_from_cart($product_id) {
    unset($_SESSION['cart'][(int) $product_id]);
}

function clear_cart() {
    $_SESSION['cart'] = [];
}

/** Format a number as Rwandan Francs */
function money($amount) {
    return number_format((float) $amount, 0) . ' RWF';
}
