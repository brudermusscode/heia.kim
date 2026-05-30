BEGIN;

  CREATE TABLE profiles (
    user_id INT NOT NULL AUTO_INCREMENT,
    scores_top JSON NULL,
    scores_first JSON NULL,
    scores_recent JSON NULL,
    beatmaps_recent JSON NULL,
    artists_recent JSON NULL,
    squads JSON NULL,
    followers JSON NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

  -- ALTER TABLE profiles
  -- ADD is_default TINYINT DEFAULT 0 AFTER product_id;

COMMIT;