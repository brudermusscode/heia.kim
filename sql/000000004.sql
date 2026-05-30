ALTER TABLE user_settings
ADD account_wipes_left int default 1,
ADD account_wiped_at timestamp NULL;