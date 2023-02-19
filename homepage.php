<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');
include('./classes/Notify.php');

ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);

$username = "";
$verified = False;
$isFollowing = False;

if (isset($_COOKIE['SNID'])) {
    //$token = $_COOKIE['SNID'];
    //echo '{ "Success": "Existing cookies!" }'; //added echo for debugging purposes
    http_response_code(200); //added success response
} else {
    echo '{ "Error": "Please Log in first!" }'; //added response
    http_response_code(409); //added error response
    header('location:login.html'); //added redirect to login.html
    exit(); //addded exit
}

$token = $_COOKIE['SNID'];
$authorities = DB::query('SELECT * FROM users WHERE verified = 1');
$userid = DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
//echo '{ "ID": "'.$userid.'" }';//added for debugging purposes
$username = DB::query('SELECT users.`username` FROM users WHERE users.id = :userid;', array(':userid'=>$userid))[0]['username'];
//echo '{ "Username": "'.$username.'" }';//added for debugging purposes
$verified = DB::query('SELECT users.verified FROM users WHERE username=:username;', array(':username'=>$username))[0]['verified'];

if (isset($_GET['username'])) {
        if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {

                $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
                $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
                //echo '{ "verified": "'.$verified.'" }';//added for debugging purposes
                $followerid = Login::isLoggedIn();
                
                if (isset($_POST['follow'])) {

                        if ($userid != $followerid) {

                                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        if ($followerid == 6) {
                                                DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                                        }
                                        DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
                                } else {
                                        echo 'Already following!';
                                }
                                $isFollowing = True;
                        }
                }
                if (isset($_POST['unfollow'])) {

                        if ($userid != $followerid) {

                                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        if ($followerid == 6) {
                                                DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
                                        }
                                        DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
                                }
                                $isFollowing = False;
                        }
                }
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                        //echo 'Already following!';
                        $isFollowing = True;
                }

                if (isset($_POST['deletepost'])) {
                        if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                                DB::query('DELETE FROM posts WHERE id=:postid and user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
                                DB::query('DELETE FROM post_dislikes WHERE post_id=:postid', array(':postid'=>$_GET['postid'])); //added delete for dislikes
                                echo 'Post deleted!';
                        }
                }

                if (isset($_POST['post']) ) {
                        if ($_FILES['postimg']['size'] == 0) {
                                Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid, $_POST['location_link'], $_POST['location']);
                        } else {
                                $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid, $_POST['location_link'], $_POST['location']);
                                Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
                                Post::notifAll($_POST['postbody'], Login::isLoggedIn(), $userid);
                        }
                }

                if (isset($_GET['postid']) && !isset($_POST['deletepost'])) {
                        Post::likePost($_GET['postid'], $followerid);
                        Post::dislikePost($_GET['postid'], $followerid);//added dislikepost
                }

               

        } else {
                die('User not found!');
        }
        
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fdc405"/>
    <title>Safety Pin | Homepage</title>
    <meta
      name="description"           
      content="This is the home page where users can see all posts all users.">
    <link rel="shortcut icon" type="image/png" href="HOMEICON/48.png"/>
    <link rel="apple-touch-icon" href="HOMEICON/192.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="assets/css/Highlight-Clean.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean1.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/untitled.css">
    <link rel="manifest" href="/manifest.json">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLDKS-bf23Ci0qVDKI0Vpl-KznERj62jc"></script>
    <style type="text/css">
    /* width */
    ::-webkit-scrollbar {
      width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      background: #f1f1f1; 
    }
     
    /* Handle */
    ::-webkit-scrollbar-thumb {
      background: #888; 
      border-radius: 50px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
    .comment_btn {
        border: none;
        background: none;
        color: lightgray;
        font-size: 13px;
        margin: 8px 0px 0px 15px;
    }

    .comment_btn:hover {
        color: gray;
    }

    .postimg {
        opacity: 0;
        transition: all 3s ease-out;
        width: 100%;
        height: 100%;
    }

    #img-holder > img {
        width:25%;
    }
    
    #loading_label{
        display: none;
        color: white;
        background-color: lightgray;
        padding: 6px 12px;
        margin-bottom: 0;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 4px;
    }
    
    img[src="https://cdn.000webhost.com/000webhost/logo/footer-powered-by-000webhost-white2.png"]{display:none;}
    </style>
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async></script> 
        <script>
            var OneSignal = window.OneSignal || [];
            OneSignal.push(["init", {
                appId: "bf35c756-d339-4516-8616-68ac6c050407",
            }]);
        </script>
    
</head>

<body onload="animatedCursor();">
    <header class="hidden-sm hidden-md hidden-lg">
            <div class="navbar-header" style="display: inline"><a class="navbar-brand navbar-link" href="#"><img src="assets/img/icon-logo.png" alt="Safety Pin" width="30px" height="40px" style="margin-top: 7px"></i></a><!--added icon-->
                    <form class="navbar-form navbar-left" style="display: inline; float: left">
                        <div class="searchbox" style="margin-top: 10px"><i class="glyphicon glyphicon-search"></i>
                            <input id="searchbox" class="form-control sbox" type="text" style="width: 105%"><label for="searchbox" style="color:transparent; font-size:0px; padding:0px; margin:0px">Search</label>
                            <ul class="list-group autocomplete" style="position:absolute;width:175%; z-index:100">
                            </ul>
                        </div>
                    </form>
                    <ul style="float: right; display: inline; margin-left: -50px; margin-top: 5px">
                        <li role="presentation" class="img-mob-active" style="display: inline"> <!--put indicator na active to sa js-->
                            <a href="homepage.php" style="display: inline; padding: 8px 5px"><img class="iconh" src="assets/img/homeicon.png" alt="home" width="40px" height="40px"></a><!--added HOME img-->
                        </li>
                        <!--removed li containing message-->
                        <li role="presentation" class="img-mob" style="display: inline">
                            <a href="emergency-services.php" style="display: inline; padding: 8px 5px"><img class="iconh" src="assets/img/esicon.png" alt="e-service" width="40px" height="40px"></a><!--added ES img-->
                        </li>
                        <li role="presentation" class="img-mob" style="display: inline">
                            <a href="notify.php" style="display: inline; padding: 8px 5px"><img class="iconh" src="assets/img/notifsicon.png" alt="notifs" width="40px" height="40px"></a><!--added NOTIFS img-->
                        </li>
                        <li class="dropdown" style="display: inline">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">
                                <img src="assets/img/usersicon.png" alt="users" width="40px" height="40px"><!--added USER img-->
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="profile.php">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="my-account.php">My Account</a></li>
                                <li role="presentation"><a href="logout.php">Logout </a></li>
                            </ul>
                        </li>
                        </ul>
            </div>
    </header>
    <div>
        <nav class="navbar navbar-default hidden-xs navigation-clean">
            <div class="container">
                <div class="navbar-header"><a class="navbar-brand navbar-link" data-toggle="tooltip" title="Safety Pin Logo"><img src="assets/img/logo.png" alt="Safety Pin" width="180px" height="44px"/></a><!--added icon-->
                    <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
                </div>
                <div class="collapse navbar-collapse" id="navcol-1">
                    <form class="navbar-form navbar-left">
                        <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                            <input id="searchbox" class="form-control sbox" type="text"><label for="searchbox" style="color:transparent; font-size:0px; padding:0px; margin:0px">Search</label>
                            <ul class="list-group autocomplete" style="position:absolute;width:100%; z-index:100">
                            </ul>
                        </div>
                    </form>
                    <ul class="nav navbar-nav hidden-md hidden-lg navbar-right">
                        <li role="presentation" class="img-mob-active"> <!--put indicator na active to sa js-->
                            <a href="homepage.php"><img class="iconh" src="assets/img/homeicon.png" alt="home" width="40px" height="40px"></a><!--added HOME img-->
                        </li>
                        <!--removed li containing message-->
                        <li role="presentation" class="img-mob">
                            <a href="emergency-services.php"><img class="iconh" src="assets/img/esicon.png" alt="e-service" width="40px" height="40px"></a><!--added ES img-->
                        </li>
                        <li role="presentation" class="img-mob">
                            <a href="notify.php"><img class="iconh" src="assets/img/notifsicon.png" alt="notifs" width="40px" height="40px"></a><!--added NOTIFS img-->
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">
                                <img src="assets/img/usersicon.png" alt="users" width="40px" height="40px"><!--added USER img-->
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="profile.php">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="my-account.php">My Account</a></li>
                                <li role="presentation"><a href="logout.php">Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav hidden-xs hidden-sm navbar-right">
                        <li role="presentation" class="img-manip-active"> <!--put indicator na active to sa js-->
                            <a href="homepage.php" data-toggle="tooltip" title="Homepage"><img class="iconh" src="assets/img/homeicon.png" alt="home" width="40px" height="40px"></a><!--added HOME img-->
                        </li>
                        <!--removed li containing message-->
                        <li role="presentation" class="img-manip">
                            <a href="emergency-services.php" data-toggle="tooltip" title="Emergency Services"><img class="iconh" src="assets/img/esicon.png" alt="e-service" width="40px" height="40px"></a><!--added ES img-->
                        </li>
                        <li role="presentation" class="img-manip">
                            <a href="notify.php" data-toggle="tooltip" title="Notification"><img class="iconh" src="assets/img/notifsicon.png" alt="notifs" width="40px" height="40px"></a><!--added NOTIFS img-->
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">
                                <img src="assets/img/usersicon.png" alt="users" width="40px" height="40px"><!--added USER img-->
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="profile.php">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="my-account.php">My Account</a></li>
                                <li role="presentation"><a href="logout.php">Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                </div>
            </div>
        </nav>
    
    <div>
        <div class="container">
            <h1>Timeline</h1>

            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item" style="margin-bottom:30px; border-radius:10px; padding-bottom:0px;"><span><strong>Safety Pin:</strong> Emergency Web App</span>
                            <p><br>Haven't installed it yet? Install it right now by clicking the button below!<br><br><center><button id="installbtn" class="btn btn-default" style="width:100%; padding:10px;">Install Safety Pin</button></center></p>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item" style="margin-bottom:30px; border-radius:10px; padding-bottom:0px;"> <!--added post at the top of timeline-->
                                <form action="homepage.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data" id="my-form">
                                        <textarea id="postarea" name="postbody" onkeyup="textAreaAdjust(this)" style="overflow:hidden; width:100%; resize:none; margin-bottom:10px; border-color:#e1d4bf;" maxlength="160"></textarea><label for="postarea" style="color:transparent; font-size:0px; padding:0px; margin:0px">PostArea</label><!--added style and js-->
                                        <br /><input type="text" id="Location_Link" name="location_link" style="display: none"/><input type="text" id="Location" name="location" style="display: none"/>

                                    <div id="wrapper"><!--added wrapper for upload imgs-->
                                        <div id="img-holder"></div>
                                    </div>

                                    <div class="modal-footer" style="padding-top:5px; padding-bottom: 0px;"> <!--added style-->        
                                    <!--added img/vid upload-->
                                        <div style="box-sizing:border-box; text-align:center;"> <!--added css to make 2 columns-->
                                            <div style="float:left; width:50%;" data-toggle="tooltip" title="Add Image">
                                                <label><img src="assets/img/insertIMG.png" alt="insert image" width="42px" height="32px"/>
                                                <input id="img-input" style="display:none;" type="file" name="postimg" accept="image/png, image/gif, image/jpeg" multiple/>
                                                </label>
                                            </div>
                                            <div style="float:left; width:50%;" data-toggle="tooltip" title="Tag Authorities">
                                                <label><img src="assets/img/tag.png" alt="tag authorities" width="50px" height="34px" onClick="$('#tagmodal').modal('show');" style="filter: invert(92%) sepia(19%) saturate(295%) hue-rotate(343deg) brightness(96%) contrast(82%);"/>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer" style="padding-top:5px; padding-bottom: 5px;">
                                        <input type="submit" name="post" id="submitThis" style="display: none">
                                        <label for="submitThis" id="post_button" class="btn btn-default" onclick="disFunction()" style="display: none">Post</label>
                                        <label id="loading_label">Posting...</label>
                                    </div>

                                </form>
                        </li>   
                        <div class="timelineposts">

                        </div>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
    <div class="modal fade" id="commentsmodal" role="dialog" tabindex="-1" style="padding-top:130px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></a>
                    <h4 class="modal-title">Comment/s</h4></div>
                <div class="modal-body" style="max-height: 350px; overflow-y:auto;">
                    <p>COMMENT SECTION</p>
                </div>
                <div class="cmf" style="padding: 12px 10px 7px 10px; border-top: 1px solid #e5e5e5;"> 
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="tagmodal" role="dialog" tabindex="-1" style="padding-top:180px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></a>
                    <h4 class="modal-title">Tag Authorities</h4></div>
                    <?php 
                    foreach($authorities as $a){
                        echo '<div style="max-height: 350px; overflow-y:auto; position:relative; padding:10px; border-bottom: 1px solid #E5E5E5;">
                        <input type="checkbox" name="authority" value="@'.$a['username'].'">
                        <a href="/profile.php?username='.$a['username'].'" target="_blank" rel="noopener noreferrer"><label for="" style="font-weight:normal; font-size: 16px">'.$a['fullname'].' <i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip" title="Verified User" style="font-size:16px;color:#e61c5d;"></i></label></a>
                        </div>';
                    }
                    ?>
                        <div class="modal-footer" style="padding-top:5px; padding-bottom: 5px; border-top: none;">
                            <input type="button" name="add" id="addAuth" value="Add" class="btn btn-default" onClick="addTag();">
                        </div>
            </div>
        </div>
    </div>

    <!--<div class="footer-dark">
        <footer>
            <div class="container">
                <p class="copyright">Social Network© 2016</p>
            </div>
        </footer>
    </div>-->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script src="animatedCursor.js"></script>
    
    <script>
        function disFunction(){
          var x = document.getElementById("post_button");
          var y = document.getElementById("loading_label");
            x.style.display = "none";
            y.style.display = "inline-block";
        }
    </script>
    
    <script>
     if ('serviceWorker' in navigator) { //service-worker for add to home screen
        console.log("Will the service worker register?");
        navigator.serviceWorker.register('OneSignalSDKWorker.js')
        
          .then(function(reg){
            console.log("Yes, it did.");
       
         }).catch(function(err) {
            console.log("No it didn't. This happened:", err)
        });
     }
    </script>
    
     <script>
        function addTag() {
            if (!$.trim($("#postarea").val())){
              $("input[name=authority]").each(function() {
                if (this.checked) {
                  let old_text = $('#postarea').val() ? $('#postarea').val() + ' ' : '';
                  $('#postarea').text(old_text + $(this).val());
                }
              })
              $('#tagmodal').modal('hide');
            }
            else{
              $("input[name=authority]").each(function() {
                if (this.checked) {
                  let old_text = $('#postarea').val() ? $('#postarea').val() + ' '  : '';
                  $('#postarea').val(old_text + $(this).val());
                }
              })
              $('#tagmodal').modal('hide');    
            }
        }
    </script>
    
    <script>
        OneSignal.push(function() {
            /* These examples are all valid */
           var isPushSupported = OneSignal.isPushNotificationsSupported();
           if (isPushSupported){
               console.log('supported');
               OneSignal.getUserId(function(isEnabled){
                   if (isEnabled){
                       console.log("Push notifications are enabled!");
                       OneSignal.getUserId(function(userId){
                           console.log("OneSignal User ID:",  userId);
                       });
                   }
                   else{
                       console.log("Push notifications are not enabled yet.");
                       OneSignal.push(function(){
                           OneSignal.showHttpPrompt();
                       });
                   }
               });
            } else {
                console.log("Push notifications are not supported.");
              }
        });
    </script>
    
    <script type="text/javascript"> //JAVASCRIPT FOR OTHER FUNCTIONS
        //added if (page is redirected by forward/back navigation in browser) will reload the page
        var perfEntries = performance.getEntriesByType("navigation");
        if (perfEntries[0].type === "back_forward") {
            location.reload(true);  
        }
        //end reload

/**
        //added to pop the login
        function showDialog() {    
            $('#poplog').load('login.html');
            $('#logshow').modal('show');
        }
        //end pop 
*/
        //added text area auto resize
        function textAreaAdjust(element) {
            element.style.height = "1px";
            element.style.height = (25+element.scrollHeight)+"px";
        }
        //end of text area auto resize

        //added file reader for img preview
        $("#img-input").on('change', function () {
            var countFiles = $(this)[0].files.length; //Get count of selected files
            var imgPath = $(this)[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            var image_holder = $("#img-holder");
            image_holder.empty();

            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof (FileReader) != "undefined") {

                    for (var i = 0; i < countFiles; i++) { //loop for each file selected for uploaded.
                        var reader = new FileReader();

                        reader.onload = function (e) {
                            $("<img />", {
                            "src": e.target.result,
                            "class": "thumb-image"
                            }).appendTo(image_holder);
                        }

                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[i]);
                    }

                } else {
                    alert("This browser does not support FileReader.");
                }
            } else {
                alert("Please select only images");
            }
        });
        //end of file reader
        
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Update UI notify the user they can install the PWA
        //showInstallPromotion();
        // Optionally, send analytics event that PWA install promo was shown.
        console.log(`'beforeinstallprompt' event was fired.`);
        });
        
        //button to popup
        var buttonInstall = document.getElementById("installbtn");
        buttonInstall.addEventListener('click', async () => {
    
        // Hide the app provided install promotion
        //hideInstallPromotion();
        // Show the install prompt
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        const { outcome } = await deferredPrompt.userChoice;
        // Optionally, send analytics event with outcome of user choice
        console.log(`User response to the install prompt: ${outcome}`);
        // We've used the prompt, and can't use it again, throw it away
        deferredPrompt = null;
        });
    </script>

    <script type="text/javascript">
    var start = 5;
    var working = false;
    $(window).scroll(function() {
            if ($(this).scrollTop() +1 >= $('body').height() - $(window).height()) {
                    if (working == false) {
                            working = true;

                            $.ajax({

                                    type: "GET",
                                    url: "api/posts?start="+start,
                                    processData: false,
                                    contentType: "application/json",
                                    data: '',
                                    success: function(r) {
                                            var posts = JSON.parse(r)
                                            $.each(posts, function(index) {

                                                    if (posts[index].PostImage == "" && posts[index].Verified == 1) { //added condition so that if post have img will post correctly
                                                        $('.timelineposts').html(
                                                            $('.timelineposts').html() + 
                                                            //added style to list-group-item
                                                            '<ul class="list-group"><li class="list-group-item" style="margin-top:5px; border-radius:10px;" id="'+posts[index].PostId+'"><p style="font-size:18px; margin-bottom:10px;"><i class="glyphicon glyphicon-map-marker" data-toggle="tooltip" title="Posted From" style="font-size:20px;color:#e61c5d;"></i>&nbsp<a href="'+posts[index].Location_Link+'" target="_blank" rel="noopener noreferrer">'+posts[index].Location+'</a></p><blockquote><p>'+posts[index].PostBody+'</p><footer>By<a href="profile.php?username='+posts[index].Username+'"> '+posts[index].PostedBy+' </a><i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip" title="Verified User" style="font-size:16px;color:#e61c5d;"></i> on '+posts[index].PostDate+'</footer></blockquote><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-up" style="color:'+posts[index].LColor+'"></i><span style="color:'+posts[index].LColor+'"> '+posts[index].Likes+' Upvote</span></button><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id1=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-down" style="color:'+posts[index].DColor+'"></i><span style="color:'+posts[index].DColor+'"> '+posts[index].Dislikes+' Downvote</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-comment" style="color:#fdc405;"></i><span style="color:#fdc405;">&nbsp'+posts[index].Commented+' Comment/s</span></button></li></ul>'
                                                            )
                                                    } else if (posts[index].Verified == 1) {
                                                            $('.timelineposts').html(
                                                                $('.timelineposts').html() +
                                                            '<ul class="list-group"><li class="list-group-item" style="margin-top:5px; border-radius:10px;" id=""><p style="font-size:18px; margin-bottom:10px;"><i class="glyphicon glyphicon-map-marker" data-toggle="tooltip" title="Posted From" style="font-size:20px;color:#e61c5d;"></i>&nbsp<a href="'+posts[index].Location_Link+'" target="_blank" rel="noopener noreferrer">'+posts[index].Location+'</a></p><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].postId+'" alt="post image"><footer>By <a href="profile.php?username='+posts[index].Username+'"> '+posts[index].PostedBy+'</a><i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip" title="Verified User" style="font-size:16px;color:#e61c5d;"></i> on '+posts[index].PostDate+'</footer></blockquote><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-up" style="color:'+posts[index].LColor+'"></i><span style="color:'+posts[index].LColor+'"> '+posts[index].Likes+' Upvote</span></button><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id1=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-down" style="color:'+posts[index].DColor+'"></i><span style="color:'+posts[index].DColor+'"> '+posts[index].Dislikes+' Downvote</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-comment" style="color:#fdc405;"></i><span style="color:#fdc405;">&nbsp'+posts[index].Commented+' Comment/s</span></button></li></ul>'
                                                            )
                                                    }   else {
                                                            $('.timelineposts').html(
                                                                $('.timelineposts').html() +
                                                            '<ul class="list-group"><li class="list-group-item" style="margin-top:5px; border-radius:10px;" id=""><p style="font-size:18px; margin-bottom:10px;"><i class="glyphicon glyphicon-map-marker" data-toggle="tooltip" title="Posted From" style="font-size:20px;color:#e61c5d;"></i>&nbsp<a href="'+posts[index].Location_Link+'" target="_blank" rel="noopener noreferrer">'+posts[index].Location+'</a></p><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].postId+'" alt="post image"><footer>By <a href="profile.php?username='+posts[index].Username+'"> '+posts[index].PostedBy+'</a> on '+posts[index].PostDate+'</footer></blockquote><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-up" style="color:'+posts[index].LColor+'"></i><span style="color:'+posts[index].LColor+'"> '+posts[index].Likes+' Upvote</span></button><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id1=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-down" style="color:'+posts[index].DColor+'"></i><span style="color:'+posts[index].DColor+'"> '+posts[index].Dislikes+' Downvote</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-comment" style="color:#fdc405;"></i><span style="color:#fdc405;">&nbsp'+posts[index].Commented+' Comment/s</span></button></li></ul>'
                                                            )
                                                    }

                                                    $('[data-postid]').click(function() {
                                                            var buttonid = $(this).attr('data-postid');
                                                            $.ajax({

                                                                    type: "GET",
                                                                    url: "api/comments?postid=" + $(this).attr('data-postid'),
                                                                    processData: false,
                                                                    contentType: "application/json",
                                                                    data: '',
                                                                    success: function(r) {
                                                                            var res = JSON.parse(r)
                                                                            showCommentsModal(res);
                                                                            $('[data-postid1im]').click(function() {
                                                                                    $.ajax({
                                                                                            type: "POST",
                                                                                            url: "api/commented?postid=" + buttonid,
                                                                                            processData: false,
                                                                                            contentType: "application/json",
                                                                                            data: '{ "commentbody": "'+ $("#commentbodyim").val() +'" }',
                                                                                            success: function(r)
                                                                                            {
                                                                                                    document.getElementById('commentbodyim').value = ""
                                                                                                    //$('#commentsmodal').modal('hide')
                                                                                                    $("[data-postid='"+buttonid+"']").trigger("click")
                                                                                                    var res = JSON.parse(r)
                                                                                                    console.log(res)
                                                                                                    $("[data-postid='"+buttonid+"']").html(' <i class="glyphicon glyphicon-comment" style="color:#fdc405"></i><span style="color:#fdc405;"> '+res.Commented+' Comment/s</span>')
                                                                                                    
                                                                                            },
                                                                                            error: function(r) {
                                                                                                    console.log(r)
                                                                                                    
                                                                                            }

                                                                                    });
                                                                            });
                                                                    },
                                                                    error: function(r) {
                                                                            console.log(r)
                                                                            var res = "";
                                                                            showCommentsModal(res);
                                                                            $('[data-postid1im]').click(function() {
                                                                                    $.ajax({
                                                                                            type: "POST",
                                                                                            url: "api/commented?postid=" + buttonid,
                                                                                            processData: false,
                                                                                            contentType: "application/json",
                                                                                            data: '{ "commentbody": "'+ $("#commentbodyim").val() +'" }',
                                                                                            success: function(r) {
                                                                                                    document.getElementById('commentbodyim').value = ""
                                                                                                    $("[data-postid='"+buttonid+"']").trigger("click")
                                                                                                    
                                                                                                    var res = JSON.parse(r)
                                                                                                    console.log(res)
                                                                                                    $("[data-postid='"+buttonid+"']").html(' <i class="glyphicon glyphicon-comment" style="color:#fdc405"></i><span style="color:#fdc405;"> '+res.Commented+' Comment/s</span>')

                                                                                            },
                                                                                            error: function(r) {
                                                                                                    console.log(r)
                                                                                                    
                                                                                            }

                                                                                    });
                                                                            });
                                                                    }

                                                            });
                                                    });

                                                    $('[data-id]').click(function() {
                                                            var buttonid = $(this).attr('data-id');

                                                            $('[data-id]').prop('disabled', true);
                                                            $('[data-id1]').prop('disabled', true);
                                                            setTimeout(function() {
                                                                $('[data-id]').prop('disabled', false);
                                                                $('[data-id1]').prop('disabled', false);
                                                            }, 1000);

                                                            $.ajax({

                                                                    type: "POST",
                                                                    url: "api/likes?id=" + $(this).attr('data-id'),
                                                                    processData: false,
                                                                    contentType: "application/json",
                                                                    data: '',
                                                                    success: function(r) {
                                                                            var res = JSON.parse(r)
                                                                            $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-circle-arrow-up" style="color:'+res.Color1+'"></i><span style="color:'+res.Color1+'"> '+res.Likes+' Upvote</span>')
                                                                            $("[data-id1='"+buttonid+"']").html(' <i class="glyphicon glyphicon-circle-arrow-down" style="color:'+res.Color2+'"></i><span style="color:'+res.Color2+'"> '+res.Dislikes+' Downvote</span>')
                                                                    },
                                                                    error: function(r) {
                                                                            console.log(r)
                                                                    }

                                                            });
                                                    })

                                                    $('[data-id1]').click(function() { //added for dislike
                                                            var buttonid = $(this).attr('data-id1');

                                                            $('[data-id]').prop('disabled', true);
                                                            $('[data-id1]').prop('disabled', true);
                                                            setTimeout(function() {
                                                                $('[data-id]').prop('disabled', false);
                                                                $('[data-id1]').prop('disabled', false);
                                                            }, 1000);
                                                            
                                                            $.ajax({

                                                                    type: "POST",
                                                                    url: "api/dislikes?id=" + $(this).attr('data-id1'),
                                                                    processData: false,
                                                                    contentType: "application/json",
                                                                    data: '',
                                                                    success: function(r) {
                                                                            var res = JSON.parse(r)
                                                                            $("[data-id1='"+buttonid+"']").html(' <i class="glyphicon glyphicon-circle-arrow-down" style="color:'+res.Color1+'"></i><span style="color:'+res.Color1+'"> '+res.Dislikes+' Downvote</span>')
                                                                            $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-circle-arrow-up" style="color:'+res.Color2+'"></i><span style="color:'+res.Color2+'"> '+res.Likes+' Upvote</span>')
                                                                    },
                                                                    error: function(r) {
                                                                            console.log(r)
                                                                    }

                                                            });
                                                    })

                                            })

                                            $('.postimg').each(function() {
                                                    this.src=$(this).attr('data-tempsrc')
                                                    this.onload = function() {
                                                            this.style.opacity = '1';
                                                    }
                                            })

                                            scrollToAnchor(location.hash)

                                            start+=5;
                                            setTimeout(function() {
                                                    working = false;
                                            }, 3000)

                                    },
                                    error: function(r) {
                                            console.log(r)
                                    }

                            });
                    }
            }
    })

    function scrollToAnchor(aid){
    try {
    var aTag = $(aid);
        $('html,body').animate({scrollTop: aTag.offset().top},'slow');
        } catch (error) {
                console.log(error)
        }
    }

        $(document).ready(function() {
            
            var x = document.getElementById("post_button");
            x.style.display = "none";
            
            $('#postarea').on('keyup',function() {
                if ($.trim($("#postarea").val())) {
                    x.style.display = "inline-block";
                } else {
                    x.style.display = "none";
                }
            });

            $('#postarea').on('focus',function() {
                    navigator.geolocation.getCurrentPosition(success, error);
            });
            
            function success(position) {
                document.getElementById("postarea").placeholder = "Tell everyone your emergency here...";
                const latitude  = position.coords.latitude;
                const longitude = position.coords.longitude;
                const geoLink = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;
                document.getElementById('Location_Link').value = geoLink;
                $.getJSON(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`, function(data) {
                    const myArray = data;
                    const location = myArray.address.city;
                    document.getElementById('Location').value = location;
                    console.log(location);
                    console.log(geoLink);
                    });
            }
            
            function error() {
                document.getElementById("postarea").placeholder = "Open and allow location to enable posting...";
                document.getElementById("postarea").disabled = true;
                document.getElementById("postarea").value = "";
                document.getElementById("img-input").disabled = true;
                var y = document.getElementById("addAuth");
                y.style.display = "none";
                x.style.display = "none";
            }
            
            if(!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                if (userID != followerID){
                    document.getElementById("postarea").placeholder = "Posting to other profile is not allowed...";
                    document.getElementById("postarea").disabled = true;
                    document.getElementById("img-input").disabled = true;
                    var y = document.getElementById("addAuth");
                    y.style.display = "none";
                    x.style.display = "none";
                }
                else{
                    document.getElementById("postarea").placeholder = "Open and allow location to enable posting...";
                    document.getElementById("postarea").disabled = true;
                    document.getElementById("img-input").disabled = true;
                    var y = document.getElementById("addAuth");
                    y.style.display = "none";
                    x.style.display = "none";
                }
            } else {
                navigator.geolocation.getCurrentPosition(success, error);
            }

                $('.sbox').keyup(function() {
                        var sbinput = $(this).val();
                        $('.autocomplete').html("")
                        $.ajax({

                                type: "GET",
                                url: "api/search?query=" + $(this).val(),
                                processData: false,
                                contentType: "application/json",
                                data: '',
                                success: function(r) {
                                r = JSON.parse(r)
                                    if (sbinput == "") {
                                        
                                    } else {
                                        $('.autocomplete').html("")
                                        for (var i = 0; i < r.length; i++) {
                                                console.log(r[i].body)
                                                $('.autocomplete').html(
                                                        $('.autocomplete').html() +
                                                        '<a href="specific.php?id='+r[i].id+'#'+r[i].id+'"><li class="list-group-item" style="border-radius:0px;"><span>'+r[i].body+'</span></li></a>'
                                                )
                                        }
                                    }
                                    
                                },
                                error: function(r) {
                                        console.log(r)
                                }
                        })
                })

                $('.sbox').on('focusout', function () {
                    $('.autocomplete').hide("slow");
                })

                $('.sbox').on('click', function () {
                    $('.autocomplete').show();
                        $('.sbox').trigger("keyup");
                })

                $.ajax({

                        type: "GET",
                        url: "api/posts?start=0",
                        processData: false,
                        contentType: "application/json",
                        data: '',
                        success: function(r) {
                                var posts = JSON.parse(r)
                                $.each(posts, function(index) {

                                        if (posts[index].PostImage == "" && posts[index].Verified == 1) { //added condition so that if post have img will post correctly
                                            $('.timelineposts').html(
                                                $('.timelineposts').html() + 
                                                //added style to list-group-item
                                                '<ul class="list-group"><li class="list-group-item" style="margin-top:5px; border-radius:10px;" id="'+posts[index].PostId+'"><p style="font-size:18px; margin-bottom:10px;"><i class="glyphicon glyphicon-map-marker" data-toggle="tooltip" title="Posted From" style="font-size:20px;color:#e61c5d;"></i>&nbsp<a href="'+posts[index].Location_Link+'" target="_blank" rel="noopener noreferrer">'+posts[index].Location+'</a></p><blockquote><p>'+posts[index].PostBody+'</p><footer>By <a href="profile.php?username='+posts[index].Username+'"> '+posts[index].PostedBy+' </a> <i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip" title="Verified User" style="font-size:16px;color:#e61c5d;"></i> on '+posts[index].PostDate+'</footer></blockquote><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-up" style="color:'+posts[index].LColor+'"></i><span style="color:'+posts[index].LColor+'"> '+posts[index].Likes+' Upvote</span></button><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id1=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-down" style="color:'+posts[index].DColor+'"></i><span style="color:'+posts[index].DColor+'"> '+posts[index].Dislikes+' Downvote</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-comment" style="color:#fdc405;"></i><span style="color:#fdc405;">&nbsp'+posts[index].Commented+' Comment/s</span></button></li></ul>'
                                                )
                                        } else if (posts[index].Verified == 1) {
                                                    $('.timelineposts').html(
                                                        $('.timelineposts').html() +
                                                    '<ul class="list-group"><li class="list-group-item" style="margin-top:5px; border-radius:10px;" id=""><p style="font-size:18px; margin-bottom:10px;"><i class="glyphicon glyphicon-map-marker" data-toggle="tooltip" title="Posted From" style="font-size:20px;color:#e61c5d;"></i>&nbsp<a href="'+posts[index].Location_Link+'" target="_blank" rel="noopener noreferrer">'+posts[index].Location+'</a></p><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].postId+'" alt="post image"><footer>By <a href="profile.php?username='+posts[index].Username+'"> '+posts[index].PostedBy+' </a> <i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip" title="Verified User" style="font-size:16px;color:#e61c5d;"></i> on '+posts[index].PostDate+'</footer></blockquote><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-up" style="color:'+posts[index].LColor+'"></i><span style="color:'+posts[index].LColor+'"> '+posts[index].Likes+' Upvote</span></button><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id1=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-down" style="color:'+posts[index].DColor+'"></i><span style="color:'+posts[index].DColor+'"> '+posts[index].Dislikes+' Downvote</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-comment" style="color:#fdc405;"></i><span style="color:#fdc405;">&nbsp'+posts[index].Commented+' Comment/s</span></button></li></ul>'
                                                    )
                                            }  else {
                                                $('.timelineposts').html( //added css to img scr max-height
                                                    $('.timelineposts').html() +
                                                '<ul class="list-group"><li class="list-group-item" style="margin-top:5px; border-radius:10px;" id="'+posts[index].PostId+'"><p style="font-size:18px; margin-bottom:10px;"><i class="glyphicon glyphicon-map-marker" data-toggle="tooltip" title="Posted From" style="font-size:20px;color:#e61c5d;"></i>&nbsp<a href="'+posts[index].Location_Link+'" target="_blank" rel="noopener noreferrer">'+posts[index].Location+'</a></p><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].postId+'" alt="post image"><footer>By <a href="profile.php?username='+posts[index].Username+'"> '+posts[index].PostedBy+' </a> on '+posts[index].PostDate+'</footer></blockquote><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-up" style="color:'+posts[index].LColor+'"></i><span style="color:'+posts[index].LColor+'"> '+posts[index].Likes+' Upvote</span></button><button class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;" data-id1=\"'+posts[index].PostId+'\"><i class="glyphicon glyphicon-circle-arrow-down" style="color:'+posts[index].DColor+'"></i><span style="color:'+posts[index].DColor+'"> '+posts[index].Dislikes+' Downvote</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-comment" style="color:#fdc405;"></i><span style="color:#fdc405;">&nbsp'+posts[index].Commented+' Comment/s</span></button></li></ul>'
                                                )
                                        }

                                        $('[data-postid]').click(function() {
                                                var buttonid = $(this).attr('data-postid');
                                                $.ajax({

                                                        type: "GET",
                                                        url: "api/comments?postid=" + $(this).attr('data-postid'),
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r) {
                                                                var res = JSON.parse(r)
                                                                showCommentsModal(res);
                                                                $('[data-postid1im]').click(function() {
                                                                        $.ajax({

                                                                                type: "POST",
                                                                                url: "api/commented?postid=" + buttonid,
                                                                                processData: false,
                                                                                contentType: "application/json",
                                                                                data: '{ "commentbody": "'+ $("#commentbodyim").val() +'" }',
                                                                                success: function(r) {
                                                                                        document.getElementById('commentbodyim').value = ""
                                                                                        //$('#commentsmodal').modal('hide')
                                                                                        $("[data-postid='"+buttonid+"']").trigger("click")
                                                                                        var res = JSON.parse(r)
                                                                                        console.log(res)
                                                                                        $("[data-postid='"+buttonid+"']").html(' <i class="glyphicon glyphicon-comment" style="color:#fdc405"></i><span style="color:#fdc405;"> '+res.Commented+' Comment/s</span>')
                                                                                },
                                                                                error: function(r) {
                                                                                        console.log(r)
                                                                                        
                                                                                }

                                                                        });
                                                                });
                                                        },
                                                        error: function(r) {
                                                                console.log(r)
                                                                var res = "";
                                                                showCommentsModal(res);
                                                                $('[data-postid1im]').click(function() {
                                                                        $.ajax({

                                                                                type: "POST",
                                                                                url: "api/commented?postid=" + buttonid,
                                                                                processData: false,
                                                                                contentType: "application/json",
                                                                                data: '{ "commentbody": "'+ $("#commentbodyim").val() +'" }',
                                                                                success: function(r) {
                                                                                        document.getElementById('commentbodyim').value = ""
                                                                                        $("[data-postid='"+buttonid+"']").trigger("click")
                                                                                        var res = JSON.parse(r)
                                                                                        console.log(res)
                                                                                        $("[data-postid='"+buttonid+"']").html(' <i class="glyphicon glyphicon-comment" style="color:#fdc405"></i><span style="color:#fdc405;"> '+res.Commented+' Comment/s</span>')  
                                                                                },
                                                                                error: function(r) {
                                                                                        console.log(r)
                                                                                        
                                                                                }

                                                                        });
                                                                });
                                                        }

                                                });
                                        });

                                        $('[data-id]').click(function() {
                                                var buttonid = $(this).attr('data-id');

                                                $('[data-id]').prop('disabled', true);
                                                $('[data-id1]').prop('disabled', true);
                                                setTimeout(function() {
                                                    $('[data-id]').prop('disabled', false);
                                                    $('[data-id1]').prop('disabled', false);
                                                }, 1000);

                                                $.ajax({

                                                        type: "POST",
                                                        url: "api/likes?id=" + $(this).attr('data-id'),
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r) {
                                                                var res = JSON.parse(r)
                                                                $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-circle-arrow-up" style="color:'+res.Color1+'"></i><span style="color:'+res.Color1+'"> '+res.Likes+' Upvote</span>')

                                                                $("[data-id1='"+buttonid+"']").html(' <i class="glyphicon glyphicon-circle-arrow-down" style="color:'+res.Color2+'"></i><span style="color:'+res.Color2+'"> '+res.Dislikes+' Downvote</span>')
                                                        },
                                                        error: function(r) {
                                                                console.log(r)
                                                        }

                                                });
                                        })

                                        $('[data-id1]').click(function() {
                                                var buttonid = $(this).attr('data-id1');

                                                $('[data-id]').prop('disabled', true);
                                                $('[data-id1]').prop('disabled', true);
                                                setTimeout(function() {
                                                    $('[data-id]').prop('disabled', false);
                                                    $('[data-id1]').prop('disabled', false);
                                                }, 1000);

                                                $.ajax({

                                                        type: "POST",
                                                        url: "api/dislikes?id=" + $(this).attr('data-id1'),
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r) {
                                                                var res = JSON.parse(r)
                                                                $("[data-id1='"+buttonid+"']").html(' <i class="glyphicon glyphicon-circle-arrow-down" style="color:'+res.Color1+'"></i><span style="color:'+res.Color1+'"> '+res.Dislikes+' Downvote</span>')

                                                                $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-circle-arrow-up" style="color:'+res.Color2+'"></i><span style="color:'+res.Color2+'"> '+res.Likes+' Upvote</span>')
                                                        },
                                                        error: function(r) {
                                                                console.log(r)
                                                        }

                                                });
                                        })
                                })

                                $('.postimg').each(function() {
                                        this.src=$(this).attr('data-tempsrc')
                                        this.onload = function() {
                                                this.style.opacity = '1';
                                        }
                                })

                                scrollToAnchor(location.hash)

                        },
                        error: function(r) {
                                console.log(r)
                        }

                });

        });

        function showCommentsModal(res) {
            $('#commentsmodal').modal('show')
            var output = "";
            if(res == ""){
                    output = '<center><h3>No Comment/s Yet</h3><br>Be the first to comment.</center>';
            } else {
                for (var i = 0; i < res.length; i++) {
                        output += res[i].Comment;
                        output += "<span> ~ </span>";
                        output += '<span><a href="profile.php?username='+res[i].Commenter+'">'+res[i].CommentedBy+'</span></a>';
                        if(res[i].Verified == 1){ output += '&nbsp<i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip" title="Verified User" style="font-size:14px;color:#e61c5d;"></i>'; }
                        output += "<br><br>";
                        output += "<hr />";
                }
            }
                $('.modal-body').html(output)
                output = '<form action="homepage.php?postid='+res.PostId+'#'+res.PostId+'" method="post" enctype="multipart/form-data"><div style="position:relative; width:90%;"><textarea id="commentbodyim" name="commentbodyim" onkeyup="textAreaAdjust(this)" style="overflow:hidden; resize:none; border-color:#e1d4bf; width:100%; height:47px;" maxlength="160" placeholder="Comment..."></textarea></div><div style="position:relative;"><button class="btn btn-default" data-postid1im=\"'+res.PostId+'\" type="button" style="position:absolute;bottom:0;right:0;border-radius:50px;"><i class="glyphicon glyphicon-send" style="color:#fdc405;"></i></button></div></form>';
                $('.cmf').html(output)

        }
    </script>
</body>

</html>
