<?php
session_start();
include 'conexao.php';

// Segurança: Só o gerente entra aqui
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    header("Location: login.php");
    exit();
}

// LÓGICA DE CADASTRO (Criptografia acontece aqui)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $perfil = $_POST['perfil'];
    
    // password_hash cria a criptografia segura para o banco
    $senha_criptografada = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha, perfil, status_usuario) 
        VALUES ('$nome', '$email', '$senha_criptografada', '$perfil', 'ATIVO')";
    
    if($conn->query($sql)) {
        echo "<script>alert('Funcionário cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $conn->error . "');</script>";
    }
}

// Busca usuários existentes para listar na tabela
$usuarios = $conn->query("SELECT id_usuario, nome, email, perfil FROM usuarios ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Usuários | AutoPeças Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 20px; color: #1e293b; }
        .container-gestao { max-width: 900px; margin: auto; }
        .card-cadastro { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #cbd5e1; border-radius: 6px; }
        .btn-salvar { background: #7c3aed; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-salvar:hover { background: #6d28d9; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: #64748b; font-size: 0.8rem; text-transform: uppercase; }
        .voltar { display: inline-block; margin-bottom: 20px; color: #7c3aed; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container-gestao">
    <a href="gerente.php" class="voltar">← Voltar para o Painel</a>

    <div class="card-cadastro">
        <h2>➕ Cadastrar Novo Funcionário</h2>
        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" name="nome" placeholder="Nome Completo" required>
                <input type="email" name="email" placeholder="E-mail de acesso" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="password" name="senha" placeholder="Senha Temporária" required>
                <select name="perfil">
                    <option value="VENDEDOR">VENDEDOR</option>
                    <option value="ESTOQUISTA">ESTOQUISTA</option>
                    <option value="GERENTE">GERENTE</option>
                </select>
            </div>
            <button type="submit" name="cadastrar" class="btn-salvar">Finalizar Cadastro</button>
        </form>
    </div>

    <h3>👥 Usuários Cadastrados</h3>
   <table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th>Status</th> 
            <th>Ações</th> </tr>
    </thead>
    <tbody>
        <?php while($user = $usuarios->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['nome']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo $user['perfil']; ?></td>
            <td>
                <span style="color: <?php echo ($user['status_usuario'] == 'ATIVO') ? 'green' : 'red'; ?>; font-weight: bold;">
                    <?php echo $user['status_usuario'] ?? 'ATIVO'; ?>
                </span>
            </td>
            <td>
                <a href="EditarUsuario.php?id=<?php echo $user['id_usuario']; ?>" style="text-decoration:none; color:blue;">✏️ Editar</a> | 

                <?php if(($user['status_usuario'] ?? 'ATIVO') == 'ATIVO'): ?>
                    <a href="AlterarStatusUsuario.php?id=<?php echo $user['id_usuario']; ?>&novo_status=INATIVO" 
                       style="color:red; text-decoration:none;" 
                       onclick="return confirm('Deseja realmente desativar este acesso?')">🚫 Desativar</a>
                <?php else: ?>
                    <a href="AlterarStatusUsuario.php?id=<?php echo $user['id_usuario']; ?>&novo_status=ATIVO" 
                       style="color:green; text-decoration:none;">✅ Reativar</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

</body>
</html>