<?php
require_once __DIR__ . '/../Clients/BanguatSoapClient.php';
require_once __DIR__ . '/../Repositories/ExchangeRateRepository.php';

class ExchangeRateService {
    private $repo;
    private $soapClient;

    public function __construct($db) {
        $this->repo = new ExchangeRateRepository($db);
        $this->soapClient = new BanguatSoapClient();
    }

    // 1. Lógica para el día de hoy (GET)
    public function getTodayRate() {
        $today = date('Y-m-d');
        
        $cachedRate = $this->repo->findByDate($today);
        if ($cachedRate) {
            return array_merge($cachedRate, ['source' => 'Database']);
        }

        $liveRate = $this->soapClient->getTodayRate();
        if ($liveRate) {
            $this->repo->save($liveRate['fecha'], $liveRate['valor']);
            return array_merge($liveRate, ['source' => 'Banguat WS']);
        }

        throw new Exception("No se pudo obtener el tipo de cambio.");
    }

    // 3. Últimos 10 registros del historial
    public function getHistory() {
        $data = $this->repo->getLastTen();
        return [
            'items' => count($data),
            'data'  => $data
        ];
    }

    // 2. NUEVA LÓGICA: Recibe JSON, busca en BD o envía SOAP al Web Service
    public function getRateByJsonDate($jsonInput) {
        // Decodificar el JSON recibido desde Postman
        $data = json_decode($jsonInput, true);
        
        if (!isset($data['fecha'])) {
            throw new Exception("Propiedad 'fecha' requerida en el JSON.");
        }

        $rawDate = $data['fecha']; // Ejemplo: "20260529"
        
        // Validar formato Ymd
        $dateObj = DateTime::createFromFormat('Ymd', $rawDate);
        if (!$dateObj) {
            throw new Exception("Formato de fecha inválido en JSON. Se espera YYYYMMDD.");
        }
        $formattedDbDate = $dateObj->format('Y-m-d');

        // Paso A: Revisar si ya existe en la Base de Datos
        $cachedRate = $this->repo->findByDate($formattedDbDate);
        if ($cachedRate) {
            return array_merge($cachedRate, ['source' => 'Database']);
        }

        // Paso B: Si no existe, el Cliente SOAP se encarga de armar el XML y enviarlo a Banguat
        $liveRate = $this->soapClient->getRateByStartDate($rawDate);
        if ($liveRate) {
            $this->repo->save($liveRate['fecha'], $liveRate['valor']);
            return array_merge($liveRate, ['source' => 'Banguat WS']);
        }

        throw new Exception("No se encontraron registros para la fecha solicitada.");
    }
}