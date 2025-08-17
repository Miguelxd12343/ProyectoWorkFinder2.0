<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkFinderPro</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/styles_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="images/imagesolologo.png" type="image/png">
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <header>
        <div class="top-bar">
            <a href="#inicio" class="logo">WORKFINDERPRO</a>
            <a href="PHP/login.php" class="login">Iniciar sesión</a>
        </div>

        <nav>
            <ul class="nav-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#sobre-nosotros">Sobre Nosotros</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="HTML/signup.html" class="btn-green">Empieza</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Inicio -->
        <section id="inicio" class="hero">
            <img src="images/729 1.png" alt="Personas en una oficina">
            <div class="hero-content">
                <h1>Encuentra el mejor empleo</h1>
                <button class="upload-btn" onclick="window.location.href='HTML/signup.html'">
                    SUBIR HOJA DE VIDA <i class="fa fa-upload"></i>
                </button>
                <div class="info-box">
                    <p>Descubre oportunidades laborales que se ajustan a tu perfil. 
                    Postúlate hoy y comienza a dar pasos hacia el futuro que deseas.
                    ¡Tu próximo gran empleo está aquí!</p>
                </div>
            </div>
        </section>

        <!-- Sobre Nosotros -->
        <section id="sobre-nosotros">
            <div class="hero">
                <img src="images/oficina2.jpg" alt="Equipo de trabajo">
                <div class="hero-content">
                    <h1>Conectando talento con oportunidades</h1>
                </div>
            </div>
            <div class="info-box">
                <h2>¿Quiénes somos?</h2>
                <p>WorkFinderPro es una plataforma digital creada para facilitar la conexión entre empresas que buscan talento y personas que buscan empleo. Nuestro compromiso es brindar soluciones prácticas, eficientes y humanas al mundo laboral.</p>
            </div>
            <div class="info-box">
                <h2>Nuestra Misión</h2>
                <p>Impulsar el crecimiento profesional de los candidatos y el éxito empresarial de nuestros clientes mediante tecnología accesible, procesos eficientes y un enfoque centrado en las personas.</p>
            </div>
            <div class="info-box">
                <h2>Visión</h2>
                <p>Convertirnos en la plataforma líder en Latinoamérica para la gestión del talento humano, siendo reconocidos por nuestra innovación, transparencia y compromiso con el desarrollo profesional.</p>
            </div>
            <div class="info-box">
                <h2>¿Por qué elegirnos?</h2>
                <ul>
                    <li>✔ Plataforma fácil de usar</li>
                    <li>✔ Acceso a cientos de ofertas de empleo</li>
                    <li>✔ Herramientas de evaluación y seguimiento</li>
                    <li>✔ Soporte y acompañamiento personalizado</li>
                </ul>
            </div>
        </section>

        <!-- Servicios -->
        <section id="servicios">
            <div class="hero">
                <img src="images/oficina_servicios.jpg" alt="Servicios de reclutamiento">
                <div class="hero-content">
                    <h1>Servicios que impulsan tu crecimiento</h1>
                </div>
            </div>
            <div class="info-box">
                <h2>Publicación de Ofertas</h2>
                <p>Las empresas pueden publicar fácilmente vacantes laborales, personalizar los requisitos y gestionar el estado de cada puesto de trabajo desde su panel de control.</p>
            </div>
            <div class="info-box">
                <h2>Gestión de Candidatos</h2>
                <p>Accede a hojas de vida, perfiles profesionales, historial de aplicaciones y realiza un seguimiento de los procesos de selección de manera organizada.</p>
            </div>
            <div class="info-box">
                <h2>Creación de Perfil Profesional</h2>
                <p>Los candidatos pueden crear un perfil detallado, subir su hoja de vida en PDF, incluir experiencia laboral, estudios, habilidades y más.</p>
            </div>
            <div class="info-box">
                <h2>Notificaciones y Seguimiento</h2>
                <p>Recibe alertas sobre postulaciones, respuestas de empresas, y recordatorios de oportunidades según tu perfil profesional.</p>
            </div>
            <div class="info-box">
                <h2>Reportes y Estadísticas</h2>
                <p>El administrador puede generar informes PDF de usuarios registrados, empresas activas y estado general de la plataforma.</p>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>WorkFinderPro</h3>
                <p>Conectamos personas con oportunidades laborales reales. Apoya tu crecimiento profesional con nosotros.</p>
            </div>

            <div class="footer-section">
                <h3>Enlaces rápidos</h3>
                <ul>
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#sobre-nosotros">Sobre Nosotros</a></li>
                    <li><a href="#servicios">Servicios</a></li>
                    <li><a href="HTML/signup.html">Registrarse</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Contacto</h3>
                <p><i class="fas fa-envelope"></i> contacto@workfinderpro.com</p>
                <p><i class="fas fa-phone"></i> +57 300 123 4567</p>
                <p><i class="fas fa-map-marker-alt"></i> Bogotá, Colombia</p>
            </div>

            <div class="footer-section">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 WorkFinderPro. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>