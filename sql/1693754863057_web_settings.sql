BEGIN;

  ALTER TABLE web_settings
  ADD premium_feature_price float DEFAULT 2.99 AFTER  premium_feature_name;

COMMIT;