var widget = null;
$(document).ready(function() {

    //check if user is logged in already
    $(function() {
      $.ajax({
        type: "POST",
        data: {type: 5}, //check session 
        url: 'getData.php',
        datatype: 'json',
        success: function(res) {
          if(res != 0)
            startup(res);
        }
      });
    });

   // $("#signupForm").validate();
    $('[data-toggle=offcanvas]').click(function () {
      $('.row-offcanvas').toggleClass('active')
    });

  $(function() {

    var iframe = document.querySelector('#soundcloud_player');
    widget = SC.Widget(iframe);        
    widget.bind(SC.Widget.Events.READY, function() {
      //start the player
      playNext(widget);
    });

    widget.bind(SC.Widget.Events.FINISH, function() {        
      //get next song off stack
      playNext(widget);
    });

    widget.getSounds(function(sounds) {
      //console.log(sounds);
    });               

    $("#forwarButton").on("click", function(){
      // console.log("next song on stack")
      playNext(widget);
    });

    $("#pauseButton").on("click", function(){
      //  console.log("pause");
      widget.pause();
    });

    $("[id^='div']").on('click', function(){
      track = $(this).data();
      pushPlayList(track, update_DB_playlist);
      updateSideBar();
    });

    $("#userName").on('click', function(){
      $('#login').modal('show');
    });

    $("#logout").on("click", function(){
      $.ajax({
        type: "POST",
        data: {type: 6}, //check session 
        url: 'getData.php'
      });
      //this works but not well, fix it later
      setTimeout(function(){location.reload();}, 1000);
    });

  });
});

//store playlists in stack
var playListStack = [];

/*
Auth for sound cloud api
*/
function init(){
  SC.initialize({client_id: '18d6e5cbc088f68f2bb6a68ad994b3fe'});
}

/*
function to handle the search bar.  This
refreshes the albums below the player while 
not altering the player or playlist.
*/ 
function search(searchTerm, overwrite, callback){

  SC.get('/tracks', { q: searchTerm }, function(tracks){
              
    divCounter = 1;
              
    for(i = 1; i < tracks.length; i++){
      if(tracks[i].artwork_url && tracks[i].title){
                  
        $('#track'+divCounter+'-img').attr('src', tracks[i].artwork_url); //update album image and force browser reload of images.
        $('#track'+divCounter+'-title').text(tracks[i].title);
        $('#div'+divCounter).data(tracks[i]);
        if(overwrite) 
          playListStack.unshift(tracks[i]);
        divCounter++;
      }
      //get out of loop       
      if(divCounter == 11)
        i = tracks.length;
    }
   // console.log(playListStack);
   if(callback)
      callback(widget);
  });
}

/*
Login a new user.  Send their info to server
via ajax post. If successful we get back their
info including their previous playlist in json.
*/     
function login (usr, pwd) {
          
  //check user info;
  var data = {
    userName: usr,
    Pass: pwd,
    type: 1, // 1: login
  };

  $.ajax({
    type: "POST",
    data: data,
    url: 'getData.php',
    datatype: 'json',
    success: function(res) {
      if(res == "error")
        $('#errorText').text('Error Logging In');
      else{
        startup(res);//get playlist from DB and start it
      }
    }
  });        
};

/*
Sign up a new user.  Send their info to server
via ajax post. If successful we get back their
user name and genre to start the music player
with.
*/
function signup (usr, email, pwd, genre, zip) {
          
  //check user info;
  var data = {
    userName: usr,
    Pass: pwd,
    Email: email,
    Genre: genre, 
    Zip: zip,
    type: 2 // 2: signup
  };
          
  $.ajax({
    type: "POST",
    data: data,
    url: 'getData.php',
    datatype: 'json',
    success: function(res) { // res is {username, genre}
      if(res == "error")
        $('#errorText').text('Error Logging In');
      else
        startup(res); // start player
      }
  });        
};

var update_DB_playlist = function(){
    //send post to DB to update playlist
      //check user info;
  var data = {
    userName: $('#userName').text(),
    tracks: playListStack,
    type: 3 // 3: update playlist
  };
          
  $.ajax({
    type: "POST",
    data: data,
    url: 'getData.php',
  }); 
}

/*
Called after a successful login or signup.
Set up the main window by hiding and showing 
specific divs.  Call search function to get 
the player started. 

TODO: add function or call to update the playlist
*/
function startup(response){
            
  response = JSON.parse(response);
  $('#mainContent').show();
  $('#navBarLogin').hide(); 
  $('#searchButton').show();
  $('#loginButtons').hide();
  $('#login').modal('hide');
  $('#signup').modal('hide');
  $('#userName').text(" "+response.user);
  //SC API Auth
  init();

  if(response.track1 == null){
    //fill playlist based on genre
   // console.log("got null track");
    search(response.genre, true, playNext);
  }
}

//on click function for album art boxes
// push new song to stack
function pushPlayList(track, callback){
  //push to stack
  //update playlist in db
  playListStack.push(track);
  if(callback){
    callback();
  }
}

/*
Pop the next track off the stack and play it
*/
var playNext = function(widget){
  size = playListStack.length-1;
  if(playListStack[size-1] != "Null"){
    widget.load(playListStack.pop().uri+"&auto_play=true");
    updateSideBar();
  }
}

function updateSideBar(){
 // console.log(playListStack);
  count = 1;
  //update playlist titles
  for(i = playListStack.length; i > 0; i--){
      $("#playlist"+count).text(playListStack[i-1].title);
    count++;
  }
  //update empty playlist spots
  for(i = playListStack.length; i < 11; i++){
      $("#playlist"+i).text("Empty! Add more songs");
  }
}

