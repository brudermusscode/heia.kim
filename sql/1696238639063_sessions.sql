BEGIN;

  TRUNCATE TABLE sessions;

  ALTER TABLE sessions

  DROP created_at,
  DROP updated_at,
  DROP token,

  ADD token VARCHAR(255) NOT NULL after user_id,
  ADD UNIQUE(`token`),
  ADD updated_at TIMESTAMP NULL after deleted_at,
  ADD created_at TIMESTAMP default CURRENT_TIMESTAMP after updated_at,

  RENAME COLUMN geo_city TO city,
  RENAME COLUMN geo_country_code TO country,
  RENAME COLUMN geo_region TO region,
  RENAME COLUMN geo_continent_code TO continent,

  ADD timezone VARCHAR(92) NULL after continent,
  Add postal_code VARCHAR(24) NULL after city;

COMMIT;