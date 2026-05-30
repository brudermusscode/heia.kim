BEGIN;

  ALTER TABLE clans
  ADD headline TEXT NULL AFTER owner,
  ADD logo TEXT NULL AFTER image,
  ADD joinable TINYINT DEFAULT 1 AFTER logo;

  DROP TABLE clan_settings;

COMMIT;