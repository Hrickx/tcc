<?php
include 'conexao.php';

$mensagem = "";
$tipo_alerta = ""; 

if (isset($_POST['btn_salvar'])) {
    $email = $_POST['email_final'];
    $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
    $upd = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
    $upd->bind_param("ss", $nova_senha, $email);
    if ($upd->execute()) {
        $conn->query("DELETE FROM recuperacao_senha WHERE email = '$email'");
        $mensagem = "Senha alterada com sucesso!";
        $tipo_alerta = "success";
    }
}

if (isset($_POST['btn_pedir'])) {
    $email = $_POST['email_recup'];
    $check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $token = bin2hex(random_bytes(15));
        $exp = date("Y-m-d H:i:s", strtotime('+1 hour'));
        $ins = $conn->prepare("INSERT INTO recuperacao_senha (email, token, expiracao) VALUES (?, ?, ?)");
        $ins->bind_param("sss", $email, $token, $exp);
        $ins->execute();
        $link = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?token=" . $token;
        $mensagem = "Link gerado! <br> <a href='$link' class='link-simulado'>Clique aqui para redefinir</a>";
        $tipo_alerta = "success";
    } else {
        $mensagem = "E-mail não encontrado.";
        $tipo_alerta = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha | AutoPeças Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Cores Padrão da sua Tela de Login */
        :root {
            --bg-color: #f8fafc;
            --primary-dark: #1e293b; /* Cinza da Navbar/Login */
            --accent-blue: #2563eb;  /* Azul do botão de finalizar */
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        
        body { 
            background-color: var(--bg-color); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            color: var(--text-main); 
        }
        
        .card { 
            background: white; 
            padding: 2.5rem; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
            width: 100%; 
            max-width: 400px; 
            text-align: center;
            border-top: 5px solid var(--primary-dark);
        }
        
        .logo { 
            font-size: 1.5rem; 
            font-weight: bold; 
            color: var(--primary-dark); 
            margin-bottom: 1.5rem; 
            display: block; 
        }
        
        h2 { font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--primary-dark); }
        p { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem; }

        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        input { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #cbd5e1; 
            border-radius: 8px; 
            margin-bottom: 1rem; 
            font-size: 1rem; 
        }

        button { 
            width: 100%; 
            padding: 1rem; 
            background: var(--primary-dark); 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 1rem; 
            font-weight: 600; 
            cursor: pointer; 
            transition: 0.3s; 
        }

        button:hover { background: #334155; }

        .link-simulado { color: var(--accent-blue); font-weight: bold; text-decoration: none; }
        .back-link { display: block; margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted); text-decoration: none; }
        .back-link:hover { color: var(--primary-dark); text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <span class="logo">AutoPeças Pro</span>

    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_alerta; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['token'])): 
        // Lógica de verificação do token...
        $token = $_GET['token'];
        $stmt = $conn->prepare("SELECT email FROM recuperacao_senha WHERE token = ? AND expiracao > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0):
            $dados = $res->fetch_assoc();
    ?>
            <h2>Nova Senha</h2>
            <p>Redefinindo para <strong><?php echo $dados['email']; ?></strong></p>
            <form method="POST">
                <input type="hidden" name="email_final" value="<?php echo $dados['email']; ?>">
                <input type="password" name="nova_senha" placeholder="Nova Senha" required autofocus>
                <button type="submit" name="btn_salvar">Salvar Alteração</button>
            </form>
        <?php else: ?>
            <p>Link expirado ou inválido.</p>
        <?php endif; ?>

    <?php else: ?>
        <h2>Recuperar Senha</h2>
        <p>Use o e-mail cadastrado na sua conta.</p>
        <form method="POST">
            <input type="email" name="email_recup" placeholder="seu@email.com" required>
            <button type="submit" name="btn_pedir">Gerar Link</button>
        </form>
    <?php endif; ?>

    <a href="login.php" class="back-link">Voltar para o login</a>
</div>

</body>
</html>