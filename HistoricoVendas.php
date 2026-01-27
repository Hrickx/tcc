<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    header("Location: login.php");
    exit();
}

// Query que junta Vendas, Itens, Peças, Usuários (vendedor) e Pagamentos
$sql = "SELECT 
            v.id_venda, 
            u.nome AS vendedor, 
            p.nome AS peca, 
            iv.quantidade, 
            v.valor_total, 
            pg.forma_pagamento, 
            v.data_venda  
        FROM vendas v
        JOIN usuarios u ON v.id_vendedor = u.id_usuario
        JOIN itens_venda iv ON v.id_venda = iv.id_venda
        JOIN pecas p ON iv.id_peca = p.id_peca
        LEFT JOIN pagamento pg ON v.id_venda = pg.id_venda 
        ORDER BY v.data_venda DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Vendas | AutoPeças Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #7c3aed; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 12px; text-align: left; color: #64748b; border-bottom: 2px solid #e2e8f0; font-size: 0.8rem; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
        .badge-pgto { background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 6px; font-weight: bold; font-size: 0.75rem; }
        .btn-voltar { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #7c3aed; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <a href="gerente.php" class="btn-voltar">← Voltar ao Painel</a>
        <h1>🛒 Histórico Geral de Vendas</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendedor</th>
                    <th>Peça</th>
                    <th>Qtd</th>
                    <th>Total</th>
                    <th>Pagamento</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id_venda']; ?></td>
                    <td><strong><?php echo $row['vendedor']; ?></strong></td>
                    <td><?php echo $row['peca']; ?></td>
                    <td><?php echo $row['quantidade']; ?></td>
                    <td>R$ <?php echo number_format($row['valor_total'], 2, ',', '.'); ?></td>
                    <td><span class="badge-pgto"><?php echo $row['forma_pagamento'] ?? 'Pendente'; ?></span></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['data_venda'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>