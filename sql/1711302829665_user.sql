  ALTER TABLE users
    ADD frozen_at TIMESTAMP DEFAULT NULL AFTER remote_address;
