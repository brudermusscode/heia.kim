BEGIN;

  ALTER TABLE clans
  ADD name_updated_at TIMESTAMP NULL AFTER joinable;

COMMIT;