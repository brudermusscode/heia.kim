ALTER TABLE clan_feed_items
  MODIFY type VARCHAR(255) NULL;

UPDATE clan_users SET clan_priv = 6 WHERE clan_priv IN (2,3,8,9,12);

DELETE FROM notifications where type = "__system__";

UPDATE clan_feed_items SET type = "__squad__/created" WHERE type = "create";
UPDATE clan_feed_items SET type = "__squad__/edit/publicity", reference_id = 1 WHERE type = "joinable";
UPDATE clan_feed_items SET type = "__squad__/edit/name" WHERE type = "name";
UPDATE clan_feed_items SET type = "__squad__/edit/tag" WHERE type = "tag";
UPDATE clan_feed_items SET type = "__member__/left" WHERE type = "leave";
UPDATE clan_feed_items SET type = "__member__/joined" WHERE type = "join";
UPDATE clan_feed_items SET type = "__member__/chief+new" WHERE type = "owner";
UPDATE clan_feed_items SET type = "__post__" WHERE type = "__member__/post";