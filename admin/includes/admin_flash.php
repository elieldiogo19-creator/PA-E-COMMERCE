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

/**
 * Detecta se a requisição atual é AJAX
 */
function isAjax(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Retorna resposta JSON e encerra
 */
function jsonResponse(array $data, int $status = 200): void {
    if (ob_get_length()) ob_clean();
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Resposta de sucesso padronizada
 */
function jsonSuccess(string $mensagem, array $extra = []): void {
    jsonResponse(array_merge([
        'sucesso'  => true,
        'mensagem' => $mensagem,
    ], $extra));
}

/**
 * Resposta de erro padronizada
 */
function jsonError(string $mensagem, array $errors = []): void {
    jsonResponse([
        'sucesso'  => false,
        'mensagem' => $mensagem,
        'errors'   => $errors,
    ], 422);
}