-- make users table

CREATE TABLE users (
  id INT AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  location VARCHAR(255) NOT NULL,
  genre VARCHAR(255) NOT NULL,
  email TEXT, 
  pass TEXT
  PRIMARY KEY  (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- create songs
CREATE TABLE songs(
  id INT AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  url VARCHAR(255) NOT NULL,
  PRIMARY KEY  (id),
  CONSTRAINT unique(name, url)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- user playlist
CREATE TABLE userPlaylist (
  user_id INT,
  song_id INT,
  CONSTRAINT fk_song_id FOREIGN KEY (song_id) REFERENCES songs (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;