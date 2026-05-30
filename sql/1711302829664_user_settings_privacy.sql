  ALTER TABLE user_settings_privacy
    ADD mailing_newsletter TINYINT DEFAULT 1 AFTER can_interact,
    ADD mailing_expiring_premium TINYINT DEFAULT 1 AFTER mailing_newsletter,
    ADD mailing_reminder TINYINT DEFAULT 1 AFTER mailing_expiring_premium,
    ADD mailing_birthday TINYINT DEFAULT 1 AFTER mailing_reminder;