<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';

// Proteção: Apenas Gerente
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    header("Location: login.html");
    exit();
}

// Busca pedidos que NÃO estão mais pendentes
$sql = "SELECT pc.*, p.nome as peca_nome, u.nome as estoquista_nome, f.nome as fornecedor_nome
        FROM pedido_compra pc
        JOIN pecas p ON pc.id_peca = p.id_peca
        JOIN usuarios u ON pc.id_responsavel_estoque = u.id_usuario
        JOIN fornecedor f ON pc.id_fornecedor = f.id_fornecedor
        WHERE pc.status != 'PENDENTE'
        ORDER BY pc.data_pedido_status DESC";

$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Decisões | AutoPeças Pro</title>
   <style>
    /* Configurações de Página e Texto */
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        background: #f4f7f6; 
        padding: 20px; 
        color: #333; 
    }

    .container { 
        max-width: 1100px; 
        margin: auto; 
    }

    h1 { 
        color: #1e293b; 
        border-bottom: 2px solid #ddd; 
        padding-bottom: 10px; 
    }

    /* Estilização da Tabela de Histórico */
    .tabela-historico { 
        width: 100%; 
        border-collapse: collapse; 
        background: white; 
        border-radius: 8px; 
        overflow: hidden; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        margin-top: 20px; 
    }

    th, td { 
        padding: 15px; 
        text-align: left; 
        border-bottom: 1px solid #eee; 
    }

    th { 
        background: #1e293b; 
        color: white; 
        font-size: 0.9rem; 
        text-transform: uppercase; 
    }

    tr:hover { 
        background-color: #f8fafc; 
    }

    /* Badges de Status */
    .status-badge { 
        padding: 6px 12px; 
        border-radius: 20px; 
        font-size: 0.75rem; 
        font-weight: bold; 
        display: inline-block; 
    }

    .status-APROVADO { 
        background: #dcfce7; 
        color: #166534; 
    }

    .status-REJEITADO { 
        background: #fee2e2; 
        color: #991b1b; 
    }

    .status-CONCLUÍDO { 
        background: #dbeafe; 
        color: #1e40af; 
    }

    /* Elementos de Texto e Navegação */
    .justificativa-text { 
        font-style: italic; 
        color: #64748b; 
        font-size: 0.85rem; 
        display: block; 
        margin-top: 5px; 
    }

    .btn-voltar { 
        display: inline-block; 
        margin-bottom: 15px; 
        text-decoration: none; 
        color: #3b82f6; 
        font-weight: bold; 
    }
</style>
</head>
<body>
    <div class="container">
        <a href="gerente.php" class="btn-voltar">← Voltar ao Painel</a>
        <h1>📜 Histórico de Pedidos de Compra</h1>

        <table class="tabela-historico">
            <thead>
                <tr>
                    <th>Data Decisão</th>
                    <th>Estoquista</th>
                    <th>Item / Fornecedor</th>
                    <th>Qtd</th>
                    <th>Valor Total</th>
                    <th>Status</th>
                    <th>Observações/Justificativa</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['data_pedido_status'])); ?></td>
                    <td><?php echo htmlspecialchars($row['estoquista_nome']); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['peca_nome']); ?></strong><br>
                        <small style="color: #666;"><?php echo htmlspecialchars($row['fornecedor_nome']); ?></small>
                    </td>
                    <td><?php echo $row['quantidade']; ?></td>
                    <td style="font-weight: bold;">R$ <?php echo number_format($row['valor_total_compra'], 2, ',', '.'); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if($row['status'] == 'REJEITADO'): ?>
                            <span class="justificativa-text"><strong>Motivo:</strong> <?php echo htmlspecialchars($row['justificativa']); ?></span>
                        <?php elseif($row['status'] == 'CONCLUÍDO'): ?>
                            <span style="color: #10b981; font-size: 0.85rem;">✔️ Recebido no estoque</span>
                        <?php elseif($row['status'] == 'APROVADO'): ?>
                            <span style="color: #f59e0b; font-size: 0.85rem;">⏳ Aguardando entrega</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>