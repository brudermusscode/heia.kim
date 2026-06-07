-- We have removed Google for OAuth.
DELETE FROM features WHERE name = "connect_google";

-- We have added GitHub for OAuth.
INSERT INTO features (name, active) VALUES ("connect_github", 1);
