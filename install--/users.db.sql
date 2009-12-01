CREATE TABLE playlists (
  pl_name       varchar(25) PRIMARY KEY ON CONFLICT FAIL NOT NULL UNIQUE ON CONFLICT FAIL DEFAULT 'playlist',
  pl_contents   text NOT NULL DEFAULT '0|',
  pl_user_name  varchar(15) NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (pl_user_name)
    REFERENCES users(user_name)
);

CREATE TABLE users (
  user_name varchar(15) DEFAULT 'username' UNIQUE NOT NULL,
  user_password varchar(40) DEFAULT '7ef083b4f9dd719830c46ed43d0d882eae05c097' NOT NULL,
  user_settings varchar(255)  NULL,
  last_key varchar(40)  NULL,
  last_key_date integer  NULL,
  user_admin_level integer DEFAULT 0 NOT NULL,
  user_id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT
);

CREATE TRIGGER insert_playlist_bad_username 
BEFORE INSERT ON playlists 
FOR EACH ROW 
BEGIN 

SELECT CASE
    WHEN (SELECT 1 FROM users WHERE users.user_name = NEW.pl_user_name) IS NULL
    THEN RAISE(ABORT, 'The username specified for this playlist does not exist in the users table.')
  END;

END;

CREATE TRIGGER update_playlist_bad_username 
BEFORE UPDATE OF pl_user_name 
ON playlists 
FOR EACH ROW 
BEGIN 

SELECT CASE
    WHEN (SELECT 1 FROM users WHERE users.user_name = NEW.pl_user_name) IS NULL
    THEN RAISE(ABORT, 'The username specified for this playlist does not exist in the users table.')
  END;

END;

CREATE TRIGGER update_users_username_update_playlists 
BEFORE UPDATE OF user_name 
ON users 
FOR EACH ROW 
BEGIN 

UPDATE playlists SET pl_user_name=NEW.user_name WHERE pl_user_name=OLD.user_name;

END;

CREATE TRIGGER when_delete_user_delete_playlists 
BEFORE DELETE ON users 
FOR EACH ROW 
BEGIN 

DELETE FROM playlists WHERE pl_user_name=OLD.user_name;

END;

