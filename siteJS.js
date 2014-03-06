var widget = null;

//store playlists in stack
var playListStack = [];

//local storage for non private
// user info
var userAcctInfo;

$(document).ready(function() {

    //check if user is logged in already
    $(function() {

      $.ajax({
        type: "POST",
        data: {type: 5}, //check session 
        url: 'getData.php',
        datatype: 'json',
        success: function(res) {
          if(res != 0){
            console.log("found")
            startup(res);
          }
        }
      });
    });

    jQuery.validator.addClassRules({
       name: {
        required: true,
        minlength: 5
      },
      zip: {
        required: true,
        digits: true,
        minlength: 5,
        maxlength: 5
      },
      Pass: {
        required: true,
        minlength: 8,
        maxlength: 25
      }
    });

    $("#signupForm").validate();
    $("#loginForm").validate();
    $("#userInfoForm").validate();

    $('[data-toggle=offcanvas]').click(function () {
      $('.row-offcanvas').toggleClass('active')
    });

  $(function() {

    var iframe = document.querySelector('#soundcloud_player');
    widget = SC.Widget(iframe);        
    widget.bind(SC.Widget.Events.READY, function() {
      //start the player
      playNext(widget, true);
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

    $("#userName").on('click', function(res){
      $('#userInfo').modal('show');
      data = JSON.parse(userAcctInfo);
      $('#UDname').val(data.user);
      $('#UDemail').val(data.email);
      $('#UDzip').val(data.location);
      $('#UDgenre').val(data.genre);
    });

    $("#userInfoForm").submit(function(e){
      e.preventDefault();
      if (!$("#userInfoForm").valid()) return;
      updateInfo(
        $("#UDemail").val(),
        $("#UDgenre").val(),
        $("#UDzip").val());
      $("#userInfo").modal('hide');
    });

    $("#signupForm").submit(function(e){
      e.preventDefault();
      console.log("got signup");
    });

    $("#loginForm").submit(function(e){
      e.preventDefault();
    });

    $("#searchForm").submit(function(e){
      e.preventDefault(); 
      search( $("#searchForm :input").val());
    });

    $("#logout").on("click", function(){
      $.ajax({
        type: "POST",
        data: {type: 6}, //check session 
        url: 'getData.php'
      });
      widget.pause();
      setTimeout(function(){
        shutdown();}, 1000);
    });

  });
});

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
  $('#loginSpinner').show();
  console.log(CryptoJS.SHA3(pwd,{outputLength: 224}).toString());
  setTimeout(function(){
  //check user info;
  var data = {
    userName: usr,
    Pass: CryptoJS.SHA3(pwd,{outputLength: 224}).toString(),
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
        userAcctInfo = res;
      }
    }
  }); 
  $('#loginSpinner').hide();       
}, 1000);
};


/*
Sign up a new user.  Send their info to server
via ajax post. If successful we get back their
user name and genre to start the music player
with.
*/
function signup (usr, email, pwd, genre, zip) {
   if (!$("#signupForm").valid()) return;       
  //check user info;
  var data = {
    userName: usr,
    Pass: CryptoJS.SHA3(pwd,{outputLength: 224}).toString(),
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
      if(res == "error 1")
        $('#errorTextSignup').text('Username is already taken');
      else if(res == "error 2")
        $('#errorTextSignup').text('Email is already in use');
      else
        startup(res); // start player
        userAcctInfo = res;
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
  $('#searchButton').show();
  $('#loginButtons').hide();
  $('#login').modal('hide');
  $('#signup').modal('hide');
  $('#userName').show();
  $('#userName').text(" "+response.user);
  $('#controlButtons').show();
  //SC API Auth
  init();

  if(response.track1 == null){
    //fill playlist based on genre
   // console.log("got null track");
    search(response.genre, true, playNext);
  }
}

function updateInfo(email, genre, loc){
  var data = {
    Email: email,
    Genre: genre, 
    Zip: loc,
    type: 7 // 7: update DB
  };     
  console.log("sent to db "+data);
  //check user info;        
  $.ajax({
    type: "POST",
    data: data,
    url: 'getData.php',
    datatype: 'json',
    success: function(res){
      console.log("DB updated")
    }
  });        
};

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
var playNext = function(widget, pause){
  size = playListStack.length-1;
  if(playListStack[size-1] != "Null"){
    widget.load(playListStack.pop().uri+"&auto_play=true");
    updateSideBar();
  }
      if(pause){
      console.log("this should be paused now");
      widget.pause();
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

function shutdown(){
  //clean up the page to bring it back
  // to the login screen
  $('#mainContent').hide();
  $('#searchButton').hide();
  $('#loginButtons').show();
  $('#userName').hide();
  $('#controlButtons').hide();
  userAcctInfo = null;
  playListStack = null;
  setTimeout(function(){
    location.reload(true), 2000});

}