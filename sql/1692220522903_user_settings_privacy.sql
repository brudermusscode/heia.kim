BEGIN;

  ALTER TABLE user_settings_privacy
  MODIFY visibility TINYINT(1) NOT NULL DEFAULT 0;

  UPDATE user_settings_privacy SET visibility = 0;

COMMIT;