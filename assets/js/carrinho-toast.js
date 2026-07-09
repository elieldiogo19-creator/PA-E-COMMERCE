// ============================================
// TOAST + AJAX ADICIONAR AO CARRINHO
// ============================================

// Criar container do toast
if (!document.querySelector('.toast-container')) {
    const container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
}

/**
 * Mostra notificação toast dourada
 */
function mostrarToast(titulo, mensagem = '', duracao = 3000) {
    const container = document.querySelector('.toast-container');
    
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `
        <div class="toast-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <div class="toast-content">
            <p class="toast-title">${titulo}</p>
            ${mensagem ? `<p class="toast-message">${mensagem}</p>` : ''}
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
    `;
    
    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 400);
    }, duracao);
}

/**
 * Atualiza badge do carrinho no navbar
 */
function atualizarBadgeCarrinho(totalItens) {
    const cartIcon = document.querySelector('.cart-icon');
    if (!cartIcon) return;
    
    let badge = cartIcon.querySelector('.cart-count');
    
    if (totalItens > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'cart-count';
            cartIcon.appendChild(badge);
        }
        badge.textContent = totalItens;
        badge.classList.add('pulse');
        setTimeout(() => badge.classList.remove('pulse'), 500);
    } else if (badge) {
        badge.remove();
    }
}

/**
 * Adiciona produto ao carrinho via AJAX
 */
function adicionarAoCarrinhoAjax(produtoId, produtoNome, botao) {
    if (botao) {
        botao.style.pointerEvents = 'none';
        botao.style.opacity = '0.7';
    }
    
    // Detecta se está numa subpasta (pages/) ou na raiz
    const basePath = window.location.pathname.includes('/pages/') ? '../' : '';
    
    const formData = new FormData();
    formData.append('id', produtoId);
    
    fetch(basePath + 'actions/adicionar_ao_carrinho.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            mostrarToast('✓ Produto adicionado ao carrinho', produtoNome);
            atualizarBadgeCarrinho(data.total_itens);
        } else {
            mostrarToast('Erro', data.mensagem || 'Não foi possível adicionar');
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        mostrarToast('Erro', 'Falha na conexão');
    })
    .finally(() => {
        if (botao) {
            botao.style.pointerEvents = '';
            botao.style.opacity = '';
        }
    });
}

// Auto-conecta botões com classe .btn-adicionar
document.addEventListener('click', function(e) {
    const botao = e.target.closest('.btn-adicionar');
    if (!botao) return;
    
    e.preventDefault();
    
    const produtoId = botao.dataset.id;
    const produtoNome = botao.dataset.nome || 'Produto';
    
    if (produtoId) {
        adicionarAoCarrinhoAjax(produtoId, produtoNome, botao);
    }
});

// Botão de "Adicionar ao Carrinho" nos DETALHES (mostra toast + redireciona)
document.addEventListener('click', function(e) {
    const botao = e.target.closest('.btn-adicionar-detalhes');
    if (!botao) return;
    
    e.preventDefault();
    
    const produtoId = botao.dataset.id;
    const produtoNome = botao.dataset.nome || 'Produto';
    
    if (!produtoId) return;
    
    // Desabilita o botão
    botao.style.pointerEvents = 'none';
    botao.style.opacity = '0.7';
    
    const basePath = window.location.pathname.includes('/pages/') ? '../' : '';
    
    const formData = new FormData();
    formData.append('id', produtoId);
    
    fetch(basePath + 'actions/adicionar_ao_carrinho.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            // Mostra toast
            mostrarToast('✓ Produto adicionado ao carrinho', produtoNome);
            atualizarBadgeCarrinho(data.total_itens);
            
            // Aguarda 1.5s e redireciona pro carrinho
            setTimeout(() => {
                window.location.href = basePath + 'pages/carrinho.php';
            }, 1500);
        } else {
            mostrarToast('Erro', data.mensagem || 'Não foi possível adicionar');
            botao.style.pointerEvents = '';
            botao.style.opacity = '';
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        mostrarToast('Erro', 'Falha na conexão');
        botao.style.pointerEvents = '';
        botao.style.opacity = '';
    });
});