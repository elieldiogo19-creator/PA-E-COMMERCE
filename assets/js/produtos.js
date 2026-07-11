// ============================================
// PRODUTOS - Filtro AJAX (categoria + ordenação)
// ============================================

document.addEventListener('DOMContentLoaded', function() {

    const sidebarItems     = document.querySelectorAll('.sidebar-item');
    const produtosContent  = document.querySelector('.produtos-content');
    const produtosTitulo   = document.querySelector('.produtos-titulo');
    const produtosCount    = document.querySelector('.produtos-count');
    const selectOrdem      = document.querySelector('.select-ordem');

    if (!produtosContent) return;

    /**
     * Constrói a URL final combinando categoria + ordem
     */
    function construirURL(categoriaId, ordem) {
        const params = new URLSearchParams();

        if (categoriaId && categoriaId !== '0') {
            params.set('categoria', categoriaId);
        }

        if (ordem && ordem !== 'recentes') {
            params.set('ordem', ordem);
        }

        const query = params.toString();
        return 'produtos.php' + (query ? '?' + query : '');
    }

    /**
     * Pega a categoria atualmente ativa
     */
    function getCategoriaAtiva() {
        const ativo = document.querySelector('.sidebar-item.active');
        if (!ativo) return '0';

        const href = ativo.getAttribute('href');
        const match = href.match(/categoria=(\d+)/);
        return match ? match[1] : '0';
    }

    /**
     * Pega o nome da categoria ativa
     */
    function getCategoriaNome() {
        const ativo = document.querySelector('.sidebar-item.active');
        if (!ativo) return 'Shop / Loja';

        const href = ativo.getAttribute('href');
        if (!href.includes('categoria=')) return 'Shop / Loja';

        return ativo.querySelector('span').textContent;
    }

    /**
     * Carrega produtos via AJAX
     */
    function carregarProdutos(url, titulo = 'Shop / Loja') {
        const cabecalho = produtosContent.querySelector('.produtos-cabecalho');

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

        cabecalho.insertAdjacentHTML('afterend', loadingHTML);

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const loading = produtosContent.querySelector('.produtos-loading');
            if (loading) loading.remove();

            cabecalho.insertAdjacentHTML('afterend', html);

            produtosTitulo.textContent = titulo;

            const countSpan = produtosContent.querySelector('#count-produtos');
            if (countSpan && produtosCount) {
                const total = parseInt(countSpan.textContent);
                produtosCount.textContent = `${total} ${total === 1 ? 'produto' : 'produtos'}`;
                countSpan.remove();
            }

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

    // ============================
    // Listener: sidebar de categorias
    // ============================
    sidebarItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            const href = this.getAttribute('href');
            const categoriaNome = this.querySelector('span').textContent;

            // Atualiza classe active
            sidebarItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Pega a categoria da URL clicada
            const match = href.match(/categoria=(\d+)/);
            const categoriaId = match ? match[1] : '0';

            // Pega a ordem atual do select
            const ordemAtual = selectOrdem ? selectOrdem.value : 'recentes';

            // Constrói URL combinada
            const urlFinal = construirURL(categoriaId, ordemAtual);

            // Atualiza URL do navegador
            window.history.pushState({ url: urlFinal }, '', urlFinal);

            // Título correto
            const tituloFinal = categoriaId !== '0' ? categoriaNome : 'Shop / Loja';

            carregarProdutos(urlFinal, tituloFinal);
        });
    });

    // ============================
    // Listener: dropdown de ordenação
    // ============================
    if (selectOrdem) {
        selectOrdem.addEventListener('change', function() {
            const novaOrdem = this.value;
            const categoriaAtiva = getCategoriaAtiva();
            const categoriaNome = getCategoriaNome();

            const urlFinal = construirURL(categoriaAtiva, novaOrdem);

            window.history.pushState({ url: urlFinal }, '', urlFinal);

            carregarProdutos(urlFinal, categoriaNome);
        });
    }

    // ============================
    // Listener: voltar/avançar do navegador
    // ============================
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const catId = urlParams.get('categoria') || '0';
        const ordem = urlParams.get('ordem') || 'recentes';

        // Atualiza sidebar
        sidebarItems.forEach(i => i.classList.remove('active'));

        let itemAtivo;
        let titulo = 'Shop / Loja';

        if (catId !== '0') {
            itemAtivo = document.querySelector(`.sidebar-item[href="produtos.php?categoria=${catId}"]`);
            if (itemAtivo) {
                itemAtivo.classList.add('active');
                titulo = itemAtivo.querySelector('span').textContent;
            }
        } else {
            itemAtivo = document.querySelector('.sidebar-item[href="produtos.php"]');
            if (itemAtivo) itemAtivo.classList.add('active');
        }

        // Atualiza select
        if (selectOrdem) selectOrdem.value = ordem;

        const urlFinal = construirURL(catId, ordem);
        carregarProdutos(urlFinal, titulo);
    });
});