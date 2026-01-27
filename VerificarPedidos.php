<?php
session_start();
include 'conexao.php';

// Proteção: Apenas Gerente acessa
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    header("Location: login.php");
    exit();
}

// Query com os nomes de colunas que definimos no banco e no processamento
$sql = "SELECT pc.*, u.nome as solicitante, pec.nome as peca_nome, f.nome as fornecedor_nome
        FROM pedido_compra pc
        JOIN usuarios u ON pc.id_responsavel_estoque = u.id_usuario
        JOIN pecas pec ON pc.id_peca = pec.id_peca
        JOIN fornecedor f ON pc.id_fornecedor = f.id_fornecedor
        WHERE pc.status = 'PENDENTE' 
        ORDER BY pc.id_pedido DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Aprovação de Pedidos | AutoPeças Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
   <style>
    /* Configurações de Página */
    body { 
        font-family: 'Inter', sans-serif; 
        background-color: #f1f5f9; 
        padding: 20px; 
    }

    .container { 
        max-width: 800px; 
        margin: auto; 
    }

    /* Card e Títulos */
    .card-pedido { 
        background: white; 
        border-radius: 12px; 
        padding: 20px; 
        margin-bottom: 20px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
        border-left: 6px solid #7c3aed; 
    }

    h1 { 
        color: #1e293b; 
        margin-bottom: 30px; 
    }

    h3 { 
        color: #7c3aed; 
        margin-bottom: 10px; 
    }

    /* Informações e Linhas */
    .info-row { 
        display: flex; 
        gap: 20px; 
        margin: 15px 0; 
        background: #f8fafc; 
        padding: 10px; 
        border-radius: 8px; 
        font-size: 0.9rem; 
    }

    /* Botões de Ação */
    .btn-aprovar { 
        background: #10b981; 
        color: white; 
        padding: 12px 24px; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer; 
        font-weight: bold; 
    }

    .btn-rejeitar { 
        background: #ef4444; 
        color: white; 
        padding: 12px 24px; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer; 
        font-weight: bold; 
    }

    /* Área de Justificativa e Formulário */
    .justificativa { 
        width: 100%; 
        margin-top: 15px; 
        padding: 15px; 
        display: none; 
        background: #fff1f2; 
        border-radius: 8px; 
        border: 1px solid #fecdd3; 
    }

    textarea { 
        width: 100%; 
        border: 1px solid #cbd5e1; 
        border-radius: 4px; 
        padding: 10px; 
        margin: 10px 0; 
    }

    /* Links e Navegação */
    .voltar { 
        display: inline-block; 
        margin-bottom: 20px; 
        text-decoration: none; 
        color: #64748b; 
        font-weight: 600; 
    }
</style>
</head>
<body>
    <div class="container">
        <a href="gerente.php" class="voltar">← Voltar ao Painel</a>
        <h1>🔔 Pedidos de Compra Pendentes</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($pedido = $result->fetch_assoc()): ?>
                <div class="card-pedido">
                    <h3>Pedido #<?php echo $pedido['id_pedido']; ?> - <?php echo $pedido['peca_nome']; ?></h3>
                    <p><strong>Solicitante:</strong> <?php echo $pedido['solicitante']; ?></p>
                    <p><strong>Fornecedor:</strong> <?php echo $pedido['fornecedor_nome']; ?></p>
                    
                   <div class="info-row">
                        <span><strong>Qtd:</strong> <?php echo $pedido['quantidade']; ?></span>
                        <span><strong>Unit:</strong> R$ <?php echo number_format($pedido['valor_unitario'], 2, ',', '.'); ?></span>
                        <span style="color: #10b981; font-weight: bold;">
                            <strong>Total:</strong> R$ <?php echo number_format($pedido['valor_total_compra'], 2, ',', '.'); ?>
                        </span>
                    </div>

                    <form action="DecidirPedido.php" method="POST">
                        <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                        
                        <button type="submit" name="acao" value="APROVAR" class="btn-aprovar">✅ Aprovar Pedido</button>
                        <button type="button" class="btn-rejeitar" onclick="mostrarJustificativa(<?php echo $pedido['id_pedido']; ?>)">❌ Rejeitar</button>
                        
                        <div id="div-recusa-<?php echo $pedido['id_pedido']; ?>" class="justificativa">
                            <label><strong>Motivo da Recusa:</strong></label>
                            <textarea name="justificativa" rows="2" placeholder="Explique por que o pedido foi negado..."></textarea>
                            <button type="submit" name="acao" value="REJEITAR" class="btn-rejeitar" style="width: 100%;">Confirmar Rejeição</button>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card-pedido" style="border-left-color: #cbd5e1;">
                <p>Nenhum pedido aguardando aprovação.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function mostrarJustificativa(id) {
            const div = document.getElementById('div-recusa-' + id);
            div.style.display = div.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>