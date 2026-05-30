BEGIN;

  CREATE TABLE clan_feed_items (
    id INT NOT NULL AUTO_INCREMENT,
    clan_id INT NOT NULL,
    user_id INT NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

  -- ALTER TABLE clan_feed_items
  -- ADD is_default TINYINT DEFAULT 0 AFTER product_id;

COMMIT;