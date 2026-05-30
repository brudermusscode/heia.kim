BEGIN;

  DROP TABLE clan_feeds;

  ALTER TABLE clan_feed_items
  ADD type TEXT NULL AFTER user_id;

COMMIT;