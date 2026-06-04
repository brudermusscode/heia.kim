-- Third party/vendor/provider which offer API to interact with like osu!,
-- google and discord.
CREATE TABLE `api_providers` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `provider` VARCHAR(32) NOT NULL,
    `access_token` TEXT NULL,
    `refresh_token` TEXT NULL,
    `expires_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO api_providers (provider) VALUES ("osu!");
INSERT INTO api_providers (provider) VALUES ("discord");
