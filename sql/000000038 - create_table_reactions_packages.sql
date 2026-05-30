BEGIN;

CREATE TABLE reaction_packages(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  package_name INT NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE INDEX user_id
ON reaction_packages (user_id);

CREATE TABLE reaction_package_emojis(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  reaction_package_id INT NULL,
  reaction VARCHAR(222) NULL,
  emoji VARCHAR(32) NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE INDEX user_id
ON reaction_package_emojis (user_id);

CREATE INDEX reaction_package_id
ON reaction_package_emojis (reaction_package_id);

COMMIT;
