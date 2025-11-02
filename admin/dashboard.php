<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

requireLogin();
if (!isAdmin()) {
    header("Location: ../fitplay.php");
    exit();
}

// Estatísticas
$users_count = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$courses_count = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];
$pending_courses = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'pendente'")->fetch_assoc()['total'];
$pending_personals = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'personal' AND aprovado = 0")->fetch_assoc()['total'];

// Buscar personais pendentes
$pending_personals_list = $conn->query("SELECT * FROM usuarios WHERE tipo = 'personal' AND aprovado = 0");

// Buscar cursos pendentes
$pending_courses_list = $conn->query("SELECT c.*, u.nome as personal_name 
                                     FROM cursos c 
                                     JOIN usuarios u ON c.personal_id = u.id 
                                     WHERE c.status = 'pendente'");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitPlay</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <h1>Dashboard Administrativo</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Usuários</h3>
                <p><?php echo $users_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total de Cursos</h3>
                <p><?php echo $courses_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Cursos Pendentes</h3>
                <p><?php echo $pending_courses; ?></p>
            </div>
            <div class="stat-card">
                <h3>Personais Pendentes</h3>
                <p><?php echo $pending_personals; ?></p>
            </div>
        </div>
        
        <div class="dashboard-sections">
            <section class="pending-personals">
                <h2>Personais Trainers Pendentes</h2>
                
                <?php if ($pending_personals_list->num_rows > 0): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Data de Cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($personal = $pending_personals_list->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($personal['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($personal['email']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($personal['created_at'])); ?></td>
                                        <td>
                                            <a href="aprovar_personal.php?id=<?php echo $personal['id']; ?>&action=approve" class="btn btn-success">Aprovar</a>
                                            <a href="aprovar_personal.php?id=<?php echo $personal['id']; ?>&action=reject" class="btn btn-danger">Rejeitar</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Não há personais trainers pendentes de aprovação.</p>
                <?php endif; ?>
            </section>
            
            <section class="pending-courses">
                <h2>Cursos Pendentes</h2>
                
                <?php if ($pending_courses_list->num_rows > 0): ?>
                    <div class="courses-grid">
                        <?php while($course = $pending_courses_list->fetch_assoc()): ?>
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
                                    <p class="course-instructor">Por: <?php echo htmlspecialchars($course['personal_name']); ?></p>
                                    <p class="course-price">R$ <?php echo number_format($course['preco'], 2, ',', '.'); ?></p>
                                    <div class="course-actions">
                                        <a href="aprovar_curso.php?curso_id=<?php echo $course['id']; ?>&action=approve" class="btn btn-success">Aprovar</a>
                                        <a href="aprovar_curso.php?curso_id=<?php echo $course['id']; ?>&action=reject" class="btn btn-danger">Rejeitar</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>Não há cursos pendentes de aprovação.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>