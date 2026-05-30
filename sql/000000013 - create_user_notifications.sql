BEGIN;

CREATE TABLE user_notifications(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  type TEXT NULL,
  reference_id INT NULL,
  message TEXT NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE INDEX user_id_index
ON user_notifications (user_id);

CREATE INDEX reference_id_index
ON user_notifications (reference_id);

ALTER TABLE relationships DROP PRIMARY KEY;

ALTER TABLE relationships
ADD id INT NOT NULL AUTO_INCREMENT FIRST,
ADD updated_at TIMESTAMP NULL,
ADD created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
ADD PRIMARY KEY (id);

CREATE INDEX user1_index
ON relationships (user1);

CREATE INDEX user2_index
ON relationships (user2);

ALTER TABLE `user_settings`
ADD `checked_notifications_at` TIMESTAMP NULL AFTER `birthday`;

ALTER TABLE `user_notifications`
ADD `read_at` TIMESTAMP NULL AFTER `message`;

COMMIT;