BEGIN;

CREATE TABLE `squad_logs` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `squad_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `affected_user_id` INT NULL,
    `reference_id` INT NULL,
    `type` VARCHAR(64) NOT NULL,
    `deleted_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

COMMIT;
