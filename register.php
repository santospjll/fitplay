<?php
// register.php (colocar na pasta raiz)
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    
    // Verificar se email já existe
    $check_query = "SELECT id FROM usuarios WHERE email = '$email'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        $error = "Este email já está cadastrado!";
    } else {
        // Aprovação automática para clientes, pendente para personais
        $aprovado = ($tipo == 'cliente') ? 1 : 0;
        
        $query = "INSERT INTO usuarios (nome, email, senha, tipo, aprovado) 
                  VALUES ('$nome', '$email', '$senha', '$tipo', $aprovado)";
        
        if ($conn->query($query)) {
            if ($tipo == 'cliente') {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['nome'] = $nome;
                $_SESSION['email'] = $email;
                $_SESSION['tipo'] = $tipo;
                
                header("Location: cliente/dashboard.php");
                exit();
            } else {
                $success = "Cadastro realizado! Aguarde a aprovação do administrador.";
            }
        } else {
            $error = "Erro ao cadastrar: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FitFlux</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="auth-container">
        <div class="auth-form">
            <h2>Cadastre-se na FitFlux</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <div class="form-group">
                    <label for="tipo">Tipo de Usuário</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Selecione</option>
                        <option value="cliente">Aluno</option>
                        <option value="personal">Personal Trainer</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Cadastrar</button>
            </form>
            
            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>