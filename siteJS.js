var widget = null;

//store playlists in stack
var playListStack = new Array();

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
            startup(res);
            userAcctInfo = res;
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
        minlength: 4,
        maxlength: 25
      }
    });

    //start jQuery validator
    $("#signupForm").validate();
    $("#loginForm").validate();
    $("#userInfoForm").validate();

    // toggle for side bar
    $('[data-toggle=offcanvas]').click(function () {
      $('.row-offcanvas').toggleClass('active')
    });

    //player and event handlers
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

    });               

    $("#forwarButton").on("click", function(){
      playNext(widget);
    });

    $("#pauseButton").on("click", function(){
      widget.pause();
    });

    // user clicked on an album image, get the data stored
    // in the div and push it to the stack
    $("[id^='div']").on('click', function(){
      data = $(this).data();
      pushPlayList(data.track, data.title.replace(/["']/g, ""), update_DB_playlist);
      updateSideBar();
    });

    $("[id^='playlist']").on('click', function(){
      data = $(this).data();
      widget.load(data.track+"&auto_play=true");
    });

    // open user info modal
    $("#userName").on('click', function(res){
      $('#userInfo').modal('show');
      data = JSON.parse(userAcctInfo);
      $('#UDname').val(data.user);
      $('#UDemail').val(data.email);
      $('#UDzip').val(data.location);
      $('#UDgenre').val(data.genre);
    });

    // user info modal submit button
    // send updated info to DB
    $("#userInfoForm").submit(function(e){
      e.preventDefault();
      if (!$("#userInfoForm").valid()) return; //form is not valid
      updateInfo(
        $("#UDemail").val(),
        $("#UDgenre").val(),
        $("#UDzip").val());
      $("#userInfo").modal('hide');
    });

    $("#signupForm").submit(function(e){
      e.preventDefault();
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
      // wait for server to clear session data
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
        $('#div'+divCounter).data({ track: tracks[i].uri, title: tracks[i].title});
        if(overwrite) 
          playListStack.unshift({ track: tracks[i].uri, title: tracks[i].title.replace(/["']/g, "")});
        divCounter++;
      }

      //get out of loop       
      if(divCounter == 11)
        i = tracks.length;
    }
   if(callback)
      callback(widget);
  });
}

/*
Login a new user.  Send their info to server
via ajax post. If successful we get back their
info including their previous playlist  as text
then convert to json.
*/     
function login (usr, pwd) {
  $('#loginSpinner').show();
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
      if(res == "error" ||  $.isEmptyObject(res) == 1)
        $('#errorText').text('Error Logging In');
      else{
        startup(res);//get playlist from DB and start it
        userAcctInfo = res;
      }
    }
  }); 
  $('#loginSpinner').hide();       
}, 500);
};


/*
Sign up a new user.  Send their info to server
via ajax post. If successful we get back their
user name and genre to get them started with 
a fresh playlist.
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

/*
Send the updated playlist back to the DB as
a text string.
*/
function update_DB_playlist(){
    //send post to DB to update playlist
      //check user info;
  var data = {
    playlist: JSON.stringify(playListStack),
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
*/
function startup(response){
            
  response = JSON.parse(response);
  $('#frontpage').hide();
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

  if(response.playlist == null){
    //fill playlist based on genre
    search(response.genre, true, playNext);
    console.log("in null playlist");
  }
  else{
    playListStack = $.parseJSON(response.playlist);
    console.log("parse playlist " + playListStack.track);
    search(response.genre, false, playNext);
  }
}

/*
If the user updated their info through the user info
modal we send the updated data to the DB
*/
function updateInfo(email, genre, loc){
  var data = {
    Email: email,
    Genre: genre, 
    Zip: loc,
    type: 7 // 7: update DB
  };     
  //check user info;        
  $.ajax({
    type: "POST",
    data: data,
    url: 'getData.php',
    datatype: 'json',
    success: function(res){
     // console.log("DB updated")
    }
  });        
};

/*
Called when a user click on album art.  Push
the track data to the stack
*/
function pushPlayList(uri, text,  callback){
  //push to stack
  //update playlist in db
  playListStack.push({ track: uri, title: text});
  if(callback){
    callback();
  }
}

/*
Pop the next track off the stack and play it
*/
var playNext = function(widget, pause){
  size = playListStack.length-1;
  if(playListStack[size-1] != null){
    widget.load(playListStack.pop().track+"&auto_play=true");
    updateSideBar();
  }
      if(pause){
        widget.pause();
      }
    //send updated playlist to server
    update_DB_playlist();
}

/*
Update the playlist sidebar
*/
function updateSideBar(){
  count = 1;
  //update playlist titles
  for(i = playListStack.length; i > 0; i--){
      $("#playlist"+count).text(playListStack[i-1].title);
      Playtrack = playListStack[i-1].track;
      Playtitle =playListStack[i-1].title;
      $("#playlist"+count).data({ track: Playtrack, title: Playtitle});
    count++;
  }
  //update empty playlist spots
  for(i = playListStack.length; i < 11; i++){
      $("#playlist"+i).text("Empty! Add more songs");
      $("#playlist"+i).data();
  }
}

/*
called after the shutdown request is sent to the server
this restores the page back to the login and stop the player
*/
function shutdown(){
  $('#mainContent').hide();
  $('#frontpage').show();
  $('#searchButton').hide();
  $('#loginButtons').show();
  $('#userName').hide();
  $('#controlButtons').hide();
  //clear saved info
  userAcctInfo = null;
  playListStack = null;
  setTimeout(function(){
    location.reload(true), 1000});
}