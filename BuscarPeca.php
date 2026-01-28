
<?php
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);
session_start();

// NOVA TRAVA: Se a sessão caiu, avisa o JS com erro 401
if (!isset($_SESSION['id_usuario'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

error_reporting(0); 
header('Content-Type: application/json');

include 'conexao.php';

$id = isset($_GET['id_peca']) ? intval($_GET['id_peca']) : 0;
// ... resto do seu código igual ...

$id = isset($_GET['id_peca']) ? intval($_GET['id_peca']) : 0;

if ($id > 0) {
    // Buscamos o preço de venda definido pelo gerente
    $sql = "SELECT preco_venda FROM pecas WHERE id_peca = $id";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $peca = $res->fetch_assoc();
        // Retorna o preço em formato JSON para o JavaScript ler 
        echo json_encode(['preco' => $peca['preco_venda']]);
    } else {
        echo json_encode(['preco' => null]);
    }
} 
?>