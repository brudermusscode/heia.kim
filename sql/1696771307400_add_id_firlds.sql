BEGIN;

  ALTER TABLE user_settings
    DROP PRIMARY KEY,
    ADD id INT AUTO_INCREMENT PRIMARY KEY,
    DROP image;

  ALTER TABLE user_settings_privacy
    DROP PRIMARY KEY,
    ADD id INT AUTO_INCREMENT PRIMARY KEY,
    CHANGE policies_consent accepts_policies TINYINT(1) NULL DEFAULT NULL,
    CHANGE visibility is_public TINYINT(1) NULL DEFAULT 1,
    DROP can_react,
    DROP friends_visibility;

COMMIT;