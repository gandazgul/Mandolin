CREATE TABLE albums (
  alb_id      integer PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
  alb_name    varchar(60) NOT NULL,
  alb_art_id  integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (alb_art_id)
    REFERENCES artists(art_id)
);

CREATE TABLE artists (
  art_id    integer PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
  art_name  varchar(60) NOT NULL UNIQUE
);

CREATE TABLE [movies] (
[id] INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
[title] TEXT DEFAULT 'title' NOT NULL,
[mID] TEXT DEFAULT '0' UNIQUE NOT NULL,
[category] TEXT DEFAULT 'No Category' NOT NULL,
[path] TEXT DEFAULT '/' NOT NULL
);

CREATE TABLE [music] (
[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[song_id] varchar(40) DEFAULT '0' UNIQUE NOT NULL,
[song_path] varchar(255) DEFAULT '.' UNIQUE NOT NULL,
[song_name] varchar(60) DEFAULT 'name' NOT NULL,
[song_ext] varchar(4) DEFAULT 'mp3' NOT NULL,
[song_album] integer DEFAULT '0' NOT NULL,
[song_art] integer DEFAULT '0' NOT NULL,
[song_comments] varchar(255)  NULL,

  /* Foreign keys */
  FOREIGN KEY (song_album)
    REFERENCES albums(alb_id),
  FOREIGN KEY (song_art)
    REFERENCES artists(art_id)
);

CREATE TABLE [playlists] (
[id] INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
[pl_name] varchar(25) DEFAULT 'playlist' UNIQUE NOT NULL,
[pl_contents] TEXT DEFAULT '0|' NOT NULL,
[pl_user_name] varchar(15)  NOT NULL,

  /* Foreign keys */
  FOREIGN KEY (pl_user_name)
    REFERENCES users(user_name)
);

CREATE TABLE [users] (
[user_id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[user_name] varchar(15) DEFAULT 'username' UNIQUE NOT NULL,
[user_password] varchar(40) DEFAULT '7ef083b4f9dd719830c46ed43d0d882eae05c097' NOT NULL,
[user_settings] TEXT DEFAULT '{"plFormat":"m3u","bitrate":"128"}' NULL,
[last_key] varchar(40)  NULL,
[last_key_date] integer  NULL,
[user_admin_level] INTEGER DEFAULT '0' NOT NULL
);

CREATE TRIGGER albums_bi_fk_artists
  BEFORE INSERT
  ON albums
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM artists WHERE artists.art_id = NEW.alb_art_id) IS NULL
    THEN RAISE(ABORT, 'Can''t insert that album because artist ID doesnt exist on artists table')
  END;
END;

CREATE TRIGGER albums_bu_fk_artists
  BEFORE UPDATE OF alb_art_id
  ON albums
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM artists WHERE artists.art_id = NEW.alb_art_id) IS NULL
    THEN RAISE(ABORT, 'Can''t update record. artist id is doesn''t exist in the artists table.')
  END;
END;

CREATE TRIGGER artists_au_fkr_albums
  AFTER UPDATE OF art_id
  ON artists
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM albums WHERE alb_art_id = OLD.art_id) IS NOT NULL
    THEN RAISE(ABORT, 'Can''t change Artist ID because this artist has albums conected with it.')
  END;
END;

CREATE TRIGGER artists_au_fkr_music
  AFTER UPDATE OF art_id
  ON artists
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM music WHERE song_art = OLD.art_id) IS NOT NULL
    THEN RAISE(ABORT, 'Can''t update artist id because it doesn''t exist on table artists')
  END;
END;

CREATE TRIGGER artists_bd_fkr_albums
  BEFORE DELETE
  ON artists
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM albums WHERE alb_art_id = OLD.art_id) IS NOT NULL
    THEN RAISE(ABORT, 'Can''t delete this artist because there are albums conected to it.')
  END;
END;

CREATE TRIGGER artists_bd_fkr_music
  BEFORE DELETE
  ON artists
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM music WHERE song_art = OLD.art_id) IS NOT NULL
    THEN RAISE(ABORT, 'Can''t delete this artist, because there is music conected to it')
  END;
END;

CREATE TRIGGER [music_bi_fk_albums]
BEFORE INSERT ON [music]
FOR EACH ROW
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM albums WHERE albums.alb_id = NEW.song_album) IS NULL
    THEN RAISE(ABORT, 'Can''t insert that song because the album ID doesn''t exist in the albums table')
  END;
END;

CREATE TRIGGER [music_bi_fk_artists]
BEFORE INSERT ON [music]
FOR EACH ROW
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM artists WHERE artists.art_id = NEW.song_art) IS NULL
    THEN RAISE(ABORT, 'Can''t insert this song, because the artist id doesn''t exist in the artists table')
  END;
END;

CREATE TRIGGER [music_bu_fk_albums]
BEFORE UPDATE OF [SONG_ALBUM]
ON [music]
FOR EACH ROW
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM albums WHERE albums.alb_id = NEW.song_album) IS NULL
    THEN RAISE(ABORT, 'Can''t update this song''s album ID because it doesnt exist in the albums table')
  END;
END;

CREATE TRIGGER [music_bu_fk_artists]
BEFORE UPDATE OF [SONG_ART]
ON [music]
FOR EACH ROW
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM artists WHERE artists.art_id = NEW.song_art) IS NULL
    THEN RAISE(ABORT, 'Can''t update artist id for this song because it doesnt exist in the table artists')
  END;
END;

CREATE TRIGGER [playlist_insert_bad_username]
BEFORE INSERT ON [playlists]
FOR EACH ROW
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM users WHERE users.user_name = NEW.pl_user_name) IS NULL
    THEN RAISE(ABORT, 'The username specified for this playlist does not exist in the users table.')
  END;
END;

CREATE TRIGGER [playlist_update_bad_username]
BEFORE UPDATE OF [PL_USER_NAME]
ON [playlists]
FOR EACH ROW
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM users WHERE users.user_name = NEW.pl_user_name) IS NULL
    THEN RAISE(ABORT, 'UPDATE statement conflicted with COLUMN REFERENCE constraint [playlists -> users].')
  END;
END;

CREATE TRIGGER [users_update_username_update_playlists]
BEFORE UPDATE OF [USER_NAME]
ON [users]
FOR EACH ROW
BEGIN

UPDATE playlists SET pl_user_name=NEW.user_name WHERE pl_user_name=OLD.user_name;

END;

CREATE TRIGGER [users_when_delete_playlists]
BEFORE DELETE ON [users]
FOR EACH ROW
BEGIN

DELETE FROM playlists WHERE pl_user_name=OLD.user_name;

END;

INSERT INTO users(user_name, user_password, user_admin_level) VALUES('admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1);
