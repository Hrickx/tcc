<?php
// Proteção de Sessão e Configurações (Padronizado)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM pecas ORDER BY nome ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Preços | Painel Gerencial</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --success: #059669;
            --danger: #dc2626;
            --bg: #f8fafc;
            --text: #1e293b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn-voltar {
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            transition: color 0.2s;
        }

        .btn-voltar:hover { color: var(--primary); }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            padding: 15px;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
        }

        tr:hover { background-color: #f8fafc; }

        .badge {
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .btn-edit {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .btn-edit:hover { opacity: 0.9; }

        .price-tag { font-family: monospace; font-weight: 600; font-size: 1.05rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-actions">
        <div>
            <h1 style="margin:0; font-size: 1.75rem;">📦 Gestão de Estoque</h1>
            <p style="color: #64748b; margin: 5px 0 0;">Controle financeiro e precificação de produtos</p>
        </div>
        <a href="gerente.php" class="btn-voltar">← Painel Principal</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Produto / Descrição</th>
                    <th>Custo Unit.</th>
                    <th>Preço de Venda</th>
                    <th>Margem de Lucro</th>
                    <th style="text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    $margem = $row['preco_venda'] - $row['preco_custo'];
                    $porcentagem = ($row['preco_custo'] > 0) ? ($margem / $row['preco_custo']) * 100 : 0;
                    $classe = ($margem > 0) ? 'badge-success' : 'badge-danger';
                ?>
                <tr>
                    <td>
                        <strong><?php echo $row['nome']; ?></strong><br>
                        <small style="color: #94a3b8;"><?php echo substr($row['descricao'], 0, 40); ?>...</small>
                    </td>
                    <td class="price-tag">R$ <?php echo number_format($row['preco_custo'], 2, ',', '.'); ?></td>
                    <td class="price-tag" style="color: var(--primary);">R$ <?php echo number_format($row['preco_venda'], 2, ',', '.'); ?></td>
                    <td>
                        <span class="badge <?php echo $classe; ?>">
                            <?php echo ($margem >= 0 ? '+' : '') . 'R$ ' . number_format($margem, 2, ',', '.'); ?> 
                            (<?php echo number_format($porcentagem, 1); ?>%)
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <a href="EditarPeca.php?id=<?php echo $row['id_peca']; ?>" class="btn-edit"> Precificar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Ping para manter a sessão do Gerente viva
    setInterval(() => { fetch('ping.php'); }, 60000);
</script>

</body>
</html>