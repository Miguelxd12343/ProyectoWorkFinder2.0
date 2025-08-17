<?php
// Cargar configuración
require_once 'app/config/config.php';

// Autocarga de clases (controladores, modelos)
spl_autoload_register(function ($className) {
    $path = 'app/libraries/' . $className . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// Enrutamiento básico
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'Home/index';
$url = filter_var($url, FILTER_SANITIZE_URL);
$params = explode('/', $url);

// Determinar controlador, método y parámetros
$controllerName = !empty($params[0]) ? ucfirst($params[0]) . 'Controller' : 'HomeController';
$methodName = isset($params[1]) ? $params[1] : 'index';
$arguments = array_slice($params, 2);

// Ruta del controlador
$controllerPath = 'app/controllers/' . $controllerName . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    $controller = new $controllerName();

    if (method_exists($controller, $methodName)) {
        call_user_func_array([$controller, $methodName], $arguments);
    } else {
        // Método no encontrado
        http_response_code(404);
        echo "Método '$methodName' no encontrado en el controlador '$controllerName'.";
    }
} else {
    // Controlador no encontrado
    http_response_code(404);
    echo "Controlador '$controllerName' no encontrado.";
}