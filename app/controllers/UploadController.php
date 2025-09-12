<?php
class UploadController {
    
    public function serveFile() {
        $file = $_GET['file'] ?? null;
        
        if (!$file) {
            http_response_code(404);
            exit;
        }
        
        // Sanitizar la ruta del archivo
        $file = str_replace(['../', '..\\'], '', $file);
        $filePath = __DIR__ . '/../../public/' . $file;
        
        // Verificar que el archivo existe y está dentro de uploads
        if (!file_exists($filePath) || strpos(realpath($filePath), realpath(__DIR__ . '/../../public/uploads/')) !== 0) {
            http_response_code(404);
            exit;
        }
        
        // Determinar el tipo de contenido
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        // Configurar headers
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        
        // Servir el archivo
        readfile($filePath);
        exit;
    }
}
?>