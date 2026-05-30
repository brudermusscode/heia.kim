BEGIN;

  CREATE TABLE user_settings_premium (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    premium_name_style TEXT NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE (user_id),
    INDEX (user_id)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

  -- ALTER TABLE user_settings_premium
  -- ADD is_default TINYINT DEFAULT 0 AFTER product_id;

COMMIT;