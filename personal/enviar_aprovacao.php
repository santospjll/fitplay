<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isPersonal()) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['curso_id'])) {
    $curso_id = intval($_GET['curso_id']);
    $personal_id = $_SESSION['user_id'];
    
    // Verificar se o curso pertence ao personal
    $check_query = "SELECT id FROM cursos WHERE id = $curso_id AND personal_id = $personal_id";
    $result = $conn->query($check_query);
    
    if ($result->num_rows > 0) {
        $update_query = "UPDATE cursos SET status = 'pendente' WHERE id = $curso_id";
        
        if ($conn->query($update_query)) {
            $_SESSION['success'] = "Curso enviado para aprovação do administrador!";
        } else {
            $_SESSION['error'] = "Erro ao enviar curso para aprovação: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Curso não encontrado ou você não tem permissão para editá-lo.";
    }
}

header("Location: dashboard.php");
exit();
?>