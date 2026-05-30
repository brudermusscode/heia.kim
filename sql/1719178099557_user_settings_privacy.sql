ALTER TABLE user_settings_privacy
  ADD receive_invites TINYINT DEFAULT 1 AFTER image_history;