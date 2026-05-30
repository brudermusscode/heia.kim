BEGIN;

ALTER TABLE `api_google`
  COLLATE 'utf8mb4_general_ci',
  CHANGE `google_user_id` `google_user_id` varchar(324) COLLATE 'utf8mb4_general_ci' NOT NULL,
  CHANGE `access_token` `access_token` TEXT COLLATE 'utf8mb4_general_ci' NULL,
  CHANGE `refresh_token` `refresh_token` TEXT COLLATE 'utf8mb4_general_ci' NULL,
  CHANGE `id_token` `id_token` TEXT COLLATE 'utf8mb4_general_ci' NULL,
  CHANGE `scope` `scope` TEXT COLLATE 'utf8mb4_general_ci' NULL,
  CHANGE `token_type` `token_type` TEXT COLLATE 'utf8mb4_general_ci' NULL;

COMMIT;