<?php
function pegarCorDaImagem($caminhoImagem) {
    // Se não tiver imagem, retorna cinza
    if (!file_exists($caminhoImagem)) return '#333333';
    
    // Cria uma miniatura 1x1 pixel da imagem
    $img = imagecreatefromstring(file_get_contents($caminhoImagem));
    $mini = imagecreatetruecolor(1, 1);
    imagecopyresampled($mini, $img, 0, 0, 0, 0, 1, 1, imagesx($img), imagesy($img));
    
    // Pega a cor desse 1 pixel
    $rgb = imagecolorat($mini, 0, 0);
    $r = ($rgb >> 16) & 255;
    $g = ($rgb >> 8) & 255;
    $b = $rgb & 255;
    
    // Escurece um pouco para o texto branco ficar legível
    $r = max(0, $r - 50);
    $g = max(0, $g - 50);
    $b = max(0, $b - 50);
    
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}
?>