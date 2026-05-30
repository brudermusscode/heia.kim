BEGIN;

ALTER TABLE clan_images
ADD image_path TEXT NULL AFTER user_id;

COMMIT;