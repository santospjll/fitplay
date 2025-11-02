<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isAdmin()) {
    header("Location: ../fitplay.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $query = "UPDATE usuarios SET aprovado = 1 WHERE id = $id AND tipo = 'personal'";
        $message = "Personal trainer aprovado com sucesso!";
    } elseif ($action == 'reject') {
        $query = "DELETE FROM usuarios WHERE id = $id AND tipo = 'personal'";
        $message = "Personal trainer rejeitado e removido do sistema!";
    }
    
    if ($conn->query($query)) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = "Erro ao processar solicitação: " . $conn->error;
    }
}

header("Location: dashboard.php");
exit();
?>