ALTER TABLE map_requests
  ADD deleted_at TIMESTAMP NULL after active,
  ADD updated_at TIMESTAMP NULL after deleted_at,
  ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP after updated_at;