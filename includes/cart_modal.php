<!-- Modal do Carrinho -->
<div class="modal" id="cart-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: #1f1f1f; border-radius: 10px; max-width: 600px; width: 90%; max-height: 90%; overflow: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h2 style="margin: 0; color: #80DEEA; font-size: 1.5rem;">Meu Carrinho</h2>
            <span class="close-modal" onclick="closeCartModal()" style="background: none; border: none; color: #999; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; font-weight: bold;">&times;</span>
        </div>
        <div class="modal-body" style="padding: 1.5rem;">
            <div id="empty-cart" style="text-align: center; padding: 2rem; color: #999;">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; color: #80DEEA;"></i>
                <h3 style="margin-bottom: 0.5rem; color: #f4f4f4;">Seu carrinho está vazio</h3>
                <p>Adicione cursos incríveis ao seu carrinho!</p>
            </div>
            
            <div id="cart-content" style="display: none;">
                <div id="cart-items" style="max-height: 400px; overflow-y: auto; margin-bottom: 1rem;">
                    <!-- Itens do carrinho serão inseridos aqui via JavaScript -->
                </div>
                
                <div class="cart-summary" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                    <div class="cart-total" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; font-size: 1.2rem; color: #f4f4f4;">
                        <strong>Total: </strong>
                        <span id="cart-total" style="color: #80DEEA; font-weight: bold;">R$ 0,00</span>
                    </div>
                    
                    <div class="cart-actions" style="display: flex; gap: 1rem;">
                        <button class="btn" onclick="clearCart()" style="flex: 1; background: #dc3545; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-trash"></i> Limpar Carrinho
                        </button>
                        <button class="btn" onclick="finalizarCompra()" style="flex: 1; background: #80DEEA; color: black; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-credit-card"></i> Finalizar Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
    color: #80DEEA;
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

.btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}
</style>

<script>
// Funções do carrinho
function openCartModal() {
    updateCartModal();
    document.getElementById('cart-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeCartModal() {
    document.getElementById('cart-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Fechar modal clicando fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('cart-modal');
    if (e.target === modal) {
        closeCartModal();
    }
});

// Tecla ESC para fechar modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCartModal();
    }
});

async function updateCartModal() {
    try {
        const response = await fetch('includes/cart.php?action=get');
        const itens = await response.json();
        
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
                            '<span style="font-size: 1.5rem;">💪</span>'
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

async function finalizarCompra() {
    try {
        const response = await fetch('includes/cart.php?action=get');
        const itens = await response.json();
        
        if (itens.length === 0) {
            showNotification('Carrinho vazio!', 'warning');
            return;
        }

        showNotification('Processando pagamento...', 'info');
        
        // Simular processamento
        setTimeout(() => {
            showNotification('Compra realizada com sucesso!', 'success');
            clearCart();
            closeCartModal();
            
            setTimeout(() => {
                window.location.href = 'cliente/dashboard.php';
            }, 2000);
        }, 2000);
        
    } catch (error) {
        console.error('Erro ao finalizar compra:', error);
        showNotification('Erro ao processar compra', 'error');
    }
}

function showNotification(message, type = 'info') {
    // Remover notificações existentes
    const existingNotifications = document.querySelectorAll('.notification-cart');
    existingNotifications.forEach(notif => notif.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-cart`;
    notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 3000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        transform: translateX(150%);
        transition: transform 0.4s;
    `;
    
    if (type === 'success') notification.style.backgroundColor = '#28a745';
    else if (type === 'error') notification.style.backgroundColor = '#dc3545';
    else if (type === 'warning') notification.style.backgroundColor = '#ffa500';
    else notification.style.backgroundColor = '#80DEEA';
    
    notification.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem;">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover após 3 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.transform = 'translateX(150%)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 400);
        }
    }, 3000);
}

// Adicionar evento de clique ao ícone do carrinho no header
document.addEventListener('DOMContentLoaded', function() {
    const cartIcon = document.getElementById('cart-icon');
    if (cartIcon) {
        cartIcon.addEventListener('click', openCartModal);
    }
});
</script>