-- CREATE TABLE profiles (
--   id INT NOT NULL AUTO_INCREMENT,

--   deleted_at TIMESTAMP NULL,
--   updated_at TIMESTAMP NULL,
--   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE profiles
  DROP COLUMN scores_top,
  DROP COLUMN scores_first,
  DROP COLUMN scores_recent,
  DROP COLUMN beatmaps_recent,
  DROP COLUMN artists_recent,
  DROP COLUMN squads,
  DROP COLUMN followers,
  ADD COLUMN sections_visibility JSON NULL AFTER user_id,
  ADD INDEX user_id (user_id);