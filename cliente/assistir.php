<?php
// cliente/assistir.php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isCliente()) {
    header("Location: ../fitplay.php");
    exit();
}

if (!isset($_GET['curso_id'])) {
    header("Location: dashboard.php");
    exit();
}

$curso_id = intval($_GET['curso_id']);
$cliente_id = $_SESSION['user_id'];

// Verificar se o cliente comprou o curso (INCLUINDO CURSOS DELETADOS)
$check_query = "SELECT c.* 
                FROM compras comp 
                JOIN cursos c ON comp.curso_id = c.id 
                WHERE comp.cliente_id = $cliente_id 
                AND comp.curso_id = $curso_id 
                AND (c.deleted_at IS NULL OR c.deleted_at IS NOT NULL)";
$result = $conn->query($check_query);

if ($result->num_rows == 0) {
    header("Location: dashboard.php");
    exit();
}

$curso = $result->fetch_assoc();
// ... resto do código
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistir: <?php echo htmlspecialchars($curso['titulo']); ?> - FitPlay</title>
    <style>
        .video-container {
            max-width: 1200px;
            margin: 100px auto 40px;
            padding: 0 20px;
        }
        
        .video-player {
            width: 100%;
            height: 600px;
            background-color: #000;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .video-info {
            background-color: #333;
            padding: 30px;
            border-radius: 10px;
        }
        
        .video-title {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .video-instructor {
            color: var(--gray-color);
            margin-bottom: 20px;
        }
        
        .video-description {
            line-height: 1.8;
        }
        
        @media (max-width: 768px) {
            .video-player {
                height: 300px;
            }
            
            .video-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="video-container">
        <div class="video-player">
            <?php if (strpos($curso['video_url'], 'youtube.com') !== false || strpos($curso['video_url'], 'youtu.be') !== false): ?>
                <!-- Embed do YouTube -->
                <iframe 
                    width="100%" 
                    height="100%" 
                    src="https://www.youtube.com/embed/<?php echo getYouTubeId($curso['video_url']); ?>" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            <?php else: ?>
                <!-- Player de vídeo HTML5 -->
                <video width="100%" height="100%" controls>
                    <source src="<?php echo htmlspecialchars($curso['video_url']); ?>" type="video/mp4">
                    Seu navegador não suporta o elemento de vídeo.
                </video>
            <?php endif; ?>
        </div>
        
        <div class="video-info">
            <h1 class="video-title"><?php echo htmlspecialchars($curso['titulo']); ?></h1>
            <p class="video-instructor">Instrutor: <?php echo htmlspecialchars($curso['instructor_name']); ?></p>
            <div class="video-description">
                <?php echo nl2br(htmlspecialchars($curso['descricao'])); ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>

    <?php
    function getYouTubeId($url) {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }
    ?>
</body>
</html>