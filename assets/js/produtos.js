document.addEventListener('DOMContentLoaded', function() {

    const sidebarItems   = document.querySelectorAll('.sidebar-item');
    const produtosContent = document.querySelector('.produtos-content');
    const produtosTitulo = document.querySelector('.produtos-titulo');
    const produtosCount  = document.querySelector('.produtos-count');
    const selectOrdem    = document.querySelector('.select-ordem');

    if (!produtosContent) return;

    // 🆕 Configuração
    const POR_PAGINA = 8;

    /**
     * Constrói a URL final combinando categoria + ordem + página
     */
    function construirURL(categoriaId, ordem, pagina = 1) {
        const params = new URLSearchParams();

        if (categoriaId && categoriaId !== '0') {
            params.set('categoria', categoriaId);
        }

        if (ordem && ordem !== 'recentes') {
            params.set('ordem', ordem);
        }

        // 🆕 Adiciona página se > 1
        if (pagina && pagina > 1) {
            params.set('pagina', pagina);
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

            // 🆕 Atualizar contador (Mostrando X–Y de Z)
            atualizarContador();

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

            // 🆕 Anexar listeners da paginação (novos elementos)
            attachPaginacaoListeners();
        })
        .catch(err => {
            console.error('Erro:', err);
            const loading = produtosContent.querySelector('.produtos-loading');
            if (loading) loading.innerHTML = '<p>Erro ao carregar produtos.</p>';
        });
    }

    /**
     * 🆕 Atualiza o contador "Mostrando X–Y de Z produtos"
     */
    function atualizarContador() {
        const totalEl    = produtosContent.querySelector('#total-produtos');
        const paginaEl   = produtosContent.querySelector('#pagina-atual');

        if (!totalEl || !paginaEl || !produtosCount) return;

        const total     = parseInt(totalEl.textContent) || 0;
        const paginaAt  = parseInt(paginaEl.textContent) || 1;

        if (total === 0) {
            produtosCount.textContent = '0 produtos';
            return;
        }

        const inicio = ((paginaAt - 1) * POR_PAGINA) + 1;
        const fim    = Math.min(paginaAt * POR_PAGINA, total);
        const label  = total === 1 ? 'produto' : 'produtos';

        produtosCount.innerHTML = `Mostrando <strong>${inicio}–${fim}</strong> de <strong>${total}</strong> ${label}`;

        // Remove os spans ocultos após usá-los
        totalEl.remove();
        paginaEl.remove();
        const cont = produtosContent.querySelector('#count-produtos');
        if (cont) cont.remove();
        const tPag = produtosContent.querySelector('#total-paginas');
        if (tPag) tPag.remove();
    }

    /**
     * 🆕 Anexa listeners aos botões da paginação
     */
    function attachPaginacaoListeners() {
        const pagLinks = produtosContent.querySelectorAll('.pag-btn[data-pagina]');

        pagLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const pagina        = parseInt(this.dataset.pagina);
                const categoriaId   = getCategoriaAtiva();
                const categoriaNome = getCategoriaNome();
                const ordemAtual    = selectOrdem ? selectOrdem.value : 'recentes';

                const urlFinal = construirURL(categoriaId, ordemAtual, pagina);

                window.history.pushState({ url: urlFinal }, '', urlFinal);

                carregarProdutos(urlFinal, categoriaNome);

                // 🆕 Scroll suave até o topo dos produtos
                produtosContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    // ==========================
    // Listener: sidebar de categorias
    // ==========================
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

            // 🆕 Ao trocar categoria, volta para página 1
            const urlFinal = construirURL(categoriaId, ordemAtual, 1);

            // Atualiza URL do navegador
            window.history.pushState({ url: urlFinal }, '', urlFinal);

            // Título correto
            const tituloFinal = categoriaId !== '0' ? categoriaNome : 'Shop / Loja';

            carregarProdutos(urlFinal, tituloFinal);
        });
    });

    // ==========================
    // Listener: dropdown de ordenação
    // ==========================
    if (selectOrdem) {
        selectOrdem.addEventListener('change', function() {
            const novaOrdem     = this.value;
            const categoriaAtiva = getCategoriaAtiva();
            const categoriaNome  = getCategoriaNome();

            // 🆕 Ao trocar ordenação, volta para página 1
            const urlFinal = construirURL(categoriaAtiva, novaOrdem, 1);

            window.history.pushState({ url: urlFinal }, '', urlFinal);

            carregarProdutos(urlFinal, categoriaNome);
        });
    }

    // ==========================
    // Listener: voltar/avançar do navegador
    // ==========================
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const catId    = urlParams.get('categoria') || '0';
        const ordem    = urlParams.get('ordem')     || 'recentes';
        const pagina   = parseInt(urlParams.get('pagina')) || 1; // 🆕

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

        const urlFinal = construirURL(catId, ordem, pagina); // 🆕
        carregarProdutos(urlFinal, titulo);
    });

    // 🆕 Anexar listeners iniciais da paginação (no load da página)
    attachPaginacaoListeners();

    // 🆕 Atualizar contador no load inicial (se veio server-side)
    atualizarContador();
});