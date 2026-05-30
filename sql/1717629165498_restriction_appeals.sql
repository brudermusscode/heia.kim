
  ALTER TABLE restriction_appeals
  ADD status VARCHAR(32) DEFAULT "AWAITING_PROCESSING" AFTER restriction_id;