<?php
$pageTitle = "Apoio ao Cliente | CANZALA";
$pageCSS = "s";
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

            <form class="contact-form" action="#" method="POST">
                <input type="text" name="nome" placeholder="O seu nome completo" required>
                <input type="email" name="email" placeholder="O seu email corporativo" required>
                <input type="text" name="assunto" placeholder="Assunto da mensagem" required>
                <textarea name="mensagem" rows="6" placeholder="Detalhe a sua solicitação aqui..." required></textarea>
                
                <button type="submit" class="btn primary-btn" style="width: 100%;">
                    Submeter Pedido
                </button>
            </form>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>