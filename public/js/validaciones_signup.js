document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('signupForm') || document.getElementById('signupEmpresaForm');
    if (!form) return;

    // Crear modal para errores
    createErrorModal();

    // Guardar datos del formulario automáticamente
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        // Cargar datos guardados al iniciar
        if (input.type !== 'password') {
            const savedValue = localStorage.getItem('form_' + input.name);
            if (savedValue) {
                input.value = savedValue;
            }
        }

        // Guardar datos mientras el usuario escribe
        input.addEventListener('input', function() {
            if (this.type !== 'password') {
                localStorage.setItem('form_' + this.name, this.value);
            }
        });
    });

    form.addEventListener('submit', function(e) {
        const formData = new FormData(this);
        const data = {};
        
        // Recopilar todos los datos del formulario
        for (let [key, value] of formData.entries()) {
            data[key] = value.trim();
        }

        // Validaciones según el tipo de formulario
        let isEmpresa = this.id === 'signupEmpresaForm';
        
        if (isEmpresa) {
            if (!validarFormularioEmpresa(data)) {
                e.preventDefault();
                return;
            }
        } else {
            if (!validarFormularioCandidato(data)) {
                e.preventDefault();
                return;
            }
        }

        // Si llega aquí, todo está correcto, limpiar datos guardados
        clearSavedData();
    });

    function validarFormularioCandidato(data) {
        // Validar campos vacíos
        if (!data.nombre || !data.email || !data.contrasena || !data.confirm_password) {
            showError("Por favor, completa todos los campos obligatorios.");
            return false;
        }

        // Validar nombre solo letras
        const nameRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        if (!nameRegex.test(data.nombre)) {
            showError("El nombre solo puede contener letras y espacios.");
            return false;
        }

        // Validar email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            showError("Por favor, ingresa un correo electrónico válido.");
            return false;
        }

        // Validar contraseña
        const passwordError = validarContrasena(data.contrasena);
        if (passwordError) {
            showError(passwordError);
            return false;
        }

        // Validar que coincidan
        if (data.contrasena !== data.confirm_password) {
            showError("Las contraseñas no coinciden.");
            return false;
        }

        return true;
    }

    function validarFormularioEmpresa(data) {
        // Validar campos vacíos
        if (!data.nombre_empresa || !data.email || !data.contrasena || 
            !data.confirm_password || !data.direccion || !data.nit_cif || !data.telefono) {
            showError("Por favor, completa todos los campos obligatorios.");
            return false;
        }

        // Validar email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            showError("Por favor, ingresa un correo electrónico válido.");
            return false;
        }

        // Validar teléfono
        const phoneRegex = /^[\d\s\+\-\(\)]+$/;
        if (!phoneRegex.test(data.telefono)) {
            showError("Por favor, ingresa un número de teléfono válido.");
            return false;
        }

        // Validar contraseña
        const passwordError = validarContrasena(data.contrasena);
        if (passwordError) {
            showError(passwordError);
            return false;
        }

        // Validar que coincidan
        if (data.contrasena !== data.confirm_password) {
            showError("Las contraseñas no coinciden.");
            return false;
        }

        return true;
    }

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

    function clearSavedData() {
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            localStorage.removeItem('form_' + input.name);
        });
    }

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