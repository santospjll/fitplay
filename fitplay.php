<?php
// fitplay.php
session_start();
include 'includes/db.php';
include 'includes/auth.php';

$page_title = "FitPlay - Plataforma de Cursos Fitness";

// Buscar cursos aprovados E NÃO DELETADOS
$query = "SELECT c.*, u.nome as instructor_name 
          FROM cursos c 
          JOIN usuarios u ON c.personal_id = u.id 
          WHERE c.status = 'aprovado' AND c.deleted_at IS NULL
          ORDER BY c.id DESC 
          LIMIT 6";
$result = $conn->query($query);
$featured_courses = $result->fetch_all(MYSQLI_ASSOC);

// Buscar todos os cursos aprovados E NÃO DELETADOS
$query_all = "SELECT c.*, u.nome as instructor_name 
              FROM cursos c 
              JOIN usuarios u ON c.personal_id = u.id 
              WHERE c.status = 'aprovado' AND c.deleted_at IS NULL
              ORDER BY c.id DESC";
$result_all = $conn->query($query_all);
$all_courses = $result_all->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
       /* Override das cores para Laranja Energético */
:root {
    --primary-color: #FF6B35;
    --primary-dark: #E55A2B;
    --secondary-color: #FF8E53;
    --accent-color: #FFD166;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
}

/* ===== NAVBAR FIXA ===== */
header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

/* ===== HERO SECTION ===== */
.hero {
    margin-top: 0; /* remove o espaço cinza */
    height: 100vh; /* ocupa toda a tela */
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
    background-size: cover;
    background-position: center top;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

/* Garantir que o conteúdo não fique atrás da navbar */
body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* ===== MODAIS ===== */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}

/* ===== CARRINHO ===== */
.cart-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
    margin-bottom: 0.5rem;
    border: 1px solid rgba(255,255,255,0.1);
}

.cart-item-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    background: #141414;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-info {
    flex: 1;
    min-width: 0;
}

.cart-item-info h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
    color: #f4f4f4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cart-item-info p {
    margin: 0 0 0.25rem 0;
    font-size: 0.8rem;
    color: #999;
}

.cart-item-price {
    font-weight: bold;
    color: #FF6B35;
    font-size: 0.9rem;
}

.remove-item {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background-color 0.3s;
    flex-shrink: 0;
}

.remove-item:hover {
    background: rgba(220, 53, 69, 0.1);
}

/* ===== ANIMAÇÕES ===== */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.processing {
    animation: pulse 1.5s infinite;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Transforme seu corpo, transforme sua vida</h1>
            <p>Acesse os melhores cursos de fitness com os melhores profissionais</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn">Comece Agora</a>
            <?php else: ?>
                <a href="<?php echo $_SESSION['tipo']; ?>/dashboard.php" class="btn">Meu Dashboard</a>
            <?php endif; ?>
        </div>
    </section>

    <section id="featured-courses">
        <h2 class="section-title">Cursos em Destaque</h2>
        <div class="courses-grid">
            <?php foreach($featured_courses as $course): ?>
               <div class="course-card">
    <?php if (!empty($course['capa_url'])): ?>
        <div class="course-img">
            <img src="<?php echo $course['capa_url']; ?>" alt="<?php echo htmlspecialchars($course['titulo']); ?>">
        </div>
    <?php else: ?>
        <div class="course-img">
            <span>💪</span>
        </div>
    <?php endif; ?>
    <div class="course-info">
        <h3 class="course-title"><?php echo htmlspecialchars($course['titulo']); ?></h3>
        <p class="course-description"><?php echo htmlspecialchars($course['descricao']); ?></p>
        <p class="course-instructor">Por: <?php echo htmlspecialchars($course['instructor_name']); ?></p>
        <div class="course-footer">
            <div class="course-price">R$ <?php echo number_format($course['preco'], 2, ',', '.'); ?></div>
            <div class="course-actions">
    <!-- CORREÇÃO AQUI: mudado para cursos_detalhes.php -->
    <a href="cursos_detalhes.php?id=<?php echo $course['id']; ?>" class="btn btn-secondary">
        <i class="fas fa-eye"></i> Ver Detalhes
    </a>
    <?php if (isset($_SESSION['user_id']) && $_SESSION['tipo'] == 'cliente'): ?>
        <button class="btn" onclick="addToCart({
            id: <?php echo $course['id']; ?>,
            title: '<?php echo addslashes($course['titulo']); ?>',
            price: <?php echo $course['preco']; ?>,
            instructor: '<?php echo addslashes($course['instructor_name']); ?>',
            image: '<?php echo addslashes($course['capa_url'] ?? ''); ?>'
        })">
            <i class="fas fa-shopping-cart"></i> Comprar
        </button>
    <?php elseif (!isset($_SESSION['user_id'])): ?>
        <a href="login.php" class="btn btn-primary">Comprar</a>
    <?php endif; ?>
</div>
        </div>
    </div>
</div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="all-courses">
        <h2 class="section-title">Todos os Cursos</h2>
        <div class="courses-grid">
            <?php foreach($all_courses as $course): ?>
                <div class="course-card">
                    <?php if (!empty($course['capa_url'])): ?>
                        <div class="course-img">
                            <img src="<?php echo $course['capa_url']; ?>" alt="<?php echo htmlspecialchars($course['titulo']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="course-img">
                            <span>💪</span>
                        </div>
                    <?php endif; ?>
                    <div class="course-info">
                        <h3 class="course-title"><?php echo htmlspecialchars($course['titulo']); ?></h3>
                        <p class="course-description"><?php echo htmlspecialchars($course['descricao']); ?></p>
                        <p class="course-instructor">Por: <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                        <div class="course-price">R$ <?php echo number_format($course['preco'], 2, ',', '.'); ?></div>
                        <div class="course-actions">
                            <!-- CORREÇÃO AQUI: mudado para cursos_detalhes.php -->
                            <a href="cursos_detalhes.php?id=<?php echo $course['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </a>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['tipo'] == 'cliente'): ?>
                                <button class="btn" onclick="addToCart({
                                    id: <?php echo $course['id']; ?>,
                                    title: '<?php echo addslashes($course['titulo']); ?>',
                                    price: <?php echo $course['preco']; ?>,
                                    instructor: '<?php echo addslashes($course['instructor_name']); ?>',
                                    image: '<?php echo addslashes($course['capa_url'] ?? ''); ?>'
                                })">
                                    <i class="fas fa-shopping-cart"></i> Comprar
                                </button>
                            <?php elseif (!isset($_SESSION['user_id'])): ?>
                                <a href="login.php" class="btn btn-primary">Comprar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
// Sistema de carrinho simplificado
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

    <?php if (isset($_SESSION['user_id']) && $_SESSION['tipo'] == 'cliente'): ?>
        <!-- Modal do Carrinho - Incluído diretamente -->
        <div class="modal" id="cart-modal">
            <div class="modal-content" style="background: #1f1f1f; border-radius: 10px; max-width: 600px; width: 90%; max-height: 90%; overflow: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h2 style="margin: 0; color: #FF6B35; font-size: 1.5rem;">Meu Carrinho</h2>
                    <span class="close-modal" onclick="closeCartModal()" style="background: none; border: none; color: #999; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; font-weight: bold;">&times;</span>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <div id="empty-cart" style="text-align: center; padding: 2rem; color: #999;">
                        <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; color: #FF6B35;"></i>
                        <h3 style="margin-bottom: 0.5rem; color: #f4f4f4;">Seu carrinho está vazio</h3>
                        <p>Adicione cursos incríveis ao seu carrinho!</p>
                    </div>
                    
                    <div id="cart-content" style="display: none;">
                        <div id="cart-items" style="max-height: 400px; overflow-y: auto; margin-bottom: 1rem;"></div>
                        
                        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; font-size: 1.2rem; color: #f4f4f4;">
                                <strong>Total: </strong>
                                <span id="cart-total" style="color: #FF6B35; font-weight: bold;">R$ 0,00</span>
                            </div>
                            
                            <div style="display: flex; gap: 1rem;">
                                <button class="btn" onclick="clearCart()" style="flex: 1; background: #dc3545; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                    <i class="fas fa-trash"></i> Limpar Carrinho
                                </button>
                                <button class="btn" onclick="openPaymentModal()" style="flex: 1; background: #FF6B35; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                    <i class="fas fa-credit-card"></i> Finalizar Compra
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Pagamento PIX -->
        <div class="modal" id="payment-modal">
            <div class="modal-content" style="background: #1f1f1f; border-radius: 15px; max-width: 500px; width: 90%; padding: 2rem; text-align: center; box-shadow: 0 15px 40px rgba(0,0,0,0.7);">
                <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="margin: 0; color: #FF6B35; font-size: 1.5rem;">Pagamento via PIX</h2>
                    <span class="close-modal" onclick="closePaymentModal()" style="background: none; border: none; color: #999; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; font-weight: bold;">&times;</span>
                </div>
                
                <div id="payment-content">
                    <div class="payment-info" style="margin-bottom: 1.5rem;">
                        <p style="color: #f4f4f4; margin-bottom: 0.5rem;">Escaneie o QR code abaixo com seu app bancário</p>
                        <div style="background: white; padding: 1rem; border-radius: 10px; display: inline-block; margin: 1rem 0;">
                            <!-- Substitua pela URL do seu QR code PIX real -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PIX_AQUI_SEU_CODIGO_PIX" 
                                 alt="QR Code PIX" 
                                 style="width: 200px; height: 200px; border: 1px solid #ddd;">
                        </div>
                        <p style="color: #FF6B35; font-size: 1.5rem; font-weight: bold; margin: 1rem 0;" id="pix-amount">
                            R$ 0,00
                        </p>
                        <p style="color: #999; font-size: 0.9rem; margin-bottom: 1rem;">
                            Ou copie o código PIX abaixo:
                        </p>
                        <div style="background: rgba(255,255,255,0.1); padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                            <code style="color: #FF6B35; font-size: 0.8rem; word-break: break-all;" id="pix-code">
                                00020126580014br.gov.bcb.pix0136aae6e21a-8c09-4b58-ba8e-5f8c6c9e5f9f5204000053039865405<?php echo isset($total) ? number_format($total, 2, '', '') : '00000'; ?>5802BR5913FitPlay LTDA6008Sao Paulo62070503***6304
                            </code>
                        </div>
                        <button onclick="copyPixCode()" style="background: #FF6B35; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 600; margin-bottom: 1rem;">
                            <i class="fas fa-copy"></i> Copiar Código PIX
                        </button>
                    </div>
                    
                    <div class="payment-timer" style="margin: 1.5rem 0;">
                        <p style="color: #f4f4f4; margin-bottom: 0.5rem;">Tempo restante para pagamento:</p>
                        <div style="font-size: 1.5rem; color: #FF6B35; font-weight: bold;" id="payment-timer">
                            30:00
                        </div>
                    </div>
                    
                    <div class="payment-actions" style="display: flex; gap: 1rem; justify-content: center;">
                        <button onclick="closePaymentModal()" style="background: #666; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button onclick="confirmPayment()" style="background: #28a745; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-check"></i> Já Paguei
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        let paymentTimer;
        let timeLeft = 30 * 60; // 30 minutos em segundos

        // Funções do carrinho
        function openCartModal() {
            console.log('Abrindo modal do carrinho...');
            updateCartModal();
            document.getElementById('cart-modal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeCartModal() {
            document.getElementById('cart-modal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openPaymentModal() {
            // Calcular total
            const totalElement = document.getElementById('cart-total');
            const totalText = totalElement.textContent.replace('R$ ', '').replace(',', '.');
            const total = parseFloat(totalText);
            
            if (total <= 0) {
                showNotification('Carrinho vazio!', 'warning');
                return;
            }

            // Atualizar valor no modal PIX
            document.getElementById('pix-amount').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            
            // Iniciar timer
            startPaymentTimer();
            
            // Abrir modal
            document.getElementById('payment-modal').style.display = 'flex';
            closeCartModal();
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').style.display = 'none';
            document.body.style.overflow = 'auto';
            stopPaymentTimer();
        }

        function startPaymentTimer() {
            timeLeft = 30 * 60; // Reset para 30 minutos
            updateTimerDisplay();
            
            paymentTimer = setInterval(() => {
                timeLeft--;
                updateTimerDisplay();
                
                if (timeLeft <= 0) {
                    stopPaymentTimer();
                    showNotification('Tempo para pagamento esgotado!', 'warning');
                    closePaymentModal();
                }
            }, 1000);
        }

        function stopPaymentTimer() {
            if (paymentTimer) {
                clearInterval(paymentTimer);
                paymentTimer = null;
            }
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const timerElement = document.getElementById('payment-timer');
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Mudar cor quando estiver acabando o tempo
            if (timeLeft < 300) { // 5 minutos
                timerElement.style.color = '#dc3545';
            } else if (timeLeft < 600) { // 10 minutos
                timerElement.style.color = '#ffc107';
            } else {
                timerElement.style.color = '#FF6B35';
            }
        }

        function copyPixCode() {
            const pixCode = document.getElementById('pix-code').textContent;
            navigator.clipboard.writeText(pixCode).then(() => {
                showNotification('Código PIX copiado!', 'success');
            }).catch(() => {
                showNotification('Erro ao copiar código', 'error');
            });
        }

        async function confirmPayment() {
            const paymentContent = document.getElementById('payment-content');
            paymentContent.classList.add('processing');
            
            showNotification('Verificando pagamento...', 'info');
            
            try {
                // Chamar a API para processar a compra
                const response = await fetch('includes/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=finalizar_compra'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Pagamento confirmado com sucesso! 🎉', 'success');
                    
                    // Limpar carrinho e atualizar interface
                    updateCartCount();
                    closePaymentModal();
                    
                    // Redirecionar para dashboard após 2 segundos
                    setTimeout(() => {
                        window.location.href = 'cliente/dashboard.php';
                    }, 2000);
                } else {
                    showNotification('Erro: ' + result.message, 'error');
                    paymentContent.classList.remove('processing');
                }
                
            } catch (error) {
                console.error('Erro ao confirmar pagamento:', error);
                showNotification('Erro ao processar pagamento', 'error');
                paymentContent.classList.remove('processing');
            }
        }

        async function updateCartModal() {
            try {
                const response = await fetch('includes/cart.php?action=get');
                const itens = await response.json();
                console.log('Itens do carrinho:', itens);
                
                const cartItems = document.getElementById('cart-items');
                const cartTotal = document.getElementById('cart-total');
                const emptyCart = document.getElementById('empty-cart');
                const cartContent = document.getElementById('cart-content');
                
                if (itens.length === 0) {
                    emptyCart.style.display = 'block';
                    cartContent.style.display = 'none';
                } else {
                    emptyCart.style.display = 'none';
                    cartContent.style.display = 'block';
                    
                    // Atualizar lista de itens
                    cartItems.innerHTML = itens.map(item => `
                        <div class="cart-item">
                            <div class="cart-item-image">
                                ${item.image ? 
                                    `<img src="${item.image}" alt="${item.title}">` : 
                                    '<span>💪</span>'
                                }
                            </div>
                            <div class="cart-item-info">
                                <h4>${item.title}</h4>
                                <p>Por: ${item.instructor}</p>
                                <div class="cart-item-price">R$ ${item.price.toFixed(2).replace('.', ',')}</div>
                            </div>
                            <button class="remove-item" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `).join('');
                    
                    // Calcular total
                    const total = itens.reduce((sum, item) => sum + item.price, 0);
                    cartTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
                }
            } catch (error) {
                console.error('Erro ao atualizar modal:', error);
            }
        }

        async function removeFromCart(cursoId) {
            try {
                const response = await fetch('includes/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&curso_id=${cursoId}`
                });

                const result = await response.json();
                
                if (result.success) {
                    updateCartCount();
                    updateCartModal();
                    showNotification('Curso removido do carrinho', 'success');
                }
            } catch (error) {
                console.error('Erro ao remover do carrinho:', error);
                showNotification('Erro ao remover curso do carrinho', 'error');
            }
        }

        async function clearCart() {
            try {
                const response = await fetch('includes/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear'
                });

                const result = await response.json();
                
                if (result.success) {
                    updateCartCount();
                    updateCartModal();
                    showNotification('Carrinho limpo', 'success');
                }
            } catch (error) {
                console.error('Erro ao limpar carrinho:', error);
                showNotification('Erro ao limpar carrinho', 'error');
            }
        }

        // Adicionar evento de clique ao ícone do carrinho
        document.addEventListener('DOMContentLoaded', function() {
            const cartIcon = document.getElementById('cart-icon');
            if (cartIcon) {
                console.log('Ícone do carrinho encontrado, adicionando evento...');
                cartIcon.addEventListener('click', openCartModal);
                
                // Também adicionar como fallback
                cartIcon.onclick = openCartModal;
            } else {
                console.log('Ícone do carrinho NÃO encontrado!');
            }
        });

        // Fechar modais clicando fora
        document.addEventListener('click', function(e) {
            const cartModal = document.getElementById('cart-modal');
            const paymentModal = document.getElementById('payment-modal');
            
            if (e.target === cartModal) {
                closeCartModal();
            }
            if (e.target === paymentModal) {
                closePaymentModal();
            }
        });

        // Tecla ESC para fechar modais
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCartModal();
                closePaymentModal();
            }
        });

        // Função auxiliar para notificações
        function showNotification(message, type = 'info') {
            // Você pode implementar um sistema de notificações mais elaborado aqui
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };
            
            // Criar notificação simples
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type] || colors.info};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 5px;
                z-index: 10000;
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 4000);
        }
        </script>
    <?php endif; ?>
</body>
</html>