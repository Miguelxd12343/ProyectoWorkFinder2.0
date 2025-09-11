<?php
namespace App\Controllers;

use App\Models\Oferta;

class OfertaController {
    private $ofertaModel;

    public function __construct($db) {
        $this->ofertaModel = new Oferta($db);
    }

    public function crear() {
        session_start();
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
            header("Location: " . URLROOT . "/Login/index");
            exit;
        }

        $mensaje = "";
        $tipo_mensaje = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = $_POST['titulo'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';
            $contrato = $_POST['tipo_contrato'] ?? '';

            if ($titulo && $descripcion) {
                $ok = $this->ofertaModel->crearOferta($_SESSION['usuario_id'], $titulo, $descripcion, $ubicacion, $contrato);

                if ($ok) {
                    $mensaje = "Oferta publicada exitosamente.";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al publicar la oferta.";
                    $tipo_mensaje = "error";
                }
            } else {
                $mensaje = "Todos los campos obligatorios deben estar completos.";
                $tipo_mensaje = "error";
            }
        }

        require __DIR__ . '/../views/empresa/crear_oferta.php';
    }
}
