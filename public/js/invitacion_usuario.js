// public/js/modules/invitations.js
class InvitationModule {
    constructor() {
        if ($('.invitations-section').length > 0) {
            this.init();
        }
    }
    
    init() {
        this.bindEvents();
        this.setupAnimations();
    }
    
    bindEvents() {
        // Manejar acciones de invitaciones con AJAX
        $(document).on('click', '.invitation-action-btn', this.handleInvitationAction.bind(this));
        
        // Actualizar invitaciones automáticamente cada 30 segundos
        setInterval(this.refreshInvitations.bind(this), 30000);
    }
    
    handleInvitationAction(e) {
        e.preventDefault();
        
        const button = $(e.target);
        const action = button.data('action');
        const invitationId = button.data('id');
        const card = button.closest('.invitation-card');
        
        // Confirmar acción
        const confirmMessage = action === 'aceptar' ? 
            '¿Estás seguro de que quieres aceptar esta invitación?' :
            '¿Estás seguro de que quieres rechazar esta invitación?';
            
        if (!confirm(confirmMessage)) {
            return;
        }
        
        // Mostrar loading
        this.showLoading();
        
        // Deshabilitar botones
        card.find('.invitation-action-btn').prop('disabled', true);
        
        // Enviar petición AJAX
        $.ajax({
            url: '/invitations/process',
            method: 'POST',
            data: {
                id_invitacion: invitationId,
                accion: action
            },
            success: (response) => {
                this.hideLoading();
                
                if (response.success) {
                    this.showAlert(response.message, 'success');
                    
                    // Animar y remover la tarjeta
                    card.addClass('processing');
                    setTimeout(() => {
                        card.fadeOut(500, () => {
                            card.remove();
                            this.updateStats();
                            this.checkEmptyState();
                        });
                    }, 1000);
                    
                } else {
                    this.showAlert(response.message, 'error');
                    // Rehabilitar botones
                    card.find('.invitation-action-btn').prop('disabled', false);
                }
            },
            error: (xhr, status, error) => {
                this.hideLoading();
                this.showAlert('Error de conexión. Intenta de nuevo.', 'error');
                // Rehabilitar botones
                card.find('.invitation-action-btn').prop('disabled', false);
            }
        });
    }
    
    refreshInvitations() {
        $.ajax({
            url: '/invitations/api',
            method: 'GET',
            success: (data) => {
                this.updateInvitationsDisplay(data.invitations);
                this.updateStatsDisplay(data.stats);
            },
            error: (xhr, status, error) => {
                console.error('Error refreshing invitations:', error);
            }
        });
    }
    
    updateInvitationsDisplay(invitations) {
        // Actualizar la lista de invitaciones si hay cambios
        const currentCount = $('.invitation-card').length;
        
        if (invitations.length !== currentCount) {
            // Recargar la página para mostrar cambios
            window.location.reload();
        }
    }
    
    updateStatsDisplay(stats) {
        $('.stat-card.invitaciones .number').text(stats.total_invitations);
        $('.stat-card.ofertas .number').text(stats.opportunities);
    }
    
    updateStats() {
        // Actualizar contadores localmente
        const currentCount = $('.invitation-card').length - 1; // -1 porque ya removimos una
        $('.stat-card.invitaciones .number').text(currentCount);
        $('.stat-card.ofertas .number').text(currentCount);
    }
    
    checkEmptyState() {
        if ($('.invitation-card').length === 0) {
            $('.invitations-section').html(`
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h2>No tienes invitaciones</h2>
                    <p>Las empresas interesadas en tu perfil aparecerán aquí</p>
                    <a href="/profile" class="action-button inline">
                        <div class="icon">👤</div>
                        <div class="label">Completar Perfil</div>
                    </a>
                </div>
            `);
        }
    }
    
    setupAnimations() {
        // Animaciones de entrada
        const cards = document.querySelectorAll('.invitation-card, .stat-card, .empty-state');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });
        
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    }
    
    showLoading() {
        $('#loadingOverlay').fadeIn(300);
    }
    
    hideLoading() {
        $('#loadingOverlay').fadeOut(300);
    }
    
    showAlert(message, type = 'info') {
        // Remover alertas anteriores
        $('.alert').remove();
        
        // Crear nueva alerta
        const alertClass = type === 'success' ? 'alert' : 'alert error';
        const alertHtml = `<div class="${alertClass}" style="display: none;">${message}</div>`;
        
        $('.header').prepend(alertHtml);
        $('.alert').slideDown(300);
        
        // Auto-hide después de 5 segundos
        setTimeout(() => {
            $('.alert').slideUp(300);
        }, 5000);
    }
}

// Inicializar módulo
$(document).ready(() => new InvitationModule());