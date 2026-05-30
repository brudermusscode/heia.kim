BEGIN;

  ALTER TABLE api_google
  ADD token_type TEXT NULL AFTER scope;

COMMIT;