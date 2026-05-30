BEGIN;

CREATE TABLE user_setting_privacy(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,

  visibility enum(
    "everyone", "friendsplus", "friends", "nobody"
  ) NULL DEFAULT 'everyone',

  friends_visibility enum(
    "everyone", "friendsplus", "friends", "nobody"
  ) NULL DEFAULT 'everyone',

  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE UNIQUE INDEX user_id_index
ON user_setting_privacy (user_id);

COMMIT;