// Sistema de Modal de Preview
class FitPlayPreview {
    constructor() {
        this.modal = null;
        this.init();
    }

    init() {
        this.createModal();
        this.bindEvents();
    }

    createModal() {
        // Criar modal dinamicamente
        this.modal = document.createElement('div');
        this.modal.className = 'modal preview-modal';
        this.modal.id = 'preview-modal';
        this.modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Detalhes do Curso</h2>
                    <span class="close-modal">&times;</span>
                </div>
                <div class="modal-body" id="preview-modal-body">
                    <!-- Conteúdo será carregado via AJAX -->
                </div>
            </div>
        `;
        document.body.appendChild(this.modal);
    }

    // Carregar dados do curso via AJAX
    async loadCourseData(courseId) {
        try {
            const response = await fetch(`api/get_course.php?id=${courseId}`);
            const course = await response.json();
            return course;
        } catch (error) {
            console.error('Erro ao carregar dados do curso:', error);
            return null;
        }
    }

    // Mostrar preview do curso
    async showPreview(courseId) {
        // Mostrar loading
        this.showLoading();

        // Carregar dados do curso
        const course = await this.loadCourseData(courseId);
        
        if (!course) {
            this.showError('Erro ao carregar dados do curso.');
            return;
        }

        // Preencher modal com dados do curso
        this.populateModal(course);
        
        // Mostrar modal
        this.openModal();
    }

    showLoading() {
        document.getElementById('preview-modal-body').innerHTML = `
            <div class="loading-preview">
                <div class="loading-spinner"></div>
                <p>Carregando informações do curso...</p>
            </div>
        `;
        this.openModal();
    }

    showError(message) {
        document.getElementById('preview-modal-body').innerHTML = `
            <div class="error-preview">
                <i class="fas fa-exclamation-triangle"></i>
                <p>${message}</p>
                <button class="btn btn-primary" onclick="fitPlayPreview.closeModal()">Fechar</button>
            </div>
        `;
    }

    populateModal(course) {
        const isPurchased = course.ja_comprou || false;
        const videoPreview = this.getVideoPreview(course.video_url);

        document.getElementById('preview-modal-body').innerHTML = `
            <div class="preview-container">
                <div class="preview-header">
                    <div class="preview-image">
                        ${course.capa_url ? 
                            `<img src="${course.capa_url}" alt="${course.titulo}">` : 
                            '<div class="course-img-placeholder">💪</div>'
                        }
                    </div>
                    <div class="preview-info">
                        <h3 class="preview-title">${course.titulo}</h3>
                        <p class="preview-instructor">
                            <i class="fas fa-user-tie"></i>
                            Por: ${course.instructor_name}
                        </p>
                        <p class="preview-description">${course.descricao}</p>
                        <div class="preview-meta">
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>Acesso Vitalício</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-play-circle"></i>
                                <span>Vídeo Aulas</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="preview-content">
                    <div class="preview-video-section">
                        <h4>Preview do Curso</h4>
                        <div class="video-preview">
                            ${videoPreview}
                        </div>
                    </div>

                    <div class="preview-details">
                        <div class="details-section">
                            <h4>O que você vai aprender</h4>
                            <ul class="learning-list">
                                <li><i class="fas fa-check"></i> Técnicas profissionais</li>
                                <li><i class="fas fa-check"></i> Exercícios demonstrativos</li>
                                <li><i class="fas fa-check"></i> Orientação personalizada</li>
                                <li><i class="fas fa-check"></i> Material de apoio</li>
                            </ul>
                        </div>

                        <div class="details-section">
                            <h4>Este curso inclui</h4>
                            <ul class="features-list">
                                <li><i class="fas fa-play-circle"></i> Acesso vitalício</li>
                                <li><i class="fas fa-mobile-alt"></i> Acesso em todos dispositivos</li>
                                <li><i class="fas fa-certificate"></i> Certificado de conclusão</li>
                                <li><i class="fas fa-question-circle"></i> Suporte do instrutor</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="preview-actions">
                    <div class="price-section">
                        <div class="course-price">R$ ${parseFloat(course.preco).toFixed(2)}</div>
                        ${isPurchased ? 
                            `<a href="cliente/assistir.php?curso_id=${course.id}" class="btn btn-primary">
                                <i class="fas fa-play"></i> Acessar Curso
                            </a>` :
                            `<button class="btn btn-primary" onclick="addToCartAndClose(${course.id}, '${course.titulo}', ${course.preco}, '${course.instructor_name}', '${course.capa_url || ''}')">
                                <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                            </button>`
                        }
                    </div>
                    <button class="btn btn-secondary" onclick="fitPlayPreview.closeModal()">
                        Continuar Navegando
                    </button>
                </div>
            </div>
        `;
    }

    getVideoPreview(videoUrl) {
        if (!videoUrl) {
            return '<div class="no-preview">Preview não disponível</div>';
        }

        if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
            const videoId = this.extractYouTubeId(videoUrl);
            if (videoId) {
                return `
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1" 
                                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    </div>
                `;
            }
        }

        return `
            <div class="video-wrapper">
                <video controls style="width: 100%;">
                    <source src="${videoUrl}" type="video/mp4">
                    Seu navegador não suporta o vídeo.
                </video>
            </div>
        `;
    }

    extractYouTubeId(url) {
        const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[7].length === 11) ? match[7] : false;
    }

    openModal() {
        this.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        this.modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    bindEvents() {
        // Fechar modal com X
        this.modal.querySelector('.close-modal').addEventListener('click', () => {
            this.closeModal();
        });

        // Fechar modal clicando fora
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeModal();
            }
        });

        // Fechar com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.style.display === 'flex') {
                this.closeModal();
            }
        });
    }
}

// Inicializar preview system
const fitPlayPreview = new FitPlayPreview();

// Função global para abrir preview
window.openCoursePreview = (courseId) => {
    fitPlayPreview.showPreview(courseId);
};

// Função para adicionar ao carrinho e fechar modal
window.addToCartAndClose = (id, title, price, instructor, image) => {
    const success = addToCart({
        id: id,
        title: title,
        price: parseFloat(price),
        instructor: instructor,
        image: image
    });
    
    if (success) {
        fitPlayPreview.closeModal();
    }
};