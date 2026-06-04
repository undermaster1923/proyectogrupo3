<?php
require_once __DIR__ . '/../Repositories/AccountRepository.php';

class AccountService {
    private $repo;

    public function __construct($db) {
        $this->repo = new AccountRepository($db);
    }

    public function getAccountByCui($cui) {
        $account = $this->repo->findByCui($cui);
        if (!$account) {
            throw new Exception("No se encontró una cuenta asociada al CUI {$cui}.");
        }
        return $account;
    }

    public function createAccount($cui) {
        $existing = $this->repo->findByCui($cui);
        if ($existing) {
            throw new Exception("Ya existe una cuenta registrada para el CUI {$cui}.");
        }

        $numeroCuenta = $this->generateNumeroCuenta();
        return $this->repo->create($numeroCuenta, $cui);
    }

    private function generateNumeroCuenta() {
        do {
            $numero = str_pad(random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while ($this->repo->numeroCuentaExists($numero));

        return $numero;
    }
}
