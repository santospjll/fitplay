class Carrinho {
    constructor() {
        this.init();
    }

    init() {
        this.updateCartCount();
        this.setupEventListeners();
    }

    async addToCart(curso) {
        try {
            const response = await fetch('includes/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&curso=${encodeURIComponent(JSON.stringify(curso))}`
            });

            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.updateCartCount();
                if (document.getElementById('cart-modal').style.display === 'flex') {
                    this.updateCartModal();
                }
            } else {
                this.showNotification(result.message, 'warning');
            }
        } catch (error) {
            console.error('Erro ao adicionar ao carrinho:', error);
            this.showNotification('Erro ao adicionar curso ao carrinho', 'error');
        }
    }

    async removeFromCart(cursoId) {
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
                this.updateCartCount();
                this.updateCartModal();
                this.showNotification('Curso removido do carrinho', 'success');
            }
        } catch (error) {
            console.error('Erro ao remover do carrinho:', error);
            this.showNotification('Erro ao remover curso do carrinho', 'error');
        }
    }

    async clearCart() {
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
                this.updateCartCount();
                this.updateCartModal();
                this.showNotification('Carrinho limpo', 'success');
            }
        } catch (error) {
            console.error('Erro ao limpar carrinho:', error);
            this.showNotification('Erro ao limpar carrinho', 'error');
        }
    }

    async updateCartCount() {
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

    async updateCartModal() {
        try {
            const response = await fetch('includes/cart.php?action=get');
            const itens = await response.json();
            
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const emptyCart = document.getElementById('empty-cart');
            const cartContent = document.getElementById('cart-content');
            
            if (itens.length === 0) {
                if (emptyCart) emptyCart.style.display = 'block';
                if (cartContent) cartContent.style.display = 'none';
            } else {
                if (emptyCart) emptyCart.style.display = 'none';
                if (cartContent) cartContent.style.display = 'block';
                
                if (cartItems) {
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
                            <button class="remove-item" onclick="carrinho.removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `).join('');
                }
                
                const total = itens.reduce((sum, item) => sum + item.price, 0);
                if (cartTotal) cartTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            }
        } catch (error) {
            console.error('Erro ao atualizar modal:', error);
        }
    }

    async finalizarCompra() {
        try {
            const response = await fetch('includes/cart.php?action=get');
            const itens = await response.json();
            
            if (itens.length === 0) {
                this.showNotification('Carrinho vazio!', 'warning');
                return;
            }

            this.showNotification('Processando pagamento...', 'info');
            
            // Simular processamento
            setTimeout(() => {
                this.showNotification('Compra realizada com sucesso!', 'success');
                this.clearCart();
                this.closeCartModal();
                
                setTimeout(() => {
                    window.location.href = 'cliente/dashboard.php';
                }, 2000);
            }, 2000);
            
        } catch (error) {
            console.error('Erro ao finalizar compra:', error);
            this.showNotification('Erro ao processar compra', 'error');
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type} show`;
        notification.innerHTML = `
            <div class="notification-content">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }

    openCartModal() {
        this.updateCartModal();
        document.getElementById('cart-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    closeCartModal() {
        document.getElementById('cart-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    setupEventListeners() {
        const cartModal = document.getElementById('cart-modal');
        if (cartModal) {
            cartModal.addEventListener('click', (e) => {
                if (e.target === cartModal) {
                    this.closeCartModal();
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeCartModal();
            }
        });

        const cartIcon = document.getElementById('cart-icon');
        if (cartIcon) {
            cartIcon.addEventListener('click', () => {
                this.openCartModal();
            });
        }
    }
}

const carrinho = new Carrinho();

function addToCart(curso) {
    carrinho.addToCart(curso);
}