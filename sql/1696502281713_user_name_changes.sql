BEGIN;

  ALTER TABLE `user_name_changes`
  CHANGE `name` `name` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  ADD CONSTRAINT unique_name UNIQUE (name);

COMMIT;