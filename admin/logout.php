<?php
require_once __DIR__ . '/../includes/admin-auth.php';

unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email']);

header('Location: login.php');
exit;
