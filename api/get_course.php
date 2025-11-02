<?php
session_start();
include '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID do curso não fornecido']);
    exit();
}

$curso_id = $conn->real_escape_string($_GET['id']);

// Buscar dados do curso
$query = "SELECT c.*, u.nome as instructor_name 
          FROM cursos c 
          JOIN usuarios u ON c.personal_id = u.id 
          WHERE c.id = '$curso_id' AND c.status = 'aprovado'";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Curso não encontrado']);
    exit();
}

$curso = $result->fetch_assoc();

// Verificar se usuário já comprou o curso
$ja_comprou = false;
if (isset($_SESSION['user_id']) && $_SESSION['tipo'] == 'cliente') {
    $cliente_id = $_SESSION['user_id'];
    $check_compra = $conn->query("SELECT id FROM compras WHERE cliente_id = '$cliente_id' AND curso_id = '$curso_id'");
    $ja_comprou = $check_compra->num_rows > 0;
}

// Adicionar flag de compra ao retorno
$curso['ja_comprou'] = $ja_comprou;

echo json_encode($curso);
?>