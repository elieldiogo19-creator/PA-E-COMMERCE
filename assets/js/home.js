// ============================================
// CAROSSEL INFINITO AUTOMÁTICO (HERO)
// ============================================
const initInfiniteTracker = () => {
    const hero = document.querySelector('.hero');
    if (!hero) return;

    let slides = hero.querySelectorAll('.carrosel');
    if (slides.length <= 1) return;

    let index = 0;
    const intervalTime = 4000; // Tempo de rotação (4 segundos)
    let autoSlideInterval;

    const startAutoSlide = () => {
        autoSlideInterval = setInterval(() => {
            index++;
            if (index >= slides.length) {
                index = 0; // Volta ao início de forma infinita
            }
            hero.scrollTo({
                left: hero.offsetWidth * index,
                behavior: 'smooth'
            });
        }, intervalTime);
    };

    const stopAutoSlide = () => {
        clearInterval(autoSlideInterval);
    };

    // Inicia o autoplay
    startAutoSlide();

    // Pausa o carrossel se o usuário arrastar manualmente
    hero.addEventListener('touchstart', stopAutoSlide);
    hero.addEventListener('mousedown', stopAutoSlide);
    hero.addEventListener('touchend', startAutoSlide);
    hero.addEventListener('mouseup', startAutoSlide);
};

// ============================================
// ANIMAÇÕES DE SCROLL (REVEAL)
// ============================================
const observador = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('show');
        } else {
            entry.target.classList.remove('show');
        }
    });
}, {
    threshold: 0.1
});

const aplicarRevelar = () => {
    const elementos = document.querySelectorAll('.revelar');
    elementos.forEach((el) => observador.observe(el));
};

// Inicialização Geral
window.addEventListener('load', () => {
    initInfiniteTracker();
    aplicarRevelar();
});

function irParaDetalhes(id) {
    window.location.href = `pages/detalhes.php?id=${id}`;
}
// ============================================
// CORES DINÂMICAS NOS CARDS (Análise de Imagem)
// ============================================

function getAverageColor(imageElement, callback) {
    // Cria um canvas invisível para analisar a imagem
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    // Resolve problema de CORS (se a imagem for do mesmo domínio)
    img.crossOrigin = "Anonymous";
    
    img.onload = function() {
        // Redimensiona para 50x50px (suficiente para pegar a cor média)
        canvas.width = 50;
        canvas.height = 50;
        
        // Desenha a imagem reduzida
        ctx.drawImage(img, 0, 0, 50, 50);
        
        // Pega os dados dos pixels
        const imageData = ctx.getImageData(0, 0, 50, 50);
        const data = imageData.data;
        
        let r = 0, g = 0, b = 0;
        let count = 0;
        
        // Soma todas as cores (pulando pixels transparentes)
        for (let i = 0; i < data.length; i += 4) {
            if (data[i+3] > 128) { // Se não for transparente
                r += data[i];
                g += data[i+1];
                b += data[i+2];
                count++;
            }
        }
        
        // Calcula a média
        r = Math.floor(r / count);
        g = Math.floor(g / count);
        b = Math.floor(b / count);
        
        // Escurece um pouco para garantir contraste com texto branco
        r = Math.floor(r * 0.8);
        g = Math.floor(g * 0.8);
        b = Math.floor(b * 0.8);
        
        callback(`rgb(${r}, ${g}, ${b})`);
    };
    
    img.src = imageElement.src;
}

// Aplica cores dinâmicas aos cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.products-grid .card');
    
    cards.forEach(card => {
        const img = card.querySelector('img');
        if (img && img.src) {
            getAverageColor(img, (color) => {
                card.style.backgroundColor = color;
                card.style.backgroundImage = `linear-gradient(135deg, ${color} 0%, rgba(0,0,0,0.3) 100%)`;
                
                // Ajusta a cor do texto/botão se necessário
                const btn = card.querySelector('.btn');
                if (btn) {
                    btn.style.backgroundColor = 'rgba(255,255,255,0.9)';
                    btn.style.color = color;
                }
            });
        }
    });
});