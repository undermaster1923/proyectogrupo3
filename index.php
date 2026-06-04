<?php
ini_set('display_errors', 0);
error_reporting(0);
date_default_timezone_set('America/Guatemala');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Controllers/ExchangeRateController.php';
require_once __DIR__ . '/src/Controllers/AccountController.php';

// CORS — deben ir antes de cualquier salida
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: false");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Inicializar Conexión y Controladores
$database = new Database();
$db = $database->getConnection();
$controller        = new ExchangeRateController($db);
$accountController = new AccountController($db);

$method = $_SERVER['REQUEST_METHOD'];
$path   = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// --- ENDPOINT 1: GET|POST /api/tipocambio ---
// GET  → devuelve el tipo de cambio del día actual
// POST → recibe { "fecha": "YYYYMMDD" } y devuelve el tipo de cambio de esa fecha
if ($path === 'api/tipocambio') {
    if ($method === 'GET') {
        $controller->handleGet();
    } elseif ($method === 'POST') {
        $controller->handlePost();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido. Solo GET y POST.']);
    }

// --- ENDPOINT 2: GET /api/historialcambio ---
// GET → devuelve los últimos 10 registros de tipo de cambio almacenados en BD
} elseif ($path === 'api/historialcambio') {
    if ($method === 'GET') {
        $controller->handleHistory();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido. Solo GET.']);
    }

// --- ENDPOINT 3: GET|POST /api/agregarcuenta/{cui} ---
// GET  → consulta la cuenta asociada al CUI recibido en la URL
// POST → crea una nueva cuenta de ahorro para el CUI recibido en la URL
} elseif (preg_match('#^api/agregarcuenta/(\d+)$#i', $path, $matches)) {
    $cui = $matches[1];
    if ($method === 'GET') {
        $accountController->handleGet($cui);
    } elseif ($method === 'POST') {
        $accountController->handlePost($cui);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido. Solo GET y POST.']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada.']);
}

