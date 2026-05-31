-- CREATE TABLE authentications (
--   id INT NOT NULL AUTO_INCREMENT,

--   deleted_at TIMESTAMP NULL,
--   updated_at TIMESTAMP NULL,
--   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE sessions
 DROP COLUMN browser,
 DROP COLUMN browser_version,
 DROP COLUMN city,
 DROP COLUMN region,
 DROP COLUMN postal_code,
 DROP COLUMN timezone,
 DROP COLUMN continent,
 DROP COLUMN device_type,
 DROP COLUMN os_title,
 DROP COLUMN os_type,
 DROP COLUMN os,
 DROP COLUMN browser_title;
