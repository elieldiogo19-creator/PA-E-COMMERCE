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
