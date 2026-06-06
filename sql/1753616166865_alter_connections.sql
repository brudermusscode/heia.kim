-- Creating a new connection for signing up will create a new
-- Authentication with a token. This token can be saved here
-- to authenticate the user with the connection later.
ALTER TABLE connections
 ADD COLUMN `authentication_token` TEXT NULL AFTER `user_id`;
