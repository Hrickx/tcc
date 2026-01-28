<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    header("Location: login.php");
    exit();
}

// 1. Busca os dados atuais do usuário para preencher o formulário
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM usuarios WHERE id_usuario = $id");
    $dados = $res->fetch_assoc();
}

// 2. Lógica de Atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar'])) {
    $id = intval($_POST['id_usuario']);
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $perfil = $_POST['perfil'];
    $status = $_POST['status_usuario'];

    // Verifica se o gerente digitou uma nova senha
    if (!empty($_POST['nova_senha'])) {
        $senha_hash = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome='$nome', email='$email', perfil='$perfil', status_usuario='$status', senha='$senha_hash' WHERE id_usuario = $id";
    } else {
        // Se a senha estiver vazia, não atualiza a coluna 'senha'
        $sql = "UPDATE usuarios SET nome='$nome', email='$email', perfil='$perfil', status_usuario='$status' WHERE id_usuario = $id";
    }

    if ($conn->query($sql)) {
        echo "<script>alert('Dados atualizados!'); window.location.href='UsuariosGestao.php';</script>";
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 12px; max-width: 500px; margin: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; }
        .btn { background: #7c3aed; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .info-senha { font-size: 0.8rem; color: #64748b; margin-top: -5px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Editar Funcionário</h2>
        <form method="POST" action="EditarUsuario.php?id=<?php echo $id; ?>">
            <input type="hidden" name="id_usuario" value="<?php echo $dados['id_usuario']; ?>">
            
            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo $dados['nome']; ?>" required>
            
            <label>E-mail:</label>
            <input type="email" name="email" value="<?php echo $dados['email']; ?>" required>
            
            <label>Perfil:</label>
            <select name="perfil">
                <option value="VENDEDOR" <?php if($dados['perfil'] == 'VENDEDOR') echo 'selected'; ?>>VENDEDOR</option>
                <option value="ESTOQUISTA" <?php if($dados['perfil'] == 'ESTOQUISTA') echo 'selected'; ?>>ESTOQUISTA</option>
                <option value="GERENTE" <?php if($dados['perfil'] == 'GERENTE') echo 'selected'; ?>>GERENTE</option>
            </select>

            <label>Status:</label>
            <select name="status_usuario">
                <option value="ATIVO" <?php if($dados['status_usuario'] == 'ATIVO') echo 'selected'; ?>>ATIVO</option>
                <option value="INATIVO" <?php if($dados['status_usuario'] == 'INATIVO') echo 'selected'; ?>>INATIVO</option>
            </select>

            <label>Nova Senha:</label>
            <input type="password" name="nova_senha" placeholder="Deixe em branco para não alterar">
            <p class="info-senha">A senha atual está criptografada e não pode ser visualizada.</p>
            
            <button type="submit" name="atualizar" class="btn">Atualizar Dados</button>
        </form>
        <a href="UsuariosGestao.php" style="display:block; text-align:center; margin-top:15px; text-decoration:none; color:#64748b;">Cancelar</a>
    </div>
</body>
</html>