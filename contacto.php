<?php
session_start();
require __DIR__ . '/config/db.php'; // Conexão com o banco

$pageTitle = "Apoio ao Cliente | CANZALA";
$pageCSS = "s";

// Variáveis para mensagens de alerta
$mensagemSucesso = '';
$mensagemErro = '';

// Pega o serviço da URL (se o cliente vier da página servicos.php)
$servicoPreSelecionado = $_GET['servico'] ?? '';

// Processa o formulário quando o botão é clicado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verifica se o usuário está logado (tua tabela exige usuario_id)
    if (empty($_SESSION['usuario_id'])) {
        $mensagemErro = "Atenção: Precisa iniciar sessão na sua conta para solicitar um serviço ou enviar mensagem.";
    } else {
        $usuario_id   = $_SESSION['usuario_id'];
        $tipo_servico = $_POST['assunto'] ?? 'Contacto Geral';
        $descricao    = $_POST['mensagem'] ?? '';

        try {
            // Insere na tabela solicitacoes_servico
            $stmt = $pdo->prepare("INSERT INTO solicitacoes_servico (usuario_id, tipo_servico, descricao) VALUES (?, ?, ?)");
            $stmt->execute([$usuario_id, $tipo_servico, $descricao]);
            
            $mensagemSucesso = "Solicitação enviada com sucesso! A nossa equipa técnica entrará em contacto em breve.";
            
            // Limpa o formulário após enviar
            $servicoPreSelecionado = ''; 
        } catch (PDOException $e) {
            $mensagemErro = "Erro ao processar solicitação. Tente novamente mais tarde.";
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<main>
    <section class="contact section" id="contact" style="margin-top: 20px;">
        <div class="container contact-container">
            <div class="contact-info">
                <span class="small-title">Atendimento</span>
                <h2>Suporte ao Cliente</h2>
                <p>
                    A nossa equipa comercial e técnica está sempre disponível para o ajudar. 
                    Utilize os nossos canais abaixo para solicitar assistência, discutir orçamentos ou enviar sugestões.
                </p>

                <div class="contact-box">
                    <strong>Email Institucional:</strong>
                    <span>info@canzala.com</span>
                </div>
                <div class="contact-box">
                    <strong>Linha de Apoio:</strong>
                    <span>+244 900 000 000</span>
                </div>
                <div class="contact-box">
                    <strong>Sede Central:</strong>
                    <span>Luanda, Angola</span>
                </div>
            </div>

            <form class="contact-form" action="contacto.php" method="POST">
                
                <!-- ALERTAS VISUAIS -->
                <?php if ($mensagemSucesso): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
                        <strong>Sucesso!</strong> <?= $mensagemSucesso; ?>
                    </div>
                <?php endif; ?>

                <?php if ($mensagemErro): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
                        <strong>Erro!</strong> <?= $mensagemErro; ?>
                    </div>
                <?php endif; ?>

                <!-- Os campos Nome e Email não vão para a tabela serviços, mas ficam para estética e contato -->
                <input type="text" name="nome" placeholder="O seu nome completo" required 
                       value="<?= $_SESSION['usuario_nome'] ?? ''; ?>">
                       
                <input type="email" name="email" placeholder="O seu email corporativo" required>
                
                <!-- Aqui entra a mágica do preenchimento automático -->
                <input type="text" name="assunto" placeholder="Assunto ou Serviço desejado" required 
                       value="<?= htmlspecialchars($servicoPreSelecionado); ?>">
                       
                <textarea name="mensagem" rows="6" placeholder="Detalhe a sua solicitação ou morada da instalação aqui..." required></textarea>
                
                <button type="submit" class="btn primary-btn" style="width: 100%;">
                    Submeter Pedido
                </button>
            </form>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>