BEGIN;

  ALTER TABLE privileges
  DROP COLUMN id,
  ADD PRIMARY KEY (name),
  ADD INDEX (name);

COMMIT;