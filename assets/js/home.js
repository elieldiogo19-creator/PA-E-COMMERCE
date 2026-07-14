// ============================================
// CAROSSEL INFINITO AUTOMÁTICO (HERO)
// ============================================
const initInfiniteTracker = () => {
    const hero = document.querySelector('.hero');
    if (!hero) return;

    let slides = hero.querySelectorAll('.carrosel');
    if (slides.length <= 1) return;

    let index = 0;
    const intervalTime = 4000;
    let autoSlideInterval;

    const startAutoSlide = () => {
        autoSlideInterval = setInterval(() => {
            index++;
            if (index >= slides.length) index = 0;
            hero.scrollTo({
                left: hero.offsetWidth * index,
                behavior: 'smooth'
            });
        }, intervalTime);
    };

    const stopAutoSlide = () => clearInterval(autoSlideInterval);

    startAutoSlide();

    hero.addEventListener('touchstart', stopAutoSlide);
    hero.addEventListener('mousedown',  stopAutoSlide);
    hero.addEventListener('touchend',   startAutoSlide);
    hero.addEventListener('mouseup',    startAutoSlide);
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
}, { threshold: 0.1 });

const aplicarRevelar = () => {
    document.querySelectorAll('.revelar').forEach((el) => observador.observe(el));
};

// ============================================
// INICIALIZAÇÃO GERAL
// ============================================
window.addEventListener('load', () => {
    initInfiniteTracker();
    aplicarRevelar();
});

function irParaDetalhes(id) {
    window.location.href = `pages/detalhes.php?id=${id}`;
}

// ============================================
// CORES DINÂMICAS NOS CARDS (v2 – Melhorada)
// ============================================

/**
 * Paleta de fallback (cores douradas/quentes se a análise falhar)
 */
const FALLBACK_COLORS = [
    { r: 180, g: 140, b: 60  }, // Dourado escuro
    { r: 155, g: 90,  b: 50  }, // Terracota
    { r: 100, g: 80,  b: 120 }, // Roxo suave
    { r: 60,  g: 100, b: 130 }, // Azul petróleo
    { r: 110, g: 130, b: 90  }, // Verde oliva
];

/**
 * Cache em memória (evita recalcular na mesma sessão)
 */
const colorCache = new Map();

/**
 * Extrai a cor dominante de uma imagem — versão inteligente
 * Ignora pixels muito claros (fundo branco) ou muito escuros (sombras)
 */
function getSmartColor(imageElement, callback) {
    const src = imageElement.src;

    // ---- Cache ----
    if (colorCache.has(src)) {
        callback(colorCache.get(src));
        return;
    }

    // ---- Cache no sessionStorage (persiste entre navegações) ----
    const cached = sessionStorage.getItem('color_' + src);
    if (cached) {
        const parsed = JSON.parse(cached);
        colorCache.set(src, parsed);
        callback(parsed);
        return;
    }

    const canvas = document.createElement('canvas');
    const ctx    = canvas.getContext('2d', { willReadFrequently: true });
    const img    = new Image();

    img.crossOrigin = 'Anonymous';

    img.onload = function() {
        const SIZE = 40; // pequeno = rápido
        canvas.width  = SIZE;
        canvas.height = SIZE;

        try {
            ctx.drawImage(img, 0, 0, SIZE, SIZE);
            const data = ctx.getImageData(0, 0, SIZE, SIZE).data;

            let r = 0, g = 0, b = 0, count = 0;

            // Amostragem inteligente: ignora pixels brancos/pretos/transparentes
            for (let i = 0; i < data.length; i += 4) {
                const pR = data[i];
                const pG = data[i + 1];
                const pB = data[i + 2];
                const pA = data[i + 3];

                if (pA < 128) continue;                              // transparente
                if (pR > 240 && pG > 240 && pB > 240) continue;      // quase branco
                if (pR < 15  && pG < 15  && pB < 15)  continue;      // quase preto

                // Ignora cinzas muito saturados (sem cor)
                const max = Math.max(pR, pG, pB);
                const min = Math.min(pR, pG, pB);
                if (max - min < 15 && max > 200) continue;           // cinza claro

                r += pR;
                g += pG;
                b += pB;
                count++;
            }

            // Se quase todos os pixels foram ignorados, usa fallback
            if (count < 20) {
                useFallback(src, callback);
                return;
            }

            r = Math.floor(r / count);
            g = Math.floor(g / count);
            b = Math.floor(b / count);

            // Ajusta luminância inteligentemente
            const color = adjustForContrast(r, g, b);

            // Guarda em cache
            colorCache.set(src, color);
            try {
                sessionStorage.setItem('color_' + src, JSON.stringify(color));
            } catch (e) { /* quota exceeded, ignora */ }

            callback(color);

        } catch (err) {
            console.warn('Erro ao analisar imagem (CORS?):', src);
            useFallback(src, callback);
        }
    };

    img.onerror = () => useFallback(src, callback);
    img.src = src;
}

/**
 * Ajusta a cor para ter bom contraste com texto branco
 * - Se for muito clara → escurece
 * - Se for muito escura → clareia ligeiramente
 * - Se for saturada → mantém vibrante
 */
function adjustForContrast(r, g, b) {
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b);

    let factor;
    if (luminance > 180)      factor = 0.55;  // muito clara → escurece muito
    else if (luminance > 130) factor = 0.70;  // média-clara → escurece
    else if (luminance > 80)  factor = 0.85;  // média → escurece pouco
    else                      factor = 1.0;   // já é escura → mantém

    return {
        r: Math.max(20, Math.floor(r * factor)),
        g: Math.max(20, Math.floor(g * factor)),
        b: Math.max(20, Math.floor(b * factor))
    };
}

/**
 * Retorna uma cor de fallback baseada no hash da URL da imagem
 * (assim a mesma imagem sempre tem a mesma cor)
 */
function useFallback(src, callback) {
    let hash = 0;
    for (let i = 0; i < src.length; i++) {
        hash = ((hash << 5) - hash) + src.charCodeAt(i);
        hash |= 0;
    }
    const idx = Math.abs(hash) % FALLBACK_COLORS.length;
    callback(FALLBACK_COLORS[idx]);
}

/**
 * Aplica a cor num card (background + textos)
 */
function applyColorToCard(card, color) {
    const { r, g, b } = color;
    const rgb        = `rgb(${r}, ${g}, ${b})`;
    const rgbDark    = `rgb(${Math.floor(r * 0.6)}, ${Math.floor(g * 0.6)}, ${Math.floor(b * 0.6)})`;
    const rgbLight   = `rgb(${Math.min(255, Math.floor(r * 1.6 + 40))}, ${Math.min(255, Math.floor(g * 1.6 + 40))}, ${Math.min(255, Math.floor(b * 1.6 + 40))})`;

    // Transição suave
    card.style.transition = 'background 0.6s ease';
    card.style.background = `linear-gradient(135deg, ${rgb} 0%, ${rgbDark} 100%)`;
    card.classList.add('card-color-loaded');

    // ===== TÍTULO (h3) com cor dinâmica clara =====
    const titulo = card.querySelector('h3');
    if (titulo) {
        titulo.style.background = `linear-gradient(135deg, ${rgbLight} 0%, #ffffff 100%)`;
        titulo.style.webkitBackgroundClip = 'text';
        titulo.style.backgroundClip = 'text';
        titulo.style.webkitTextFillColor = 'transparent';
        titulo.style.color = 'transparent';
        titulo.style.textShadow = 'none';
        titulo.style.filter = `drop-shadow(0 2px 4px rgba(0,0,0,0.3))`;
    }

    // ===== SUBTÍTULO (span) com tom claro complementar =====
    const subtitulo = card.querySelector('.text span');
    if (subtitulo) {
        subtitulo.style.color = rgbLight;
        subtitulo.style.opacity = '1';
        subtitulo.style.fontWeight = '600';
        subtitulo.style.textShadow = '0 1px 2px rgba(0,0,0,0.3)';
    }

    // ===== BOTÃO complementar =====
    const btn = card.querySelector('.btn');
    if (btn) {
        btn.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        btn.style.color           = rgb;
        btn.style.fontWeight      = '700';
    }
}

/**
 * Processa todos os cards do Home
 */
function initDynamicColors() {
    const cards = document.querySelectorAll('.products-grid .card');
    if (!cards.length) return;

    cards.forEach(card => {
        const img = card.querySelector('img');
        if (!img) return;

        // Se a imagem já carregou → processa
        if (img.complete && img.naturalWidth > 0) {
            getSmartColor(img, (color) => applyColorToCard(card, color));
        } else {
            // Espera carregar
            img.addEventListener('load', () => {
                getSmartColor(img, (color) => applyColorToCard(card, color));
            }, { once: true });

            img.addEventListener('error', () => {
                useFallback(img.src, (color) => applyColorToCard(card, color));
            }, { once: true });
        }
    });
}

// Executa quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', initDynamicColors);