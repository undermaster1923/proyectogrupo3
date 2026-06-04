<?php
class AccountRepository {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function findByCui($cui) {
        $query = "SELECT numero_cuenta, cui, tipo_cuenta, fecha_creacion, saldo FROM cuentas WHERE cui = :cui";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':cui', $cui);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($numeroCuenta, $cui) {
        $query = "INSERT INTO cuentas (numero_cuenta, cui) VALUES (:numero_cuenta, :cui)";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':numero_cuenta', $numeroCuenta);
        $stmt->bindParam(':cui', $cui);
        $stmt->execute();
        return $this->findByCui($cui);
    }

    public function numeroCuentaExists($numeroCuenta) {
        $query = "SELECT COUNT(*) FROM cuentas WHERE numero_cuenta = :numero_cuenta";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':numero_cuenta', $numeroCuenta);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }
}
