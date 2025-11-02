<?php
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isPersonal()) {
    header("Location: ../fitplay.php");
    exit();
}

$personal_id = $_SESSION['user_id'];

// Verificar status de aprovação
$user_query = $conn->query("SELECT aprovado FROM usuarios WHERE id = $personal_id");
$user_data = $user_query->fetch_assoc();
$is_approved = $user_data['aprovado'];

// Buscar cursos do personal (APENAS NÃO DELETADOS)
$courses_query = "SELECT * FROM cursos WHERE personal_id = $personal_id AND deleted_at IS NULL ORDER BY id DESC";
$courses_result = $conn->query($courses_query);

// Buscar vendas
$sales_query = "SELECT c.titulo, comp.data_compra, u.nome as aluno_nome 
                FROM compras comp 
                JOIN cursos c ON comp.curso_id = c.id 
                JOIN usuarios u ON comp.cliente_id = u.id 
                WHERE c.personal_id = $personal_id 
                ORDER BY comp.data_compra DESC";
$sales_result = $conn->query($sales_query);

// Mensagens
$message = '';
if (isset($_GET['error']) && $_GET['error'] == 'waiting_approval') {
    $message = '<div class="alert warning">Aguarde a aprovação do administrador para criar cursos.</div>';
}
if (isset($_GET['success'])) {
    $message = '<div class="alert success">' . htmlspecialchars($_GET['success']) . '</div>';
}
if (isset($_GET['error']) && $_GET['error'] != 'waiting_approval') {
    $message = '<div class="alert error">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Trainer Dashboard - FitPlay</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: 1px solid #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .course-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .course-actions .btn {
            flex: 1;
            text-align: center;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <h1>Dashboard do Personal Trainer</h1>
        
        <?php echo $message; ?>
        
        <?php if (!$is_approved): ?>
            <div class="alert warning">
                <h3>Cadastro em Análise</h3>
                <p>Seu cadastro está aguardando aprovação do administrador. Você poderá criar cursos após a aprovação.</p>
            </div>
        <?php else: ?>
            <div class="dashboard-actions">
                <a href="novo_curso.php" class="btn">
                    <i class="fas fa-plus"></i> Criar Novo Curso
                </a>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-sections">
            <section class="my-courses">
                <h2>Meus Cursos</h2>
                
                <?php if ($courses_result->num_rows > 0): ?>
                    <div class="courses-grid">
                        <?php while($course = $courses_result->fetch_assoc()): ?>
                            <div class="course-card">
                                <?php if (!empty($course['capa_url'])): ?>
                                    <div class="course-img">
                                        <img src="../<?php echo $course['capa_url']; ?>" alt="<?php echo htmlspecialchars($course['titulo']); ?>">
                                        <div class="course-badge"><?php echo strtoupper($course['status']); ?></div>
                                    </div>
                                <?php else: ?>
                                    <div class="course-img">
                                        <span>💪</span>
                                        <div class="course-badge"><?php echo strtoupper($course['status']); ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="course-info">
                                    <h3 class="course-title"><?php echo htmlspecialchars($course['titulo']); ?></h3>
                                    <p class="course-description"><?php echo htmlspecialchars($course['descricao']); ?></p>
                                    <p class="course-status">Status: 
                                        <span class="status-<?php echo $course['status']; ?>">
                                            <?php 
                                            $status_text = [
                                                'pendente' => 'Pendente',
                                                'aprovado' => 'Aprovado',
                                                'rejeitado' => 'Rejeitado'
                                            ];
                                            echo $status_text[$course['status']]; 
                                            ?>
                                        </span>
                                    </p>
                                    <p class="course-price">R$ <?php echo number_format($course['preco'], 2, ',', '.'); ?></p>
                                    <div class="course-actions">
                                        <a href="editar_curso.php?curso_id=<?php echo $course['id']; ?>" class="btn">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                       <a href="excluir_curso.php?curso_id=<?php echo $course['id']; ?>" class="btn">
                                            <i class="fas fa-edit"></i> Excluir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h2>Nenhum curso criado</h2>
                        <p>Você ainda não criou nenhum curso.</p>
                        <?php if ($is_approved): ?>
                            <a href="novo_curso.php" class="btn">Criar Primeiro Curso</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
            
            <?php if ($sales_result->num_rows > 0): ?>
            <section class="my-sales">
                <h2>Minhas Vendas</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Aluno</th>
                                <th>Data da Compra</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($sale = $sales_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sale['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['aluno_nome']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($sale['data_compra'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>

   <?php include '../includes/footer.php'; ?>

<script>
// Solução com verificação de URL e tratamento de erro
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-danger') || 
            e.target.closest('.btn-danger')) {
            
            const button = e.target.classList.contains('btn-danger') ? 
                          e.target : e.target.closest('.btn-danger');
            
            const cursoId = button.getAttribute('data-curso-id');
            const courseCard = button.closest('.course-card');
            
            if (confirm('Tem certeza que deseja excluir este curso?\n\nSe o curso já foi comprado por alunos, ele será arquivado e continuará disponível apenas para esses alunos. Caso contrário, será excluído permanentemente.')) {
                // Mostrar loading
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';
                button.classList.add('loading');
                courseCard.classList.add('loading');
                
                // Testar se o arquivo existe antes de redirecionar
                testarArquivoExclusao(cursoId);
            }
            
            e.preventDefault();
        }
    });
    
    function testarArquivoExclusao(cursoId) {
        // Primeiro verifica se o arquivo existe
        fetch('excluir_curso.php?curso_id=' + cursoId)
            .then(response => {
                if (response.ok) {
                    // Arquivo existe, redireciona
                    window.location.href = 'excluir_curso.php?curso_id=' + cursoId;
                } else {
                    // Arquivo não encontrado, mostra erro
                    alert('Erro: Arquivo de exclusão não encontrado (404). Entre em contato com o suporte.');
                    // Recarregar a página para remover o estado de loading
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao tentar excluir o curso. Verifique o console para mais detalhes.');
                location.reload();
            });
    }
});
</script>

</body>
</html>