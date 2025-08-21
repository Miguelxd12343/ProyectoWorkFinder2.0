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
            <a href="<?= URLROOT ?>/Login/index" class="login">Iniciar sesión</a>
        </div>

        <nav>
            <ul class="nav-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#sobre-nosotros">Sobre Nosotros</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="<?= URLROOT ?>/Signup/mostrarFormulario" class="btn-green">Empieza</a></li>
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

        <!-- Sobre Nosotros - CORREGIDO EL ID -->
        <section id="sobre-nosotros">
            <div class="container">
                <div class="section-header">
                    <h2>Conectando talento con oportunidades</h2>
                    <p>WorkFinderPro es una plataforma digital creada para facilitar la conexión entre empresas que buscan talento y personas que buscan empleo.</p>
                </div>

                <div class="about-grid">
                    <div class="about-card">
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>¿Quiénes somos?</h3>
                        <p>Una plataforma digital innovadora que brinda soluciones prácticas, eficientes y humanas al mundo laboral, conectando el talento con las mejores oportunidades.</p>
                    </div>

                    <div class="about-card">
                        <div class="icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3>Nuestra Misión</h3>
                        <p>Impulsar el crecimiento profesional mediante tecnología accesible, procesos eficientes y un enfoque centrado en las personas.</p>
                    </div>

                    <div class="about-card">
                        <div class="icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Visión</h3>
                        <p>Convertirnos en la plataforma líder en Latinoamérica para la gestión del talento humano, reconocidos por nuestra innovación y transparencia.</p>
                    </div>

                    <div class="about-card">
                        <div class="icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>¿Por qué elegirnos?</h3>
                        <p>Ofrecemos una plataforma fácil de usar, acceso a cientos de ofertas, herramientas avanzadas y soporte personalizado para tu éxito.</p>
                    </div>
                </div>

                <div class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <h3>500+</h3>
                            <p>Empleos Publicados</p>
                        </div>
                        <div class="stat-item">
                            <h3>1200+</h3>
                            <p>Candidatos Registrados</p>
                        </div>
                        <div class="stat-item">
                            <h3>150+</h3>
                            <p>Empresas Activas</p>
                        </div>
                        <div class="stat-item">
                            <h3>95%</h3>
                            <p>Tasa de Satisfacción</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Servicios - CORREGIDO EL ID -->
        <section id="servicios">
            <div class="container">
                <div class="section-header">
                    <h2>Servicios que impulsan tu crecimiento</h2>
                    <p>Descubre nuestras soluciones integrales diseñadas para conectar talento con oportunidades</p>
                </div>

                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3>Publicación de Ofertas</h3>
                        <p>Las empresas pueden gestionar vacantes laborales de manera eficiente desde su panel de control personalizado.</p>
                        <ul class="service-features">
                            <li>Personalización de requisitos</li>
                            <li>Gestión de estado en tiempo real</li>
                            <li>Panel de control intuitivo</li>
                        </ul>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3>Gestión de Candidatos</h3>
                        <p>Accede a perfiles completos y realiza seguimiento organizado de los procesos de selección.</p>
                        <ul class="service-features">
                            <li>Revisión de hojas de vida</li>
                            <li>Historial de aplicaciones</li>
                            <li>Seguimiento de procesos</li>
                        </ul>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h3>Perfil Profesional</h3>
                        <p>Crea un perfil detallado con tu experiencia, estudios y habilidades para destacar ante empleadores.</p>
                        <ul class="service-features">
                            <li>Carga de CV en PDF</li>
                            <li>Experiencia laboral detallada</li>
                            <li>Gestión de habilidades</li>
                        </ul>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Notificaciones Inteligentes</h3>
                        <p>Recibe alertas personalizadas sobre oportunidades que coinciden con tu perfil profesional.</p>
                        <ul class="service-features">
                            <li>Alertas de postulaciones</li>
                            <li>Respuestas de empresas</li>
                            <li>Oportunidades relevantes</li>
                        </ul>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Reportes y Estadísticas</h3>
                        <p>Genera informes detallados sobre usuarios, empresas y el estado general de la plataforma.</p>
                        <ul class="service-features">
                            <li>Informes PDF automáticos</li>
                            <li>Estadísticas en tiempo real</li>
                            <li>Análisis de rendimiento</li>
                        </ul>
                    </div>
                </div>
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