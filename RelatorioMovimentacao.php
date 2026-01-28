<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';

// Proteção: Apenas Gerente acessa
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    die("Acesso negado.");
}

// Busca o histórico de movimentação cruzando com a tabela de peças
$sql = "SELECT m.*, p.nome as peca_nome 
        FROM movimentacao_estoque m
        JOIN pecas p ON m.id_peca = p.id_peca
        ORDER BY m.data_movimentacao DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Estoque | AutoPeças Pro</title>
    <style>
    /* Configurações Globais e Corpo */
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        background: #f8fafc; 
        padding: 30px; 
    }

    /* Container Principal */
    .container { 
        max-width: 1000px; 
        margin: auto; 
        background: white; 
        padding: 20px; 
        border-radius: 8px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
    }

    /* Estilização da Tabela */
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 20px; 
    }

    th, td { 
        padding: 12px; 
        text-align: left; 
        border-bottom: 1px solid #e2e8f0; 
    }

    th { 
        background-color: #1e293b; 
        color: white; 
    }

    /* Status e Tipos de Movimentação */
    .tipo-entrada { 
        color: #10b981; 
        font-weight: bold; 
    }

    .tipo-saida { 
        color: #ef4444; 
        font-weight: bold; 
    }

    /* Links de Navegação */
    .voltar { 
        text-decoration: none; 
        color: #64748b; 
        font-weight: bold; 
        margin-bottom: 20px; 
        display: inline-block; 
    }
</style>
</head>
<body>

<div class="container">
    <a href="gerente.php" class="voltar">← Voltar ao Painel</a>
    <h1>📊 Relatório de Movimentação de Estoque</h1>
    <p>Acompanhe todas as entradas e saídas de produtos em tempo real.</p>

    <table>
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Peça</th>
                <th>Quantidade</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['data_movimentacao'])); ?></td>
                    <td><?php echo $row['peca_nome']; ?></td>
                    <td><?php echo $row['quantidade']; ?></td>
                    <td class="<?php echo ($row['tipo'] == 'ENTRADA') ? 'tipo-entrada' : 'tipo-saida'; ?>">
                        <?php echo $row['tipo']; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
