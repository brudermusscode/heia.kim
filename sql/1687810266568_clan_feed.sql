BEGIN;

  ALTER TABLE clan_feed
  RENAME TO clan_feeds;

COMMIT;