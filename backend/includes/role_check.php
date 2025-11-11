<?php
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../frontend/auth/login.html');
    exit;
}
?>
