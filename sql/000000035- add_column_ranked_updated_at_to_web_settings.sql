BEGIN;

ALTER TABLE web_settings
ADD ranked_updated_at TIMESTAMP NULL AFTER metric;

COMMIT;