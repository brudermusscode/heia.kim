BEGIN;

  ALTER TABLE scores
  ADD deleted_at TIMESTAMP NULL after online_checksum,
  ADD updated_at TIMESTAMP NULL after deleted_at,
  ADD created_at TIMESTAMP default CURRENT_TIMESTAMP after updated_at;

COMMIT;