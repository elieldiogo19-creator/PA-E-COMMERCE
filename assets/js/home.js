// ============================================
// HOME.JS - Versão Estável e Segura
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    console.log('✅ home.js carregado com sucesso');

    // ============================================
    // SCROLL REVEAL (Animação ao rolar)
    // ============================================
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.revelar').forEach(el => {
        observer.observe(el);
    });

    // ============================================
    // CARROSSEL INFINITO NO HERO
    // ============================================
    const hero = document.querySelector('.hero');
    if (hero) {
        let current = 0;
        const slides = hero.querySelectorAll('.carrosel');
        
        if (slides.length > 1) {
            const slideWidth = hero.clientWidth;

            const nextSlide = () => {
                current = (current + 1) % slides.length;
                hero.scrollTo({
                    left: slideWidth * current,
                    behavior: 'smooth'
                });
            };

            let interval = setInterval(nextSlide, 5000);

            hero.addEventListener('mouseenter', () => clearInterval(interval));
            hero.addEventListener('mouseleave', () => {
                interval = setInterval(nextSlide, 5000);
            });

            console.log(`✅ Carrossel com ${slides.length} slides iniciado`);
        }
    }

    // Função para ir para detalhes
    window.irParaDetalhes = function(id) {
        if (id && id !== '#') {
            window.location.href = `pages/detalhes.php?id=${id}`;
        }
    };

    console.log('✅ Todos os scripts da Home inicializados com sucesso');
});

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