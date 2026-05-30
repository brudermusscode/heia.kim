BEGIN;

  ALTER TABLE map_searches
  MODIFY search_string VARCHAR(254),
  ADD CONSTRAINT unq_search_string UNIQUE (search_string);

COMMIT;