<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isPersonal()) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['curso_id'])) {
    header("Location: dashboard.php");
    exit();
}

$curso_id = intval($_GET['curso_id']);
$personal_id = $_SESSION['user_id'];

// Verificar se o curso pertence ao personal E não foi deletado
$query = "SELECT * FROM cursos WHERE id = $curso_id AND personal_id = $personal_id AND deleted_at IS NULL";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    header("Location: dashboard.php");
    exit();
}

$curso = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $video_url = $conn->real_escape_string($_POST['video_url']);
    
    $update_query = "UPDATE cursos SET titulo = '$titulo', descricao = '$descricao', 
                     preco = $preco, video_url = '$video_url', status = 'pendente' 
                     WHERE id = $curso_id AND personal_id = $personal_id AND deleted_at IS NULL";
    
    if ($conn->query($update_query)) {
        $_SESSION['success'] = "Curso atualizado com sucesso! Aguarde a nova aprovação do administrador.";
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Erro ao atualizar curso: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso - FitPlay</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <h1>Editar Curso</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="titulo">Título do Curso</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($curso['titulo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" required><?php echo htmlspecialchars($curso['descricao']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="preco">Preço (R$)</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0" value="<?php echo $curso['preco']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="video_url">URL do Vídeo</label>
                <input type="url" id="video_url" name="video_url" value="<?php echo htmlspecialchars($curso['video_url']); ?>" required>
            </div>
            
            <button type="submit" class="btn">Atualizar Curso</button>
            <a href="dashboard.php" class="btn btn-outline">Cancelar</a>
        </form>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>