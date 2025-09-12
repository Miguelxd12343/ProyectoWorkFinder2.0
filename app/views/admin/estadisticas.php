<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - WorkFinderPro</title>
    <link rel="stylesheet" href="../public/css/dashboard_admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../public/css/styles_Estadisticas.css?v=<?php echo time(); ?>">
    <link rel="icon" href="public/images/imagesolologo.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/date-fns/1.30.1/index.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
        <ul>
            <li><a href="<?= URLROOT ?>/Admin/dashboard">Inicio</a></li>
            <li><a href="<?= URLROOT ?>/Admin/gestionarEmpresas">Gestionar Empresas</a></li>
            <li><a href="<?= URLROOT ?>/Admin/gestionarCandidatos">Gestionar Candidatos</a></li>
            <li><a href="<?= URLROOT ?>/Admin/estadisticas" class="active">Estadísticas</a></li>
            <li><a href="<?= URLROOT ?>/Login/logout">Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido Principal -->
    <div class="main">
        <!-- Header de Estadísticas -->
        <div class="estadisticas-header">
            <h1>📊 Estadísticas del Sistema</h1>
            <p>Análisis detallado de la plataforma WorkFinderPro</p>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="resumen-container">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3><?= $data['total_usuarios'] ?? 0 ?></h3>
                    <p>Total Usuarios</p>
                    <small class="stat-change positive">+<?= $data['usuarios_mes'] ?? 0 ?> este mes</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🏢</div>
                <div class="stat-content">
                    <h3><?= $data['total_empresas'] ?? 0 ?></h3>
                    <p>Empresas Registradas</p>
                    <small class="stat-change positive">+<?= $data['empresas_mes'] ?? 0 ?> este mes</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💼</div>
                <div class="stat-content">
                    <h3><?= $data['total_ofertas'] ?? 0 ?></h3>
                    <p>Ofertas de Trabajo</p>
                    <small class="stat-change positive">+<?= $data['ofertas_mes'] ?? 0 ?> este mes</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📋</div>
                <div class="stat-content">
                    <h3><?= $data['total_postulaciones'] ?? 0 ?></h3>
                    <p>Postulaciones</p>
                    <small class="stat-change positive">+<?= $data['postulaciones_mes'] ?? 0 ?> este mes</small>
                </div>
            </div>
        </div>

        <!-- Filtros de Período -->
        <div class="filtros-container">
            <h3>Filtrar por Período</h3>
            <div class="filtros-botones">
                <button class="btn-filtro active" data-periodo="7">Últimos 7 días</button>
                <button class="btn-filtro" data-periodo="30">Últimos 30 días</button>
                <button class="btn-filtro" data-periodo="90">Últimos 3 meses</button>
                <button class="btn-filtro" data-periodo="365">Último año</button>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="graficos-container">
            <!-- Gráfico de Registros por Tiempo -->
            <div class="grafico-card">
                <div class="grafico-header">
                    <h3>Registros por Tiempo</h3>
                    <p>Evolución de registros de usuarios y empresas</p>
                </div>
                <div class="grafico-body">
                    <canvas id="graficoRegistros" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Gráfico de Distribución de Usuarios -->
            <div class="grafico-card">
                <div class="grafico-header">
                    <h3>Distribución de Usuarios</h3>
                    <p>Candidatos vs Empresas</p>
                </div>
                <div class="grafico-body">
                    <canvas id="graficoDistribucion" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Gráfico de Ofertas por Categoría -->
            <div class="grafico-card">
                <div class="grafico-header">
                    <h3>Ofertas por Categoría</h3>
                    <p>Las categorías más demandadas</p>
                </div>
                <div class="grafico-body">
                    <canvas id="graficoCategorias" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Gráfico de Actividad Diaria -->
            <div class="grafico-card">
                <div class="grafico-header">
                    <h3>Actividad Diaria</h3>
                    <p>Postulaciones por día de la semana</p>
                </div>
                <div class="grafico-body">
                    <canvas id="graficoActividad" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Tablas de Top Rankings -->
        <div class="rankings-container">
            <div class="ranking-card">
                <h3>🏆 Top Empresas con Más Ofertas</h3>
                <div class="ranking-list">
                    <?php if (!empty($data['top_empresas'])): ?>
                        <?php foreach ($data['top_empresas'] as $index => $empresa): ?>
                            <div class="ranking-item">
                                <span class="ranking-posicion"><?= $index + 1 ?></span>
                                <span class="ranking-nombre"><?= htmlspecialchars($empresa['Nombre']) ?></span>
                                <span class="ranking-valor"><?= $empresa['total_ofertas'] ?> ofertas</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No hay datos disponibles</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ranking-card">
                <h3>⭐ Categorías Más Populares</h3>
                <div class="ranking-list">
                    <?php if (!empty($data['top_categorias'])): ?>
                        <?php foreach ($data['top_categorias'] as $index => $categoria): ?>
                            <div class="ranking-item">
                                <span class="ranking-posicion"><?= $index + 1 ?></span>
                                <span class="ranking-nombre"><?= htmlspecialchars($categoria['categoria']) ?></span>
                                <span class="ranking-valor"><?= $categoria['total'] ?> ofertas</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No hay datos disponibles</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Métricas Adicionales -->
        <div class="metricas-adicionales">
            <div class="metrica-card">
                <h4>Tasa de Conversión</h4>
                <div class="metrica-valor"><?= number_format($data['tasa_conversion'] ?? 0, 1) ?>%</div>
                <p>Postulaciones por oferta</p>
            </div>

            <div class="metrica-card">
                <h4>Crecimiento Mensual</h4>
                <div class="metrica-valor"><?= ($data['crecimiento_usuarios'] ?? 0) > 0 ? '+' : '' ?><?= number_format($data['crecimiento_usuarios'] ?? 0, 1) ?>%</div>
                <p>Nuevos usuarios</p>
            </div>

            <div class="metrica-card">
                <h4>Empresas Activas</h4>
                <div class="metrica-valor"><?= $data['empresas_activas'] ?? 0 ?></div>
                <p>Con ofertas publicadas</p>
            </div>

            <div class="metrica-card">
                <h4>Promedio Ofertas</h4>
                <div class="metrica-valor"><?= number_format($data['promedio_ofertas'] ?? 0, 1) ?></div>
                <p>Por empresa</p>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="acciones-container">
            <a href="<?= URLROOT ?>/Admin/exportarEstadisticas" class="btn-accion-principal">
                📄 Exportar Reporte
            </a>
            <button onclick="actualizarDatos()" class="btn-accion-secundario">
                🔄 Actualizar Datos
            </button>
            <a href="<?= URLROOT ?>/Admin/dashboard" class="btn-accion-secundario">
                🏠 Volver al Dashboard
            </a>
        </div>
    </div>

    <script>
        // Datos dinámicos desde PHP
        const datosGraficos = {
            registros: <?= json_encode($data['registros_tiempo'] ?? []) ?>,
            distribucion: <?= json_encode($data['distribucion_usuarios'] ?? []) ?>,
            categorias: <?= json_encode($data['ofertas_categoria'] ?? []) ?>,
            actividad: <?= json_encode($data['actividad_semanal'] ?? []) ?>
        };

        // Inicializar gráficos cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            inicializarGraficos();
            configurarFiltros();
        });

        function inicializarGraficos() {
            // Gráfico de Registros por Tiempo
            const ctxRegistros = document.getElementById('graficoRegistros').getContext('2d');
            new Chart(ctxRegistros, {
                type: 'line',
                data: {
                    labels: datosGraficos.registros.labels || [],
                    datasets: [{
                        label: 'Usuarios',
                        data: datosGraficos.registros.usuarios || [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Empresas',
                        data: datosGraficos.registros.empresas || [],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Distribución (Donut)
            const ctxDistribucion = document.getElementById('graficoDistribucion').getContext('2d');
            new Chart(ctxDistribucion, {
                type: 'doughnut',
                data: {
                    labels: ['Candidatos', 'Empresas'],
                    datasets: [{
                        data: [
                            datosGraficos.distribucion.candidatos || 0,
                            datosGraficos.distribucion.empresas || 0
                        ],
                        backgroundColor: ['#3b82f6', '#ef4444'],
                        borderWidth: 3,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Gráfico de Categorías (Barras horizontales)
            const ctxCategorias = document.getElementById('graficoCategorias').getContext('2d');
            new Chart(ctxCategorias, {
                type: 'bar',
                data: {
                    labels: datosGraficos.categorias.labels || [],
                    datasets: [{
                        label: 'Ofertas',
                        data: datosGraficos.categorias.values || [],
                        backgroundColor: '#10b981',
                        borderColor: '#059669',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Actividad Semanal
            const ctxActividad = document.getElementById('graficoActividad').getContext('2d');
            new Chart(ctxActividad, {
                type: 'radar',
                data: {
                    labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                    datasets: [{
                        label: 'Postulaciones',
                        data: datosGraficos.actividad || [0,0,0,0,0,0,0],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.2)',
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#ffffff',
                        pointHoverBackgroundColor: '#ffffff',
                        pointHoverBorderColor: '#8b5cf6'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function configurarFiltros() {
            const botonesFiltro = document.querySelectorAll('.btn-filtro');
            botonesFiltro.forEach(boton => {
                boton.addEventListener('click', function() {
                    // Remover clase activa de todos los botones
                    botonesFiltro.forEach(b => b.classList.remove('active'));
                    // Agregar clase activa al botón clickeado
                    this.classList.add('active');
                    
                    // Obtener el período seleccionado
                    const periodo = this.dataset.periodo;
                    actualizarGraficosPorPeriodo(periodo);
                });
            });
        }

        function actualizarGraficosPorPeriodo(periodo) {
            // Aquí puedes hacer una llamada AJAX para obtener datos filtrados
            fetch(`<?= URLROOT ?>/Admin/datosEstadisticasPorPeriodo/${periodo}`)
                .then(response => response.json())
                .then(data => {
                    // Actualizar los gráficos con los nuevos datos
                    console.log('Datos actualizados:', data);
                    // Implementar la lógica de actualización de gráficos
                })
                .catch(error => {
                    console.error('Error al obtener datos:', error);
                });
        }

        function actualizarDatos() {
            // Mostrar indicador de carga
            const boton = event.target;
            const textoOriginal = boton.textContent;
            boton.textContent = '🔄 Actualizando...';
            boton.disabled = true;

            // Recargar la página después de un breve delay
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        // Animación de contadores
        function animarContadores() {
            const contadores = document.querySelectorAll('.stat-content h3');
            contadores.forEach(contador => {
                const valorFinal = parseInt(contador.textContent);
                let valorActual = 0;
                const incremento = valorFinal / 50;
                
                const timer = setInterval(() => {
                    valorActual += incremento;
                    if (valorActual >= valorFinal) {
                        contador.textContent = valorFinal;
                        clearInterval(timer);
                    } else {
                        contador.textContent = Math.floor(valorActual);
                    }
                }, 20);
            });
        }

        // Ejecutar animación al cargar
        window.addEventListener('load', animarContadores);
    </script>
</body>
</html>