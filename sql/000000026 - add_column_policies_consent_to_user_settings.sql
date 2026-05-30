BEGIN;

ALTER TABLE user_setting_privacy
ADD policies_consent BOOLEAN NULL default 0 AFTER user_id;

COMMIT;