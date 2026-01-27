<?php
session_start();
include('conexao.php');

if(isset($_POST['email']) && isset($_POST['password'])) {
    
    // Sanitização apenas do e-mail
    $email = $conn->real_escape_string($_POST['email']);
    $senha_digitada = $_POST['password']; 

    // 1. Buscamos o usuário APENAS pelo e-mail
   // Adicionamos a verificação de status na busca
    $sql_code = "SELECT * FROM usuarios WHERE email = '$email' AND status_usuario = 'ATIVO'";
    $sql_query = $conn->query($sql_code) or die("Erro no banco: " . $conn->error);

    // 2. Verificamos se o e-mail existe
    if($sql_query->num_rows == 1) {
        $usuario = $sql_query->fetch_assoc();
        
        // 3. COMPARANDO A SENHA CRIPTOGRAFADA
        // password_verify compara o que o usuário digitou com o hash salvo no banco
        if(password_verify($senha_digitada, $usuario['senha'])) {
            
            // Se cair aqui, a senha está correta. Montamos a sessão:
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['perfil'] = $usuario['perfil'];

            // Redirecionamento original
            if($usuario['perfil'] == 'GERENTE') {
                header("Location: gerente.php");
            } else if($usuario['perfil'] == 'VENDEDOR') {
                header("Location: vendedor.php");
            } else if($usuario['perfil'] == 'ESTOQUISTA') {
                header("Location: estoquista.php");
            }
            exit(); // Importante para parar a execução após o redirecionamento

        } else {
            // Senha incorreta
            echo "<script>alert('E-mail ou senha incorretos!');</script>";
        }
    } else {
        // E-mail não encontrado
        echo "<script>alert('E-mail ou senha incorretos!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AutoPeças Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
    /* Configurações Globais e Reset */
    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
        font-family: 'Inter', sans-serif; 
    }

    body { 
        background-color: #f4f7f6; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        height: 100vh; 
    }

    /* Container Principal de Login */
    .login-container { 
        background-color: #ffffff; 
        padding: 40px; 
        border-radius: 12px; 
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); 
        width: 100%; 
        max-width: 400px; 
    }

    /* Cabeçalho do Formulário */
    .header { 
        text-align: center; 
        margin-bottom: 30px; 
    }

    .header h1 { 
        color: #1a202c; 
        font-size: 26px; 
        font-weight: 600; 
        letter-spacing: -1px; 
    }

    .header p { 
        color: #718096; 
        font-size: 14px; 
        margin-top: 8px; 
    }

    /* Grupos de Formulário e Inputs */
    .form-group { 
        margin-bottom: 20px; 
    }

    .form-group label { 
        display: block; 
        margin-bottom: 8px; 
        color: #4a5568; 
        font-size: 14px; 
        font-weight: 600; 
    }

    .form-group input { 
        width: 100%; 
        padding: 12px 16px; 
        border: 1px solid #e2e8f0; 
        border-radius: 8px; 
        outline: none; 
        transition: all 0.2s; 
    }

    .form-group input:focus { 
        border-color: #3182ce; 
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); 
    }

    /* Botão de Ação */
    .btn-login { 
        width: 100%; 
        padding: 12px; 
        background-color: #2d3748; 
        color: white; 
        border: none; 
        border-radius: 8px; 
        font-size: 16px; 
        font-weight: 600; 
        cursor: pointer; 
        transition: background 0.2s; 
        margin-top: 10px; 
    }

    .btn-login:hover { 
        background-color: #1a202c; 
    }

    /* Links do Rodapé */
    .footer-links { 
        margin-top: 25px; 
        text-align: center; 
        font-size: 13px; 
        color: #718096; 
    }

    .footer-links a { 
        color: #3182ce; 
        text-decoration: none; 
        font-weight: 600; 
    }
</style>
</head>
<body>

    <div class="login-container">
        <div class="header">
            <h1>AutoPeças Pro</h1>
            <p>Gerenciamento de Inventário</p>
        </div>

        <form action="" method="POST">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="seu@email.com" required>
            </div>
            
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" placeholder="Sua senha" required>
            </div>

            <button type="submit" class="btn-login">ACESSAR SISTEMA</button>
        </form>

        <div class="footer-links">
           <p style="margin-top: 15px; font-size: 0.85rem; text-align: center;">
    <a href="recuperar.php" style="color: #7c3aed; text-decoration: none; font-weight: 600;">
        Esqueceu a senha?
    </a>
</p>
            <p style="margin-top: 12px;">Novo por aqui? <br> <a href="#">Solicite acesso à gerência</a></p>
        </div>
    </div>

</body>
</html>