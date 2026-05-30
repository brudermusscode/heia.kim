BEGIN;

ALTER TABLE user_setting_privacy
ADD can_react VARCHAR(24) NULL DEFAULT 'everyone' AFTER friends_visibility;

COMMIT;