<?php
include('./classes/DB.php');
include('./classes/Login.php');

ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        header('location:login.html');
}

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
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fdc405"/>
    <title>Safety Pin | Notifications</title>
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
        width:100%;
    }
    
    img[src="https://cdn.000webhost.com/000webhost/logo/footer-powered-by-000webhost-white2.png"]{display:none;}
    </style>
</head>

<body>
    <header class="hidden-sm hidden-md hidden-lg">
            <div class="navbar-header" style="display: inline"><a class="navbar-brand navbar-link" href="#"><img src="assets/img/icon-logo.png" alt="Safety Pin" width="30px" height="40px" style="margin-top: 7px"></i></a><!--added icon-->
                    <form class="navbar-form navbar-left" style="display: inline; float: left">
                        <div class="searchbox" style="margin-top:10px"><i class="glyphicon glyphicon-search"></i>
                            <input id="searchbox" class="form-control sbox" type="text" style="width: 105%"><label for="searchbox" style="color:transparent; font-size:0px; padding:0px; margin:0px">Search</label>
                            <ul class="list-group autocomplete" style="position:absolute;width:175%; z-index:100">
                            </ul>
                        </div>
                    </form>
                    <ul style="float: right; display: inline; margin-left: -50px; margin-top: 5px">
                        <li role="presentation" class="img-mob" style="display: inline"> <!--put indicator na active to sa js-->
                            <a href="homepage.php" style="display: inline; padding: 8px 5px"><img class="iconh" src="assets/img/homeicon.png" alt="home" width="40px" height="40px"></a><!--added HOME img-->
                        </li>
                        <!--removed li containing message-->
                        <li role="presentation" class="img-mob" style="display: inline">
                            <a href="emergency-services.php" style="display: inline; padding: 8px 5px"><img class="iconh" src="assets/img/esicon.png" alt="e-service" width="40px" height="40px"></a><!--added ES img-->
                        </li>
                        <li role="presentation" class="img-mob-active" style="display: inline">
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
                <div class="navbar-header"><a class="navbar-brand navbar-link" href="#" data-toggle="tooltip" title="Safety Pin Logo"><img src="assets/img/logo.png" alt="Safety Pin" width="180px" height="44px"></i></a><!--added icon-->
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
                        <li role="presentation" class="img-mob"> <!--put indicator na active to sa js-->
                            <a href="homepage.php"><img class="iconh" src="assets/img/homeicon.png" alt="home" width="40px" height="40px"></a><!--added HOME img-->
                        </li>
                        <!--removed li containing message-->
                        <li role="presentation" class="img-mob">
                            <a href="emergency-services.php"><img class="iconh" src="assets/img/esicon.png" alt="e-service" width="40px" height="40px"></a><!--added ES img-->
                        </li>
                        <li role="presentation" class="img-mob-active">
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
                        <li role="presentation" class="img-manip"> <!--put indicator na active to sa js-->
                            <a href="homepage.php" data-toggle="tooltip" title="Homepage"><img class="iconh" src="assets/img/homeicon.png" alt="home" width="40px" height="40px"></a><!--added HOME img-->
                        </li>
                        <!--removed li containing message-->
                        <li role="presentation" class="img-manip">
                            <a href="emergency-services.php" data-toggle="tooltip" title="Emergency Services"><img class="iconh" src="assets/img/esicon.png" alt="e-service" width="40px" height="40px"></a><!--added ES img-->
                        </li>
                        <li role="presentation" class="img-manip-active">
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
            </nav>
        </div>
    
    <div class="container">
        <h1>Notifications </h1></div>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item" style="margin-bottom:30px; border-radius:10px; padding-bottom:0px;"><span><strong>Welcome to Notification Page</strong></span>
                            <p>This page contains all the notifications you've received regarding users that tagged you in their post, commented on your post and emergency post updates.</p>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h2 style="margin-top:0px; border-bottom:1px solid #D8CBCC">Tagged Posts</h2>
                    <ul class="list-group">
                      <?php
                      if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid))) {

                              $notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid ORDER BY id DESC', array(':userid'=>$userid));

                              foreach($notifications as $n) {

                                      if ($n['type'] == 1) {
                                              $senderName = DB::query('SELECT fullname FROM users WHERE users.id=:senderid', array(':senderid'=>$n['sender']))[0]['fullname'];
                                              $timeStamp = DB::query('SELECT posted_at FROM posts WHERE posts.id=:post_id', array(':post_id'=>$n['post_id']))[0]['posted_at'];

                                              if ($n['extra'] == "") {
                                                      echo "You got a notification!<hr />";
                                              } else {
                                                      $extra = json_decode($n['extra']);
                                                      echo '<a href="/specific.php?id='.$n['post_id'].'"><li class="list-group-item"><span>'.$senderName.' mentioned you in a post! - '.$extra->postbody.'</span><br>Timestamp: '.$timeStamp.'</li></a><br>';
                                              }

                                      } else if ($n['type'] == 2) {
                                              $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                                              echo '<li class="list-group-item"><span>'.$senderName.' upvote your post!</span></li>';
                                      } else if ($n['type'] == 3) {
                                              $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                                              echo '<li class="list-group-item"><span>'.$senderName.' downvote your post!</span></li>';
                                      } 

                              }

                      }
                      ?>
                    </ul>
                    <h2 style="border-bottom:1px solid #D8CBCC">Comments on Posts</h2>
                    <ul class="list-group">
                      <?php
                        if (DB::query('SELECT * FROM comments WHERE receiver=:userid', array(':userid'=>$userid))){
                            $comments = DB::query('SELECT * FROM comments WHERE receiver=:userid ORDER BY id DESC', array(':userid'=>$userid));
                            foreach($comments as $c){
                                $fullname = DB::query('SELECT fullname FROM users WHERE id=:userid', array(':userid'=>$c['user_id']))[0]['fullname'];
                                echo '<a href="/specific.php?id='.$c['post_id'].'"><li class="list-group-item"><span>'.$fullname.' commented on your post - '.$c['comment'].'</span><br>Timestamp: '.$c['posted_at'].'</li></a><br>';
                            }
                      }
                      ?>
                    </ul>
                    
                    <h2 style="border-bottom:1px solid #D8CBCC">Post Updates</h2>
                    <ul class="list-group">
                      <?php
                        $posts = array();
                        $posts = DB::query('SELECT posts.id,fullname,body,location,posted_at FROM posts,users WHERE posts.user_id=users.id ORDER BY posts.id DESC');
                        foreach($posts as $p){
                            echo '<a href="/specific.php?id='.$p['id'].'"><li class="list-group-item"><span>'.$p['fullname'].' posted an emergency update - '.$p['body'].'</span><br>Location: '.$p['location'].'<br>Timestamp: '.$p['posted_at'].'</li></a><br>';
                        }
                      ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script>
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
                                                        '<a href="/specific.php?id='+r[i].id+'#'+r[i].id+'"><li class="list-group-item" style="border-radius:0px;"><span>'+r[i].body+'</span></li></a>'
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
    </script>
    
    <script type="text/javascript"> //JAVASCRIPT FOR OTHER FUNCTIONS
        //added if (page is redirected by forward/back navigation in browser) will reload the page
        var perfEntries = performance.getEntriesByType("navigation");
        if (perfEntries[0].type === "back_forward") {
            location.reload(true);  
        }
    </script>
</body>

</html>
