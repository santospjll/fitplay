<?php
// cliente/dashboard.php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isCliente()) {
    header("Location: ../fitplay.php");
    exit();
}

$cliente_id = $_SESSION['user_id'];

// Buscar cursos comprados (INCLUINDO CURSOS DELETADOS para alunos que já compraram)
$courses_query = "SELECT c.*, u.nome as instructor_name, comp.data_compra 
                  FROM compras comp 
                  JOIN cursos c ON comp.curso_id = c.id 
                  JOIN usuarios u ON c.personal_id = u.id 
                  WHERE comp.cliente_id = $cliente_id 
                  AND (c.deleted_at IS NULL OR c.deleted_at IS NOT NULL)
                  ORDER BY comp.data_compra DESC";
$courses_result = $conn->query($courses_query);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Cursos - FitPlay</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
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
            justify-content: between;
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

        /* Estilos para os cards dos cursos */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .course-card {
            background: #111111;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #333333;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255,102,0,0.3);
            border-color: #ff6600;
        }

        .course-img {
            width: 100%;
            height: 200px;
            background: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .course-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .course-img span {
            font-size: 3rem;
        }

        .course-info {
            padding: 20px;
        }

        .course-title {
            color: #ff6600;
            margin-bottom: 10px;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .course-description {
            color: #cccccc;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }

        .course-instructor {
            color: #ffffff;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .dashboard-container h1 {
            color: #ff6600;
            margin-bottom: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            background: #111111;
            border-radius: 8px;
            border: 1px solid #333333;
        }

        .empty-state h2 {
            color: #ff6600;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #cccccc;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .course-detail-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
            
            .courses-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <h1>Meus Cursos</h1>
        
        <?php if ($courses_result->num_rows > 0): ?>
            <div class="courses-grid">
                <?php while($course = $courses_result->fetch_assoc()): ?>
                    <div class="course-card">
                        <div class="course-img">
                            <?php if (!empty($course['capa_url']) && $course['capa_url'] !== 'null' && $course['capa_url'] !== 'NULL'): ?>
                                <img src="../<?php echo htmlspecialchars($course['capa_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($course['titulo']); ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <span style="display: none;">💪</span>
                            <?php else: ?>
                                <span>💪</span>
                            <?php endif; ?>
                        </div>
                        <div class="course-info">
                            <h3 class="course-title"><?php echo htmlspecialchars($course['titulo']); ?></h3>
                            <p class="course-description"><?php echo htmlspecialchars($course['descricao']); ?></p>
                            <p class="course-instructor">Por: <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                            <button class="btn" onclick="openCourseModal(<?php echo htmlspecialchars(json_encode($course)); ?>)">Ver detalhes</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h2>Você ainda não comprou nenhum curso</h2>
                <p>Explore nossa biblioteca de cursos e comece sua jornada fitness!</p>
                <a href="../fitplay.php" class="btn">Explorar Cursos</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de Detalhes do Curso -->
    <div class="modal" id="course-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-course-title">Detalhes do Curso</h2>
                <span class="close-modal" onclick="closeCourseModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="course-detail-grid">
                    <div class="course-image">
                        <img id="modal-course-image" src="" alt="" style="display: none;">
                        <span id="modal-course-emoji" class="emoji-fallback" style="display: none;">💪</span>
                    </div>
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-user-tie meta-icon"></i>
                            <span>Instrutor: <strong id="modal-course-instructor"></strong></span>
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
                            <span>Preço: R$ <strong id="modal-course-price"></strong></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar meta-icon"></i>
                            <span>Comprado em: <strong id="modal-course-purchase-date"></strong></span>
                        </div>
                    </div>
                </div>

                <div class="course-description-full">
                    <h3>Descrição do Curso</h3>
                    <p id="modal-course-description"></p>
                </div>

                <div class="instructor-info">
                    <div class="instructor-avatar" id="modal-instructor-avatar"></div>
                    <div>
                        <h4 id="modal-instructor-name"></h4>
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
                <a href="#" class="btn btn-primary" id="modal-access-course">
                    <i class="fas fa-play"></i> Acessar Curso
                </a>
                <button class="btn btn-secondary" onclick="closeCourseModal()">Fechar</button>
            </div>
        </div>
    </div>
    
    <script>
        function openCourseModal(course) {
            console.log('Abrindo modal para curso:', course);
            
            // Preencher os dados do modal
            document.getElementById('modal-course-title').textContent = course.titulo;
            document.getElementById('modal-course-description').textContent = course.descricao;
            document.getElementById('modal-course-instructor').textContent = course.instructor_name;
            document.getElementById('modal-course-price').textContent = parseFloat(course.preco).toFixed(2).replace('.', ',');
            
            // Configurar imagem do curso
            const courseImage = document.getElementById('modal-course-image');
            const courseEmoji = document.getElementById('modal-course-emoji');
            
            console.log('URL da imagem:', course.capa_url);
            
            // Resetar display
            courseImage.style.display = 'none';
            courseEmoji.style.display = 'none';
            
            // Verificar se a imagem existe
            if (course.capa_url && course.capa_url !== 'null' && course.capa_url !== 'NULL' && course.capa_url.trim() !== '') {
                let imageUrl = course.capa_url;
                if (!imageUrl.startsWith('http') && !imageUrl.startsWith('../')) {
                    imageUrl = '../' + imageUrl;
                }
                
                courseImage.src = imageUrl;
                courseImage.alt = course.titulo;
                courseImage.style.display = 'block';
                
                console.log('Tentando carregar imagem:', imageUrl);
                
                courseImage.onload = function() {
                    console.log('Imagem carregada com sucesso');
                    courseImage.style.display = 'block';
                    courseEmoji.style.display = 'none';
                };
                
                courseImage.onerror = function() {
                    console.log('Erro ao carregar imagem, mostrando emoji');
                    courseImage.style.display = 'none';
                    courseEmoji.style.display = 'block';
                };
                
                if (courseImage.complete) {
                    if (courseImage.naturalHeight === 0) {
                        courseImage.onerror();
                    } else {
                        courseImage.onload();
                    }
                }
            } else {
                console.log('Sem imagem, mostrando emoji');
                courseEmoji.style.display = 'block';
            }
            
            // Data de compra
            if (course.data_compra) {
                const purchaseDate = new Date(course.data_compra).toLocaleDateString('pt-BR');
                document.getElementById('modal-course-purchase-date').textContent = purchaseDate;
            } else {
                document.getElementById('modal-course-purchase-date').textContent = 'Data não disponível';
            }
            
            // Avatar do instrutor
            document.getElementById('modal-instructor-avatar').textContent = course.instructor_name.charAt(0).toUpperCase();
            document.getElementById('modal-instructor-name').textContent = course.instructor_name;
            
            // Link para acessar o curso
            document.getElementById('modal-access-course').href = `../cliente/assistir.php?curso_id=${course.id}`;
            
            // Mostrar o modal
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
    </script>
</body>
</html>