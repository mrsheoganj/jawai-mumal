<?php
/**
 * Admin Authentication Gatekeeper
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
