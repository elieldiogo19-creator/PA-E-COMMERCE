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

// ============================================
// AJAX DENTRO DO CARRINHO (aumentar/diminuir/remover/limpar)
// ============================================
document.addEventListener('click', function(e) {
    const botao = e.target.closest('.btn-carrinho-acao');
    if (!botao) return;
    
    e.preventDefault();
    
    const acao = botao.dataset.acao;
    const produtoId = botao.dataset.id || 0;
    
    if (!acao) return;
    
    // Monta URL da ação
    let url = 'carrinho.php?';
    if (acao === 'limpar') {
        url += 'limpar=1';
    } else {
        url += acao + '=' + produtoId;
    }
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.sucesso) return;
        
        // Atualiza badge do navbar
        atualizarBadgeCarrinho(data.total_itens);
        
        // Atualiza totais na página
        const totalValor = document.querySelector('.total-valor');
        const mobileTotalValor = document.querySelector('.mobile-total-valor');
        
        if (totalValor) totalValor.textContent = data.total_geral + ' Kz';
        if (mobileTotalValor) mobileTotalValor.textContent = data.total_geral + ' Kz';
        
        // Atualiza subtotal na linha de resumo
        const resumoLinhas = document.querySelectorAll('.resumo-linha');
        if (resumoLinhas.length > 0) {
            const subtotalSpan = resumoLinhas[0].querySelector('span:last-child');
            if (subtotalSpan) subtotalSpan.textContent = data.total_geral + ' Kz';
        }
        
        // Se carrinho ficou vazio, recarrega
        if (data.carrinho_vazio) {
            location.reload();
            return;
        }
        
        // Ação de LIMPAR - recarrega
        if (data.acao === 'limpar') {
            location.reload();
            return;
        }
        
        // Encontra o card do produto
        const produtoItem = botao.closest('.produto-item');
        if (!produtoItem) return;
        
        if (data.acao === 'remover' || (data.acao === 'diminuir' && data.removido)) {
            // Anima remoção do item
            produtoItem.style.transition = 'all 0.4s ease';
            produtoItem.style.opacity = '0';
            produtoItem.style.transform = 'translateX(-30px)';
            produtoItem.style.maxHeight = produtoItem.offsetHeight + 'px';
            
            setTimeout(() => {
                produtoItem.style.maxHeight = '0';
                produtoItem.style.padding = '0';
                produtoItem.style.margin = '0';
                produtoItem.style.overflow = 'hidden';
            }, 200);
            
            setTimeout(() => produtoItem.remove(), 600);
            
            // Toast de remoção
            mostrarToast('Produto removido do carrinho');
        } else {
            // Atualiza quantidade
            const qtyNumero = produtoItem.querySelector('.qty-numero');
            const subtotalEl = produtoItem.querySelector('.produto-subtotal');
            
            if (qtyNumero) {
                qtyNumero.textContent = data.nova_quantidade;
                qtyNumero.style.transform = 'scale(1.3)';
                qtyNumero.style.transition = 'transform 0.2s ease';
                setTimeout(() => qtyNumero.style.transform = 'scale(1)', 200);
            }
            
            if (subtotalEl) {
                subtotalEl.textContent = data.novo_subtotal + ' Kz';
            }
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        mostrarToast('Erro', 'Falha ao processar ação');
    });
});