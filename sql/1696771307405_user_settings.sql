BEGIN;

  ALTER TABLE user_settings
    DROP COLUMN headline;

  ALTER TABLE user_settings_premium
    ADD headline VARCHAR(32) NULL after user_id;

  ALTER TABLE user_settings_privacy
    CHANGE accepts_policies accepts_policies TINYINT(1) NULL DEFAULT 1;

COMMIT;