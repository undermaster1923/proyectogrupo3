<?php
class BanguatSoapClient {
    private $endpoint = "https://www.banguat.gob.gt/variables/ws/TipoCambio.asmx";

    public function getTodayRate() {
        return $this->getRateByStartDate(date('Ymd'));
    }

    public function getRateByStartDate($xmlDate) {
        $dateObj = DateTime::createFromFormat('Ymd', $xmlDate);
        if (!$dateObj) {
            throw new Exception("Formato de fecha interno inválido.");
        }
        $formattedDate = $dateObj->format('d/m/Y');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xmlns:xsd="http://www.w3.org/2001/XMLSchema"
               xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <TipoCambioFechaInicial xmlns="http://www.banguat.gob.gt/variables/ws/">
      <fechainit>' . $formattedDate . '</fechainit>
    </TipoCambioFechaInicial>
  </soap:Body>
</soap:Envelope>';

        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $xml,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://www.banguat.gob.gt/variables/ws/TipoCambioFechaInicial"',
                'Content-Length: ' . strlen($xml),
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 15,
        ]);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception("Error de conexión con Banguat: " . $curlError);
        }

        return $this->parseResponse($response);
    }

    private function parseResponse($xmlString) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString);
        if ($xml === false) {
            throw new Exception("Respuesta inválida de Banguat.");
        }

        $xml->registerXPathNamespace('b', 'http://www.banguat.gob.gt/variables/ws/');
        $vars = $xml->xpath('//b:Var');

        if (empty($vars)) {
            throw new Exception("Banguat no devolvió datos para esta fecha.");
        }

        $data = $vars[0];
        return [
            'fecha' => $this->formatDate((string) $data->fecha),
            'valor' => (float) $data->venta,
        ];
    }

    private function formatDate($dateStr) {
        $date = DateTime::createFromFormat('d/m/Y', trim($dateStr));
        return $date ? $date->format('Y-m-d') : null;
    }
}
