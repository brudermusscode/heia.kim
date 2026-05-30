BEGIN;

  ALTER TABLE web_urls
  ADD beatmap_mirror TEXT NULL AFTER osu_beatmaps;

COMMIT;