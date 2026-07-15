<?php
$pageTitle = "Sobre a Empresa | CANZALA";
$pageCSS = "s";
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main>
    <section class="about section" id="about">
        <div class="container about-container">
            <div class="about-image">
                <img src="<?php echo $baseUrl; ?>assets/img/Eq.jpg" alt="Estrutura e Tecnologia CANZALA">
            </div>

            <div class="about-content">
                <span class="small-title">A Nossa Visão</span>
                <h2>Plataforma de E-commerce CANZALA, LDA</h2>
                <p>
                    Com o crescimento do mercado digital, identificamos a necessidade de otimizar a divulgação dos nossos produtos e serviços, oferecendo aos clientes um processo de vendas ágil, seguro e altamente eficiente.
                </p>
                <p>
                    A CANZALA foi desenvolvida para ser mais do que uma loja virtual. Somos uma plataforma completa que moderniza o atendimento, facilita a gestão de pedidos e proporciona uma experiência de navegação premium.
                </p>

                <h3 style="margin: 20px 0 10px; color: #222;">Os Nossos Diferenciais:</h3>
                <div class="about-features" style="grid-template-columns: 1fr;">
                    <div class="feature">Plataforma segura com proteção de dados e criptografia.</div>
                    <div class="feature">Catálogo digital otimizado e sistema inteligente de carrinho.</div>
                    <div class="feature">Arquitetura robusta utilizando as melhores práticas web.</div>
                    <div class="feature">Atendimento ao cliente rápido e interface intuitiva.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- A EQUIPA (Apresentados como líderes/especialistas da empresa) -->
    <section class="section" style="background-color: #fafafa;">
        <div class="container">
            <div class="section-title">
                <h2>O Nosso Corpo Técnico</h2>
                <p>Os especialistas responsáveis pela gestão e desenvolvimento tecnológico da CANZALA.</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card" style="padding: 20px;">
                    <h3 style="color: #d4af37; margin-bottom: 5px;">Eliel Manuel Mucanza Diogo</h3>
                    <p style="font-size: 0.9rem;">Direção Tecnológica</p>
                </div>
                <div class="service-card" style="padding: 20px;">
                    <h3 style="color: #d4af37; margin-bottom: 5px;">José Diogo Cama</h3>
                    <p style="font-size: 0.9rem;">Especialista Web</p>
                </div>
                <div class="service-card" style="padding: 20px;">
                    <h3 style="color: #d4af37; margin-bottom: 5px;">Garcia Manuel Mateus</h3>
                    <p style="font-size: 0.9rem;">Engenharia de Sistemas</p>
                </div>
                <div class="service-card" style="padding: 20px;">
                    <h3 style="color: #d4af37; margin-bottom: 5px;">Alexandre da Silva Sebastião</h3>
                    <p style="font-size: 0.9rem;">Gestão de Operações</p>
                </div>
                <div class="service-card" style="padding: 20px;">
                    <h3 style="color: #d4af37; margin-bottom: 5px;">Daniel João Dambi dos Santos</h3>
                    <p style="font-size: 0.9rem;">Segurança da Informação</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>