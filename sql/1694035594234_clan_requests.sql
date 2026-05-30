BEGIN;

  ALTER TABLE clan_requests
  CHANGE user_id user_id INT NOT NULL,
  CHANGE clan_id clan_id INT NOT NULL;

COMMIT;