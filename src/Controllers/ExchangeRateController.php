<?php
require_once __DIR__ . '/../Services/ExchangeRateService.php';

class ExchangeRateController {
    private $service;

    public function __construct($db) {
        $this->service = new ExchangeRateService($db);
    }

    public function handleGet() {
        try {
            $result = $this->service->getTodayRate();
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function handleHistory() {
        try {
            $result = $this->service->getHistory();
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function handlePost() {
        try {
            // Leer el cuerpo de la petición (ahora esperamos JSON)
            $jsonInput = file_get_contents('php://input');
            if (empty($jsonInput)) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'El cuerpo de la petición JSON está vacío.']);
                return;
            }

            $result = $this->service->getRateByJsonDate($jsonInput);
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}