BEGIN;

  ALTER TABLE web_settings
  ADD privacy_policies_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER premium_feature_name;

COMMIT;