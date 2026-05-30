BEGIN;

  ALTER TABLE web_settings
    DROP COLUMN show_errors;

COMMIT;