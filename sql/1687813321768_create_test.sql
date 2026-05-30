BEGIN;

  CREATE TABLE test (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT(11) NULL,
      action VARCHAR(24) NULL,
      message TEXT NULL,
      deleted_at TIMESTAMP NULL,
      updated_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

COMMIT;