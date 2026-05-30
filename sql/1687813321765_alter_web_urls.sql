BEGIN;

  ALTER TABLE web_urls
  ADD data_diretory_path TEXT NULL AFTER upload_dir;

COMMIT;