<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isPersonal()) {
    header("Location: ../fitplay.php");
    exit();
}

if (!isset($_GET['curso_id'])) {
    $_SESSION['error'] = "ID do curso não especificado.";
    header("Location: dashboard.php");
    exit();
}

$curso_id = intval($_GET['curso_id']);
$personal_id = intval($_SESSION['user_id']);

// Usar transação para garantir consistência
try {
    $conn->begin_transaction();

    // Verificar se o curso existe e pertence ao personal e não está arquivado
    $stmt = $conn->prepare("SELECT id, deleted_at FROM cursos WHERE id = ? AND personal_id = ?");
    if (!$stmt) throw new Exception('Falha ao preparar verificação do curso: ' . $conn->error);
    $stmt->bind_param('ii', $curso_id, $personal_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $stmt->close();
        $_SESSION['error'] = "Curso não encontrado ou você não tem permissão para excluí-lo.";
        $conn->rollback();
        header("Location: dashboard.php");
        exit();
    }

    $row = $res->fetch_assoc();
    $stmt->close();

    // Se já está arquivado, informar ao usuário
    if (!is_null($row['deleted_at'])) {
        $_SESSION['error'] = "Curso já está arquivado.";
        $conn->rollback();
        header("Location: dashboard.php");
        exit();
    }

    // Contar compras associadas (não depender apenas de campo status)
    $compras_stmt = $conn->prepare("SELECT COUNT(*) as total FROM compras WHERE curso_id = ?");
    if (!$compras_stmt) throw new Exception('Falha ao preparar verificação de compras: ' . $conn->error);
    $compras_stmt->bind_param('i', $curso_id);
    $compras_stmt->execute();
    $compras_res = $compras_stmt->get_result();
    $compras_data = $compras_res->fetch_assoc();
    $compras_stmt->close();

    if ($compras_data['total'] > 0) {
        // Soft-delete (arquivar)
        $upd = $conn->prepare("UPDATE cursos SET deleted_at = NOW() WHERE id = ?");
        if (!$upd) throw new Exception('Falha ao preparar atualização: ' . $conn->error);
        $upd->bind_param('i', $curso_id);
        if (!$upd->execute()) {
            throw new Exception('Erro ao arquivar curso: ' . $upd->error);
        }
        $upd->close();

        $_SESSION['success'] = "Curso arquivado com sucesso! Ele continuará disponível para alunos que já o compraram.";
        $conn->commit();
    } else {
        // Tentar exclusão física
        $del = $conn->prepare("DELETE FROM cursos WHERE id = ?");
        if (!$del) throw new Exception('Falha ao preparar exclusão: ' . $conn->error);
        $del->bind_param('i', $curso_id);
        if (!$del->execute()) {
            // Se falhar por FK, faz soft-delete como fallback
            $errno = $conn->errno;
            $err = $conn->error;
            $del->close();

            if ($errno == 1451) { // ER_ROW_IS_REFERENCED_2 / foreign key constraint
                $upd2 = $conn->prepare("UPDATE cursos SET deleted_at = NOW() WHERE id = ?");
                if (!$upd2) throw new Exception('Falha ao preparar atualização fallback: ' . $conn->error);
                $upd2->bind_param('i', $curso_id);
                if (!$upd2->execute()) throw new Exception('Erro ao arquivar curso (fallback): ' . $upd2->error);
                $upd2->close();

                $_SESSION['success'] = "Curso arquivado (fallback) porque há referências no banco.";
                $conn->commit();
            } else {
                throw new Exception('Erro ao excluir curso: ' . $err);
            }
        } else {
            $del->close();
            $_SESSION['success'] = "Curso excluído permanentemente com sucesso!";
            $conn->commit();
        }
    }

} catch (Exception $e) {
    // Em caso de erro, rollback e informar
    if ($conn && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    $_SESSION['error'] = 'Erro ao processar exclusão: ' . $e->getMessage();
}

header("Location: dashboard.php");
exit();
?>