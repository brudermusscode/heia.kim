CREATE TABLE user_reports(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  user_notification TINYINT(1) NULL default 1,
  reference_id int NULL,
  report_type VARCHAR(28) NULL,
  comment_string TEXT NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE INDEX user_id_index
ON user_reports (user_id);

CREATE INDEX reference_id_index
ON user_reports (reference_id);