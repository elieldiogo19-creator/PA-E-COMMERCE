<?php
require_once __DIR__ . '/admin_flash.php';

$sucessos = getFlash('sucesso');
$erros    = getFlash('erro');
$infos    = getFlash('info');
$avisos   = getFlash('aviso');
?>

<?php if (!empty($sucessos) || !empty($erros) || !empty($infos) || !empty($avisos)): ?>
<div class="flash-container">

    <?php foreach ($sucessos as $msg): ?>
        <div class="flash flash-sucesso" role="alert">
            <div class="flash-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="flash-content">
                <strong>Sucesso</strong>
                <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <button type="button" class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    <?php endforeach; ?>

    <?php foreach ($erros as $msg): ?>
        <div class="flash flash-erro" role="alert">
            <div class="flash-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <div class="flash-content">
                <strong>Erro</strong>
                <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <button type="button" class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    <?php endforeach; ?>

    <?php foreach ($avisos as $msg): ?>
        <div class="flash flash-aviso" role="alert">
            <div class="flash-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div class="flash-content">
                <strong>Atenção</strong>
                <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <button type="button" class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    <?php endforeach; ?>

    <?php foreach ($infos as $msg): ?>
        <div class="flash flash-info" role="alert">
            <div class="flash-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            </div>
            <div class="flash-content">
                <strong>Informação</strong>
                <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <button type="button" class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    <?php endforeach; ?>

</div>

<script>
    // Auto-fechar mensagens de sucesso após 5s
    setTimeout(() => {
        document.querySelectorAll('.flash-sucesso, .flash-info').forEach(el => {
            el.classList.add('flash-hiding');
            setTimeout(() => el.remove(), 400);
        });
    }, 5000);
</script>
<?php endif; ?>