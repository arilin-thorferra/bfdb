/*
 * GCDb - master SQLite schema
 * ===========================
 *
 * DO NOT execute this script if you do not intend to initialize the
 * database. It will destroy all existing data!
 */

 PRAGMA foreign_keys = on;

-- users

DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  email         TEXT UNIQUE NOT NULL,
  passwd        TEXT NOT NULL,
  adult_ok      INTEGER NOT NULL DEFAULT 0 CHECK (adult_ok IN(0,1)),
  is_admin      INTEGER NOT NULL DEFAULT 0 CHECK (is_admin IN(0,1)),
  created_date  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_date  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TRIGGER users_updated AFTER UPDATE ON users
BEGIN
  UPDATE users SET updated_date = datetime('now')
  WHERE id = NEW.id;
END;

-- characters

DROP TABLE IF EXISTS characters;

CREATE TABLE characters (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  uniqid        TEXT UNIQUE NOT NULL,
  user_id       INTEGER NOT NULL,
  char_name     TEXT NOT NULL,
  species       TEXT,
  gender        TEXT,
  appearance    TEXT,
  bio           TEXT,
  likes         TEXT,
  dislikes      TEXT,
  notes         TEXT,
  adult         INTEGER NOT NULL DEFAULT 0 CHECK (adult IN(0,1)),
  show_in_list  INTEGER NOT NULL DEFAULT 0 CHECK (show_in_list IN(0,1)),
  created_date  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_date  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
);
CREATE INDEX char_name ON characters (char_name);

CREATE TRIGGER chars_updated AFTER UPDATE ON characters
BEGIN
  UPDATE characters SET updated_date = datetime('now')
  WHERE id = NEW.id;
END;

-- link kinds

DROP TABLE IF EXISTS link_kinds;

CREATE TABLE link_kinds (
  id    TEXT PRIMARY KEY,
  name  TEXT NOT NULL
);
INSERT INTO link_kinds (id, name)
  VALUES ('www', 'Homepage'),
         ('twt', 'Twitter'),
         ('mst', 'Mastodon'),
         ('tel', 'Telegram'),
         ('dsc', 'Discord'),
         ('pat', 'Patreon'),
         ('fur', 'Furbase'),
         ('faf', 'Fur Affinity'),
         ('fls', 'F-List'),
         ('wea', 'Weasyl'),
         ('ink', 'Inkbunny'),
         ('fnt', 'Furry Network');

-- links

DROP TABLE IF EXISTS links;

CREATE TABLE links (
  link_id       INTEGER PRIMARY KEY AUTOINCREMENT,
  char_id       INTEGER NOT NULL,
  kind_id       TEXT NOT NULL,
  content       TEXT NOT NULL,
  CONSTRAINT fk_link_char FOREIGN KEY (char_id)
    REFERENCES characters(id) ON DELETE CASCADE,
  CONSTRAINT fk_link_kind FOREIGN KEY (kind_id)
    REFERENCES link_kinds(id)
);
CREATE INDEX kind_char_id ON links (char_id);

-- images

DROP TABLE IF EXISTS images;

CREATE TABLE images (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  char_id       INTEGER NOT NULL,
  filename      TEXT,
  caption       TEXT,
  artist        TEXT,
  height        INTEGER,
  width         INTEGER,
  adult         INTEGER NOT NULL DEFAULT 0 CHECK (adult IN(0,1)),
  created_date  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_char_id FOREIGN KEY (char_id) REFERENCES characters(id)
    ON DELETE CASCADE
);
CREATE INDEX image_char_id ON links (char_id);
