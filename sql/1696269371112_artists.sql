BEGIN;

  ALTER TABLE artists
  ADD deleted_at TIMESTAMP NULL after name;

COMMIT;