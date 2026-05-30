BEGIN;

ALTER TABLE web_sessions
DROP COLUMN serial,
DROP COLUMN httpx_forwarded,
DROP COLUMN geo_request_ip;

ALTER TABLE users
DROP COLUMN httpx_forwarded;

COMMIT;
