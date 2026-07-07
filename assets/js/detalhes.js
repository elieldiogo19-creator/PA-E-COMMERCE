document.addEventListener('DOMContentLoaded', function() {
    
    // Animação suave ao carregar a página
    const container = document.querySelector('.container-producto');
    if (container) {
        container.style.opacity = '0';
        container.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            container.style.transition = 'all 0.6s ease';
            container.style.opacity = '1';
            container.style.transform = 'translateY(0)';
        }, 100);
    }

    // Efeito de zoom na imagem ao passar o mouse
    const foto = document.getElementById('foto-dinamica');
    const card = document.querySelector('.card');
    
    if (foto && card) {
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            foto.style.transform = `scale(1.05) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            foto.style.transition = 'transform 0.1s ease-out';
        });
        
        card.addEventListener('mouseleave', function() {
            foto.style.transform = 'scale(1) rotateX(0) rotateY(0)';
            foto.style.transition = 'transform 0.3s ease-out';
        });
    }

    // Contador de caracteres da descrição (opcional)
    const descricao = document.getElementById('descricao-dinamica');
    if (descricao) {
        // Adiciona efeito de fade-in no texto
        descricao.style.opacity = '0';
        setTimeout(() => {
            descricao.style.transition = 'opacity 0.8s ease';
            descricao.style.opacity = '1';
        }, 300);
    }

    // Confirmação ao clicar em "Comprar Agora"
    const btnComprar = document.querySelector('.btn-comprar');
    if (btnComprar) {
        btnComprar.addEventListener('click', function(e) {
            // Opcional: Adicionar loading state
            this.style.opacity = '0.8';
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
        });
    }

    // Feedback visual ao adicionar ao carrinho
    const btnCarrinho = document.querySelector('.btn-carrinho');
    if (btnCarrinho && !btnCarrinho.disabled) {
        btnCarrinho.addEventListener('click', function(e) {
            // Se for link <a>, deixa comportamento padrão
            // Se for botão, pode adicionar efeito aqui
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> Adicionado!';
            this.style.backgroundColor = '#2ecc71'; // Verde de sucesso
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.backgroundColor = ''; // Volta ao normal
            }, 1500);
        });
    }

    console.log('✅ Detalhes do produto carregados com sucesso!');
});