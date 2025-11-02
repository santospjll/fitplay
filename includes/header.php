[file name]: header.php
[file content begin]
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitPlay - Plataforma de Cursos Fitness</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF6B35;
            --primary-dark: #E55A2B;
            --secondary-color: #00b4d8;
            --dark-color: #141414;
            --dark-light: #1f1f1f;
            --light-color: #f4f4f4;
            --gray-color: #999;
            --success-color: #28a745;
            --warning-color: #ffa500;
            --danger-color: #dc3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--dark-color);
            color: var(--light-color);
            line-height: 1.6;
            
        }

        a {
            text-decoration: none;
            color: var(--light-color);
            transition: color 0.3s;
        }

        ul {
            list-style: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header - CORREÇÃO: Background totalmente preto */
        header {
            background-color: #000000; /* Alterado para preto sólido */
            padding: 15px 0;
            position: fixed;
            width: 100%;
            z-index: 1000;
            top: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            border-bottom: 1px solid #333; /* Adiciona uma borda sutil */
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* ADICIONE ESTAS LINHAS PARA COLOCAR A LOGO BEM NA ESQUERDA */
.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--primary-color);
    text-decoration: none;
    margin-right: auto; /* Empurra tudo para a direita */
    padding-left: 0; /* Remove qualquer padding */
}

/* Opcional: se quiser colar mesmo na borda */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Se ainda não estiver colada na borda, adicione isso: */
.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%; /* Garante que ocupa toda a largura */
}

        .logo-svg {
            position: relative;
            left: -350px;
            height: 40px;
            transition: transform 0.3s ease;
        }

        .logo:hover .logo-svg {
            transform: scale(1.1);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-links a {
            font-weight: 500;
            position: relative;
            padding: 8px 0;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-color);
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-name {
            color: var(--light-color);
            font-weight: 500;
        }

        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--light-color);
            color: var(--light-color);
        }

        .btn-outline:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-icon {
            position: relative;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .cart-icon:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary-color);
            color: black;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 90px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 3000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            transform: translateX(150%);
            transition: transform 0.4s;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background-color: var(--success-color);
        }

        .notification.error {
            background-color: var(--danger-color);
        }

        .notification.warning {
            background-color: var(--warning-color);
            color: #212529;
        }

        /* Hero Section */
        .hero {
            height: 80vh;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding-top: 80px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Courses Section */
        .section-title {
            font-size: 1.8rem;
            margin: 40px 0 20px;
            padding-left: 20px;
            position: relative;
            color: var(--primary-color);
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 20px;
            bottom: -5px;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 0 20px 40px;
        }

        .course-card {
            background-color: var(--dark-light);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .course-img {
            width: 100%;
            height: 160px;
            background-color: #555;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            position: relative;
            overflow: hidden;
        }

        .course-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .course-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--primary-color);
            color: black;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .course-info {
            padding: 20px;
        }

        .course-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            height: 50px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            color: white;
            
        }

        .course-description {
            color: var(--gray-color);
            font-size: 0.9rem;
            margin-bottom: 15px;
            height: 60px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .course-instructor {
            color: var(--gray-color);
        }

        .course-rating {
            color: var(--warning-color);
        }

        .course-price {
            font-weight: bold;
            color: white;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        .course-actions {
            display: flex;
            gap: 10px;
        }

        .course-actions .btn {
            flex: 1;
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .course-status {
            margin-bottom: 10px;
        }

        .status-pendente {
            color: var(--warning-color);
            font-weight: bold;
        }

        .status-aprovado {
            color: var(--success-color);
            font-weight: bold;
        }

        .status-rejeitado {
            color: var(--danger-color);
            font-weight: bold;
        }

        /* Footer */
        footer {
            background-color: #000;
            padding: 50px 0 20px;
            margin-top: 60px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .footer-logo-svg {
            width: 35px;
            height: 35px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--light-color);
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column a:hover {
            color: var(--primary-color);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background-color: #333;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .social-links a:hover {
            background-color: var(--primary-color);
        }

        .copyright {
            text-align: center;
            color: var(--gray-color);
            font-size: 0.9rem;
            padding-top: 20px;
            border-top: 1px solid #333;
        }

        /* Dashboard Styles */
        .dashboard-container {
            padding: 100px 20px 50px;
            min-height: 100vh;
        }

        .dashboard-container h1 {
            margin-bottom: 30px;
            font-size: 2.5rem;
            color: var(--primary-color);
        }

        .dashboard-actions {
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--dark-light), #2a2a2a);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .stat-card h3 {
            font-size: 1rem;
            color: var(--gray-color);
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .dashboard-sections {
            display: grid;
            gap: 40px;
        }

        .dashboard-sections h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            color: var(--primary-color);
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--dark-light);
            border-radius: 8px;
            overflow: hidden;
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        table th {
            background-color: #2a2a2a;
            font-weight: 600;
            color: var(--primary-color);
        }

        table tr:hover {
            background-color: #2a2a2a;
        }

        /* Alert Styles */
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert.success {
            background-color: rgba(40, 167, 69, 0.2);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }

        .alert.error {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid var(--danger-color);
            color: var(--danger-color);
        }

        .alert.warning {
            background-color: rgba(255, 193, 7, 0.2);
            border: 1px solid var(--warning-color);
            color: var(--warning-color);
        }

        /* Form Styles */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 20px 50px;
        }

        .auth-form {
            background-color: var(--dark-light);
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .auth-form h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-color);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #222;
            color: white;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .form-actions .btn {
            flex: 1;
        }

        /* Course Form */
        .course-form {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-color);
        }

        .empty-state h2 {
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        /* Course Player */
        .course-player-container {
            padding: 100px 20px 50px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .course-video h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .course-instructor {
            color: var(--gray-color);
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .video-wrapper {
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .video-wrapper iframe,
        .video-wrapper video {
            width: 100%;
            height: 500px;
            display: block;
        }

        .course-description h2 {
            margin-bottom: 15px;
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .course-description p {
            line-height: 1.8;
            color: var(--gray-color);
        }

        .course-sidebar {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .course-info-card {
            background-color: var(--dark-light);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .course-cover {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .course-info-card h3 {
            margin-bottom: 10px;
            font-size: 1.3rem;
            color: var(--primary-color);
        }

        .meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: var(--gray-color);
        }

        .meta-item i {
            margin-right: 10px;
            width: 20px;
        }

        /* File Upload */
        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload-btn {
            background-color: #333;
            color: white;
            padding: 12px 15px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
            border: 1px solid #444;
        }

        .file-upload-btn:hover {
            background-color: #444;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content p {
                font-size: 1.2rem;
            }

            .courses-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .user-info {
                flex-direction: column;
                gap: 10px;
            }

            .course-player-container {
                grid-template-columns: 1fr;
                padding: 100px 15px 30px;
            }

            .video-wrapper iframe,
            .video-wrapper video {
                height: 300px;
            }

            .dashboard-container {
                padding: 100px 15px 30px;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .form-actions {
                flex-direction: column;
            }

            .auth-form {
                padding: 30px 20px;
            }
        }

        @media (max-width: 480px) {
            .courses-grid {
                grid-template-columns: 1fr;
            }
            
            .course-actions {
                flex-direction: column;
            }
            
            .hero {
                height: 70vh;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Notification -->
    <div class="notification" id="notification"></div>

    <header>
        <div class="container">
            <div class="header-content">
    <a href="<?php echo $base_path ?? ''; ?>fitplay.php" class="logo">
    <!-- Logo a partir de arquivo SVG -->
    <?php
    // SOLUÇÃO DEFINITIVA - Caminho absoluto baseado no servidor
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script_path = dirname($_SERVER['SCRIPT_NAME']);
    
    // Remove barras duplas e ajusta o caminho
    $base_url = $protocol . "://" . $host . $script_path;
    $base_url = rtrim($base_url, '/') . '/';
    
    // Determinar o caminho da logo
    if (strpos($script_path, '/admin') !== false || 
        strpos($script_path, '/personal') !== false || 
        strpos($script_path, '/cliente') !== false) {
        $logo_url = $base_url . '../logo/fitplay1.svg';
    } else {
        $logo_url = $base_url . 'logo/fitplay1.svg';
    }
    ?>
    <img src="<?php echo $logo_url; ?>" alt="FitPlay" class="logo-svg">
</a>
                   
                <ul class="nav-links">
                    <?php
                    // Determinar o caminho base baseado no diretório atual
                    $current_dir = basename(dirname($_SERVER['PHP_SELF']));
                    $base_path = ($current_dir == 'admin' || $current_dir == 'personal' || $current_dir == 'cliente') ? '../' : '';
                    ?>
                    
                    <li><a href="<?php echo $base_path; ?>fitplay.php">Início</a></li>
                    <li><a href="<?php echo $base_path; ?>fitplay.php#all-courses">Cursos</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $base_path . $_SESSION['tipo']; ?>/dashboard.php">Meu Dashboard</a></li>
                        <li class="user-info">
                            <span class="user-name">Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?></span>
                            <?php if ($_SESSION['tipo'] == 'cliente'): ?>
                                <div class="cart-icon" id="cart-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="cart-count">0</span>
                                </div>
                            <?php endif; ?>
                            <a href="<?php echo $base_path; ?>../fitplay/logout.php" class="btn btn-outline">Sair</a>
                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_path; ?>login.php" class="btn btn-outline">Entrar</a></li>
                        <li><a href="<?php echo $base_path; ?>register.php" class="btn">Cadastrar</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>
[file content end]