BEGIN;

CREATE TABLE anti_cheat_software(
  id INT NOT NULL AUTO_INCREMENT,
  software_name TEXT NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE INDEX software_name_index
ON anti_cheat_software (software_name);

INSERT INTO anti_cheat_software (software_name) values ('Aim Assist');
INSERT INTO anti_cheat_software (software_name) values ('Timewarp');
INSERT INTO anti_cheat_software (software_name) values ('Relax');
INSERT INTO anti_cheat_software (software_name) values ('Autopilot');
INSERT INTO anti_cheat_software (software_name) values ('Replay Stealer');
INSERT INTO anti_cheat_software (software_name) values ('Replay Editor');
INSERT INTO anti_cheat_software (software_name) values ('AR Changer');
INSERT INTO anti_cheat_software (software_name) values ('OD/CS Changer');
INSERT INTO anti_cheat_software (software_name) values ('Hidden Remover');
INSERT INTO anti_cheat_software (software_name) values ('Flashlight Remover');
INSERT INTO anti_cheat_software (software_name) values ('Chatter');

COMMIT;