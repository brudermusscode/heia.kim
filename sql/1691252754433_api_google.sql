BEGIN;

  ALTER TABLE api_google
  ADD id_token VARCHAR(1224) NULL AFTER refresh_token;

COMMIT;