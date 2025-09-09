document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('signupEmpresaForm');

    // Crear modal para errores
    createErrorModal();

    form.addEventListener('submit', function(e) {
        const nombreEmpresa = document.getElementById('nombre_empresa').value.trim();
        const email = document.getElementById('email').value.trim();
        const password1 = document.getElementById('password1').value;
        const password2 = document.getElementById('password2').value;
        const direccion = document.getElementById('direccion').value.trim();
        const nitCif = document.getElementById('nit_cif').value.trim();
        const telefono = document.getElementById('telefono').value.trim();

        // Validar campos vacíos
        if (!nombreEmpresa || !email || !password1 || !password2 || !direccion || !nitCif || !telefono) {
            showError("Por favor, completa todos los campos obligatorios.");
            e.preventDefault();
            return;
        }

        // Validar email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError("Por favor, ingresa un correo electrónico válido.");
            e.preventDefault();
            return;
        }

        // Validar teléfono
        const phoneRegex = /^[\d\s\+\-\(\)]+$/;
        if (!phoneRegex.test(telefono)) {
            showError("Por favor, ingresa un número de teléfono válido.");
            e.preventDefault();
            return;
        }

        // Validar contraseña robusta
        const passwordError = validarContrasena(password1);
        if (passwordError) {
            showError(passwordError);
            e.preventDefault();
            return;
        }

        // Validar que las contraseñas coincidan
        if (password1 !== password2) {
            showError("Las contraseñas no coinciden.");
            e.preventDefault();
            return;
        }
    });

    function validarContrasena(password) {
        if (password.length < 6) {
            return "La contraseña debe tener al menos 6 caracteres.";
        }
        if (!/[a-z]/.test(password)) {
            return "La contraseña debe contener al menos una letra minúscula.";
        }
        if (!/[A-Z]/.test(password)) {
            return "La contraseña debe contener al menos una letra mayúscula.";
        }
        if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
            return "La contraseña debe contener al menos un símbolo especial (!@#$%^&* etc.).";
        }
        return null;
    }

    // Mismas funciones de modal que el signup de candidatos
    function createErrorModal() {
        const modalHTML = `
            <div id="errorModal" class="modal-overlay" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="error-icon">⚠️</span>
                        <h3>Error de validación</h3>
                        <span class="close-btn" onclick="closeErrorModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p id="errorMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button onclick="closeErrorModal()" class="btn-ok">Entendido</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    window.showError = function(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').style.display = 'flex';
    }

    window.closeErrorModal = function() {
        document.getElementById('errorModal').style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeErrorModal();
        }
    });
});