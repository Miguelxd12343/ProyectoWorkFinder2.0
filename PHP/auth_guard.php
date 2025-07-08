<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    // Si no hay sesión iniciada, redirige al login
    header("Location: login.php");
    exit();
}
?>