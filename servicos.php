<?php
$pageTitle = "Serviços | CANZALA, LDA";
$pageCSS = "s";
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main>
    <section class="hero" id="home">
        <div class="hero-overlay"></div>
        <div class="hero-content container">
            <div class="hero-text">
                <p>Especialistas em Segurança e Energia</p>
                <h1>Proteção e Continuidade para o seu Negócio</h1>
                <p>
                    A CANZALA, LDA oferece soluções integradas de instalação e manutenção técnica.
                    Garantimos a segurança do seu património e a disponibilidade de energia para que a sua empresa nunca
                    pare.
                </p>
                <div class="hero-buttons">
                    <a href="#services" class="btn primary-btn">Nossos Serviços</a>
                    <a href="<?php echo $baseUrl; ?>contacto.php" class="btn secondary-btn">Solicitar Orçamento</a>
                </div>
            </div>

            <div class="hero-card">
                <h3>Por que escolher a CANZALA?</h3>
                <p>Equipa técnica qualificada e equipamentos de alta tecnologia ao serviço da sua tranquilidade.</p>
                <div class="card-stats">
                    <div>
                        <h2>100%</h2>
                        <span>Fiabilidade</span>
                    </div>
                    <div>
                        <h2>24/7</h2>
                        <span>Monitorização</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services section" id="services">
        <div class="container">
            <div class="section-title">
                <h2>Os Nossos Serviços Técnicos</h2>
                <p>Soluções profissionais de instalação e manutenção preventiva ou corretiva.</p>
            </div>

            <div class="services-grid">

                <!-- Serviço 1 -->
                <div class="service-card">
                    <div class="service-icon">
                        <!-- Ícone de Energia/Raio (Gerador) -->
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                        </svg>
                    </div>
                    <h3>Manutenção de Geradores</h3>
                    <p>Assistência preventiva e corretiva para garantir que a sua fonte de energia alternativa funcione
                        de forma ininterrupta e eficiente.</p>
                    <!-- ADICIONA ESTE BOTÃO EM CADA CARD, MUDANDO APENAS O NOME DO SERVIÇO NO LINK -->
                    <a href="contacto.php?servico=Manutenção de Geradores" class="btn primary-btn"
                        style="margin-top: 15px; display: block; text-align: center;">Solicitar Serviço</a>
                </div>

                <!-- Serviço 2 -->
                <div class="service-card">
                    <div class="service-icon">
                        <!-- Ícone de Escudo/Segurança (Intrusão) -->
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3>Instalação de Sist. de Intrusão</h3>
                    <p>Projetamos e instalamos alarmes de alta precisão para detetar rapidamente acessos não autorizados
                        às suas instalações.</p>
                    <!-- ADICIONA ESTE BOTÃO EM CADA CARD, MUDANDO APENAS O NOME DO SERVIÇO NO LINK -->
                    <a href="contacto.php?servico=Instalação de Sist. de Intrusão" class="btn primary-btn"
                        style="margin-top: 15px; display: block; text-align: center;">Solicitar Serviço</a>
                </div>

                <!-- Serviço 3 -->
                <div class="service-card">
                    <div class="service-icon">
                        <!-- Ícone de Câmara (Video Vigilância) -->
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 7l-7 5 7 5V7z"></path>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                        </svg>
                    </div>
                    <h3>Instalação de Vídeo Vigilância</h3>
                    <p>Implementação de circuitos fechados de TV (CCTV) com câmaras de alta definição para monitorização
                        em tempo real do seu espaço.</p>
                    <!-- ADICIONA ESTE BOTÃO EM CADA CARD, MUDANDO APENAS O NOME DO SERVIÇO NO LINK -->
                    <a href="contacto.php?servico=Instalação de Vídeo Vigilância" class="btn primary-btn"
                        style="margin-top: 15px; display: block; text-align: center;">Solicitar Serviço</a>
                </div>

                <!-- Serviço 4 -->
                <div class="service-card">
                    <div class="service-icon">
                        <!-- Ícone de Cartão/Crachá (Controlo de Acesso) -->
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="2" ry="2"></rect>
                            <circle cx="9" cy="10" r="2"></circle>
                            <path d="M15 10h2M15 14h2M5 16h8"></path>
                        </svg>
                    </div>
                    <h3>Instalação de Controlo de Acesso</h3>
                    <p>Sistemas de restrição de entrada através de biometria, cartões magnéticos ou códigos, garantindo
                        acesso exclusivo a pessoas autorizadas.</p>
                    <!-- ADICIONA ESTE BOTÃO EM CADA CARD, MUDANDO APENAS O NOME DO SERVIÇO NO LINK -->
                    <a href="contacto.php?servico=Instalação de Controlo de Acesso" class="btn primary-btn"
                        style="margin-top: 15px; display: block; text-align: center;">Solicitar Serviço</a>
                </div>

                <!-- Serviço 5 -->
                <div class="service-card">
                    <div class="service-icon">
                        <!-- Ícone de Engrenagem (Manutenção) -->
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                            </path>
                        </svg>
                    </div>
                    <h3>Manutenção de Vídeo Vigilância</h3>
                    <p>Limpeza de lentes, afinação de foco, configuração de DVR/NVR e substituição de peças avariadas
                        nas suas câmaras.</p>
                    <!-- ADICIONA ESTE BOTÃO EM CADA CARD, MUDANDO APENAS O NOME DO SERVIÇO NO LINK -->
                    <a href="contacto.php?servico=Manutenção de Vídeo Vigilância" class="btn primary-btn"
                        style="margin-top: 15px; display: block; text-align: center;">Solicitar Serviço</a>
                </div>

                <!-- Serviço 6 -->
                <div class="service-card">
                    <div class="service-icon">
                        <!-- Ícone de Chave de Boca (Manutenção) -->
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                            </path>
                        </svg>
                    </div>
                    <h3>Manutenção de Sist. de Intrusão</h3>
                    <p>Calibração de sensores de movimento, verificação de baterias e testes de comunicação com a
                        central de alarmes.</p>
                    <!-- ADICIONA ESTE BOTÃO EM CADA CARD, MUDANDO APENAS O NOME DO SERVIÇO NO LINK -->
                    <a href="contacto.php?servico=Manutenção de Sist. de Intrusão" class="btn primary-btn"
                        style="margin-top: 15px; display: block; text-align: center;">Solicitar Serviço</a>
                </div>

            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>