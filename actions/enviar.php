<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$captchaConfig = require __DIR__ . '/../config/captcha.php';


$nome = htmlspecialchars(trim($_POST['nome'] ?? ''), ENT_QUOTES, 'UTF-8');

$email = trim($_POST['email'] ?? '');

$telefone = htmlspecialchars(
    trim($_POST['phone'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$tipoProjeto = htmlspecialchars(
    trim($_POST['client_type'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$empresa = htmlspecialchars(
    trim($_POST['company'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$servico = htmlspecialchars(
    trim($_POST['service'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$mensagem = htmlspecialchars(
    trim($_POST['mensagem'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$consentimento = $_POST['consentimento'] ?? '';

if (
    empty($nome) ||
    empty($email) ||
    empty($telefone) ||
    empty($mensagem) ||
    $consentimento !== 'sim' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    header('Location: ../index.php?erro=1#contact');
    exit;
}

// CAPTCHA
$turnstileToken = $_POST['cf-turnstile-response'] ?? '';

if (empty($turnstileToken)) {
    header('Location: ../index.php?erro=captcha#contact');
    exit;
}

$verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

$postData = http_build_query([
    'secret' => $captchaConfig['secret_key'],
    'response' => $turnstileToken,
    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
]);

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => $postData,
        'timeout' => 10
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($verifyUrl, false, $context);

if ($result === false) {
    header('Location: ../index.php?erro=captcha#contact');
    exit;
}

$captchaResult = json_decode($result, true);

if (empty($captchaResult['success'])) {
    header('Location: ../index.php?erro=captcha#contact');
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;

    $mail->Username = $_ENV['MAIL_USERNAME'];
    $mail->Password = $_ENV['MAIL_PASSWORD'];

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int) $_ENV['MAIL_PORT'];

    $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);

    $mail->addAddress($_ENV['MAIL_TO']);

    $mail->addReplyTo($email, $nome);

    $mail->isHTML(true);

    $mail->Subject = 'Novo contato recebido - Portfolio Nyxsen';

    $mail->Body = "
     <!DOCTYPE html>
<html lang='pt-BR'>
<head>
  <meta charset='UTF-8'>
  <title>Novo contato</title>
</head>
<body style='margin:0; padding:0; background:#0b0b0b; font-family: Arial, sans-serif;'>

  <table width='100%' cellpadding='0' cellspacing='0' style='background:#0b0b0b; padding:30px 0;'>
    <tr>
      <td align='center'>

        <table width='600' cellpadding='0' cellspacing='0' style='background:#111; border-radius:12px; overflow:hidden; border:1px solid #222;'>

          <tr>
            <td style='padding:28px; text-align:center; background:#050505;'>
              <h1 style='margin:0; color:#ffffff; font-size:28px; letter-spacing:2px;'>
                NYXSEN
              </h1>
              <p style='margin:8px 0 0; color:#0057ff; font-size:12px; letter-spacing:3px;'>
                Digital
              </p>
            </td>
          </tr>

          <tr>
            <td style='height:4px; background:#0057ff;'></td>
          </tr>

          <tr>
            <td style='padding:35px 30px; text-align:center;'>
              <h2 style='margin:0 0 18px; color:#ffffff; font-size:26px;'>
                Novo contato recebido
              </h2>

              <p style='margin:0 0 28px; color:#b8b8b8; font-size:15px; line-height:1.6;'>
                Um visitante preencheu o formulário do site. Veja os dados abaixo:
              </p>

              <table width='100%' cellpadding='0' cellspacing='0' style='text-align:left;'>
                <tr>
                  <td style='padding:12px; color:#888; border-bottom:1px solid #222;'>Nome</td>
                  <td style='padding:12px; color:#fff; border-bottom:1px solid #222;'>$nome</td>
                </tr>

                <tr>
                  <td style='padding:12px; color:#888; border-bottom:1px solid #222;'>E-mail</td>
                  <td style='padding:12px; color:#fff; border-bottom:1px solid #222;'>$email</td>
                </tr>

                <tr>
                  <td style='padding:12px; color:#888; border-bottom:1px solid #222;'>Telefone</td>
                  <td style='padding:12px; color:#fff; border-bottom:1px solid #222;'>$telefone</td>
                </tr>
                
                <tr>
                 <td style='padding:12px; color:#888; border-bottom:1px solid #222;'>Tipo de projeto</td>
                 <td style='padding:12px; color:#fff; border-bottom:1px solid #222;'>$tipoProjeto</td>
                </tr> 
                
                <tr>
                  <td style='padding:12px; color:#888; border-bottom:1px solid #222;'>Serviço</td>
                  <td style='padding:12px; color:#fff; border-bottom:1px solid #222;'>$servico</td>
                </tr>
              </table>

              <div style='margin-top:28px; padding:20px; background:#0a0a0a; border:1px solid #222; border-radius:8px; text-align:left;'>
                <p style='margin:0 0 10px; color:#0057ff; font-weight:bold;'>
                  Mensagem:
                </p>

                <p style='margin:0; color:#d4d4d4; line-height:1.7;'>
                  $mensagem
                </p>
              </div>

              <p style='margin:30px 0 0; color:#777; font-size:13px;'>
                Para responder ao cliente, clique em responder neste e-mail.
              </p>
            </td>
            
          </tr>

          <tr>
            <td style='padding:20px; text-align:center; background:#050505;'>
              <p style='margin:0; color:#666; font-size:12px;'>
                © 2026 Nyxsen digitial. Todos os direitos reservados.
              </p>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>
</html>
";

    $mail->send();


/* E-mail automático para o cliente */
$auto = new PHPMailer(true);

$auto->isSMTP();

$auto->Host = $_ENV['MAIL_HOST'];
$auto->SMTPAuth = true;

$auto->Username = $_ENV['MAIL_USERNAME'];
$auto->Password = $_ENV['MAIL_PASSWORD'];

$auto->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$auto->Port = (int) $_ENV['MAIL_PORT'];

$auto->setFrom($_ENV['MAIL_FROM'], 'Nyxsen');
$auto->addAddress($email, $nome);

$auto->isHTML(true);
$auto->Subject = 'Recebemos seu contato - Nyxsen';

$auto->Body = "
  <div style='background:#0b0b0b; padding:30px; font-family:Arial, sans-serif; color:#fff;'>
    <div style='max-width:600px; margin:0 auto; background:#111; border:1px solid #222; border-radius:12px; overflow:hidden;'>
      
      <div style='padding:28px; text-align:center; background:#050505; border-bottom:4px solid #0047d9;'>
        <h1 style='margin:0; letter-spacing:2px;'>NYXSEN</h1>
        <p style='margin:8px 0 0; color:#0047d9; letter-spacing:3px; font-size:12px;'>Digital</p>
      </div>

      <div style='padding:32px;'>
        <h2 style='margin-top:0;'>Recebemos seu contato</h2>

        <p style='color:#cfcfcf; line-height:1.7;'>
          Olá, <strong>$nome</strong>! Obrigado por entrar em contato com a Nyxsen.
        </p>

        <p style='color:#cfcfcf; line-height:1.7;'>
          Recebemos sua solicitação sobre <strong>$servico</strong> e nossa equipe irá analisar as informações enviadas.
          Em breve entraremos em contato para entender melhor seu projeto e dar continuidade ao atendimento.
        </p>

        <p style='color:#cfcfcf; line-height:1.7;'>
          Caso queira complementar alguma informação, basta responder este e-mail.
        </p>

        <p style='margin-top:30px; color:#777; font-size:13px;'>
          Atenciosamente,<br>
          Equipe Nyxsen
        </p>
      </div>

    </div>
  </div>
";

$auto->send();
    
    echo 'Email enviado com sucesso';

    header('Location: ../index.php?sucess=1#contact');

    exit;
} catch (Exception $e) {
    echo "Erro: {$mail->ErrorInfo}";
}