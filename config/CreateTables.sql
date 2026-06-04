CREATE DATABASE Banco;

CREATE TABLE IF NOT EXISTS TipoCambio (
    id          INT     NOT NULL AUTO_INCREMENT,
    rate_date   DATE            NOT NULL,
    rate_value  DECIMAL(10, 4)  NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_rate_date (rate_date)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Cache local de tipos de cambio obtenidos desde el servicio SOAP de Banguat';
    
    
    
CREATE TABLE cuentas (
    numero_cuenta CHAR(10)       NOT NULL PRIMARY KEY,
    cui           CHAR(13)       NOT NULL,
    tipo_cuenta   VARCHAR(20)    NOT NULL DEFAULT 'Ahorro',
    fecha_creacion DATE          NOT NULL DEFAULT (CURRENT_DATE),
    saldo         DECIMAL(12, 2) NOT NULL DEFAULT 100.00
);

