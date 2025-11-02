<?php
// cursos_detalhes.php
session_start();
include 'includes/db.php';
include 'includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: fitplay.php");
    exit();
}

$curso_id = $conn->real_escape_string($_GET['id']);

// Verificar se o usuário já comprou este curso
$ja_comprou = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check_purchase = "SELECT id FROM compras WHERE cliente_id = '$user_id' AND curso_id = '$curso_id'";
    $purchase_result = $conn->query($check_purchase);
    $ja_comprou = $purchase_result->num_rows > 0;
}

// Buscar dados do curso (APENAS NÃO DELETADO)
$query = "SELECT c.*, u.nome as instructor_name, u.email as instructor_email 
          FROM cursos c 
          JOIN usuarios u ON c.personal_id = u.id 
          WHERE c.id = '$curso_id' AND c.status = 'aprovado' AND c.deleted_at IS NULL";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: fitplay.php");
    exit();
}

$curso = $result->fetch_assoc();

// Buscar cursos relacionados (APENAS NÃO DELETADOS)
$query_related = "SELECT c.*, u.nome as instructor_name 
                  FROM cursos c 
                  JOIN usuarios u ON c.personal_id = u.id 
                  WHERE c.status = 'aprovado' AND c.deleted_at IS NULL AND c.id != '$curso_id' 
                  ORDER BY RAND() LIMIT 3";
$related_courses = $conn->query($query_related);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($curso['titulo']); ?> - FitPlay</title>
    <link rel="stylesheet" href="assets/css/principal.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/courses.css">
    <link rel="stylesheet" href="assets/css/video.css">
    <style>
        /* Estilos do Modal igual ao Dashboard */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.95);
            overflow-y: auto;
        }

        .modal-content {
            background: #000000;
            margin: 2% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(255,165,0,0.3);
            position: relative;
            border: 2px solid #333333;
        }

        .modal-header {
            padding: 25px;
            background: #000000;
            border-bottom: 2px solid #ff6600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            color: #ff6600;
            flex: 1;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .close-modal {
            color: #ffffff;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            padding: 0 10px;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            color: #ff6600;
        }

        .modal-body {
            padding: 25px;
            background: #000000;
            color: #ffffff;
        }

        .course-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .course-image {
            border-radius: 8px;
            overflow: hidden;
            background: #111111;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            position: relative;
            border: 1px solid #333333;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .course-image .emoji-fallback {
            font-size: 4rem;
        }

        .course-meta {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #111111;
            border-radius: 6px;
            border: 1px solid #333333;
            color: #ffffff;
        }

        .meta-icon {
            color: #ff6600;
            width: 20px;
        }

        .meta-item strong {
            color: #ff6600;
        }

        .course-description-full {
            background: #111111;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #333333;
        }

        .course-description-full h3 {
            color: #ff6600;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .course-description-full p {
            color: #ffffff;
            line-height: 1.6;
        }

        .instructor-info {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #111111;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #333333;
        }

        .instructor-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6600, #ff8c00);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5rem;
            color: #000000;
        }

        .instructor-info h4 {
            color: #ff6600;
            margin: 0 0 5px 0;
            font-size: 1.2rem;
        }

        .instructor-info p {
            color: #cccccc;
            margin: 0;
        }

        .modal-actions {
            padding: 20px;
            background: #111111;
            border-top: 2px solid #333333;
            text-align: center;
        }

        .course-curriculum {
            background: #111111;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #333333;
            margin-bottom: 20px;
        }

        .course-curriculum h3 {
            color: #ff6600;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .module {
            margin-bottom: 20px;
        }

        .module-title {
            color: #ff6600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }

        .lessons {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .lesson {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            margin-bottom: 8px;
            background: #000000;
            border-radius: 6px;
            border: 1px solid #333333;
            transition: all 0.3s ease;
            color: #ffffff;
        }

        .lesson:hover {
            background: #1a1a1a;
            border-color: #ff6600;
        }

        .lesson-icon {
            color: #ff6600;
        }

        /* Botões */
        .btn {
            background: linear-gradient(135deg, #ff6600, #ff8c00);
            color: #000000;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 5px;
        }

        .btn:hover {
            background: linear-gradient(135deg, #ff8c00, #ff6600);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,102,0,0.4);
        }

        .btn-secondary {
            background: #333333;
            color: #ffffff;
        }

        .btn-secondary:hover {
            background: #444444;
            box-shadow: 0 5px 15px rgba(255,255,255,0.2);
        }

        /* Estilos existentes da página */
        .course-preview {
            background: var(--gradient-card);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            margin-bottom: var(--space-xl);
        }

        .preview-header {
            padding: var(--space-xl);
            text-align: center;
            background: linear-gradient(135deg, var(--dark-light), var(--dark-lighter));
        }

        .preview-title {
            font-size: 2.5rem;
            margin-bottom: var(--space-md);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .preview-meta {
            display: flex;
            justify-content: center;
            gap: var(--space-xl);
            flex-wrap: wrap;
            margin-bottom: var(--space-lg);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            color: var(--gray-color);
        }

        .preview-actions {
            display: flex;
            gap: var(--space-md);
            justify-content: center;
            flex-wrap: wrap;
        }

        .preview-content {
            padding: var(--space-xl);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--space-xl);
        }

        .preview-sidebar {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .instructor-card {
            background: var(--dark-lighter);
            padding: var(--space-lg);
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: var(--space-lg);
        }

        .instructor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto var(--space-md);
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--dark-color);
            font-weight: bold;
        }

        .preview-features {
            list-style: none;
            margin-bottom: var(--space-lg);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-item:last-child {
            border-bottom: none;
        }

        .feature-icon {
            color: var(--primary-color);
            width: 20px;
        }

        .course-curriculum {
            background: var(--dark-lighter);
            border-radius: var(--border-radius);
            padding: var(--space-lg);
        }

        .curriculum-title {
            font-size: 1.25rem;
            margin-bottom: var(--space-lg);
            color: var(--primary-color);
        }

        .module {
            margin-bottom: var(--space-lg);
        }

        .module-title {
            font-weight: 600;
            margin-bottom: var(--space-md);
            color: var(--light-color);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .preview-video {
            margin-bottom: var(--space-lg);
        }

        .video-placeholder {
            background: #000;
            border-radius: var(--border-radius);
            padding: 40% 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-color);
            position: relative;
            overflow: hidden;
        }

        .video-placeholder img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: var(--space-md);
            z-index: 2;
        }

        .sidebar-card {
            background: var(--dark-lighter);
            padding: var(--space-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--space-lg);
        }

        .course-price-large {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            text-align: center;
        }

        .course-price-section {
            text-align: center;
        }

        @media (max-width: 768px) {
            .course-detail-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .preview-title {
                font-size: 2rem;
            }
            
            .preview-meta {
                gap: var(--space-lg);
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="video-player-section">
        <div class="course-preview">
            <div class="preview-header">
                <h1 class="preview-title"><?php echo htmlspecialchars($curso['titulo']); ?></h1>
                <p class="preview-description"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                
                <div class="preview-meta">
                    <div class="meta-item">
                        <i class="fas fa-user-tie"></i>
                        <span>Instrutor: <?php echo htmlspecialchars($curso['instructor_name']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>Acesso Vitalício</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-play-circle"></i>
                        <span>Vídeo Aulas</span>
                    </div>
                </div>

                <div class="preview-actions">
                    <?php if ($ja_comprou): ?>
                        <a href="cliente/assistir.php?curso_id=<?php echo $curso['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-play"></i> Acessar Curso
                        </a>
                    <?php else: ?>
                        <button class="btn btn-primary" onclick="addToCart(<?php echo htmlspecialchars(json_encode([
                            'id' => $curso['id'],
                            'title' => $curso['titulo'],
                            'price' => floatval($curso['preco']),
                            'instructor' => $curso['instructor_name'],
                            'image' => !empty($curso['capa_url']) ? $curso['capa_url'] : null
                        ])); ?>)">
                            <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho - R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?>
                        </button>
                        <button class="btn btn-secondary" onclick="openCourseModal()">
                            <i class="fas fa-info-circle"></i> Ver Detalhes
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="preview-content">
                <div class="content-grid">
                    <div class="preview-main">
                        <div class="preview-video">
                            <div class="video-placeholder">
                                <?php if (!empty($curso['capa_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($curso['capa_url']); ?>" alt="<?php echo htmlspecialchars($curso['titulo']); ?>">
                                <?php else: ?>
                                    <div style="font-size: 4rem; position: relative; z-index: 3;">💪</div>
                                <?php endif; ?>
                                <?php if (!$ja_comprou): ?>
                                <div class="preview-overlay">
                                    <i class="fas fa-play-circle" style="font-size: 3rem; color: var(--primary-color);"></i>
                                    <p>Clique para ver preview do curso</p>
                                    <button class="btn btn-primary" onclick="openCourseModal()">
                                        <i class="fas fa-info-circle"></i> Ver Detalhes Completos
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="course-curriculum">
                            <h3 class="curriculum-title">Conteúdo do Curso</h3>
                            
                            <div class="module">
                                <h4 class="module-title">
                                    <i class="fas fa-play-circle"></i>
                                    Módulo 1: Introdução
                                </h4>
                                <ul class="lessons">
                                    <li class="lesson">
                                        <i class="fas fa-play-circle lesson-icon"></i>
                                        <span>Boas-vindas ao curso</span>
                                    </li>
                                    <li class="lesson">
                                        <i class="fas fa-play-circle lesson-icon"></i>
                                        <span>Apresentação do instrutor</span>
                                    </li>
                                    <li class="lesson">
                                        <i class="fas fa-play-circle lesson-icon"></i>
                                        <span>O que você vai aprender</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="module">
                                <h4 class="module-title">
                                    <i class="fas fa-dumbbell"></i>
                                    Módulo 2: Fundamentos
                                </h4>
                                <ul class="lessons">
                                    <li class="lesson">
                                        <i class="fas fa-play-circle lesson-icon"></i>
                                        <span>Conceitos básicos</span>
                                    </li>
                                    <li class="lesson">
                                        <i class="fas fa-play-circle lesson-icon"></i>
                                        <span>Preparação e aquecimento</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="preview-sidebar">
                        <div class="instructor-card">
                            <div class="instructor-avatar">
                                <?php echo strtoupper(substr($curso['instructor_name'], 0, 1)); ?>
                            </div>
                            <h4><?php echo htmlspecialchars($curso['instructor_name']); ?></h4>
                            <p>Personal Trainer Certificado</p>
                        </div>

                        <div class="sidebar-card">
                            <h4>Este curso inclui:</h4>
                            <ul class="preview-features">
                                <li class="feature-item">
                                    <i class="fas fa-play-circle feature-icon"></i>
                                    <span>Acesso vitalício</span>
                                </li>
                                <li class="feature-item">
                                    <i class="fas fa-mobile-alt feature-icon"></i>
                                    <span>Acesso em todos os dispositivos</span>
                                </li>
                                <li class="feature-item">
                                    <i class="fas fa-certificate feature-icon"></i>
                                    <span>Certificado de conclusão</span>
                                </li>
                                <li class="feature-item">
                                    <i class="fas fa-question-circle feature-icon"></i>
                                    <span>Suporte do instrutor</span>
                                </li>
                            </ul>

                            <div class="course-price-section">
                                <div class="course-price-large">R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></div>
                                <?php if (!$ja_comprou): ?>
                                    <button class="btn btn-primary" style="width: 100%; margin-top: var(--space-md);" 
                                            onclick="addToCart(<?php echo htmlspecialchars(json_encode([
                                                'id' => $curso['id'],
                                                'title' => $curso['titulo'],
                                                'price' => floatval($curso['preco']),
                                                'instructor' => $curso['instructor_name'],
                                                'image' => !empty($curso['capa_url']) ? $curso['capa_url'] : null
                                            ])); ?>)">
                                        <i class="fas fa-shopping-cart"></i> Comprar Curso
                                    </button>
                                    <button class="btn btn-secondary" style="width: 100%; margin-top: var(--space-sm);" onclick="openCourseModal()">
                                        <i class="fas fa-info-circle"></i> Ver Detalhes Completos
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cursos Relacionados -->
        <?php if ($related_courses->num_rows > 0): ?>
        <section class="section">
            <h2 class="section-title">Cursos Relacionados</h2>
            <div class="courses-grid">
                <?php while($related = $related_courses->fetch_assoc()): ?>
                <div class="course-card">
                    <?php if (!empty($related['capa_url'])): ?>
                        <div class="course-img">
                            <img src="<?php echo htmlspecialchars($related['capa_url']); ?>" alt="<?php echo htmlspecialchars($related['titulo']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="course-img">
                            <span>💪</span>
                        </div>
                    <?php endif; ?>
                    <div class="course-info">
                        <h3 class="course-title"><?php echo htmlspecialchars($related['titulo']); ?></h3>
                        <p class="course-description"><?php echo htmlspecialchars($related['descricao']); ?></p>
                        <p class="course-instructor">Por: <?php echo htmlspecialchars($related['instructor_name']); ?></p>
                        <div class="course-footer">
                            <div class="course-price">R$ <?php echo number_format($related['preco'], 2, ',', '.'); ?></div>
                            <a href="cursos_detalhes.php?id=<?php echo $related['id']; ?>" class="btn btn-secondary">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <!-- Modal de Detalhes do Curso (igual ao Dashboard) -->
    <div class="modal" id="course-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-course-title">Detalhes do Curso</h2>
                <span class="close-modal" onclick="closeCourseModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="course-detail-grid">
                    <div class="course-image">
                        <?php if (!empty($curso['capa_url'])): ?>
                            <img id="modal-course-image" src="<?php echo htmlspecialchars($curso['capa_url']); ?>" alt="<?php echo htmlspecialchars($curso['titulo']); ?>">
                        <?php else: ?>
                            <span id="modal-course-emoji" class="emoji-fallback">💪</span>
                        <?php endif; ?>
                    </div>
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-user-tie meta-icon"></i>
                            <span>Instrutor: <strong id="modal-course-instructor"><?php echo htmlspecialchars($curso['instructor_name']); ?></strong></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock meta-icon"></i>
                            <span>Acesso Vitalício</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-play-circle meta-icon"></i>
                            <span>Vídeo Aulas</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-dollar-sign meta-icon"></i>
                            <span>Preço: R$ <strong id="modal-course-price"><?php echo number_format($curso['preco'], 2, ',', '.'); ?></strong></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar meta-icon"></i>
                            <span>Disponível desde: <strong><?php echo date('d/m/Y', strtotime($curso['created_at'])); ?></strong></span>
                        </div>
                    </div>
                </div>

                <div class="course-description-full">
                    <h3>Descrição do Curso</h3>
                    <p id="modal-course-description"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                </div>

                <div class="instructor-info">
                    <div class="instructor-avatar" id="modal-instructor-avatar">
                        <?php echo strtoupper(substr($curso['instructor_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h4 id="modal-instructor-name"><?php echo htmlspecialchars($curso['instructor_name']); ?></h4>
                        <p>Personal Trainer Certificado</p>
                    </div>
                </div>

                <div class="course-curriculum">
                    <h3>Conteúdo do Curso</h3>
                    <div class="module">
                        <h4 class="module-title">
                            <i class="fas fa-play-circle"></i>
                            Módulo 1: Introdução
                        </h4>
                        <ul class="lessons">
                            <li class="lesson">
                                <i class="fas fa-play-circle lesson-icon"></i>
                                <span>Boas-vindas ao curso</span>
                            </li>
                            <li class="lesson">
                                <i class="fas fa-play-circle lesson-icon"></i>
                                <span>Apresentação do instrutor</span>
                            </li>
                            <li class="lesson">
                                <i class="fas fa-play-circle lesson-icon"></i>
                                <span>O que você vai aprender</span>
                            </li>
                        </ul>
                    </div>
                    <div class="module">
                        <h4 class="module-title">
                            <i class="fas fa-dumbbell"></i>
                            Módulo 2: Fundamentos
                        </h4>
                        <ul class="lessons">
                            <li class="lesson">
                                <i class="fas fa-play-circle lesson-icon"></i>
                                <span>Conceitos básicos</span>
                            </li>
                            <li class="lesson">
                                <i class="fas fa-play-circle lesson-icon"></i>
                                <span>Preparação e aquecimento</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <?php if ($ja_comprou): ?>
                    <a href="cliente/assistir.php?curso_id=<?php echo $curso['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-play"></i> Acessar Curso
                    </a>
                <?php else: ?>
                    <button class="btn btn-primary" onclick="addToCart(<?php echo htmlspecialchars(json_encode([
                        'id' => $curso['id'],
                        'title' => $curso['titulo'],
                        'price' => floatval($curso['preco']),
                        'instructor' => $curso['instructor_name'],
                        'image' => !empty($curso['capa_url']) ? $curso['capa_url'] : null
                    ])); ?>)">
                        <i class="fas fa-shopping-cart"></i> Comprar Curso - R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?>
                    </button>
                <?php endif; ?>
                <button class="btn btn-secondary" onclick="closeCourseModal()">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal do Carrinho -->
    <?php include 'includes/cart_modal.php'; ?>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/carrinho.js"></script>
    <script>
        function openCourseModal() {
            document.getElementById('course-modal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeCourseModal() {
            document.getElementById('course-modal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Fechar modal clicando fora
        document.getElementById('course-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCourseModal();
            }
        });

        // Tecla ESC para fechar modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCourseModal();
            }
        });

        // Sistema de carrinho
        function addToCart(curso) {
            console.log('Tentando adicionar ao carrinho:', curso);
            
            fetch('includes/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&curso=${encodeURIComponent(JSON.stringify(curso))}`
            })
            .then(response => response.json())
            .then(result => {
                console.log('Resposta:', result);
                if (result.success) {
                    alert('✅ ' + result.message);
                    // Atualizar contador
                    updateCartCount();
                    // Fechar modal após adicionar ao carrinho
                    closeCourseModal();
                } else {
                    alert('⚠️ ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('❌ Erro ao adicionar curso ao carrinho');
            });
        }

        async function updateCartCount() {
            try {
                const response = await fetch('includes/cart.php?action=count');
                const result = await response.json();
                
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = result.count;
                    cartCount.style.display = result.count > 0 ? 'flex' : 'none';
                }
            } catch (error) {
                console.error('Erro ao atualizar contador:', error);
            }
        }

        // Inicializar contador quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>
</body>
</html>