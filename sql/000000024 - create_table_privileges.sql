BEGIN;

CREATE TABLE privileges(
  id INT NOT NULL AUTO_INCREMENT,
  name varchar(24) NULL,
  icon TEXT NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE INDEX name_index
ON privileges (name);

CREATE TABLE user_privileges(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  privilege_id INT NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE INDEX user_id
ON user_privileges (user_id);

CREATE INDEX privilege_id
ON user_privileges (privilege_id);

COMMIT;