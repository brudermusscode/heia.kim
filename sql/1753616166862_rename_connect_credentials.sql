-- Model is called connections, so rename the old ----------
-- `connect_credentials` to `connections`.
RENAME TABLE connect_credentials TO connections;

-- Drop some unnecessary columns from connections and rename
-- others for better understanding.
ALTER TABLE connections
  DROP COLUMN token_type,
  RENAME COLUMN type TO provider,
  RENAME COLUMN vendor_id TO provider_user_id,
  RENAME COLUMN vendor_email TO provider_user_email,
  ADD COLUMN expires_at TIMESTAMP NULL after scope;

-- osu! doesn't give us the mail and people signing up with
-- osu! probably want to use their name from there, so we need
-- a new field for a vendor's nickname.
ALTER TABLE connections
  ADD COLUMN provider_user_nickname VARCHAR(64) NULL after provider_user_email;

-- Update all old provider from 'osu' to 'osu!'
UPDATE connections SET provider = 'osu!' WHERE provider = 'osu';
