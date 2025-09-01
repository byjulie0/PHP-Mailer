<?php
require 'conexao.php';
require 'vendor/autoload.php'; // PHPMailer (instalado via Composer)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $conn->real_escape_string($_POST['email']);

    // Verifica se o email existe no banco
    $sql = "SELECT id, nome FROM usuarios WHERE email = '$email' LIMIT 1";
    $res = $conn->query($sql);

    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        $idUsuario = $user['id'];
        $nome = $user['nome'];

        // Gera nova senha aleatória
        $novaSenha = substr(md5(uniqid(rand(),
        true)), 0, 8);        
        
        

        // Atualiza no banco
        $sqlUpdate = "UPDATE usuarios SET senha = 
        '$novaSenha' WHERE id = $idUsuario";
        
        if($conn->query($sqlUpdate)){

            // Envia e-mail com PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Configurações do servidor SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // exemplo: smtp.gmail.com
                $mail->SMTPAuth   = true;
                $mail->Username   = 'julievieiramartins0@gmail.com';
                $mail->Password   = 'wpop tbnj hjrr iezd'; // de preferência app password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                // Remetente e destinatário
                $mail->setFrom('julievieiramartins0@gmail.com', 
                'Suporte - Sistema');
                $mail->addAddress($email, $nome);

                // Conteúdo do email
                $mail->isHTML(true);
                $mail->Subject = 'Recuperacao de Senha';
                $mail->Body    = "Olá <b>$nome</b>,<br><br>
                                  Sua nova senha é: <b>$novaSenha</b><br><br>
                                  Recomendamos que altere a senha após o login.";
                $mail->AltBody = "Olá $nome,\n\nSua nova senha é: $novaSenha\n\n
                Altere após o login.";

                $mail->send();
                echo "Uma nova senha foi enviada para seu e-mail.";
            } 
        
        catch (Exception $e) {
                echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
            }
        } else {
            echo "Erro ao atualizar a senha no banco.";
        }

    } else {
        echo "E-mail não encontrado.";
    }
}
?>