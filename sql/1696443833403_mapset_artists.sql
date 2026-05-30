BEGIN;

  ALTER TABLE map_artists
  RENAME TO mapset_artists,
  RENAME COLUMN beatmap_id TO mapset_id;

COMMIT;