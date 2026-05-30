  ALTER TABLE authentications
    ADD code INT(6) NULL after token,
    ADD user_id INT NULL after id;