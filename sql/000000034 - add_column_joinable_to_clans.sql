BEGIN;

ALTER TABLE clan_settings
ADD joinable BOOLEAN NULL default 1 AFTER logo;

COMMIT;