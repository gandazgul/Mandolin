CREATE TABLE playlists (
  pl_name       varchar(25) NOT NULL DEFAULT playlist,
  pl_contents   varchar(255) NOT NULL,
  pl_user_name  varchar(15) NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (pl_user_name)
    REFERENCES users(user_name)
);

CREATE TRIGGER playlists_befIns_fk_users
  BEFORE INSERT
  ON playlists
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM users WHERE users.user_name = NEW.pl_user_name) IS NULL
    THEN RAISE(ABORT, 'INSERT statement conflicted with COLUMN REFERENCE constraint [playlists -> users].')
  END;
END;

CREATE TRIGGER playlists_befUpd_fk_users
  BEFORE UPDATE OF pl_user_name
  ON playlists
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM users WHERE users.user_name = NEW.pl_user_name) IS NULL
    THEN RAISE(ABORT, 'UPDATE statement conflicted with COLUMN REFERENCE constraint [playlists -> users].')
  END;
END;

CREATE TABLE users (
  user_name         varchar(15) PRIMARY KEY NOT NULL UNIQUE,
  user_password     varchar(40) NOT NULL DEFAULT P455w0rd,
  user_settings     varchar(255),
  last_key          varchar(40),
  last_key_date     integer,
  user_admin_level  integer NOT NULL DEFAULT 1
);

CREATE TRIGGER users_afterUpd_fkr_playlists
  AFTER UPDATE OF user_name
  ON users
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM playlists WHERE pl_user_name = OLD.user_name) IS NOT NULL
    THEN RAISE(ABORT, 'UPDATE statement conflicted with COLUMN REFERENCE constraint [playlists -> users].')
  END;
END;

CREATE TRIGGER users_befDel_fkr_playlists
  BEFORE DELETE
  ON users
BEGIN
  SELECT CASE
    WHEN (SELECT 1 FROM playlists WHERE pl_user_name = OLD.user_name) IS NOT NULL
    THEN RAISE(ABORT, 'DELETE statement conflicted with COLUMN REFERENCE constraint [playlists -> users].')
  END;
END;
