<?php
$sucessos = getFlash('sucesso');
$erros = getFlash('erro');
$infos = getFlash('info');
?>

<?php if (!empty($sucessos)): ?>
    <div class="flash flash-sucesso">
        <?php foreach ($sucessos as $msg): ?>
            <p><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($erros)): ?>
    <div class="flash flash-erro">
        <?php foreach ($erros as $msg): ?>
            <p><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($infos)): ?>
    <div class="flash flash-info">
        <?php foreach ($infos as $msg): ?>
            <p><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>