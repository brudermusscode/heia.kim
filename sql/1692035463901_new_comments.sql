BEGIN;

  CREATE TABLE new_comments (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    type INT NOT NULL,
    reference_id INT NOT NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX (user_id),
    INDEX (reference_id)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

  -- ALTER TABLE new_comments
  -- ADD is_default TINYINT DEFAULT 0 AFTER product_id;

COMMIT;