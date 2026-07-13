/* ============================================
   ADMIN AJAX - Sistema genérico para formulários
   ============================================ */

(function() {
    'use strict';

    // ============ TOAST DINÂMICO ============
    function showToast(tipo, titulo, mensagem, autoClose = true) {
        let container = document.getElementById('toastContainerAdmin');

        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainerAdmin';
            container.className = 'toast-container-admin';
            document.body.appendChild(container);
        }

        const icons = {
            sucesso: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
            erro: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`,
            aviso: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
            info: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`,
        };

        const toast = document.createElement('div');
        toast.className = `toast-admin toast-${tipo}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="toast-admin-icon">${icons[tipo] || icons.info}</div>
            <div class="toast-admin-content">
                <strong>${titulo}</strong>
                <span>${mensagem}</span>
            </div>
            <button type="button" class="toast-admin-close" aria-label="Fechar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            ${autoClose ? '<div class="toast-admin-progress"></div>' : ''}
        `;

        container.appendChild(toast);

        // Anima entrada
        setTimeout(() => toast.classList.add('show'), 50);

        // Fechar manualmente
        toast.querySelector('.toast-admin-close').addEventListener('click', () => hideToast(toast));

        // Auto-close
        if (autoClose) {
            const timer = setTimeout(() => hideToast(toast), 4000);

            // Pausa ao hover
            toast.addEventListener('mouseenter', () => {
                clearTimeout(timer);
                const p = toast.querySelector('.toast-admin-progress');
                if (p) p.style.animationPlayState = 'paused';
            });
        }
    }

    function hideToast(toast) {
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 300);
    }

    // Expor globalmente
    window.showToast = showToast;

        // ============ FORMULÁRIOS AJAX ============
    document.addEventListener('submit', async function(e) {
        const form = e.target;

        if (!form.hasAttribute('data-ajax')) return;

        e.preventDefault();

        const submitBtn = form.querySelector('[type="submit"]');
        const btnText   = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="2" x2="12" y2="6"></line>
                    <line x1="12" y1="18" x2="12" y2="22"></line>
                    <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                    <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                    <line x1="2" y1="12" x2="6" y2="12"></line>
                    <line x1="18" y1="12" x2="22" y2="12"></line>
                    <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                    <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                </svg>
                A processar...
            `;
        }

        try {
            const formData = new FormData(form);

            const response = await fetch(form.action || window.location.href, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();

            if (data.sucesso) {
                showToast('sucesso', 'Sucesso', data.mensagem);

                // Callback customizado
                if (typeof window.onAjaxSuccess === 'function') {
                    window.onAjaxSuccess(data, form);
                } else if (data.redirect) {
                    // 🆕 Sinaliza para restaurar scroll ao voltar
                    sessionStorage.setItem('admin_restore_scroll', '1');

                    // 🆕 Passa o ID do produto para "flash" visual na linha
                    if (data.id) {
                        sessionStorage.setItem('admin_highlight_id', data.id);
                    }

                    // Redirect após 800ms
                    setTimeout(() => window.location.href = data.redirect, 800);
                }
            } else {
                let msg = data.mensagem || 'Ocorreu um erro.';
                if (data.errors && data.errors.length) {
                    msg = data.errors.join(' · ');
                }
                showToast('erro', 'Erro', msg, false);
            }

        } catch (err) {
            console.error(err);
            showToast('erro', 'Erro de rede', 'Não foi possível processar. Tenta novamente.', false);
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = btnText;
            }
        }
    });

    // ============ LINKS AJAX (excluir, etc) ============
    document.addEventListener('click', async function(e) {
        const link = e.target.closest('[data-ajax-action]');
        if (!link) return;

        e.preventDefault();

        const confirmMsg = link.dataset.confirm;
        if (confirmMsg && !confirm(confirmMsg)) return;

        const url = link.href;

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();

            if (data.sucesso) {
                showToast('sucesso', 'Sucesso', data.mensagem);

                if (typeof window.onAjaxActionSuccess === 'function') {
                    window.onAjaxActionSuccess(data, link);
                }
            } else {
                showToast('erro', 'Erro', data.mensagem || 'Não foi possível.', false);
            }

        } catch (err) {
            showToast('erro', 'Erro de rede', 'Tenta novamente.', false);
        }
    });

    // ============ VOLTAR SEM RECARREGAR ============
    document.addEventListener('click', function(e) {
        const link = e.target.closest('[data-history-back]');
        if (!link) return;

        e.preventDefault();

        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = link.href;
        }
    });


        // ============ SISTEMA DE SCROLL PERSISTENTE ============

    // Ao clicar num link de "editar" ou "adicionar", guarda a posição atual
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href*="editar.php"], a[href*="adicionar.php"]');
        if (!link) return;

        // Só se o link parte de uma página de listagem (tem tabela admin)
        if (document.querySelector('.admin-table')) {
            sessionStorage.setItem('admin_scroll_position', window.scrollY);
            sessionStorage.setItem('admin_scroll_url', window.location.pathname + window.location.search);
        }
    });

    // Ao carregar a página, verifica se precisa restaurar scroll
    window.addEventListener('load', function() {
        const savedUrl      = sessionStorage.getItem('admin_scroll_url');
        const savedPos      = sessionStorage.getItem('admin_scroll_position');
        const restoreFlag   = sessionStorage.getItem('admin_restore_scroll');
        const highlightId   = sessionStorage.getItem('admin_highlight_id');
        const currentUrl    = window.location.pathname + window.location.search;

        // Só restaura se estivermos na MESMA URL que guardámos
        // (funciona tanto para "voltar" quanto para redirect após salvar)
        if (savedPos && savedUrl && currentUrl.includes(savedUrl.split('?')[0])) {

            // Restaura scroll suavemente
            setTimeout(() => {
                window.scrollTo({
                    top: parseInt(savedPos),
                    behavior: 'instant'
                });

                // 🆕 Destaca a linha do produto editado
                if (highlightId) {
                    const row = document.querySelector(`tr[data-produto-id="${highlightId}"]`);
                    if (row) {
                        row.classList.add('row-highlight');
                        setTimeout(() => row.classList.remove('row-highlight'), 2500);
                    }
                }
            }, 50);

            // Limpa flags
            sessionStorage.removeItem('admin_scroll_position');
            sessionStorage.removeItem('admin_scroll_url');
            sessionStorage.removeItem('admin_restore_scroll');
            sessionStorage.removeItem('admin_highlight_id');
        }
    });

})();