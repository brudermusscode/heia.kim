-- web_exceptions migration file

BEGIN;

  -- Create the web_exceptions table
  CREATE TABLE web_exceptions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      message TEXT NULL,
      file TEXT NULL,
      line TEXT NULL,
      trace TEXT NULL,
      deleted_at TIMESTAMP NULL,
      updated_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  ALTER TABLE web_exceptions
  ADD section TEXT NULL AFTER id;


COMMIT;