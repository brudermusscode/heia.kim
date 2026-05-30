BEGIN;

-- Add UNIQUE constraint to discord_user_id
ALTER TABLE api_discord ADD CONSTRAINT unique_discord_user_id UNIQUE (discord_user_id);

COMMIT;