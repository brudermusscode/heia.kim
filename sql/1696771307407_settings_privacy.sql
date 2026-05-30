BEGIN;

  ALTER TABLE user_settings_privacy
    ADD can_interact INT NOT NULL DEFAULT 0 after is_public;

COMMIT;