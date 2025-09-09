<?php
require_once __DIR__ . '/app/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload para MODELOS y LIBRERÍAS (NO controllers)
spl_autoload_register(function ($class) {
    $dirs = [__DIR__ . '/app/libraries/', __DIR__ . '/app/models/'];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (is_file($file)) { require_once $file; return; }
    }
});

// Enrutamiento tipo /Controlador/metodo/arg1/arg2
$url = $_GET['url'] ?? 'Home/index';
$url = trim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$parts = explode('/', $url);

$controllerName = ucfirst($parts[0]) . 'Controller';
$methodName     = $parts[1] ?? 'index';
$args           = array_slice($parts, 2);

$controllerPath = __DIR__ . '/app/controllers/' . $controllerName . '.php';
if (!is_file($controllerPath)) {
    http_response_code(404);
    echo "Controlador '$controllerName' no encontrado.";
    exit;
}
require_once $controllerPath;

if (!class_exists($controllerName)) {
    http_response_code(500);
    echo "Clase '$controllerName' no definida en $controllerPath.";
    exit;
}

$controller = new $controllerName();

if (!method_exists($controller, $methodName)) {
    http_response_code(404);
    echo "Método '$methodName' no encontrado en '$controllerName'.";
    exit;
}

call_user_func_array([$controller, $methodName], $args);

