<?php
/**
 * Simple smoke tests for core helper functions.
 * Run with: php tests/smoke_test.php
 * (No PHPUnit dependency needed — keeps the project lightweight for beginners.)
 */

require_once __DIR__ . '/../includes/functions.php';

$failures = 0;

function assert_equal($actual, $expected, $label) {
    global $failures;
    if ($actual === $expected) {
        echo "[PASS] $label\n";
    } else {
        echo "[FAIL] $label — expected '$expected', got '$actual'\n";
        $failures++;
    }
}

// Test money() formatting
assert_equal(money(15000), '15,000 RWF', 'money() formats thousands correctly');
assert_equal(money(0), '0 RWF', 'money() handles zero');

// Test e() escaping
assert_equal(e('<script>alert(1)</script>'), '&lt;script&gt;alert(1)&lt;/script&gt;', 'e() escapes HTML');

// Test cart helpers (uses $_SESSION, so simulate a session array)
$_SESSION = ['cart' => []];
add_to_cart(1, 2);
assert_equal(cart_count(), 2, 'add_to_cart() adds correct quantity');

add_to_cart(1, 3);
assert_equal(cart_count(), 5, 'add_to_cart() accumulates quantity for same product');

update_cart_item(1, 1);
assert_equal(cart_count(), 1, 'update_cart_item() overwrites quantity');

remove_from_cart(1);
assert_equal(cart_count(), 0, 'remove_from_cart() removes product');

echo "\n";
if ($failures > 0) {
    echo "$failures test(s) failed.\n";
    exit(1);
}

echo "All tests passed.\n";
exit(0);
