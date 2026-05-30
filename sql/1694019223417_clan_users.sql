BEGIN;

  CREATE TABLE clan_users (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    clan_id INT NOT NULL,
    clan_priv INT NOT NULL DEFAULT 1,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `clan_users_user_id_index` (`user_id`),
    UNIQUE `clan_users_user_id_clan_id_unique` (`user_id`)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

  -- ALTER TABLE clan_users
  -- ADD is_default TINYINT DEFAULT 0 AFTER product_id;

COMMIT;