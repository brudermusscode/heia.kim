BEGIN;

  ALTER TABLE web_settings
  ADD premium_feature_name VARCHAR(255) NULL DEFAULT 'Premium+' AFTER metric,
  ADD currency VARCHAR(255) NULL DEFAULT 'EUR' AFTER metric;

COMMIT;