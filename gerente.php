<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão
session_start();

// 3. Inclui a conexão
include 'conexao.php';

// 4. Proteção de acesso inteligente
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    // Se for uma requisição do JavaScript (AJAX), manda erro 401 em vez de redirecionar com header
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        header('HTTP/1.1 401 Unauthorized');
        exit;
    } 
    // Se for acesso direto pela URL, vai para o login
    header("Location: login.php");
    exit();
} 

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

    /* Efeito de clique (diminui levemente) */
.btn-aprovar:active, .btn-rejeitar:active {
    transform: scale(0.95);
}

/* Classe para quando o botão estiver processando */
.btn-loading {
    opacity: 0.7;
    cursor: not-allowed;
    position: relative;
    pointer-events: none; /* Impede cliques duplos */
}

/* Opcional: Uma animação suave de transição */
.btn-aprovar, .btn-rejeitar {
    transition: all 0.2s ease;
}

/* Container do Dropdown */
.dropdown {
    position: relative;
    display: inline-block;
    padding-bottom: 10px; /* Cria uma área de respiro para o mouse não sair do elemento */
    margin-bottom: -10px; /* Compensa o padding para não empurrar outros elementos */
}

/* Botão Principal do Menu */
.dropbtn {
    background-color: #ffffff33;
    color: white;
    padding: 8px 15px;
    font-size: 0.85rem;
    font-weight: bold;
    border: 1px solid white;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

.dropbtn:hover {
    background-color: #ffffff55;
}

/* Conteúdo do Dropdown */
.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 220px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1000;
    border-radius: 8px;
    /* Alterado: em vez de margin, usamos top para encostar no botão */
    top: 100%; 
    overflow: hidden;
}

/* O TRUQUE: Ponte invisível entre o botão e o menu */
.dropdown-content::before {
    content: "";
    position: absolute;
    top: -10px; /* Cobre o espaço vazio acima do menu */
    left: 0;
    width: 100%;
    height: 10px;
    background: transparent;
}

/* Links dentro do menu */
.dropdown-content a {
    color: #1e293b;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    font-size: 0.85rem;
    border-bottom: 1px solid #f1f5f9;
}

.dropdown-content a:hover {
    background-color: #f8fafc;
    color: #7c3aed;
}

/* Mostrar o menu ao passar o mouse */
.dropdown:hover .dropdown-content {
    display: block;
}
</style>
</head>
<body>

    <div id="toastContainer" class="toast-container"></div>

    <nav class="navbar">
    <h1>AutoPeças Pro</h1>
    <div class="nav-right">
       
        <div class="dropdown">
            <button class="dropbtn"> Opções ▾</button>
            <div class="dropdown-content">
                <a href="HistoricoVendas.php">🛒 Histórico de Vendas</a>
                <a href="RelatorioMovimentacao.php">📊 Movimentação de Estoque</a>
                <a href="UsuariosGestao.php">👤 Gerenciar Usuários</a>
                <a href="GerenciarEstoque.php">💰 Gestão de Preços</a>
            </div>
        </div>
        
        <div class="notification-bell">
            🔔<span id="bellDot" class="bell-dot" style="display: <?php echo ($qtd_pendentes > 0) ? 'block' : 'none'; ?>;"></span>
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
                                    <button class="btn-aprovar" onclick="executarDecisao('aprovar', <?php echo $ped['id_pedido']; ?>, this)">Aprovar</button>
                                    <button class="btn-rejeitar" onclick="mostrarRejeicao(<?php echo $ped['id_pedido']; ?>)">Rejeitar</button>

                                    <div id="rejeicao<?php echo $ped['id_pedido']; ?>" style="display:none; margin-top:10px;">
                                        <input type="text" id="justificativa<?php echo $ped['id_pedido']; ?>" placeholder="Motivo..." style="padding:5px; border-radius:4px; border:1px solid #ccc;">
                                        <button class="btn-aprovar" style="padding: 4px" onclick="executarDecisao('rejeitar', <?php echo $ped['id_pedido']; ?>, this)">Confirmar</button>
                                    </div>
                                    
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

    // Função simples para mostrar a caixa de texto
function mostrarRejeicao(id) {
    const div = document.getElementById('rejeicao' + id);
    if (div) div.style.display = 'block';
}

// Função única para processar a aprovação ou rejeição
function executarDecisao(tipo, id, btn) {
    const justInput = document.getElementById('justificativa' + id);
    const just = justInput ? justInput.value : '';
    const acaoFinal = (tipo === 'aprovar') ? 'APROVADO' : 'REJEITADO';

    if (tipo === 'rejeitar' && !just) {
        alert("Por favor, informe o motivo da rejeição.");
        return;
    }

    // Desabilita o botão para evitar cliques duplos
    btn.disabled = true;
    btn.innerText = '...';

    const formData = new URLSearchParams();
    formData.append('id_pedido', id);
    formData.append('acao', acaoFinal);
    formData.append('justificativa', just);

    fetch('DecidirPedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(dados => {
        if (dados.success) {
            const linha = document.getElementById('pedido_' + id);
            if (linha) {
                linha.style.opacity = '0';
                setTimeout(() => {
                    linha.remove();
                    atualizarContadores(); // Diminui o número no card
                }, 500);
            }
        } else {
            alert("Erro: " + dados.error);
            btn.disabled = false;
            btn.innerText = (tipo === 'aprovar') ? 'Aprovar' : 'Confirmar';
        }
    })
    .catch(err => {
        alert("Erro de conexão.");
        btn.disabled = false;
    });
}
    
    // Função auxiliar para baixar o número no card de "Pendentes"
    function atualizarContadores() {
        const elemento = document.getElementById('pedidosPendentes');
        if (elemento) {
            let atual = parseInt(elemento.innerText) || 0;
            if (atual > 0) elemento.innerText = atual - 1;
        }
    }

   async function verificarNovosPedidos() {
        try {
            const resposta = await fetch('ChecarPedidosAjax.php?t=' + Date.now());
            const dados = await resposta.json();

            // 1. Atualiza o contador no card vermelho (sem piscar)
            const elementoContador = document.getElementById('pedidosPendentes');
            const totalAtual = parseInt(elementoContador.innerText);
            elementoContador.innerText = dados.total;

            // 2. Se o número de pedidos aumentou, dispara o som e o sino
            if (dados.total > totalAtual) {
                dispararNotificacao(dados.pedidos[0].solicitante, dados.pedidos[0].peca_nome);
            }

            // 3. ATUALIZA A TABELA DINAMICAMENTE (O pulo do gato)
            renderizarTabela(dados.pedidos);

        } catch (erro) { console.error("Erro na checagem"); }
    }

   function renderizarTabela(pedidos) {
        const tbody = document.getElementById('listaPedidos');
        
        // Não atualiza se o gerente estiver escrevendo uma justificativa
        const camposAbertos = document.querySelectorAll('div[id^="rejeicao"][style*="display: block"]');
        if (camposAbertos.length > 0) return; 

        if (!pedidos || pedidos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Nenhum pedido pendente.</td></tr>';
            return;
        }

        let html = '';
        pedidos.forEach(ped => {
            html += `
                <tr id="pedido_${ped.id_pedido}">
                    <td>${ped.solicitante}</td>
                    <td><strong>${ped.peca_nome}</strong><br><small>${ped.fornecedor}</small></td>
                    <td>${ped.observacao}</td>
                    <td>
                        <button class="btn-aprovar" onclick="executarDecisao('aprovar', ${ped.id_pedido}, this)">Aprovar</button>
                        <button class="btn-rejeitar" onclick="mostrarRejeicao(${ped.id_pedido})">Rejeitar</button>
                        
                        <div id="rejeicao${ped.id_pedido}" style="display:none; margin-top:10px;">
                            <input type="text" id="justificativa${ped.id_pedido}" placeholder="Motivo..." style="padding:5px; border-radius:4px; border:1px solid #ccc;">
                            <button class="btn-aprovar" style="padding: 4px" onclick="executarDecisao('rejeitar', ${ped.id_pedido}, this)">Confirmar</button>
                        </div>
                    </td>
                </tr>`;
        });
        tbody.innerHTML = html;
    }

    setInterval(verificarNovosPedidos, 5000);

        document.getElementById('lucroDia').innerText = "R$ <?php echo number_format($valor_lucro, 2, ',', '.'); ?>";
        document.getElementById('vendasQtd').innerText = "<?php echo $vendas_hoje; ?>";
        document.getElementById('pedidosPendentes').innerText = "<?php echo $qtd_pendentes; ?>";
        document.getElementById('estoqueCritico').innerText = "<?php echo $qtd_critico; ?>";

        document.querySelector('.notification-bell').addEventListener('click', function() {
        document.getElementById('bellDot').style.display = 'none';
    });

   // Mantém a sessão viva e verifica se o Gerente ainda está logado
    setInterval(() => {
        fetch('ping.php')
            .then(response => {
                if (response.status === 401) {
                    // Se a sessão caiu, redireciona suavemente para o login
                    window.location.href = 'login.php';
                }
            })
            .catch(err => console.error("Erro de conexão"));
    }, 60000); // Checa a cada 1 minuto

    // Proteção para qualquer outro Fetch que você tenha (como relatórios ou notificações)
    const originalFetch = window.fetch;
    window.fetch = function() {
        return originalFetch.apply(this, arguments).then(response => {
            if (response.status === 401) {
                window.location.href = 'login.php';
            }
            return response;
        });
    };
    </script>
</body>
</html>