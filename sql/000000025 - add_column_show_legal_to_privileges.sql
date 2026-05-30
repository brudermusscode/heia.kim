BEGIN;

ALTER TABLE privileges
ADD show_legal BOOLEAN NULL default 0 AFTER icon,
ADD color VARCHAR(24) NULL AFTER icon;

COMMIT;