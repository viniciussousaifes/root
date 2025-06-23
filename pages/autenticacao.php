<?php
function proteger_pagina() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
}
}
?>