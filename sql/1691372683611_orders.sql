BEGIN;

  CREATE TABLE orders (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_id VARCHAR(255) NULL,
    api_user_id VARCHAR(255) NULL,
    order_status TEXT NULL,
    order_amount DECIMAL(10, 2) DEFAULT 0.00,
    currency TEXT NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE (order_id),
    INDEX (user_id, order_id)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

COMMIT;