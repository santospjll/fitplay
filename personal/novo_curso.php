<?php
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isPersonal()) {
    header("Location: ../fitplay.html");
    exit();
}

// Verificar se personal está aprovado
$personal_id = $_SESSION['user_id'];
$check_approved = $conn->query("SELECT aprovado FROM usuarios WHERE id = $personal_id");
$user_data = $check_approved->fetch_assoc();

if (!$user_data['aprovado']) {
    header("Location: dashboard.php?error=waiting_approval");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $preco = $conn->real_escape_string($_POST['preco']);
    $video_url = $conn->real_escape_string($_POST['video_url']);
    
    // Upload da capa
    $capa_url = '';
    if (isset($_FILES['capa']) && $_FILES['capa']['error'] == 0) {
        $upload_dir = '../assets/img/cursos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            // Verificar tamanho do arquivo (máximo 5MB)
            if ($_FILES['capa']['size'] > 5 * 1024 * 1024) {
                $error = "A imagem deve ter no máximo 5MB.";
            } else {
                $file_name = 'curso_' . time() . '_' . $personal_id . '.' . $file_extension;
                $file_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['capa']['tmp_name'], $file_path)) {
                    $capa_url = 'assets/img/cursos/' . $file_name;
                } else {
                    $error = "Erro ao fazer upload da imagem.";
                }
            }
        } else {
            $error = "Formato de arquivo não permitido. Use JPG, PNG ou GIF.";
        }
    } else {
        $error = "Por favor, selecione uma imagem para a capa do curso.";
    }
    
    if (!$error) {
        $status = 'pendente';
        
        // Verificar se a coluna capa_url existe
        $check_column = $conn->query("SHOW COLUMNS FROM cursos LIKE 'capa_url'");
        if ($check_column->num_rows == 0) {
            // Criar a coluna se não existir
            $conn->query("ALTER TABLE cursos ADD COLUMN capa_url VARCHAR(500) AFTER video_url");
        }
        
        $query = "INSERT INTO cursos (personal_id, titulo, descricao, preco, video_url, capa_url, status) 
                  VALUES ('$personal_id', '$titulo', '$descricao', '$preco', '$video_url', '$capa_url', '$status')";
        
        if ($conn->query($query)) {
            $success = "Curso criado com sucesso! Aguarde a aprovação do administrador.";
            // Limpar o formulário
            $_POST = array();
        } else {
            $error = "Erro ao criar curso: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Curso - FitPlay</title>
    <link rel="stylesheet" href="../assets/css/principal.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .course-form {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .file-upload-preview {
            margin-top: var(--space-md);
            text-align: center;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: var(--border-radius);
            border: 2px solid var(--primary-color);
        }
        
        .form-helptext {
            color: var(--gray-color);
            font-size: 0.85rem;
            margin-top: var(--space-xs);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Criar Novo Curso</h1>
                <p class="dashboard-subtitle">Compartilhe seu conhecimento com a comunidade FitPlay</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="dashboard-section">
                <form method="POST" enctype="multipart/form-data" class="course-form">
                    <div class="form-group">
                        <label for="titulo">Título do Curso *</label>
                        <input type="text" id="titulo" name="titulo" required 
                               value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>"
                               placeholder="Ex: Yoga para Iniciantes - Aula Completa">
                        <div class="form-helptext">Um título claro e atrativo para seu curso</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao">Descrição do Curso *</label>
                        <textarea id="descricao" name="descricao" required rows="5"
                                  placeholder="Descreva o que os alunos vão aprender neste curso..."><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea>
                        <div class="form-helptext">Explique os benefícios e conteúdo do curso</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="preco">Preço (R$) *</label>
                        <input type="number" id="preco" name="preco" step="0.01" min="0" required 
                               value="<?php echo isset($_POST['preco']) ? htmlspecialchars($_POST['preco']) : ''; ?>"
                               placeholder="0.00">
                        <div class="form-helptext">Valor do curso em reais</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="video_url">URL do Vídeo *</label>
                        <input type="url" id="video_url" name="video_url" required 
                               value="<?php echo isset($_POST['video_url']) ? htmlspecialchars($_POST['video_url']) : ''; ?>"
                               placeholder="https://www.youtube.com/watch?v=... ou https://vimeo.com/...">
                        <div class="form-helptext">Link do YouTube ou Vimeo</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="capa">Capa do Curso *</label>
                        <div class="file-upload">
                            <div class="file-upload-btn" id="fileUploadBtn">
                                <i class="fas fa-cloud-upload-alt"></i> 
                                <span id="fileUploadText">Escolher Imagem da Capa</span>
                            </div>
                            <input type="file" id="capa" name="capa" accept="image/*" required 
                                   onchange="previewImage(this)">
                        </div>
                        <div class="form-helptext">
                            Formatos: JPG, PNG, GIF. Tamanho máximo: 5MB. Dimensões recomendadas: 1280x720px
                        </div>
                        <div class="file-upload-preview" id="imagePreview" style="display: none;">
                            <img id="previewImg" class="preview-image" src="" alt="Preview">
                            <div class="form-helptext" id="fileName"></div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Curso
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const fileName = document.getElementById('fileName');
            const fileUploadText = document.getElementById('fileUploadText');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    fileName.textContent = input.files[0].name;
                    fileUploadText.textContent = 'Alterar Imagem';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
                fileUploadText.textContent = 'Escolher Imagem da Capa';
            }
        }
        
        // Validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const preco = document.getElementById('preco').value;
            if (preco < 0) {
                e.preventDefault();
                alert('O preço não pode ser negativo.');
                return false;
            }
            
            const fileInput = document.getElementById('capa');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    e.preventDefault();
                    alert('A imagem deve ter no máximo 5MB.');
                    return false;
                }
            }
        });
    </script>
</body>
</html>