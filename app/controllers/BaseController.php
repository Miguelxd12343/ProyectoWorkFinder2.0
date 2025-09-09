<?php
// controllers/BaseController.php (agregar este método)

protected function timeAgo($datetime) {
    $tiempo = time() - strtotime($datetime);
    
    if ($tiempo < 60) return 'Hace unos segundos';
    if ($tiempo < 3600) return 'Hace ' . floor($tiempo/60) . ' minutos';
    if ($tiempo < 86400) return 'Hace ' . floor($tiempo/3600) . ' horas';
    if ($tiempo < 2592000) return 'Hace ' . floor($tiempo/86400) . ' días';
    
    return date('d/m/Y', strtotime($datetime));
}
?>