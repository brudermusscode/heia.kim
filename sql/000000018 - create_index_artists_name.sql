BEGIN;

CREATE INDEX name_index_amknoy
ON artists (name);

ALTER TABLE artists
ADD UNIQUE (name);

COMMIT;