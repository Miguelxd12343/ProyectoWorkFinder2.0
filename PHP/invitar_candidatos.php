<?php
require 'auth_guard.php';
require_once(__DIR__ . '/conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header('Location: login.php');
    exit;
}

$empresaId = $_SESSION['usuario_id'];
$nombreEmpresa = $_SESSION['usuario_nombre'];

// Procesar envío de invitación
if ($_POST && isset($_POST['enviar_invitacion'])) {
    $candidatoId = $_POST['candidato_id'];
    $puestoId = $_POST['puesto_id'];
    $mensaje = $_POST['mensaje'] ?? '';
    
    try {
        // Verificar que no existe ya una invitación para este candidato y puesto
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM invitaciones 
            WHERE IdEmpresa = ? AND IdCandidato = ? AND IdPuesto = ?
        ");
        $stmt->execute([$empresaId, $candidatoId, $puestoId]);
        
        if ($stmt->fetchColumn() > 0) {
            $error = "Ya has enviado una invitación a este candidato para este puesto.";
        } else {
            // Crear la invitación
            $stmt = $pdo->prepare("
                INSERT INTO invitaciones (IdEmpresa, IdCandidato, IdPuesto, Mensaje) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$empresaId, $candidatoId, $puestoId, $mensaje]);
            
            $success = "Invitación enviada correctamente al candidato.";
        }
    } catch (PDOException $e) {
        $error = "Error al enviar la invitación: " . $e->getMessage();
    }
}

// Obtener candidatos con sus perfiles
try {
    $stmt = $pdo->prepare("
        SELECT u.IdUsuario, u.Nombre, u.Email,
               p.Edad, p.Telefono, p.Direccion, p.EmpleoDeseado, p.Descripcion, 
               p.FotoPerfilPath, p.HojaDeVidaPath,
               h.ExperienciaLaboral, h.Educacion, h.Habilidades
        FROM usuario u
        LEFT JOIN perfilusuario p ON u.IdUsuario = p.IdUsuario
        LEFT JOIN hojadevida h ON u.IdUsuario = h.IdUsuario
        WHERE u.IdRol = 2
        ORDER BY u.Nombre ASC
    ");
    $stmt->execute();
    $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $candidatos = [];
    $error = "Error al obtener los candidatos.";
}

// Obtener ofertas activas de la empresa para el formulario de invitación
try {
    $stmt = $pdo->prepare("
        SELECT IdPuesto, Titulo FROM puestodetrabajo 
        WHERE IdUsuario = ? AND Estado = 'Activa' 
        ORDER BY Titulo ASC
    ");
    $stmt->execute([$empresaId]);
    $ofertasActivas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $ofertasActivas = [];
}

// Función para calcular edad
function calcularEdad($fechaNacimiento) {
    if (!$fechaNacimiento) return 'No especificada';
    $fecha = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha);
    return $edad->y . ' años';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitar Candidatos - WorkFinderPro</title>
    <link rel="stylesheet" href="../CSS/dashboard_empresa.css">
    
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</head>
<body>
<div class="sidebar">
    <h2><a href="../index.html" class="logo-link">WorkFinderPro</a></h2>
    <ul>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_empresa.php' ? 'active' : '' ?>">
            <a href="dashboard_empresa.php">Inicio</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'crear_oferta.php' ? 'active' : '' ?>">
            <a href="crear_oferta.php">Crear Oferta</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'ver_ofertas_empresa.php' ? 'active' : '' ?>">
            <a href="ver_ofertas_empresa.php">Ver Ofertas Publicadas</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'invitar_candidatos.php' ? 'active' : '' ?>">
            <a href="invitar_candidatos.php">Invitar Candidatos</a>
        </li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
    </ul>
</div>

<div class="main">
    <div class="dashboard-container">
        <div class="header">
            <?php if (isset($success)): ?>
                <div class="alert success">
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

        <!-- Lista de Candidatos -->
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
                                    <?php if ($candidato['FotoPerfilPath'] && file_exists('../uploads/' . $candidato['FotoPerfilPath'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($candidato['FotoPerfilPath']) ?>" alt="Foto de <?= htmlspecialchars($candidato['Nombre']) ?>">
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
                                        <span class="detail-value"><?= calcularEdad($candidato['Edad']) ?></span>
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

                            <div class="candidate-skills">
                                <?php if ($candidato['Habilidades']): ?>
                                    <div class="skills-section">
                                        <strong>💪 Habilidades:</strong>
                                        <p><?= htmlspecialchars(substr($candidato['Habilidades'], 0, 100)) ?>
                                        <?php if (strlen($candidato['Habilidades']) > 100): ?>...<?php endif; ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="candidate-actions">
                                <button class="btn-view-profile" onclick="verPerfil(<?= $candidato['IdUsuario'] ?>)">
                                    👁️ Ver Perfil Completo
                                </button>
                                
                                <?php if ($candidato['HojaDeVidaPath']): ?>
                                    <a href="../uploads/<?= htmlspecialchars($candidato['HojaDeVidaPath']) ?>" 
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

<!-- Modal para ver perfil completo -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="profileContent">
            <!-- El contenido se carga aquí dinámicamente -->
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
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad del sidebar (igual que en otros archivos)
    if (!document.querySelector('.sidebar-toggle')) {
        const toggleButton = document.createElement('button');
        toggleButton.className = 'sidebar-toggle';
        toggleButton.innerHTML = '☰';
        toggleButton.setAttribute('aria-label', 'Abrir menú');
        document.body.appendChild(toggleButton);
        
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
        
        const sidebar = document.querySelector('.sidebar');
        
        function openSidebar() {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSidebar() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        toggleButton.addEventListener('click', openSidebar);
        overlay.addEventListener('click', closeSidebar);
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });
        
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 968) {
                    closeSidebar();
                }
            });
        });
    }
    
    // Animaciones
    const cards = document.querySelectorAll('.candidate-card, .stat-card');
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
});

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
    // Aquí podrías hacer una llamada AJAX para cargar el perfil completo
    // Por ahora, simplemente mostramos un mensaje
    alert('Funcionalidad de ver perfil completo - ID: ' + candidatoId);
}

// Cerrar modales al hacer clic fuera
window.onclick = function(event) {
    const invitationModal = document.getElementById('invitationModal');
    const profileModal = document.getElementById('profileModal');
    
    if (event.target === invitationModal) {
        cerrarModalInvitacion();
    }
    if (event.target === profileModal) {
        profileModal.style.display = 'none';
    }
}
</script>
</body>
</html>