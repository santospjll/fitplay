<?php
// comprar_curso.php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isCliente()) {
    header("Location: ../fitplay.php");
    exit();
}

if (!isset($_GET['curso_id'])) {
    header("Location: ../fitplay.php");
    exit();
}

$curso_id = intval($_GET['curso_id']);
$cliente_id = $_SESSION['user_id'];

// Verificar se o curso existe, está aprovado E NÃO DELETADO
$curso_query = "SELECT * FROM cursos WHERE id = $curso_id AND status = 'aprovado' AND deleted_at IS NULL";
$curso_result = $conn->query($curso_query);

if ($curso_result->num_rows == 0) {
    $_SESSION['error'] = "Curso não encontrado ou não disponível para compra.";
    header("Location: ../fitplay.php");
    exit();
}

$curso = $curso_result->fetch_assoc();

// Resto do código permanece igual...
?>