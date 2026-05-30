BEGIN;

  ALTER TABLE map_search_users
  RENAME TO user_map_searches;

COMMIT;