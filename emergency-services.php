<?php
include('./classes/DB.php');
include('./classes/Login.php');

ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);

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
    <title>Safety Pin | Emergency Services</title>
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
                        <li role="presentation" class="img-mob-active" style="display: inline">
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
                        <li role="presentation" class="img-mob-active">
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
                        <li role="presentation" class="img-manip"> <!--put indicator na active to sa js-->
                            <a href="homepage.php" data-toggle="tooltip" title="Homepage"><img class="iconh" src="assets/img/homeicon.png" alt="home" width="40px" height="40px"></a><!--added HOME img-->
                        </li>
                        <!--removed li containing message-->
                        <li role="presentation" class="img-manip-active">
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
            </nav>
        </div>
    
    <div class="container">
        <h1>Emergency Services </h1></div>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item" style="margin-bottom:30px; border-radius:10px; padding-bottom:0px;"><span><strong>Welcome to Emergency Services Page</strong></span>
                            <p>This page contains all the contact number/s that you can reach during emergencies.</p>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group-item" id="toggle1" style="margin-bottom:30px; border-radius:10px; background:#FFFFFF;" aria-expanded="false"><div id="ulist1" role="button" data-toggle="collapse" data-target="#barangay_c,#contact1,#contact2,#contact3"><img src="assets/img/barangay.png" alt="barangay-logo" length="70px" width="70px" style="float:left; margin-right:8px; padding-bottom: 10px"></img><h2 style="display:inline;">Barangay Paang Bundok</h2><i class="glyphicon glyphicon-triangle-right first" style="display:inline; float: right; font-size:25px;"></i><h4 style="color:#918E8E;">Laloma, Quezon City</h4><p class="collapse" style="margin-left: 80px" id="barangay_c" aria-expanded="false">Contacts Number/s:</p></div>
                      <?php
                            $contact1 = DB::query('SELECT * FROM contacts WHERE brgy_no=1');
                            foreach($contact1 as $c){
                                if($c['type'] == "barangay")
                                    echo '<p class="collapse" style="margin-left: 80px" id="barangay_c" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                            }
                            
                                echo '<div class="collapse" id="contact1" role="button" data-toggle="collapse" data-target="#police,#police_c" aria-expanded="false" onclick="con1();"><li class="list-group-item" style="background-color:#8884B4; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con1" style="display:inline; float: right;  font-size:18px;"></i><img src="assets/img/police.png" alt="police-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">Laloma Police Station</h3><p class="collapse" id="police_c" aria-expanded="false"  style="margin-left: 45px;">Contact Number/s:';
                            
                                    foreach($contact1 as $c){
                                        if($c['type'] == "police")
                                                echo '<p class="collapse" style="margin-left: 45px" id="police_c" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                    echo '</li></div>';
                            
                                echo '<div class="collapse" id="contact2" role="button" data-toggle="collapse" data-target="#healthcenter,#healthcenter_c" aria-expanded="false" onclick="con2();"><li class="list-group-item" style="background-color:#E2836E; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con2" style="display:inline; float: right; font-size:18px;"></i><img src="assets/img/healthcare.png" alt="healthcare-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">Laloma Health Center</h3><p class="collapse" id="healthcenter_c" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';
                                    
                                    foreach($contact1 as $c){
                                        if($c['type'] == "healthcenter")
                                                echo '<p class="collapse" style="margin-left: 45px" id="healthcenter_c" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                     echo '</li></div>';
                        
                                echo '<div class="collapse" id="contact3" role="button" data-toggle="collapse" data-target="#firestation,#firestation_c" aria-expanded="false" onclick="con3();"><li class="list-group-item" style="background-color:#D49A7C; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con3" style="display:inline; float: right;  font-size:18px;"></i><img src="assets/img/firestation.png" alt="firestation-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">Laloma Fire Station</h3><p class="collapse" id="firestation_c" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';
                                
                                    foreach($contact1 as $c){
                                        if($c['type'] == "firestation")
                                                echo '<p class="collapse" style="margin-left: 45px" id="firestation_c" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                     echo '</li></div>';
                      ?>
                    </ul>
                    
                    <ul class="list-group-item" id="toggle2" role="button" style="margin-bottom:30px; border-radius:10px; background:#FFFFFF;" aria-expanded="false"><div id="ulist2" data-toggle="collapse" data-target="#barangay_c1,#contact4,#contact5,#contact6"><img src="assets/img/barangay.png" alt="barangay-logo" length="70px" width="70px" style="float:left; margin-right:8px; padding-bottom: 10px"></img><h2 style="display:inline;">Barangay Muzon</h2><i class="glyphicon glyphicon-triangle-right second" style="display:inline; float: right; font-size:25px;"></i><h4 style="color:#918E8E">SJDM, Bulacan</h4><p class="collapse" style="margin-left: 80px" id="barangay_c1" aria-expanded="false">Contacts Number/s:</p></div>
                      <?php
                            $contact2 = DB::query('SELECT * FROM contacts WHERE brgy_no=2');
                            foreach($contact2 as $c){
                                if($c['type'] == "barangay")
                                    echo '<p class="collapse" style="margin-left: 80px" id="barangay_c1" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                            }
                            
                                echo '<div class="collapse" id="contact4" role="button" data-toggle="collapse" data-target="#police1,#police_c1" aria-expanded="false" onclick="con4();"><li class="list-group-item" style="background-color:#8884B4; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con4" style="display:inline; float: right;  font-size:18px;"></i><img src="assets/img/police.png" alt="police-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">CSJDM PNP Headquarters</h3><br><p class="collapse" id="police_c1" aria-expanded="false" style="margin-left: 45px">Contact Number/s:</p>';
                                    
                                    foreach($contact2 as $c){
                                        if($c['type'] == "police")
                                                echo '<p class="collapse" style="margin-left: 45px" id="police_c1" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                    echo '</li></div>';
                                
                                echo '<div class="collapse" id="contact5" role="button" data-toggle="collapse" data-target="#healthcenter1,#healthcenter_c1" aria-expanded="false" onclick="con5();"><li class="list-group-item" style="background-color:#E2836E; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con5" style="display:inline; float: right; font-size:18px;"></i><img src="assets/img/healthcare.png" alt="healthcare-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">Brgy. Muzon Health Center</h3><br><p class="collapse" id="healthcenter_c1" aria-expanded="false" style="margin-left: 45px">Contact Number/s:</p>';
                                    foreach($contact2 as $c){
                                        if($c['type'] == "healthcenter")
                                                echo '<p class="collapse" style="margin-left: 45px" id="healthcenter_c1" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                     echo '</li></div>';
                                
                                echo '<div class="collapse" id="contact6" role="button" data-toggle="collapse" data-target="#firestation1,#firestation_c1" aria-expanded="false" onclick="con6();"><li class="list-group-item" style="background-color:#D49A7C; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con6" style="display:inline; float: right;  font-size:18px;"></i><img src="assets/img/firestation.png" alt="firestation-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">BFP CSJDM Fire Station</h3><br><p class="collapse" id="firestation_c1" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';
                                
                                    foreach($contact2 as $c){
                                        if($c['type'] == "firestation")
                                                echo '<p class="collapse" style="margin-left: 45px" id="firestation_c1" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                     echo '</li></div>';
                      ?>
                    </ul>
                    
                    <ul class="list-group-item" id="toggle3" role="button" style="margin-bottom:30px; border-radius:10px; background:#FFFFFF;" aria-expanded="false"><div id="ulist3" data-toggle="collapse" data-target="#barangay_c2,#contact7,#contact8,#contact9"><img src="assets/img/barangay.png" alt="barangay-logo" length="70px" width="70px" style="float:left; margin-right:8px; padding-bottom: 10px"></img><h2 style="display:inline;">Barangay 27 Zone 3</h2><i class="glyphicon glyphicon-triangle-right third" style="display:inline; float: right; font-size:25px;"></i><h4 style="color:#918E8E">Caloocan City</h4><p class="collapse" style="margin-left: 80px" id="barangay_c2" aria-expanded="false">Contacts Number/s:</p></div>
                      <?php
                            $contact3 = DB::query('SELECT * FROM contacts WHERE brgy_no=3');
                            foreach($contact3 as $c){
                                if($c['type'] == "barangay")
                                    echo '<p class="collapse" style="margin-left: 80px" id="barangay_c2" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                            }
                            
                                echo '<div class="collapse" id="contact7" role="button" data-toggle="collapse" data-target="#police2,#police_c2" aria-expanded="false" onclick="con7();"><li class="list-group-item" style="background-color:#8884B4; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con7" style="display:inline; float: right;  font-size:18px;"></i><img src="assets/img/police.png" alt="police-logo" length="35px" width="35px" style="float:left; margin-right:8px; margin-bottom:8px;"></img><h3 style="display:inline;">C3 Extension Police Station</h3><br><p class="collapse" id="police_c2" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';
                                
                                    foreach($contact3 as $c){
                                        if($c['type'] == "police")
                                                echo '<p class="collapse" style="margin-left: 45px" id="police_c2" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                    echo '</li></div>';
                                
                                echo '<div class="collapse" id="contact8" role="button" data-toggle="collapse" data-target="#healthcenter2,#healthcenter_c2" aria-expanded="false" onclick="con8();"><li class="list-group-item" style="background-color:#E2836E; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con8" style="display:inline; float: right; font-size:18px;"></i><img src="assets/img/healthcare.png" alt="healthcare-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">Caloocan City Medical Center</h3><br><p class="collapse" id="healthcenter_c2" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';
                                
                                    foreach($contact2 as $c){
                                        if($c['type'] == "healthcenter")
                                                echo '<p class="collapse" style="margin-left: 45px" id="healthcenter_c2" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                    echo '</li></div>';
                
                                echo '<div class="collapse" id="contact9" role="button" data-toggle="collapse" data-target="#firestation2,#firestation_c2" aria-expanded="false" onclick="con9();"><li class="list-group-item" style="background-color:#D49A7C; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con9" style="display:inline; float: right;  font-size:18px;"></i><img src="assets/img/firestation.png" alt="firestation-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">Maypajo Fire Station</h3><br><p class="collapse" id="firestation_c2" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';
                                    
                                    foreach($contact2 as $c){
                                        if($c['type'] == "firestation")
                                                echo '<p class="collapse" style="margin-left: 45px" id="firestation_c2" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                    echo '</li></div>';
                      ?>
                    </ul>
                    
                    <ul class="list-group-item" id="toggle4" role="button" style="margin-bottom:30px; border-radius:10px; background:#FFFFFF;" aria-expanded="false"><div id="ulist4" data-toggle="collapse" data-target="#barangay_c3,#contact10,#contact11,#contact12"><img src="assets/img/barangay.png" alt="barangay-logo" length="70px" width="70px" style="float:left; margin-right:8px; padding-bottom: 10px"></img><h2 style="display:inline;">Barangay 514 Zone 51</h2><i class="glyphicon glyphicon-triangle-right fourth" style="display:inline; float: right; font-size:25px;"></i><h4 style="color:#918E8E">Sampaloc, Manila</h4><p class="collapse" style="margin-left: 80px" id="barangay_c3" aria-expanded="false">Contacts Number/s:</p></div>
                      <?php
                            $contact4 = DB::query('SELECT * FROM contacts WHERE brgy_no=4');
                            foreach($contact4 as $c){
                                if($c['type'] == "barangay")
                                    echo '<p class="collapse" style="margin-left: 80px" id="barangay_c3" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                            }
                                echo '<div class="collapse" id="contact10" role="button" data-toggle="collapse" data-target="#police3,#police_c3" aria-expanded="false" onclick="con10();"><li class="list-group-item" style="background-color:#8884B4; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con10" style="display:inline; float: right;  font-size:18px;"></i><img src="assets/img/police.png" alt="police-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">España Police Station</h3><br><p class="collapse" id="police_c3" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';
                                
                                    foreach($contact4 as $c){
                                        if($c['type'] == "police")
                                                echo '<p class="collapse" style="margin-left: 45px" id="police_c3" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                    echo '</li></div>';
                                
                                echo '<div class="collapse" id="contact11" role="button" data-toggle="collapse" data-target="#healthcenter3,#healthcenter_c3" aria-expanded="false" onclick="con11();"><li class="list-group-item" style="background-color:#E2836E; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con11" style="display:inline; float: right; font-size:18px;"></i><img src="assets/img/healthcare.png" alt="healthcare-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">Dapitan Health Center</h3><br><p class="collapse" id="healthcenter_c3" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';
                                
                                    foreach($contact4 as $c){
                                        if($c['type'] == "healthcenter")
                                                echo '<p class="collapse" style="margin-left: 45px" id="healthcenter_c3" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                    echo '</li></div>';
                                
                                echo '<div class="collapse" id="contact12" role="button" data-toggle="collapse" data-target="#firestation3,#firestation_c3" aria-expanded="false" onclick="con12();"><li class="list-group-item" style="background-color:#D49A7C; border-radius:10px"></h2><i class="glyphicon glyphicon-triangle-right con12" style="display:inline; float: right;  font-size:18px;"></i><img src="assets/img/firestation.png" alt="firestation-logo" length="35px" width="35px" style="float:left; margin-right:8px;"></img><h3 style="display:inline;">Central Sampaloc Fire and Rescue</h3><br><p class="collapse" id="firestation_c3" aria-expanded="false" style="margin-left: 45px;">Contact Number/s:</p>';      
                                
                                    foreach($contact4 as $c){
                                        if($c['type'] == "firestation")
                                                echo '<p class="collapse" style="margin-left: 45px" id="firestation_c3" aria-expanded="false">'.$c['contact_number'].'<a href="tel:'.$c['contact_number'].'"><i class="glyphicon glyphicon-earphone" style="display:inline; float: right; font-size:15px;"></i></a></p>';
                                    }
                                    echo '</li></div>';
                      ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.getElementById("ulist1").onclick = function() {
      myFunction()
    };
    document.getElementById("ulist2").onclick = function() {
      myFunction1()
    };
     document.getElementById("ulist3").onclick = function() {
      myFunction2()
    };
     document.getElementById("ulist4").onclick = function() {
      myFunction3()
    };

    function myFunction() {
      let color = document.getElementById("toggle1").style.backgroundColor;
      const icon = document.querySelector('.glyphicon.first');
      if (color == 'rgb(216, 203, 204)' && icon.classList.contains('glyphicon-triangle-bottom')) {
        document.getElementById("toggle1").style.backgroundColor = "rgb(255,255,255)";
        icon.classList.remove('glyphicon-triangle-bottom');
        icon.classList.add('glyphicon-triangle-right');
      } else {
        document.getElementById("toggle1").style.backgroundColor = "rgb(216, 203, 204)";
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function myFunction1() {
      let color = document.getElementById("toggle2").style.backgroundColor;
      const icon = document.querySelector('.glyphicon.second');
      if (color == 'rgb(216, 203, 204)' && icon.classList.contains('glyphicon-triangle-bottom')) {
        document.getElementById("toggle2").style.backgroundColor = "rgb(255,255,255)";
        icon.classList.remove('glyphicon-triangle-bottom');
        icon.classList.add('glyphicon-triangle-right');
      } else {
        document.getElementById("toggle2").style.backgroundColor = "rgb(216, 203, 204)";
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function myFunction2() {
      let color = document.getElementById("toggle3").style.backgroundColor;
      const icon = document.querySelector('.glyphicon.third');
      if (color == 'rgb(216, 203, 204)' && icon.classList.contains('glyphicon-triangle-bottom')) {
        document.getElementById("toggle3").style.backgroundColor = "rgb(255,255,255)";
        icon.classList.remove('glyphicon-triangle-bottom');
        icon.classList.add('glyphicon-triangle-right');
      } else {
        document.getElementById("toggle3").style.backgroundColor = "rgb(216, 203, 204)";
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function myFunction3() {
      let color = document.getElementById("toggle4").style.backgroundColor;
      const icon = document.querySelector('.glyphicon.fourth');
      if (color == 'rgb(216, 203, 204)' && icon.classList.contains('glyphicon-triangle-bottom')) {
        document.getElementById("toggle4").style.backgroundColor = "rgb(255,255,255)";
        icon.classList.remove('glyphicon-triangle-bottom');
        icon.classList.add('glyphicon-triangle-right');
      } else {
        document.getElementById("toggle4").style.backgroundColor = "rgb(216, 203, 204)";
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con1(){
        const icon = document.querySelector('.glyphicon.con1');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con2(){
        const icon = document.querySelector('.glyphicon.con2');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con3(){
        const icon = document.querySelector('.glyphicon.con3');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con4(){
        const icon = document.querySelector('.glyphicon.con4');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con5(){
        const icon = document.querySelector('.glyphicon.con5');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con6(){
        const icon = document.querySelector('.glyphicon.con6');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con7(){
        const icon = document.querySelector('.glyphicon.con7');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con8(){
        const icon = document.querySelector('.glyphicon.con8');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con9(){
        const icon = document.querySelector('.glyphicon.con9');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con10(){
        const icon = document.querySelector('.glyphicon.con10');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con11(){
        const icon = document.querySelector('.glyphicon.con11');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    
    function con12(){
        const icon = document.querySelector('.glyphicon.con12');
        if (icon.classList.contains('glyphicon-triangle-bottom')){
            icon.classList.remove('glyphicon-triangle-bottom');
            icon.classList.add('glyphicon-triangle-right');
        } else {
        icon.classList.remove('glyphicon-triangle-right');
        icon.classList.add('glyphicon-triangle-bottom');
      }
    }
    </script>
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