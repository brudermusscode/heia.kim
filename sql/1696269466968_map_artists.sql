BEGIN;

  CREATE TABLE map_artists (
    id INT NOT NULL AUTO_INCREMENT,
    beatmap_id INT NOT NULL,
    artist_id INT NOT NULL,

    deleted_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

  -- ALTER TABLE map_artists
  -- ADD is_default TINYINT DEFAULT 0 AFTER product_id;

COMMIT;