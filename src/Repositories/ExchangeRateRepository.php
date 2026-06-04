<?php
class ExchangeRateRepository {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function findByDate($date) {
        $query = "SELECT rate_date as fecha, rate_value as valor FROM TipoCambio WHERE rate_date = :date LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastTen() {
        $query = "SELECT rate_date as fecha, rate_value as tipocambio FROM TipoCambio ORDER BY rate_date DESC LIMIT 10";
        $stmt  = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($date, $value) {
        $query = "INSERT INTO TipoCambio (rate_date, rate_value) VALUES (:date, :value)
                  ON DUPLICATE KEY UPDATE rate_value = :value";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':value', $value);
        return $stmt->execute();
    }
}