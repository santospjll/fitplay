<?php
// includes/cart.php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'cliente') {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        if (isset($_POST['curso'])) {
            $curso = json_decode($_POST['curso'], true);
            
            $carrinho = $_SESSION['carrinho'] ?? [];
            $existe = false;
            
            foreach ($carrinho as $item) {
                if ($item['id'] == $curso['id']) {
                    $existe = true;
                    break;
                }
            }
            
            if (!$existe) {
                $carrinho[] = $curso;
                $_SESSION['carrinho'] = $carrinho;
                echo json_encode(['success' => true, 'message' => 'Curso adicionado ao carrinho!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Curso já está no carrinho']);
            }
        }
        break;
        
    case 'remove':
        $curso_id = $_POST['curso_id'] ?? 0;
        $carrinho = $_SESSION['carrinho'] ?? [];
        
        $_SESSION['carrinho'] = array_filter($carrinho, function($item) use ($curso_id) {
            return $item['id'] != $curso_id;
        });
        
        echo json_encode(['success' => true, 'message' => 'Curso removido do carrinho']);
        break;
        
    case 'clear':
        $_SESSION['carrinho'] = [];
        echo json_encode(['success' => true, 'message' => 'Carrinho limpo']);
        break;
        
    case 'get':
        echo json_encode($_SESSION['carrinho'] ?? []);
        break;
        
    case 'count':
        $count = count($_SESSION['carrinho'] ?? []);
        echo json_encode(['count' => $count]);
        break;
        
    // Processar a finalização da compra e salvar no banco
    case 'finalizar_compra':
        $carrinho = $_SESSION['carrinho'] ?? [];

        if (empty($carrinho)) {
            echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
            break;
        }

        try {
            $conn->begin_transaction();

            // Prepared statement para inserir compras - CORRIGIDO
            $insert_stmt = $conn->prepare("INSERT INTO compras (cliente_id, curso_id, data_compra, status) VALUES (?, ?, NOW(), 'ativo')");
            if (!$insert_stmt) {
                throw new Exception('Falha ao preparar statement de inserção: ' . $conn->error);
            }

            foreach ($carrinho as $curso) {
                $curso_id = intval($curso['id']);

                // Verificar existência do curso e se não foi deletado
                $check_stmt = $conn->prepare("SELECT id, titulo FROM cursos WHERE id = ? AND deleted_at IS NULL");
                if (!$check_stmt) {
                    throw new Exception('Falha ao preparar statement de verificação: ' . $conn->error);
                }
                $check_stmt->bind_param("i", $curso_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();

                if ($result->num_rows == 0) {
                    $check_stmt->close();
                    throw new Exception("Curso não disponível (ID: $curso_id)");
                }

                $check_stmt->close();

                // Verificar se o usuário já comprou o curso
                $exists_stmt = $conn->prepare("SELECT id FROM compras WHERE cliente_id = ? AND curso_id = ?");
                if (!$exists_stmt) {
                    throw new Exception('Falha ao preparar statement de verificação de compra: ' . $conn->error);
                }
                $exists_stmt->bind_param("ii", $user_id, $curso_id);
                $exists_stmt->execute();
                $exists_result = $exists_stmt->get_result();

                if ($exists_result->num_rows > 0) {
                    // Já comprado: pular
                    $exists_stmt->close();
                    continue;
                }
                $exists_stmt->close();

                // Inserir compra
                $insert_stmt->bind_param("ii", $user_id, $curso_id);
                if (!$insert_stmt->execute()) {
                    throw new Exception('Falha ao inserir compra: ' . $insert_stmt->error);
                }
            }

            $insert_stmt->close();
            $conn->commit();

            // Limpar carrinho após compra bem-sucedida
            $_SESSION['carrinho'] = [];

            echo json_encode([
                'success' => true,
                'message' => 'Compra realizada com sucesso! Cursos disponíveis no seu dashboard.'
            ]);
        } catch (Exception $e) {
            if ($conn && $conn->connect_errno === 0) {
                $conn->rollback();
            }
            echo json_encode(['success' => false, 'message' => 'Erro ao processar compra: ' . $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
?>