BEGIN;

  CREATE TABLE relationship_requests (
    id INT NOT NULL AUTO_INCREMENT,
    user_id_from INT NOT NULL,
    user_id_to INT NOT NULL,
    type TEXT NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX idx_user_id_from (`user_id_from`),
    INDEX idx_user_id_to (`user_id_to`)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

COMMIT;