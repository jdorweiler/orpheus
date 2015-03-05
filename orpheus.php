<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.ico" >

    <title>Orpheus</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="offcanvas.css" rel="stylesheet">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="http://connect.soundcloud.com/sdk.js"></script>

  </head>

  <body>
    <div class="navbar navbar-fixed-top navbar-inverse navbar-" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle Playlist</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><i class="fa fa-signal fa-fw"></i> Orpheus Music Project</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav" id="controlButtons" style="display: none;">
            <li><i class="fa fa-pause fa-fw playerButtons" id="pauseButton" ></i></li>
            <li><i class="fa fa-play fa-fw playerButtons" id="playButton"></i></li>
            <li><i class="fa fa-step-forward fa-fw playerButtons" id="forwarButton"></i></li>
          </ul>

      <div class="navbar-form navbar-right" id="searchButton" style="display: none;">
        <form class="navbar-search" id="searchForm">
        <input type="text" placeholder="search" class="form-control searchbox search-query" id="searchText">
                <a class="btn btn-primary" href="#" id="userName"><i class="fa fa-user uesrIcon"></i></a>
        <a class="btn btn-primary" href="#" id="logout"><i class="fa fa-times-circle userIcon">Logout</i></a> 
        </form>
    </div>

        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </div><!-- /.navbar -->

    <div class="container">
      <div id="frontpage">
      <div class="jumbotron blank hidden-xs">
      </div>
      <div class="jumbotron frontpage">
    <div class="row" >
      <div class="col-3 col-sm-12 col-lg-12">
      <h3 class="startMessage" id="startMessage">Orpheus Music Project is a browser based music player and playlist.
        Search for songs and genres, add songs to a playlist, and everything is saved when you 
        login again.</h3>
    </div>
  </div>
  <div class="row" >
      <div class="loginButtons" id="loginButtons">
        <div class="col-2 col-sm-4 col-lg-4">
          </div>
          <div class="col-2 col-sm-2 col-lg-2">
        <a data-toggle="modal" href="#signup" class="btn btn-success btn-lg">Sign Up</a>
      </div>
      <div class="col-1 col-sm-1 col-lg-1">
          <a data-toggle="modal" href="#login" class="btn btn-success btn-lg" >Login</a>
      </div>
    </div>
  </div>
</div>
</div>

      <div class="row row-offcanvas row-offcanvas-right" id="mainContent" style="display: none;">

        <div class="col-xs-12 col-sm-9">
          <p class="pull-right visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle Playlist</button>
          </p>
          <div class="jumbotron">
            <div id='player'>
              <iframe class="soundcloud_player" id='soundcloud_player' src="https://w.soundcloud.com/player/?url=http://api.soundcloud.com/tracks/34508873"></iframe>
            </div> 
          </div>
        <div class="albumMenu" id="albumMenu">
          <div class="row"><h3 class="albumheader">Click to add to your playlist</h3>
            <div class="col-6 col-sm-4 col-lg-4" id="div1" >
              <img class="art" id="track1-img" src="#" alt="album1art" >
              <p class="albumTitles" id="track1-title">/<p>
            </div><!--/span-->
            <div class="col-6 col-sm-4 col-lg-4" id="div2" >
              <img class="art" id="track2-img" src="#" alt="album2art">
              <p class="albumTitles" id="track2-title">/<p>
            </div><!--/span-->
            <div class="col-6 col-sm-4 col-lg-4" id="div3" >
              <img class="art" id="track3-img" src="#" alt="album3art">
              <p class="albumTitles" id="track3-title">/<p>
            </div><!--/span-->
          </div> <!-- /row -->
          <div class="row" >
            <div class="col-6 col-sm-4 col-lg-4" id="div4">
              <img class="art" id="track4-img" src="#" alt="album4art">
              <p class="albumTitles" id="track4-title">/<p>
            </div><!--/span-->
            <div class="col-6 col-sm-4 col-lg-4" id="div5">
              <img class="art" id="track5-img" src="#" alt="album5art">
              <p class="albumTitles" id="track5-title">/<p>
            </div><!--/span-->
            <div class="col-6 col-sm-4 col-lg-4" id="div6">
              <img  class="art" id="track6-img" src="#" alt="album6art">
              <p class="albumTitles" id="track6-title">/<p>
            </div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->
      </div>

        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
          <h3> Playlist </h3>
            <div class="list-group">
            <a href="#" class="list-group-item active" id="playlist1">Link</a>
            <a href="#" class="list-group-item" id="playlist2" >Link</a>
            <a href="#" class="list-group-item" id="playlist3" >Link</a>
            <a href="#" class="list-group-item" id="playlist4" >Link</a>
            <a href="#" class="list-group-item" id="playlist5" >Link</a>
            <a href="#" class="list-group-item" id="playlist6" >Link</a>
            <a href="#" class="list-group-item" id="playlist7" >Link</a>
            <a href="#" class="list-group-item" id="playlist8" >Link</a>
            <a href="#" class="list-group-item" id="playlist9">Link</a>
            <a href="#" class="list-group-item" id="playlist10">Link</a>
          </div>
        </div><!--/span-->
      </div><!--/row-->
    </div><!--/.container-->
 <!-- Button trigger modal -->

<form id="signupForm">
  <!-- Modal -->
  <div class="modal fade" id="signup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h3 class="modal-title">Sign Up</h3>
          <div class="error" id="errorTextSignup"></div>
        </div>
        <div class="modal-body form-horizontal">
          <div class="divDialogElements form-group">
            <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control required name" id="name" name="name" type="text" placeholder="User Name (min 5 characters)"/>
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
            <input class="form-control required email" id="email" name="UserEmail" type="text" placeholder="E-mail"/>
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
            <input class="form-control required Pass" id="Pass" name="Pass" type="password" placeholder="password (min 5 characters)"/>
          </div>
            <h3>Just two things to help us find you great music</h3>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-music fa-fw"></i></span>
            <input class="form-control required text" id="genre" name="genre" type="text" placeholder="Favorite Genre or Artist"/>
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
            <input class="form-control required zip" id="zip" name="zip" type="text" placeholder="Zip Code"/>
          </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-success" id="signupModalButton" onclick="signup(
              document.getElementById('name').value, document.getElementById('email').value,
              document.getElementById('Pass').value, document.getElementById('genre').value,
              document.getElementById('zip').value);">
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
<!-- /.modal -->
</form>

<form id="loginForm">
   <!-- Modal -->
  <div class="modal fade" id="login" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Sign in</h4>
          <div id="loginSpinner" style="display: none;">
          <h4 > Logging in <i class="fa fa-cog fa-spin"></i></h4>
        </div>
          <div class="error" id="errorText"></div>
        </div>
        <div class="modal-body">
           <div class="divDialogElements">
            <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control required name" id="UserName" name="UserName" type="text" placeholder="User Name"/>
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
            <input class="form-control required Pass" id="PWD" name="PWD" type="password" placeholder="Password"/>
          </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" id="loginModalButton" class="btn btn-success" onclick="login (
              document.getElementById('UserName').value, document.getElementById('PWD').value);">
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</form>

<form id="userInfoForm">
    <!-- Modal -->
  <div class="modal fade" id="userInfo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h3 class="modal-title">Update your user info</h3>
        </div> 
        <div class="modal-body form-horizontal">
          <div class="divDialogElements form-group">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
            <input class="form-control required email" id="UDemail" name="UserEmail" type="text" placeholder="E-mail"/>
          </div>
            <h3>Just two things to help us find you great music</h3>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-music fa-fw"></i></span>
            <input class="form-control required text" id="UDgenre" name="genre" type="text" placeholder="Favorite Genre or Artist"/>
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
            <input class="form-control required zip" id="UDzip" name="zip" type="text" placeholder="Zip Code"/>
          </div>
          <div class="modal-content subscribed_playlists">
          </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-success">
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  <!-- /.modal -->
</form>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://w.soundcloud.com/player/api.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="siteJS.js"></script>
    <script src="jquery.validate.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha3.js"></script>
    </body>
</html>
