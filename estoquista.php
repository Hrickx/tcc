<?php
session_start();
// Proteção de acesso: só ESTOQUISTA entra aqui
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'ESTOQUISTA') {
    header("Location: login.php");
    exit();
}
include 'conexao.php';

$id_estoquista = $_SESSION['id_usuario'];
$nome_estoquista = $_SESSION['nome'] ?? 'Estoquista';

// 1. Query Ninja: Calcula o saldo atual de cada peça somando/subtraindo movimentações
$sql_pecas = "SELECT p.id_peca, p.nome, p.preco_custo,
              COALESCE(SUM(CASE WHEN m.tipo = 'ENTRADA' THEN m.quantidade ELSE -m.quantidade END), 0) as saldo_atual
              FROM pecas p
              LEFT JOIN movimentacao_estoque m ON p.id_peca = m.id_peca
              GROUP BY p.id_peca, p.nome, p.preco_custo";
$res_pecas = $conn->query($sql_pecas);

// 2. Busca pedidos enviados por este estoquista (Feedback do fluxo de compras)
$sql_pedidos = "SELECT pc.*, p.nome as peca_nome, f.nome as fornecedor_nome 
                FROM pedido_compra pc
                JOIN pecas p ON pc.id_peca = p.id_peca
                JOIN fornecedor f ON pc.id_fornecedor = f.id_fornecedor
                WHERE pc.id_responsavel_estoque = '$id_estoquista'
                ORDER BY pc.id_pedido DESC";
$res_pedidos = $conn->query($sql_pedidos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Estoquista | AutoPeças Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
    /* [Seu CSS original mantido - está muito bom e limpo] */
    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
        font-family: 'Inter', sans-serif; 
    }

    body { 
        background-color: #f8fafc; 
        color: #1e293b; 
    }

    .navbar { 
        background-color: #1e293b; 
        color: white; 
        padding: 1rem 2rem; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }

    .container { 
        max-width: 1200px; 
        margin: 2rem auto; 
        padding: 0 1rem; 
    }

    .grid { 
        display: grid; 
        grid-template-columns: 1fr 350px; 
        gap: 2rem; 
    }

    @media (max-width: 900px) { 
        .grid { 
            grid-template-columns: 1fr; 
        } 
    }

    .card { 
        background: white; 
        padding: 1.5rem; 
        border-radius: 12px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
        margin-bottom: 2rem; 
    }

    h2 { 
        font-size: 1.1rem; 
        margin-bottom: 1.2rem; 
        color: #334155; 
        border-bottom: 2px solid #f1f5f9; 
        padding-bottom: 10px; 
    }

    table { 
        width: 100%; 
        border-collapse: collapse; 
        font-size: 0.9rem; 
    }

    th { 
        text-align: left; 
        padding: 12px; 
        border-bottom: 2px solid #e2e8f0; 
        color: #64748b; 
    }

    td { 
        padding: 12px; 
        border-bottom: 1px solid #f1f5f9; 
    }

    .status-rejeitado { 
        color: #ef4444; 
        font-weight: 600; 
    }

    .status-aprovado { 
        color: #10b981; 
        font-weight: 600; 
    }

    .status-pendente { 
        color: #f59e0b; 
        font-weight: 600; 
    }

    .status-concluido { 
        color: #64748b; 
        font-weight: 600; 
    }

    .form-group { 
        margin-bottom: 1rem; 
    }

    .form-group label { 
        display: block; 
        font-size: 0.8rem; 
        font-weight: 600; 
        margin-bottom: 5px; 
    }

    select, input { 
        width: 100%; 
        padding: 10px; 
        border: 1px solid #cbd5e1; 
        border-radius: 6px; 
    }

    .btn-enviar { 
        width: 100%; 
        background: #2563eb; 
        color: white; 
        padding: 12px; 
        border: none; 
        border-radius: 6px; 
        font-weight: 600; 
        cursor: pointer; 
        margin-top: 10px; 
    }

    .btn-confirmar { 
        background: #10b981; 
        color: white; 
        border: none; 
        padding: 6px 12px; 
        border-radius: 4px; 
        cursor: pointer; 
        font-size: 0.8rem; 
        font-weight: bold; 
    }

    /* Ações da Tabela (Editar e Excluir) */
    .btn-acao-edit { 
        color: #2563eb; 
        text-decoration: none; 
        font-weight: 600; 
        margin-right: 10px; 
    }

    .btn-acao-del { 
        color: #ef4444; 
        text-decoration: none; 
        font-weight: 600; 
    }

    .btn-acao-edit:hover, 
    .btn-acao-del:hover { 
        text-decoration: underline; 
    }

    .justificativa-box { 
        background: #fff1f2; 
        border: 1px solid #fecdd3; 
        padding: 8px; 
        border-radius: 4px; 
        margin-top: 5px; 
        font-size: 0.8rem; 
        color: #9f1239; 
    }

    .logout { 
        color: #1e293b; 
        background-color: #ffffff; 
        text-decoration: none; 
        font-size: 0.8rem; 
        font-weight: bold; 
        margin-left: 20px; 
        border: 1px solid #ffffff; 
        padding: 4px 10px; 
        border-radius: 4px; 
        transition: 0.3s; 
    }

    .logout:hover { 
        color: white; 
        background-color: transparent; 
        border-color: white; 
    }

    
</style>
</head>
<body>

    <nav class="navbar">
        <h1>AutoPeças Pro</h1>
        <div>
            <span style="background: #334155; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem;">ESTOQUISTA</span>
            <span style="margin-left: 15px;">Olá, <strong><?php echo $nome_estoquista; ?></strong></span>
            <a href="logout.php" class="logout">Sair</a>
        </div>
    </nav>

    <main class="container">
        <div class="grid">
            
            <div class="main-content">
                <section class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <h2 style="border: none; margin: 0;">📦 Monitoramento de Estoque Real</h2>
                        <a href="CadastrarPeca.php" style="background: #10b981; color: white; text-decoration: none; padding: 8px 15px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; transition: 0.3s;">
                             ➕ Novo Produto
                        </a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Peça</th>
                                <th>Saldo Atual</th>
                                <th>Situação</th>
                                <th>Ações</th> </tr>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if($res_pecas && $res_pecas->num_rows > 0):
                                $res_pecas->data_seek(0);
                                while($peca = $res_pecas->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $peca['nome']; ?></td>
                                        <td><strong><?php echo $peca['saldo_atual']; ?></strong> un</td>
                                        <td>
                                            <?php if($peca['saldo_atual'] <= 10): ?>
                                                <span style="color: #ef4444; font-weight: bold;">⚠️ Crítico</span>
                                            <?php else: ?>
                                                <span style="color: #10b981;">✅ Normal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="EditarPeca.php?id=<?php echo $peca['id_peca']; ?>" class="btn-acao-edit">Editar</a>
                                            <a href="ExcluirPeca.php?id=<?php echo $peca['id_peca']; ?>" 
                                               class="btn-acao-del" 
                                               onclick="return confirm('ATENÇÃO: Isso excluirá a peça e todo seu histórico de vendas e estoque. Deseja continuar?')">
                                               Excluir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; 
                            endif; ?>
                        </tbody>
                    </table>
                </section>

                <section class="card">
                    <h2>⏳ Status das Solicitações</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Peça</th>
                                <th>Qtd</th>
                                <th>Status / Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($ped = $res_pedidos->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $ped['id_pedido']; ?></td>
                                <td><?php echo $ped['peca_nome']; ?></td>
                                <td><?php echo $ped['quantidade']; ?></td>
                                <td>
                                    <?php if($ped['status'] == 'REJEITADO'): ?>
                                        <span class="status-rejeitado">❌ REJEITADO</span>
                                        <div class="justificativa-box">
                                            <strong>Motivo:</strong> <?php echo $ped['justificativa']; ?>
                                        </div>
                                    <?php elseif($ped['status'] == 'APROVADO'): ?>
                                        <span class="status-aprovado">✅ APROVADO</span>
                                        <form action="ConfirmarChegada.php" method="POST" style="margin-top:8px;">
                                            <input type="hidden" name="id_pedido" value="<?php echo $ped['id_pedido']; ?>">
                                            <button type="submit" class="btn-confirmar">Confirmar Entrega</button>
                                        </form>
                                    <?php elseif($ped['status'] == 'CONCLUÍDO'): ?>
                                        <span class="status-concluido">✔️ CONCLUÍDO</span>
                                    <?php else: ?>
                                        <span class="status-pendente">⏳ EM ANÁLISE</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>
            </div>

            <aside class="sidebar">
                <section class="card">
                    <h2>📝 Nova Solicitação</h2>
                    <form action="ProcessarPedido.php" method="POST">
                        <div class="form-group">
                            <label>Peça para Reposição</label>
                            <select name="id_peca" required>
                                <option value="">Selecione...</option>
                                <?php 
                                $res_pecas->data_seek(0);
                                while($p = $res_pecas->fetch_assoc()): ?>
                                    <option value="<?= $p['id_peca'] ?>">
                                        <?= $p['nome'] ?> (<?= $p['saldo_atual'] ?> un)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Fornecedor</label>
                            <select name="id_fornecedor" required>
                                <?php
                                $sql_forn = "SELECT * FROM fornecedor";
                                $res_f = $conn->query($sql_forn);
                                while($f = $res_f->fetch_assoc()): ?>
                                    <option value="<?= $f['id_fornecedor'] ?>"><?= $f['nome'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantidade</label>
                            <input type="number" name="quantidade" min="1" required>
                        </div>

                        <div class="form-group">
                            <label>Prazo Esperado (Dias)</label>
                            <input type="number" name="tempo_entrega" min="1" required>
                        </div>

                        <button type="submit" class="btn-enviar">Solicitar Compra</button>
                    </form>
                </section>
            </aside>

        </div>
    </main>

    <script>
    // Atualiza a página do estoquista a cada 30 segundos 
    // para ele ver se o Ricardo já aprovou
    setInterval(function(){
        location.reload();
    }, 30000); 
</script>

</body>
</html>