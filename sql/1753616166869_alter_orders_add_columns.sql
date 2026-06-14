BEGIN;

ALTER TABLE orders
  RENAME COLUMN reference_id TO user_2_id,
  RENAME COLUMN api_user_id TO provider_user_id,
  RENAME COLUMN currency TO order_currency;

ALTER TABLE orders
  ADD COLUMN order_links TEXT NULL after order_currency;

COMMIT;
