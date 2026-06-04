<?php
class Database {
    private $host     = "yamabiko.proxy.rlwy.net";
    private $port     = "57445";
    private $db_name  = "banco";
    private $username = "root";
    private $password = "gTvvwsyfZXtamEfTXPTgzChdpsZXxAxg";
    public  $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de conexión con la base de datos.']);
            exit;
        }
        return $this->conn;
    }
}
