BEGIN;

ALTER TABLE user_reports
  RENAME TO reports;

COMMIT;