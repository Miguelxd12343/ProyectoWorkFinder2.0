<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitar Candidatos - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/css/dashboard_empresa.css">
    <link rel="icon" href="<?= URLROOT ?>/images/imagesolologo.png" type="image/png">
    
    <style>
        .candidates-section {
            margin-top: 30px;
        }

        .candidates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .candidate-card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(30, 58, 138, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.1);
            transition: all 0.3s ease;
        }

        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(30, 58, 138, 0.15);
        }

        .candidate-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .candidate-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .candidate-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            color: #1e40af;
        }

        .candidate-info h3 {
            color: #1e293b;
            font-size: 1.3em;
            margin: 0 0 5px 0;
            font-weight: 600;
        }

        .candidate-email {
            color: #64748b;
            font-size: 0.9em;
            margin: 0;
        }

        .desired-job {
            color: #1e40af;
            font-weight: 600;
            margin: 8px 0 0 0;
            font-size: 0.9em;
        }

        .candidate-details {
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #374151;
            font-size: 0.9em;
        }

        .detail-value {
            color: #64748b;
            font-size: 0.9em;
        }

        .candidate-description {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #3b82f6;
        }

        .candidate-description p {
            margin: 0;
            color: #64748b;
            line-height: 1.5;
            font-size: 0.95em;
        }

        .candidate-skills {
            margin-bottom: 20px;
        }

        .skills-section strong {
            color: #1e293b;
            font-size: 0.9em;
        }

        .skills-section p {
            margin: 8px 0 0 0;
            color: #64748b;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .candidate-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-view-profile,
        .btn-view-cv,
        .btn-invite {
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 0.9em;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-view-profile {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }

        .btn-view-cv {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
        }

        .btn-invite {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
        }

        .btn-view-profile:hover,
        .btn-view-cv:hover,
        .btn-invite:hover {
            transform: translateY(-2px);
            color: white;
        }

        .btn-view-profile:hover {
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-view-cv:hover {
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
        }

        .btn-invite:hover {
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }

        .no-offers-msg {
            color: #f59e0b;
            font-size: 0.85em;
            text-align: center;
            padding: 8px;
            background: #fffbeb;
            border-radius: 6px;
            border: 1px solid #fed7aa;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .close:hover {
            color: #000;
        }

        .modal h2 {
            color: #1e293b;
            margin-bottom: 25px;
            font-size: 1.5em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .candidate-display {
            font-weight: 600;
            color: #1e40af;
            background: #dbeafe;
            padding: 8px 12px;
            border-radius: 6px;
            display: inline-block;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .btn-cancel,
        .btn-send {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel {
            background: #6b7280;
            color: white;
        }

        .btn-send {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
        }

        .btn-cancel:hover {
            background: #4b5563;
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(30, 58, 138, 0.1);
        }

        .empty-icon {
            font-size: 4em;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            color: #1e293b;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #64748b;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .candidates-grid {
                grid-template-columns: 1fr;
            }
            
            .candidate-actions {
                flex-direction: column;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
                padding: 20px;
            }
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
            <li>
                <a href="<?= URLROOT ?>/Empresa/verOfertas">Ver Ofertas Publicadas</a>
            </li>
            <li class="active">
                <a href="<?= URLROOT ?>/Empresa/invitarCandidatos">Invitar Candidatos</a>
            </li>
            <li><a href="<?= URLROOT ?>/Empresa/logout">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="dashboard-container">
            <div class="header">
                <?php if (isset($success)): ?>
                    <div class="alert">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <h1>🎯 Invitar Candidatos</h1>
                <p class="subtitle">Encuentra y contacta directamente con el talento que necesitas</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card candidatos">
                    <div class="icon">👥</div>
                    <h3>Candidatos Disponibles</h3>
                    <div class="number"><?= count($candidatos) ?></div>
                    <p class="description">Perfiles registrados en la plataforma</p>
                </div>
                
                <div class="stat-card ofertas">
                    <div class="icon">💼</div>
                    <h3>Tus Ofertas Activas</h3>
                    <div class="number"><?= count($ofertasActivas) ?></div>
                    <p class="description">Ofertas disponibles para invitar</p>
                </div>
            </div>

            <div class="candidates-section">
                <?php if (empty($candidatos)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">👥</div>
                        <h2>No hay candidatos disponibles</h2>
                        <p>Aún no hay candidatos registrados en la plataforma</p>
                    </div>
                <?php else: ?>
                    <h2 class="section-title">👤 Candidatos Disponibles</h2>
                    <div class="candidates-grid">
                        <?php foreach ($candidatos as $candidato): ?>
                            <div class="candidate-card">
                                <div class="candidate-header">
                                    <div class="candidate-photo">
                                        <?php if ($candidato['FotoPerfilPath'] && file_exists(__DIR__ . '/../../../public/' . $candidato['FotoPerfilPath'])): ?>
                                            <img src="<?= URLROOT ?>/<?= htmlspecialchars($candidato['FotoPerfilPath']) ?>" alt="Foto de <?= htmlspecialchars($candidato['Nombre']) ?>">
                                        <?php else: ?>
                                            <div class="photo-placeholder">👤</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="candidate-info">
                                        <h3><?= htmlspecialchars($candidato['Nombre']) ?></h3>
                                        <p class="candidate-email"><?= htmlspecialchars($candidato['Email']) ?></p>
                                        <?php if ($candidato['EmpleoDeseado']): ?>
                                            <p class="desired-job">🎯 <?= htmlspecialchars($candidato['EmpleoDeseado']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="candidate-details">
                                    <?php if ($candidato['Edad']): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Edad:</span>
                                            <span class="detail-value"><?= $this->candidatoModel->calcularEdad($candidato['Edad']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($candidato['Telefono']): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Teléfono:</span>
                                            <span class="detail-value"><?= htmlspecialchars($candidato['Telefono']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($candidato['Direccion']): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Ubicación:</span>
                                            <span class="detail-value"><?= htmlspecialchars($candidato['Direccion']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($candidato['Descripcion']): ?>
                                    <div class="candidate-description">
                                        <p><?= nl2br(htmlspecialchars(substr($candidato['Descripcion'], 0, 150))) ?>
                                        <?php if (strlen($candidato['Descripcion']) > 150): ?>...<?php endif; ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="candidate-actions">
                                
                                    
                                    <?php if ($candidato['HojaDeVidaPath']): ?>
                                        <a href="<?= URLROOT ?>/Upload/servefile?file=<?= urlencode($candidato['HojaDeVidaPath']) ?>" 
                                          target="_blank" class="btn-view-cv">
                                           📄 Ver Hoja de Vida
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($ofertasActivas)): ?>
                                        <button class="btn-invite" onclick="mostrarFormularioInvitacion(<?= $candidato['IdUsuario'] ?>, '<?= htmlspecialchars($candidato['Nombre']) ?>')">
                                            ✉️ Enviar Invitación
                                        </button>
                                    <?php else: ?>
                                        <span class="no-offers-msg">⚠️ Necesitas ofertas activas para invitar</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para enviar invitación -->
    <div id="invitationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInvitacion()">&times;</span>
            <h2>✉️ Enviar Invitación</h2>
            <form method="post" id="invitationForm">
                <div class="form-group">
                    <label>Candidato:</label>
                    <span id="candidateName" class="candidate-display"></span>
                </div>
                
                <div class="form-group">
                    <label for="puesto_id">Seleccionar Oferta de Trabajo:</label>
                    <select name="puesto_id" id="puesto_id" required>
                        <option value="">-- Selecciona una oferta --</option>
                        <?php foreach ($ofertasActivas as $oferta): ?>
                            <option value="<?= $oferta['IdPuesto'] ?>"><?= htmlspecialchars($oferta['Titulo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="mensaje">Mensaje personalizado (opcional):</label>
                    <textarea name="mensaje" id="mensaje" rows="4" 
                              placeholder="Escribe un mensaje personalizado para el candidato..."></textarea>
                </div>
                
                <input type="hidden" name="candidato_id" id="candidato_id">
                <input type="hidden" name="enviar_invitacion" value="1">
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="cerrarModalInvitacion()">Cancelar</button>
                    <button type="submit" class="btn-send">📤 Enviar Invitación</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para mostrar el formulario de invitación
        function mostrarFormularioInvitacion(candidatoId, candidatoNombre) {
            document.getElementById('candidato_id').value = candidatoId;
            document.getElementById('candidateName').textContent = candidatoNombre;
            document.getElementById('invitationModal').style.display = 'block';
        }

        // Función para cerrar el modal de invitación
        function cerrarModalInvitacion() {
            document.getElementById('invitationModal').style.display = 'none';
            document.getElementById('invitationForm').reset();
        }

        // Función para ver perfil completo
        function verPerfil(candidatoId) {
            alert('Funcionalidad de ver perfil completo - ID: ' + candidatoId);
        }

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('invitationModal');
            if (event.target === modal) {
                cerrarModalInvitacion();
            }
        }
    </script>

    <script src="<?= URLROOT ?>/js/empresa_sidebar.js"></script>
</body>
</html>