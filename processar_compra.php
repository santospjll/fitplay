<?php
session_start();
include 'includes/db.php';
include 'includes/auth.php';

requireLogin();
if (!isCliente()) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

// Ler dados da compra
$input = json_decode(file_get_contents('php://input'), true);
$itens = $input['itens'] ?? [];
$cliente_id = $_SESSION['user_id'];

if (empty($itens)) {
    echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
    exit();
}

// Iniciar transação
$conn->begin_transaction();

try {
    foreach ($itens as $item) {
        $curso_id = $conn->real_escape_string($item['id']);
        
        // Verificar se o curso existe e está aprovado
        $curso_query = "SELECT * FROM cursos WHERE id = '$curso_id' AND status = 'aprovado'";
        $curso_result = $conn->query($curso_query);
        
        if ($curso_result->num_rows == 0) {
            throw new Exception("Curso não encontrado: " . $item['title']);
        }
        
        // Verificar se o cliente já comprou este curso
        $check_compra = "SELECT id FROM compras WHERE cliente_id = '$cliente_id' AND curso_id = '$curso_id'";
        $compra_result = $conn->query($check_compra);
        
        if ($compra_result->num_rows > 0) {
            throw new Exception("Você já comprou o curso: " . $item['title']);
        }
        
        // Inserir compra
        $compra_query = "INSERT INTO compras (cliente_id, curso_id, valor_pago) VALUES ('$cliente_id', '$curso_id', '{$item['price']}')";
        
        if (!$conn->query($compra_query)) {
            throw new Exception("Erro ao processar compra do curso: " . $item['title']);
        }
    }
    
    // Commit da transação
    $conn->commit();
    
    // Limpar carrinho
    $_SESSION['carrinho'] = [];
    
    echo json_encode(['success' => true, 'message' => 'Compra realizada com sucesso!']);
    
} catch (Exception $e) {
    // Rollback em caso de erro
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>