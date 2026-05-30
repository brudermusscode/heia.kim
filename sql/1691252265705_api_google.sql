BEGIN;

  CREATE TABLE api_google (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT(11) NULL,
    google_user_id VARCHAR(255) NOT NULL,
    access_token TEXT NOT NULL,
    refresh_token TEXT NOT NULL,
    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX api_google_user_id (user_id),
    UNIQUE INDEX api_google_google_user_id (google_user_id)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

  -- ALTER TABLE api_google
  -- ADD is_default TINYINT DEFAULT 0 AFTER product_id;

COMMIT;
