<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function setFlash(string $tipo, string $mensagem): void {
    $_SESSION['flash'][$tipo][] = $mensagem;
}

function getFlash(string $tipo): array {
    $msgs = $_SESSION['flash'][$tipo] ?? [];
    unset($_SESSION['flash'][$tipo]);
    return $msgs;
}