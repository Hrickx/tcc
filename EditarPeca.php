<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';

$id = $_GET['id'];
$resultado = $conn->query("SELECT * FROM pecas WHERE id_peca = $id");
$peca = $resultado->fetch_assoc();

// Lógica de Update Protegida
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco_custo = $_POST['preco_custo'];

    // Se for GERENTE, ele atualiza tudo, incluindo o preço de venda
    if ($_SESSION['perfil'] === 'GERENTE') {
        $preco_venda = $_POST['preco_venda'];
        $sql = "UPDATE pecas SET 
                nome='$nome', 
                descricao='$descricao', 
                preco_custo='$preco_custo', 
                preco_venda='$preco_venda' 
                WHERE id_peca=$id";
    } else {
        // Se for ESTOQUISTA, o preço de venda NÃO entra no comando SQL
        $sql = "UPDATE pecas SET 
                nome='$nome', 
                descricao='$descricao', 
                preco_custo='$preco_custo' 
                WHERE id_peca=$id";
    }
    
    if ($conn->query($sql)) {
        // Redireciona de volta para a tela do funcionário correto
        $destino = ($_SESSION['perfil'] === 'GERENTE') ? 'gerente.php' : 'estoquista.php';
        header("Location: $destino?status=atualizado");
        exit();
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Peça</title>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; padding: 40px; }
        .form-container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); border: 1px solid #cbd5e1; }
        h2 { color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
        label { display: block; font-size: 0.8rem; font-weight: 600; margin-top: 15px; color: #64748b; }
        input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; background: #2563eb; color: white; padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 20px; }
        .voltar { display: block; margin-bottom: 20px; text-decoration: none; color: #64748b; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="form-container">
    <a href="estoquista.php" class="voltar">← Voltar</a>
    <h2>Editar Peça #<?php echo $id; ?></h2>
    
    <form method="POST">
        <label>Nome da Peça</label>
        <input type="text" name="nome" value="<?php echo $peca['nome']; ?>" required>

        <label>Descrição</label>
        <input type="text" name="descricao" value="<?php echo $peca['descricao']; ?>">

        <label>Preço de Custo (R$)</label>
        <input type="number" step="0.01" name="preco_custo" value="<?php echo $peca['preco_custo']; ?>" required>

        <label>Preço de Venda (R$)</label>
        <input type="number" step="0.01" name="preco_venda" value="<?php echo $peca['preco_venda']; ?>" 
            required <?php echo ($_SESSION['perfil'] !== 'GERENTE') ? 'readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #64748b;"' : ''; ?>>
        <?php if ($_SESSION['perfil'] !== 'GERENTE'): ?>
            <p style="color: #ef4444; font-size: 12px; margin-top: 5px;">* Somente o gerente pode alterar o preço de venda.</p>
        <?php endif; ?>

        <button type="submit">Salvar Alterações</button>
    </form>
</div>

</body>
</html>