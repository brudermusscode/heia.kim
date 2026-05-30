BEGIN;

  ALTER TABLE user_settings
  ADD deleted_at TIMESTAMP NULL AFTER checked_notifications_at;


  ALTER TABLE user_settings_privacy
  ADD deleted_at TIMESTAMP NULL AFTER can_react;

COMMIT;