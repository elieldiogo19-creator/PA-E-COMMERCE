// ============================================
// PRODUTOS - Filtro AJAX (sem recarregar página)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    const produtosContent = document.querySelector('.produtos-content');
    const produtosTitulo = document.querySelector('.produtos-titulo');
    const produtosCount = document.querySelector('.produtos-count');
    
    if (!sidebarItems.length || !produtosContent) return;
    
    /**
     * Carrega produtos via AJAX
     */
    function carregarProdutos(url, categoriaNome = 'Shop / Loja') {
        // Área onde vão aparecer os produtos (tudo depois do cabeçalho)
        const cabecalho = produtosContent.querySelector('.produtos-cabecalho');
        
        // Loading state
        const loadingHTML = `
            <div class="produtos-loading">
                <div class="loading-spinner"></div>
                <p>A carregar produtos...</p>
            </div>
        `;
        
        // Remove conteúdo antigo (mantém cabeçalho)
        let elemento = cabecalho.nextElementSibling;
        while (elemento) {
            const proximo = elemento.nextElementSibling;
            elemento.remove();
            elemento = proximo;
        }
        
        // Insere loading
        cabecalho.insertAdjacentHTML('afterend', loadingHTML);
        
        // Fetch AJAX
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            // Remove loading
            const loading = produtosContent.querySelector('.produtos-loading');
            if (loading) loading.remove();
            
            // Insere novo HTML
            cabecalho.insertAdjacentHTML('afterend', html);
            
            // Atualiza título e contador
            produtosTitulo.textContent = categoriaNome;
            
            const countSpan = produtosContent.querySelector('#count-produtos');
            if (countSpan && produtosCount) {
                const total = parseInt(countSpan.textContent);
                produtosCount.textContent = `${total} ${total === 1 ? 'produto' : 'produtos'}`;
                countSpan.remove();
            }
            
            // Animação de entrada
            const vitrine = produtosContent.querySelector('.vitrine');
            if (vitrine) {
                vitrine.style.opacity = '0';
                vitrine.style.transform = 'translateY(15px)';
                setTimeout(() => {
                    vitrine.style.transition = 'all 0.4s ease';
                    vitrine.style.opacity = '1';
                    vitrine.style.transform = 'translateY(0)';
                }, 50);
            }
        })
        .catch(err => {
            console.error('Erro:', err);
            const loading = produtosContent.querySelector('.produtos-loading');
            if (loading) loading.innerHTML = '<p>Erro ao carregar produtos.</p>';
        });
    }
    
    // Listener nos itens da sidebar
    sidebarItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const categoriaNome = this.querySelector('span').textContent;
            
            // Atualiza classes active
            sidebarItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            // Atualiza URL do navegador (sem recarregar)
            window.history.pushState({ url: url }, '', url);
            
            // Mostra/esconde botão limpar filtro
            const btnLimpar = document.querySelector('.btn-limpar-filtro');
            const isCategoria = url.includes('?categoria=');
            
            if (isCategoria && !btnLimpar) {
                const sidebar = document.querySelector('.produtos-sidebar');
                const novoBtn = document.createElement('a');
                novoBtn.href = 'produtos.php';
                novoBtn.className = 'btn-limpar-filtro';
                sidebar.appendChild(novoBtn);
            } else if (!isCategoria && btnLimpar) {
                btnLimpar.remove();
            }
            
            // Título correto se for "Todos"
            const tituloFinal = isCategoria ? categoriaNome : 'Shop / Loja';
            
            // Carrega produtos via AJAX
            carregarProdutos(url, tituloFinal);
        });
    });
    
    // Botão voltar/avançar do navegador
    window.addEventListener('popstate', function(e) {
        const url = window.location.href;
        const urlParams = new URLSearchParams(window.location.search);
        const catId = urlParams.get('categoria');
        
        // Encontra o item correspondente e marca como ativo
        sidebarItems.forEach(i => i.classList.remove('active'));
        
        if (catId) {
            const item = document.querySelector(`.sidebar-item[href="produtos.php?categoria=${catId}"]`);
            if (item) {
                item.classList.add('active');
                const nome = item.querySelector('span').textContent;
                carregarProdutos(item.href, nome);
            }
        } else {
            const todosLink = document.querySelector('.sidebar-item[href="produtos.php"]');
            if (todosLink) {
                todosLink.classList.add('active');
                carregarProdutos('produtos.php', 'Shop / Loja');
            }
        }
    });
});