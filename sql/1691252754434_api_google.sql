BEGIN;

  ALTER TABLE api_google
  ADD scope TEXT NULL AFTER id_token;

COMMIT;