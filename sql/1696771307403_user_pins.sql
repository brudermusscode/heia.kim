BEGIN;

  CREATE TABLE user_pins (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(32) NOT NULL,
    reference_id INT NOT NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

  ALTER TABLE user_settings
    ADD headline VARCHAR(32) NULL after birthday;

COMMIT;