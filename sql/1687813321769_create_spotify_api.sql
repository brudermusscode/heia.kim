BEGIN;

  CREATE TABLE api_spotify (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT(11) NOT NULL,
      access_token TEXT NOT NULL,
      refresh_token TEXT NOT NULL,
      deleted_at TIMESTAMP NULL,
      updated_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

COMMIT;