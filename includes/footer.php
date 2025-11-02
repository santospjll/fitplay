<footer>
    <div class="container">
        <div class="footer-logo">
            <img src="<?php echo $base_path ?? ''; ?>assets/img/logo/fitplay.svg" alt="FitPlay" class="footer-logo-svg" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzUiIGhlaWdodD0iMzUiIHZpZXdCb3g9IjAgMCAzNSAzNSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTE3LjUgMTJWMTBIMTcuNVYxMloiIGZpbGw9IiM4MERFRUEiLz4KPHBhdGggZD0iTTE3LjUgMTcuNVYxNEgxNy41VjE3LjVaIiBmaWxsPSIjODBERUVBIi8+CjxwYXRoIGQ9Ik0xNy41IDIzVjIxSDE3LjVWMjNaIiBmaWxsPSIjODBERUVBIi8+CjxwYXRoIGQ9Ik0xMiAxNy41SDEwLjVWMTcuNUgxMloiIGZpbGw9IiM4MERFRUEiLz4KPHBhdGggZD0iTTIxIDE3LjVIMTkuNVYxNy41SDIxWiIgZmlsbD0iIzgwREVFQSIvPgo8cGF0aCBkPSJNMjUgMTcuNUgyMy41VjE3LjVIMjVaIiBmaWxsPSIjODBERUVBIi8+CjxwYXRoIGQ9Ik0yOSAxNy41SDI3LjVWMTcuNUgyOVoiIGZpbGw9IiM4MERFRUEiLz4KPHBhdGggZD0iTTE0IDE0SDEyLjVWMTQuNUgxNFoiIGZpbGw9IiM4MERFRUEiLz4KPHBhdGggZD0iTTIzLjUgMTRIMjFWMTRIMjMuNVoiIGZpbGw9IiM4MERFRUEiLz4KPHBhdGggZD0iTTI3LjUgMTRIMjVWMTRIMjcuNVoiIGZpbGw9IiM4MERFRUEiLz4KPHBhdGggZD0iTTI5IDE0SDI3LjVWMTRIMjlaIiBmaWxsPSIjODBERUVBIi8+CjxwYXRoIGQ9Ik0xNCAyMUgxMi41VjIxSDE0WiIgZmlsbD0iIzgwREVFQSIvPgo8cGF0aCBkPSJNMTYgMjFIMTRWMjFIMTZaIiBmaWxsPSIjODBERUVBIi8+CjxwYXRoIGQ9Ik0yMSAyMUgxOS41VjIxSDIxWiIgZmlsbD0iIzgwREVFQSIvPgo8cGF0aCBkPSJNMjMuNSAyMUgyMVYyMUgyMy41WiIgZmlsbD0iIzgwREVFQSIvPgo8cGF0aCBkPSJNMjcgMjFIMjVWMjFIMjdaIiBmaWxsPSIjODBERUVBIi8+CjxwYXRoIGQ9Ik0yOSAyMUgyNy41VjIxSDI5WiIgZmlsbD0iIzgwREVFQSIvPgo8L3N2Zz4='">
            FitPlay
        </div>
        
        <div class="footer-content">
            <div class="footer-column">
                <h3>FitPlay</h3>
                <p>A plataforma líder em cursos de fitness online, conectando os melhores profissionais a alunos dedicados.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Links Rápidos</h3>
                <ul>
                    <li><a href="<?php echo $base_path ?? ''; ?>index.php">Início</a></li>
                    <li><a href="<?php echo $base_path ?? ''; ?>index.php#all-courses">Cursos</a></li>
                    <li><a href="<?php echo $base_path ?? ''; ?>register.php?tipo=personal">Para Personais</a></li>
                    <li><a href="#">Planos</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Suporte</h3>
                <ul>
                    <li><a href="#">Central de Ajuda</a></li>
                    <li><a href="#">Contato</a></li>
                    <li><a href="#">Política de Privacidade</a></li>
                    <li><a href="#">Termos de Uso</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Newsletter</h3>
                <p>Inscreva-se para receber novidades e ofertas exclusivas.</p>
                <div class="form-group">
                    <input type="email" placeholder="Seu e-mail">
                    <button class="btn" style="margin-top: 10px; width: 100%;">Inscrever</button>
                </div>
            </div>
        </div>
        <p class="copyright">&copy; 2025 FitPlay. Todos os direitos reservados.</p>
    </div>
</footer>

<script>
// Shopping cart functionality
let cart = [];

// Initialize cart from localStorage
function initCart() {
    const savedCart = localStorage.getItem('fitplay_cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartUI();
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    if (!notification) return;
    
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 4000);
}

// Add to cart
function addToCart(course) {
    const existingItem = cart.find(item => item.id === course.id);
    
    if (existingItem) {
        showNotification('Este curso já está no seu carrinho!', 'error');
        return;
    }
    
    cart.push({...course, quantity: 1});
   
        </div>
</footer>

<?php if (isset($_SESSION['user_id']) && $_SESSION['tipo'] == 'cliente'): ?>
    <?php 
    // Debug: verificar se o arquivo existe
    if (file_exists('includes/cart_modal.php')) {
        include 'includes/cart_modal.php';
        echo '<!-- Modal do carrinho incluído -->';
    } else {
        echo '<!-- ERRO: cart_modal.php não encontrado -->';
    }
    ?>
<?php endif; ?>

</body>
</html>