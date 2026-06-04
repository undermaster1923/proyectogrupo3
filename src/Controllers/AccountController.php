<?php
require_once __DIR__ . '/../Services/AccountService.php';

class AccountController {
    private $service;

    public function __construct($db) {
        $this->service = new AccountService($db);
    }

    // GET /api/clientes → listado de todos los clientes
    public function handleList() {
        try {
            $result = $this->service->getAllAccounts();
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // GET /api/agregarcuenta/{cui} → consulta la cuenta del CUI
    public function handleGet($cui) {
        try {
            $result = $this->service->getAccountByCui($cui);
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // POST /api/agregarcuenta/{cui} → crea la cuenta para el CUI
    public function handlePost($cui) {
        try {
            $result = $this->service->createAccount($cui);
            header('HTTP/1.1 201 Created');
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('HTTP/1.1 409 Conflict');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
