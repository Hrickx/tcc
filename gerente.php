<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    header("Location: login.php");
    exit();
}
include 'conexao.php';


$nome_gerente = $_SESSION['nome'] ?? 'Gerente';

// 1. Query para o Giro: Peças mais vendidas
$sql_giro = "SELECT p.nome, SUM(iv.quantidade) as total_vendido 
             FROM itens_venda iv 
             JOIN pecas p ON iv.id_peca = p.id_peca 
             GROUP BY p.id_peca 
             ORDER BY total_vendido DESC LIMIT 3";
$res_giro = $conn->query($sql_giro);

// 2. Query para a Tabela de Pedidos Pendentes
$sql_pedidos = "SELECT 
                    pc.id_pedido, 
                    u.nome as solicitante, 
                    f.nome as fornecedor, 
                    p.nome as peca_nome, 
                    pc.status, 
                    pc.observacao
                FROM pedido_compra pc
                LEFT JOIN usuarios u ON pc.id_responsavel_estoque = u.id_usuario
                LEFT JOIN fornecedor f ON pc.id_fornecedor = f.id_fornecedor
                LEFT JOIN pecas p ON pc.id_peca = p.id_peca
                WHERE pc.status = 'PENDENTE'
                ORDER BY pc.id_pedido DESC";
$res_pedidos = $conn->query($sql_pedidos);

// 3. Query para os Indicadores
$sql_lucro = "SELECT SUM(valor_total) as total FROM vendas WHERE status = 'FINALIZADA'"; 
$res_lucro = $conn->query($sql_lucro)->fetch_assoc();
$valor_lucro = $res_lucro['total'] ?? 0;

$sql_qtd_pendentes = "SELECT COUNT(*) as total FROM pedido_compra WHERE status = 'PENDENTE'";
$res_qtd_pendentes = $conn->query($sql_qtd_pendentes)->fetch_assoc();
$qtd_pendentes = $res_qtd_pendentes['total'] ?? 0;

$sql_critico = "SELECT p.id_peca FROM pecas p 
                LEFT JOIN movimentacao_estoque m ON p.id_peca = m.id_peca 
                GROUP BY p.id_peca 
                HAVING SUM(CASE WHEN m.tipo = 'ENTRADA' THEN m.quantidade ELSE -m.quantidade END) < 10";
$res_critico = $conn->query($sql_critico);
$qtd_critico = $res_critico->num_rows;

$sql_vendas_dia = "SELECT COUNT(*) as total FROM vendas WHERE DATE(data_venda) = CURDATE()";
$res_vendas_dia = $conn->query($sql_vendas_dia)->fetch_assoc();
$vendas_hoje = $res_vendas_dia['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Gerencial | AutoPeças Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
    /* Configurações Globais */
    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
        font-family: 'Inter', sans-serif; 
    }

    body { 
        background-color: #f1f5f9; 
        color: #1e293b; 
        overflow-x: hidden; 
    }

    /* Barra de Navegação (Navbar) */
    .navbar {
        background-color: #7c3aed;
        color: white;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        /* Sombra da Navbar mais visível (de 0.1 para 0.25) */
        box-shadow: 0 4px 8px -1px rgba(0, 0, 0, 0.25);
    }

    .nav-right { 
        display: flex; 
        align-items: center; 
        gap: 20px; 
    }

    .badge-gerente { 
        background: #ffffff33; 
        padding: 5px 12px; 
        border-radius: 20px; 
        font-size: 0.85rem; 
        font-weight: bold; 
        border: 1px solid white; 
    }

    /* Botões da Navbar e Sair */
    .btn-relatorio {
        background: #ffffff;
        color: #7c3aed;
        padding: 8px 15px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn-relatorio:hover { 
        background: #f3f4f6; 
    }

    .logout { 
        color: #6a1b9a; 
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

    /* Notificações e Toast */
    .notification-bell { 
        position: relative; 
        cursor: pointer; 
        font-size: 1.2rem; 
    }

    .bell-dot { 
        position: absolute; 
        top: -2px; 
        right: -2px; 
        width: 10px; 
        height: 10px; 
        background: #ef4444; 
        border-radius: 50%; 
        display: none; 
        border: 2px solid #7c3aed; 
    }

    .toast-container { 
        position: fixed; 
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
    }

    .toast { 
        background: white; 
        border-left: 5px solid #7c3aed; 
        padding: 15px 20px; 
        border-radius: 8px; 
        /* Sombra do Toast mais forte para se destacar do fundo */
        box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.3); 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        margin-bottom: 10px; 
        transform: translateX(120%); 
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
    }

    .toast.show { 
        transform: translateX(0); 
    }

    .toast-content b { 
        display: block; 
        font-size: 0.9rem; 
        color: #7c3aed; 
    }

    .toast-content p { 
        font-size: 0.8rem; 
        color: #64748b; 
    }

    /* Estrutura de Layout (Cards e Containers) */
    .container { 
        max-width: 1200px; 
        margin: 2rem auto; 
        padding: 0 1rem; 
    }

    .card { 
        background: white; 
        padding: 1.5rem; 
        border-radius: 12px; 
        /* Sombra dos Cards aumentada (de 0.1 para 0.2) para dar mais profundidade */
        box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.2); 
        margin-bottom: 2rem; 
    }

    h2 { 
        font-size: 1.1rem; 
        margin-bottom: 1.2rem; 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        color: #1e293b; 
        border-bottom: 2px solid #f1f5f9; 
        padding-bottom: 10px; 
    }

    /* Estatísticas e Rankings */
    .stats-container { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 1.5rem; 
        margin-top: 1rem; 
    }

    .ranking-item { 
        display: flex; 
        align-items: center; 
        margin-bottom: 1rem; 
        gap: 10px; 
    }

    .bar-container { 
        flex-grow: 1; 
        background: #e2e8f0; 
        border-radius: 10px; 
        height: 12px; 
        overflow: hidden; 
    }

    .bar-fill { 
        height: 100%; 
        background: #7c3aed; 
        border-radius: 10px; 
    }

    .qtd-badge { 
        font-weight: bold; 
        color: #7c3aed; 
        min-width: 30px; 
        text-align: right; 
    }

    /* Tabelas e Ações */
    table { 
        width: 100%; 
        border-collapse: collapse; 
    }

    th { 
        text-align: left; 
        background: #f8fafc; 
        padding: 0.8rem; 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        color: #64748b; 
    }

    td { 
        padding: 0.8rem; 
        border-bottom: 1px solid #e2e8f0; 
        font-size: 0.85rem; 
    }

    .btn-aprovar { 
        background: #10b981; 
        color: white; 
        border: none; 
        padding: 6px 12px; 
        border-radius: 6px; 
        cursor: pointer; 
        font-weight: bold; 
    }

    .btn-rejeitar { 
        background: #ef4444; 
        color: white; 
        border: none; 
        padding: 6px 12px; 
        border-radius: 6px; 
        cursor: pointer; 
        font-weight: bold; 
    }
</style>
</head>
<body>

    <div id="toastContainer" class="toast-container"></div>

    <nav class="navbar">
    <h1>AutoPeças Pro</h1>
    <div class="nav-right">
       
        <a href="UsuariosGestao.php" class="btn-relatorio" style="background: #ffffff; color: #7c3aed; margin-right: 10px;">👤 Gerenciar Usuários</a>  

        <a href="RelatorioMovimentacao.php" class="btn-relatorio">📊 Ver Movimentação de Estoque</a>
        
        <div class="notification-bell">
            🔔<span id="bellDot" class="bell-dot"></span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="badge-gerente" style="text-transform: uppercase; font-size: 0.7rem;">
                <?php echo $_SESSION['perfil']; ?>
            </span>
            <span style="font-size: 0.95rem; font-weight: 600; color: white;">
               <span style="margin-left: 15px;">Olá, <strong><?php echo $nome_gerente; ?></strong></span>
            </span>
             <a href="logout.php" class="logout">Sair</a>
        </div>
    </div>
</nav>

    <main class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px; margin-bottom: 30px;">
            <div class="card" style="border-left: 5px solid #2563eb; margin-bottom:0;">
                <p style="color: #64748b; font-size: 0.75rem; font-weight: bold;">LUCRO TOTAL</p>
                <h2 id="lucroDia">R$ 0,00</h2>
            </div>
            <div class="card" style="border-left: 5px solid #10b981; margin-bottom:0;">
                <p style="color: #64748b; font-size: 0.75rem; font-weight: bold;">VENDAS HOJE</p>
                <h2 id="vendasQtd">0</h2>
            </div>
            <div class="card" style="border-left: 5px solid #f59e0b; margin-bottom:0;">
                <p style="color: #64748b; font-size: 0.75rem; font-weight: bold;">PEDIDOS PENDENTES</p>
                <h2 id="pedidosPendentes">0</h2>
            </div>
            <div class="card" style="border-left: 5px solid #ef4444; margin-bottom:0;">
                <p style="color: #64748b; font-size: 0.75rem; font-weight: bold;">ESTOQUE CRÍTICO</p>
                <h2 id="estoqueCritico">0</h2>
            </div>
        </div>

        <section class="card">
            <h2>📊 Giro de Estoque (Mais Vendidas)</h2>
            <div class="stats-container">
                <div>
                    <?php while($row = $res_giro->fetch_assoc()): 
                        $porcentagem = min($row['total_vendido'] * 10, 100); ?>
                        <div class="ranking-item">
                            <span style="width: 150px; font-size: 0.85rem;"><?php echo $row['nome']; ?></span>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: <?php echo $porcentagem; ?>%;"></div>
                            </div>
                            <span class="qtd-badge"><?php echo $row['total_vendido']; ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; font-size: 0.85rem; border-left: 4px solid #7c3aed;">
                    <p>Relatório baseado nas vendas finalizadas. O gráfico ajuda a decidir quais pedidos de compra priorizar.</p>
                </div>
            </div>
        </section>

        <section class="card">
            <h2>⚖️ Pedidos de Compra Aguardando Decisão</h2>
            <table>
                <thead>
                    <tr>
                        <th>Solicitante</th>
                        <th>Peça / Fornecedor</th>
                        <th>Observação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="listaPedidos">
                    <?php if($res_pedidos->num_rows > 0): ?>
                        <?php while($ped = $res_pedidos->fetch_assoc()): ?>
                            <tr id="pedido_<?php echo $ped['id_pedido']; ?>">
                                <td><?php echo htmlspecialchars($ped['solicitante']); ?></td>
                                <td><strong><?php echo htmlspecialchars($ped['peca_nome']); ?></strong><br><small><?php echo htmlspecialchars($ped['fornecedor']); ?></small></td>
                                <td><?php echo htmlspecialchars($ped['observacao']); ?></td>
                                <td>
                                    <button class="btn-aprovar" onclick="decidir('aprovar', <?php echo $ped['id_pedido']; ?>)">Aprovar</button>
                                    <button class="btn-rejeitar" onclick="mostrarRejeicao(<?php echo $ped['id_pedido']; ?>)">Rejeitar</button>
                                    
                                    <div id="rejeicao<?php echo $ped['id_pedido']; ?>" style="display:none; margin-top:10px;">
                                        <input type="text" id="justificativa<?php echo $ped['id_pedido']; ?>" placeholder="Motivo..." style="padding:5px; border-radius:4px; border:1px solid #ccc;">
                                        <button class="btn-aprovar" style="padding: 4px" onclick="decidir('rejeitar', <?php echo $ped['id_pedido']; ?>)">Confirmar</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;">Nenhum pedido pendente no momento.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
    function dispararNotificacao(solicitante, peca) {
        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
        audio.play().catch(e => console.log("Áudio bloqueado."));

        const container = document.getElementById('toastContainer');
        const bellDot = document.getElementById('bellDot');
        
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <div style="font-size: 1.5rem;">📦</div>
            <div class="toast-content">
                <b>Novo Pedido de Compra!</b>
                <p>${solicitante} solicitou ${peca}</p>
            </div>
        `;
        
        container.appendChild(toast);
        bellDot.style.display = 'block';
        setTimeout(() => toast.classList.add('show'), 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

    function mostrarRejeicao(id) { 
        document.getElementById('rejeicao' + id).style.display = 'block'; 
    }

    function decidir(tipo, id) {
        const just = document.getElementById('justificativa' + id)?.value || '';
        const acaoFormatada = tipo === 'aprovar' ? 'APROVADO' : 'REJEITADO';

        if (tipo === 'rejeitar' && !just) { 
            alert("Por favor, informe o motivo da rejeição."); 
            return; 
        }

        fetch('DecidirPedido.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_pedido=${id}&acao=${acaoFormatada}&justificativa=${just}`
        })
        .then(res => res.json())
        .then(dados => {
            if(dados.success) {
                alert("Pedido processado!");
                location.reload();
            }
        });
    }

    async function verificarNovosPedidos() {
        try {
            const resposta = await fetch('checarpedidosajax.php?t=' + Date.now());
            if (!resposta.ok) return;
            const dados = await resposta.json();

            if (dados.tem_novo) {
                dispararNotificacao(dados.solicitante, dados.peca);
                setTimeout(() => { location.reload(); }, 3000);
            }
        } catch (erro) { }
    }

    setInterval(verificarNovosPedidos, 5000);

    document.getElementById('lucroDia').innerText = "R$ <?php echo number_format($valor_lucro, 2, ',', '.'); ?>";
    document.getElementById('vendasQtd').innerText = "<?php echo $vendas_hoje; ?>";
    document.getElementById('pedidosPendentes').innerText = "<?php echo $qtd_pendentes; ?>";
    document.getElementById('estoqueCritico').innerText = "<?php echo $qtd_critico; ?>";
    </script>
</body>
</html>