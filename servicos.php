<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$baseUrl = '';
$pageTitle = 'Serviços - ' . $nomeProjeto;
$pageCSS = 'servicos';

$mensagem = '';
$erros = [];
$servicoSelecionado = '';

$servicos = [
    "Manutenção de Geradores",
    "Instalação de Sistemas de Intrusão",
    "Instalação de Sistemas de Video Vigilancia",
    "Instalação de Controlo de Acesso",
    "Manutenção de Sistemas de Video Vigilancia",
    "Manutenção de Sistemas de Intrusão"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_SESSION['usuario_id'])) {
        header('Location: ../auth/login.php?from=servicos');
        exit;
    }

    $tipoServico = trim($_POST['tipo_servico'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    $servicoSelecionado = $tipoServico;

    if (strlen($descricao) < 10) {
        $erros[] = "Descreva melhor o problema (mín. 10 caracteres).";
    }

    if (empty($erros)) {
        $stmt = $pdo->prepare("
            INSERT INTO solicitacoes_servico 
            (usuario_id, tipo_servico, descricao)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['usuario_id'],
            $tipoServico,
            $descricao
        ]);

        $mensagem = "Serviço solicitado com sucesso!";
        $servicoSelecionado = '';
    }
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<!-- HERO IGUAL AO EXEMPLAR -->
<section class="hero-servicos">
    <div class="hero-overlay"></div>

    <div class="hero-content container">
        <div class="hero-text">
            <p>Prestação de serviços especializados</p>
            <h1>Soluções Técnicas Profissionais</h1>
            <p>
                Instalação e manutenção de sistemas de segurança,
                controlo e infraestrutura técnica para empresas e residências.
            </p>
        </div>

        <div class="hero-card">
            <h3>Atendimento Especializado</h3>
            <p>Equipe técnica qualificada e pronta para atender.</p>

            <div class="card-stats">
                <div>
                    <h2>+200</h2>
                    <span>Projetos</span>
                </div>

                <div>
                    <h2>100%</h2>
                    <span>Compromisso</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="services section">
    <div class="container">

        <div class="section-title">
            <h2>Nossos Serviços</h2>
            <p>Especialistas em sistemas técnicos e segurança eletrónica.</p>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert-sucesso"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="alert-erro">
                <?php foreach ($erros as $erro): ?>
                    <div><?php echo htmlspecialchars($erro); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="services-grid">

            <?php foreach ($servicos as $servico): ?>
                <div class="service-card">

                    <h3><?php echo htmlspecialchars($servico); ?></h3>

                    <?php if (!empty($_SESSION['usuario_id'])): ?>

                        <?php if ($servicoSelecionado === $servico): ?>

                            <form method="POST" class="form-servico">
                                <input type="hidden" name="tipo_servico"
                                       value="<?php echo htmlspecialchars($servico); ?>">

                                <textarea name="descricao"
                                    placeholder="Descreva o problema ou necessidade..."
                                    required></textarea>

                                <button type="submit" class="btn primary-btn">
                                    Confirmar Solicitação
                                </button>
                            </form>

                        <?php else: ?>

                            <form method="POST">
                                <input type="hidden" name="tipo_servico"
                                       value="<?php echo htmlspecialchars($servico); ?>">
                                <button type="submit" class="btn primary-btn">
                                    Solicitar Serviço
                                </button>
                            </form>

                        <?php endif; ?>

                    <?php else: ?>

                        <a href="../auth/login.php?from=servicos"
                           class="btn primary-btn">
                           Iniciar sessão para solicitar
                        </a>

                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>