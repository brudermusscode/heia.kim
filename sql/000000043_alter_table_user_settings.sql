BEGIN;

ALTER TABLE user_settings
DROP COLUMN id,
ADD PRIMARY KEY (user_id),
ADD UNIQUE (user_id);

ALTER TABLE user_settings_privacy
DROP COLUMN id,
ADD PRIMARY KEY (user_id),
ADD UNIQUE (user_id);

COMMIT;