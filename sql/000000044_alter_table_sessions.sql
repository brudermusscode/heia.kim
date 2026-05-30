BEGIN;

ALTER TABLE sessions
RENAME COLUMN removed_at TO deleted_at;

COMMIT;