-- Alot of fields do not allow null values. But we need!
ALTER TABLE jobs
  MODIFY COLUMN section VARCHAR(32) NULL,
  MODIFY COLUMN name VARCHAR(124) NULL,
  MODIFY COLUMN description TEXT NULL;
