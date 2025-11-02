<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isAdmin()) {
    header("Location: ../fitplay.php");
    exit();
}

if (isset($_GET['curso_id']) && isset($_GET['action'])) {
    $curso_id = intval($_GET['curso_id']);
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $status = 'aprovado';
        $message = "Curso aprovado com sucesso!";
    } elseif ($action == 'reject') {
        $status = 'rejeitado';
        $message = "Curso rejeitado!";
    }
    
    $query = "UPDATE cursos SET status = '$status' WHERE id = $curso_id";
    
    if ($conn->query($query)) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = "Erro ao processar solicitação: " . $conn->error;
    }
}

header("Location: dashboard.php");
exit();
?>