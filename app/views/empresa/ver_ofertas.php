<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Ofertas - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/css/dashboard_empresa.css">
    <link rel="icon" href="<?= URLROOT ?>/images/imagesolologo.png" type="image/png">
    
    <style>
        .ofertas-section {
            margin-top: 30px;
        }

        .oferta-card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 30px rgba(30, 58, 138, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .oferta-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1e40af, #3b82f6);
            border-radius: 18px 18px 0 0;
        }

        .oferta-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(30, 58, 138, 0.15);
        }

        .oferta-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .oferta-titulo {
            flex: 1;
        }

        .oferta-titulo h3 {
            color: #1e40af;
            font-size: 1.4em;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .empresa-nombre {
            color: #64748b;
            font-weight: 500;
        }

        .estado-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .estado-activa {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border: 1px solid #22c55e;
        }

        .estado-inactiva {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: 1px solid #ef4444;
        }

        .oferta-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.95em;
        }

        .detail-label {
            font-weight: 600;
            color: #374151;
        }

        .oferta-descripcion {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
        }

        .oferta-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9em;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-cerrar {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-cerrar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-eliminar {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-eliminar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        .no-ofertas {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(30, 58, 138, 0.1);
        }

        .no-ofertas-icon {
            font-size: 4em;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .no-ofertas h3 {
            color: #1e293b;
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .no-ofertas p {
            color: #64748b;
            margin-bottom: 25px;
        }

        .btn-crear {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-crear:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 64, 175, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
        <ul>
            <li>
                <a href="<?= URLROOT ?>/Empresa/dashboard">Inicio</a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/Empresa/crearOferta">Crear Oferta</a>
            </li>
            <li class="active">
                <a href="<?= URLROOT ?>/Empresa/verOfertas">Ver Ofertas Publicadas</a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/Empresa/invitarCandidatos">Invitar Candidatos</a>
            </li>
            <li><a href="<?= URLROOT ?>/Empresa/logout">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="dashboard-container">
            <div class="header">
                <?php if ($mensaje): ?>
                    <div class="alert">
                        <?= htmlspecialchars($mensaje) ?>
                    </div>
                <?php endif; ?>
                
                <h1>📊 Ofertas Publicadas</h1>
                <p class="subtitle">Gestiona y administra todas tus ofertas de trabajo</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon">📋</div>
                    <h3>Total de Ofertas</h3>
                    <div class="number"><?= count($ofertas) ?></div>
                    <p class="description">Ofertas que has publicado</p>
                </div>
            </div>

            <div class="ofertas-section">
                <?php if (empty($ofertas)): ?>
                    <div class="no-ofertas">
                        <div class="no-ofertas-icon">📋</div>
                        <h3>No tienes ofertas publicadas</h3>
                        <p>Comienza creando tu primera oferta de trabajo para atraer talento.</p>
                        <a href="<?= URLROOT ?>/Empresa/crearOferta" class="btn-crear">
                            Crear Primera Oferta
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($ofertas as $oferta): ?>
                        <div class="oferta-card">
                            <div class="oferta-header">
                                <div class="oferta-titulo">
                                    <h3><?= htmlspecialchars($oferta['Titulo']) ?></h3>
                                    <div class="empresa-nombre"><?= htmlspecialchars($oferta['Empresa']) ?></div>
                                </div>
                                <div class="estado-badge estado-<?= strtolower($oferta['Estado']) ?>">
                                    <?= htmlspecialchars($oferta['Estado']) ?>
                                </div>
                            </div>

                            <div class="oferta-details">
                                <div class="detail-item">
                                    <span class="detail-label">📍 Ubicación:</span>
                                    <?= htmlspecialchars($oferta['Ubicacion']) ?>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">💼 Contrato:</span>
                                    <?= htmlspecialchars($oferta['TipoContrato']) ?>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">📅 Publicada:</span>
                                    <?= date('d/m/Y', strtotime($oferta['FechaPublicacion'])) ?>
                                </div>
                            </div>

                            <div class="oferta-descripcion">
                                <?= nl2br(htmlspecialchars($oferta['Descripcion'])) ?>
                            </div>

                            <div class="oferta-actions">
                                <?php if ($oferta['Estado'] === 'Activa'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id_oferta" value="<?= $oferta['IdPuesto'] ?>">
                                        <button type="submit" name="accion" value="cerrar" class="btn-action btn-cerrar">
                                            ⏸️ Cerrar Oferta
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_oferta" value="<?= $oferta['IdPuesto'] ?>">
                                    <button type="submit" name="accion" value="eliminar" class="btn-action btn-eliminar"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta oferta?')">
                                        🗑️ Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?= URLROOT ?>/js/empresa_sidebar.js"></script>
</body>
</html>