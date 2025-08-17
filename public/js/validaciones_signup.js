document.addEventListener('DOMContentLoaded', function () {
    const rolSelect = document.getElementById('rol');
    const empresaExtra = document.getElementById('empresaExtra');
    const direccionEmpresa = document.getElementById('direccion_empresa');
    const identificacionFiscal = document.getElementById('identificacion_fiscal');
    const form = document.getElementById('signupForm');

    rolSelect.addEventListener('change', function () {
        if (this.value === '1') {
            empresaExtra.style.display = 'block';
            direccionEmpresa.required = true;
            identificacionFiscal.required = true;
        } else {
            empresaExtra.style.display = 'none';
            direccionEmpresa.required = false;
            identificacionFiscal.required = false;
        }
    });

    form.addEventListener('submit', function(e) {
        const name = document.getElementById('nombre').value.trim();
        const email = document.getElementById('email').value.trim();
        const password1 = document.getElementById('password1').value;
        const password2 = document.getElementById('password2').value;

        if (!name || !email || !password1 || !password2) {
            alert("Por favor, completa todos los campos.");
            e.preventDefault();
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("Por favor, ingresa un correo electrónico válido.");
            e.preventDefault();
            return;
        }

        if (password1.length < 6) {
            alert("La contraseña debe tener al menos 6 caracteres.");
            e.preventDefault();
            return;
        }

        if (password1 !== password2) {
            alert("Las contraseñas no coinciden.");
            e.preventDefault();
            return;
        }
    });
});