ALTER TABLE countries
  MODIFY abbreviation VARCHAR(4) NOT NULL,
  ADD UNIQUE (abbreviation);
