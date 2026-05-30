BEGIN;

  CREATE TABLE searches (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(32) NOT NULL,
    search TEXT NOT NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

  -- ALTER TABLE searches
  -- ADD is_default TINYINT DEFAULT 0 AFTER product_id;

COMMIT;