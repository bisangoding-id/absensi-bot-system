<?php
session_start();
if (!isset($_SESSION['admin_x'])) {
    header("Location: login");
    exit;
}
?>