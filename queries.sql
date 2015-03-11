

-- Get name and email from users
SELECT name, email FROM users WHERE name='$user' LIMIT 1;

-- checking for an existing email
SELECT email FROM users WHERE email='$email' LIMIT 1;

-- adding a new user
INSERT INTO users(name,pass,email,location,genre) VALUES ('$user','$pass','$email','$location','$genre');

-- get the user id for storing in the session
SELECT id from users where name='$user'

-- delete a users playlist
DELETE FROM userPlaylist where user_id=$id;

--get a song from the songs table
Select id from songs where url='$url' and name='$title';

-- add a new song to the songs table
INSERT INTO songs values('', '$title', '$url');

-- get a user's info for login validation
SELECT id, name, pass FROM users WHERE name='$user';

-- get all of a user's info to send to frontend
SELECT id, name, email, location, genre FROM users WHERE id='$id' LIMIT 1;

-- get the song name and url for each song a user's playlist
SELECT S.name, S.url from userPlaylist PL 
inner join songs S on S.id = PL.song_id 
where PL.user_id='$id';

-- update a user's info
UPDATE users SET email='$email',genre='$genre',location='$location' WHERE id='$id';

-- get the subscription id for a user
SELECT subscribed_id FROM subedPlaylist WHERE subed_user_id='$user';

-- get the id of the user to subscribe to
select id from users where name='$to_subscribe';

-- get all of a user's playlist subscriptions
select subscribed_id from subedPlaylist where subscribed_id='$user' and subed_user_id='$subscribe_to_id';

-- add a new playlist subscription
Insert into subedPlaylist values('$user', '$subscribe_to_id');

-- delete a subscription
Delete from subedPlaylist where subscribed_id=$subscribe_to_id and subed_user_id=$user;

-- get the Name of all the users someone is subscribed to
SELECT U.name from users U 
inner join (SELECT sP.subscribed_id from subedPlaylist sP where subed_user_id='$id') as T1 
 on U.id = T1.subscribed_id); 

-- get 5 user names to show in the subscriptions modal
SELECT id, name from users where id!='$id' LIMIT 5;