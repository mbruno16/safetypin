<?php
require_once("DB.php");
require_once("Mail.php");

$db = new DB("sql207.epizy.com", "epiz_30647282_SocialNetwork", "epiz_30647282", "lq6JyYlNtMdX0kI");

if ($_SERVER['REQUEST_METHOD'] == "GET") {

        if ($_GET['url'] == "musers") {

                $token = $_COOKIE['SNID'];
                $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

                $users = $db->query("SELECT DISTINCT s.username AS Sender, r.username AS Receiver, s.id AS SenderID, r.id AS ReceiverID FROM messages LEFT JOIN users s ON s.id = messages.sender LEFT JOIN users r ON r.id = messages.receiver WHERE (s.id = :userid OR r.id=:userid)", array(":userid"=>$userid));
                $u = array();
                foreach ($users as $user) {
                        if (!in_array(array('username'=>$user['Receiver'], 'id'=>$user['ReceiverID']), $u)) {
                                array_push($u, array('username'=>$user['Receiver'], 'id'=>$user['ReceiverID']));
                        }
                        if (!in_array(array('username'=>$user['Sender'], 'id'=>$user['SenderID']), $u)) {
                                array_push($u, array('username'=>$user['Sender'], 'id'=>$user['SenderID']));
                        }
                }
                echo json_encode($u);

        } else if ($_GET['url'] == "auth") {

        } else if ($_GET['url'] == "messages") {
                $sender = $_GET['sender'];
                $token = $_COOKIE['SNID'];
                $receiver = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

                $messages = $db->query('SELECT messages.id, messages.body, s.username AS Sender, r.username AS Receiver
FROM messages
LEFT JOIN users s ON messages.sender = s.id
LEFT JOIN users r ON messages.receiver = r.id
WHERE (r.id=:r AND s.id=:s) OR r.id=:s AND s.id=:r', array(':r'=>$receiver, ':s'=>$sender));

echo json_encode($messages);

        } else if ($_GET['url'] == "search") {

                $tosearch = explode(" ", $_GET['query']);
                if (count($tosearch) == 1) {
                        $tosearch = str_split($tosearch[0], 2);
                }

                $whereclause = "";
                $paramsarray = array(':body'=>'%'.$_GET['query'].'%');
                for ($i = 0; $i < count($tosearch); $i++) {
                        if ($i % 2) {
                        $whereclause .= " OR body LIKE :p$i ";
                        $paramsarray[":p$i"] = $tosearch[$i];
                        }
                }
                $posts = $db->query('SELECT posts.id, posts.body, users.username, posts.posted_at FROM posts, users WHERE users.id = posts.user_id AND posts.body LIKE :body '.$whereclause.' LIMIT 5', $paramsarray);
                //echo "<pre>";
                echo json_encode($posts);

        } else if ($_GET['url'] == "users") {

                $token = $_COOKIE['SNID'];
                $user_id = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
                $username = $db->query('SELECT username FROM users WHERE id=:uid', array(':uid'=>$user_id))[0]['username'];
                echo $username;

        } else if ($_GET['url'] == "comments" && isset($_GET['postid'])) {
                $output = "";
                $comments = 
                $db->query('SELECT comments.post_id, comments.comment, users.username, users.fullname, users.verified FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id ORDER BY comments.posted_at DESC;', array(':postid'=>$_GET['postid']));
                $output .= "[";
                foreach($comments as $comment) {
                        $output .= "{";
                        $output .= '"PostId": '.$comment['post_id'].',';
                        $output .= '"Comment": "'.$comment['comment'].'",';
                        $output .= '"Commenter": "'.$comment['username'].'",';
                        $output .= '"Verified": '.$comment['verified'].',';
                        $output .= '"CommentedBy": "'.$comment['fullname'].'"';
                        $output .= "},";
                        $comment['comment']." ~ ".$comment['fullname']."&nbsp".$comment['verified']."<hr />";
                }
                $output = substr($output, 0, strlen($output)-1);
                $output .= "]";
                if($comment['comment']== ""){ //added condition so if no comment it will noti
                     echo '{ "Error": "No Comment" }';
                        http_response_code(409);
                } else {
                        echo $output;  
                }

        } else if ($_GET['url'] == "posts") {
                if (isset($_COOKIE['SNID'])) {
                        //$token = $_COOKIE['SNID'];
                        //echo '{ "Success": "Existing cookies!" }'; //added echo for debugging purposes
                        http_response_code(200); //added success response
                } else {
                        echo '{ "Error": "Please Log in first!" }'; //added response
                        http_response_code(409); //added error response
                        exit(); //addded exit
                }

                $token = $_COOKIE['SNID'];
                $start = (int)$_GET['start'];
                $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
                $followingposts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.postimg, posts.likes, posts.dislikes, posts.comments, posts.location_link, posts.location, users.`username`, users.`fullname`, users.`verified` FROM users, posts
                WHERE users.id = posts.user_id
                ORDER BY posts.posted_at DESC
                LIMIT 5 
                OFFSET '.$start.';', array(':userid'=>$userid), array(':userid'=>$userid));
                $response = "[";
                foreach($followingposts as $post) {
                        $postId = $post['id'];
                        $lcolor = "#e1d4bf";
                        $dcolor = "#e1d4bf";
                        if($db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$userid))){
                                $lcolor = "#e61c5d";
                                $dcolor = "#e1d4bf";
       
                        }

                        if ($db->query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$userid))) {
                                $lcolor = "#e1d4bf";
                                $dcolor = "#e61c5d";
                                
                        }


                        /* else if ($db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$userid))) {
                                if (!$db->query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$userid))){
                                        $lcolor = "#e61c5d";
                                        $dcolor = "#e1d4bf";
                                }
                        } else {
                                $lcolor = "#e1d4bf";
                                $dcolor = "#e1d4bf";
                        }*/

                        /*if(!$db->query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$userid))){
                                if ($db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$userid))){
                                        $lcolor = "#e61c5d";
                                        $dcolor = "#e1d4bf";
                                }
                                
                        } else if ($db->query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$userid))) {
                                $dcolor = "#e61c5d";
                                $lcolor = "#e1d4bf";
                        } else {
                                $lcolor = "#e1d4bf";
                                $dcolor = "#e1d4bf";
                        }*/

                        $response .= "{";
                                $response .= '"PostId": '.$post['id'].',';
                                $response .= '"Location_Link": "'.$post['location_link'].'",';
                                $response .= '"Location": "'.$post['location'].'",';
                                $response .= '"PostBody": "'.$post['body'].'",';
                                $response .= '"PostedBy": "'.$post['fullname'].'",';
                                $response .= '"Username": "'.$post['username'].'",';
                                $response .= '"Verified": "'.$post['verified'].'",';
                                $response .= '"PostDate": "'.$post['posted_at'].'",';
                                $response .= '"PostImage": "'.$post['postimg'].'",';
                                $response .= '"LColor": "'.$lcolor.'",';
                                $response .= '"DColor": "'.$dcolor.'",';
                                $response .= '"Likes": "'.$post['likes'].'",';
                                $response .= '"Commented": "'.$post['comments'].'",';
                                $response .= '"Dislikes": '.$post['dislikes'].'';
                        $response .= "},";

                }
                $response = substr($response, 0, strlen($response)-1);
                $response .= "]";

                http_response_code(200);
                echo $response;

        } else if ($_GET['url'] == "profileposts") {
                if (isset($_COOKIE['SNID'])) {
                        //$token = $_COOKIE['SNID'];
                        //echo '{ "Success": "Existing cookies!" }'; //added echo for debugging purposes
                        http_response_code(200); //added success response
                } else {
                        echo '{ "Error": "Please Log in first!" }'; //added response
                        http_response_code(409); //added error response
                        exit(); //addded exit
                }

                $token = $_COOKIE['SNID'];
                $start = (int)$_GET['start'];
                $userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $tokenid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
                $followingposts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.postimg, posts.likes, posts.dislikes, posts.comments, posts.location_link, posts.location, users.`username`, users.`fullname` FROM users, posts
                WHERE users.id = posts.user_id
                AND users.id = :userid
                ORDER BY posts.posted_at DESC
                LIMIT 5 
                OFFSET '.$start.';', array(':userid'=>$userid));
                $response = "[";
                foreach($followingposts as $post) {
                        $postId = $post['id'];
                        $lcolor = "#e1d4bf";
                        $dcolor = "#e1d4bf";
                        if($db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$tokenid))){
                                $lcolor = "#e61c5d";
                                $dcolor = "#e1d4bf";
       
                        }

                        if ($db->query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$tokenid))) {
                                $lcolor = "#e1d4bf";
                                $dcolor = "#e61c5d";
                                
                        }

                        $response .= "{";
                                $response .= '"PostId": '.$post['id'].',';
                                $response .= '"Location_Link": "'.$post['location_link'].'",';
                                $response .= '"Location": "'.$post['location'].'",';
                                $response .= '"PostBody": "'.$post['body'].'",';
                                $response .= '"PostedBy": "'.$post['fullname'].'",';
                                $response .= '"PostDate": "'.$post['posted_at'].'",';
                                $response .= '"PostImage": "'.$post['postimg'].'",';
                                $response .= '"LColor": "'.$lcolor.'",';
                                $response .= '"DColor": "'.$dcolor.'",';
                                $response .= '"Likes": "'.$post['likes'].'",';
                                $response .= '"Commented": "'.$post['comments'].'",';
                                $response .= '"Dislikes": '.$post['dislikes'].'';
                        $response .= "},";


                }
                $response = substr($response, 0, strlen($response)-1);
                $response .= "]";

                http_response_code(200);
                echo $response;

        } else if ($_GET['url'] == "specific") {
                if (isset($_COOKIE['SNID'])) {
                        //$token = $_COOKIE['SNID'];
                        //echo '{ "Success": "Existing cookies!" }'; //added echo for debugging purposes
                        http_response_code(200); //added success response
                } else {
                        echo '{ "Error": "Please Log in first!" }'; //added response
                        http_response_code(409); //added error response
                        exit(); //addded exit
                }

                $token = $_COOKIE['SNID'];
                $start = (int)$_GET['start'];
                $id = $_GET['id'];
                $userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $tokenid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
                $followingposts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.postimg, posts.likes, posts.dislikes, posts.comments, posts.location_link, posts.location, users.`username`, users.`fullname` FROM users, posts
                WHERE users.id = posts.user_id
                AND posts.id = :id 
                ORDER BY posts.posted_at DESC
                LIMIT 5 
                OFFSET '.$start.';', array(':id'=>$id));
                $response = "[";
                foreach($followingposts as $post) {
                        $postId = $post['id'];
                        $lcolor = "#e1d4bf";
                        $dcolor = "#e1d4bf";
                        if($db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$tokenid))){
                                $lcolor = "#e61c5d";
                                $dcolor = "#e1d4bf";
       
                        }

                        if ($db->query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$tokenid))) {
                                $lcolor = "#e1d4bf";
                                $dcolor = "#e61c5d";
                                
                        }

                        $response .= "{";
                                $response .= '"PostId": '.$post['id'].',';
                                $response .= '"Location_Link": "'.$post['location_link'].'",';
                                $response .= '"Location": "'.$post['location'].'",';
                                $response .= '"PostBody": "'.$post['body'].'",';
                                $response .= '"PostedBy": "'.$post['fullname'].'",';
                                $response .= '"Username": "'.$post['username'].'",';
                                $response .= '"PostDate": "'.$post['posted_at'].'",';
                                $response .= '"PostImage": "'.$post['postimg'].'",';
                                $response .= '"LColor": "'.$lcolor.'",';
                                $response .= '"DColor": "'.$dcolor.'",';
                                $response .= '"Likes": "'.$post['likes'].'",';
                                $response .= '"Commented": "'.$post['comments'].'",';
                                $response .= '"Dislikes": '.$post['dislikes'].'';
                        $response .= "},";


                }
                $response = substr($response, 0, strlen($response)-1);
                $response .= "]";

                http_response_code(200);
                echo $response;

        } 

} 
else if ($_SERVER['REQUEST_METHOD'] == "POST") {

        
/**        
        $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);

        $body = $postBody->body;
        $receiver = $postBody->receiver;

        if (strlen($body) > 100) {
                echo "{ 'Error': 'Message too long!' }";
        }
        if ($body == null) {
          $body = "";
        }
        if ($receiver == null) {
          die();
        }
        if ($userid == null) {
          die();
        }
        $db->query("INSERT INTO messages VALUES ('', :body, :sender, :receiver, '0')", array(':body'=>$body, ':sender'=>$userid, ':receiver'=>$receiver));

        echo '{ "Success": "Message Sent!" }';
**/
        if ($_GET['url'] == "message") {

        }
        
        else if ($_GET['url'] == "users") {

                $postBody = file_get_contents("php://input");
                $postBody = json_decode($postBody);

                $username = $postBody->username;
                $fullname = $postBody->fullname;
                $email = $postBody->email;
                $cnumber = $postBody->cnumber;
                $password = $postBody->password;


                    if (strlen($fullname) > 1 && strlen($fullname) <= 50 && preg_match('/[a-zA-Z0-9]+/', $fullname)) {
                    
                        if (!$db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {

                            if (strlen($username) >= 3 && strlen($username) <= 32) {
    
                                if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
    
                                    if (strlen($password) >= 6 && strlen($password) <= 60) {
    
                                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    
                                            if (strlen($cnumber) == 11) {       
                                                    
                                                if(preg_match('/^(09)[0-9]{9}/', $cnumber)) {
                                                
                                                    if (!$db->query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
            
                                                        if (!$db->query('SELECT cnumber FROM users WHERE cnumber=:cnumber', array(':cnumber'=>$cnumber))) {
                                                                $db->query('INSERT INTO users VALUES (NULL, :username, :password, :fullname, :email, :cnumber, 0, NULL)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':fullname'=>$fullname, ':email'=>$email, ':cnumber'=>$cnumber));
                                                                Mail::sendMail('Welcome to Safety Pin: Emergency Web Application!', 'Your account has been created! To continue logging in to your account, click the following link and enter your credentials: https://safety-pin.ga/login.html', $email);
                                                                echo 'Go to your email to check the confirmation of your account creation.';
                                                                http_response_code(200);
                                                        } else {
                                                            echo 'Mobile Number in use!';
                                                            http_response_code(409);
                                                        }
                                                        
                                                            
                                                    } else {
                                                            echo 'Email in use!';
                                                            http_response_code(409);
                                                    }
                                                
                                                } else {
                                                    echo 'Mobile Number must start with 09!';
                                                    http_response_code(409);
                                                }
                                                
                                            } else {
                                                echo 'Mobile Number must be 11 digits!';
                                                http_response_code(409);
                                            }
    
                                        } else {
                                            echo 'Invalid Email!';
                                            http_response_code(409);
                                        }
                                        
                                    } else {
                                        echo 'Password must be atleast 6 characters long!';
                                        http_response_code(409);
                                    }
                                    
                                } else {
                                    echo 'Invalid Username! Must only contain alphanumeric value and underscore.';
                                    http_response_code(409);
                                }
                            } else {
                                echo 'Username must be atleast 3 characters long!';
                                http_response_code(409);
                            }
    
                        } else {
                            echo 'Username exists!';
                            http_response_code(409);
                        }
                        
                    } else {
                            echo 'Invalid Fullname!';
                            http_response_code(409);
                    }


        }

        if ($_GET['url'] == "post") {
                $token = $_COOKIE['SNID'];

                $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
                echo "Dfdf";
        } 
        

        if ($_GET['url'] == "auth") {
 
                if (isset($_COOKIE['SNID'])) {
                        $token = $_COOKIE['SNID'];
                        //echo '{ "Error": "Please Log out first!" }'; //added response
                        http_response_code(409); //added error response
                        die();
                } else {
                        http_response_code(200); //added success response
                        //deleted die();
                }        

                $postBody = file_get_contents("php://input");
                $postBody = json_decode($postBody);

                $username = $postBody->username;
                $password = $postBody->password;
                if ($db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
                        if (password_verify($password, $db->query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {

                                $cstrong = True;
                                $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                                $user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
                                $db->query('INSERT INTO login_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
                                echo '{ "Token": "'.$token.'" }';
                                        //added from login php
                                        setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                                        setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
                                        //end of added code from login php                          

                        } else {
                                echo '{ "Error": "Invalid username or password!" }';
                                http_response_code(401);
                        }
                } else {
                        echo '{ "Error": "Invalid username or password!" }';
                        http_response_code(401);
                }



        } else if ($_GET['url'] == "commented") { //added comented api
                $postId = $_GET['postid'];
                $token = $_COOKIE['SNID'];
                $r = $db->query('SELECT user_id FROM posts WHERE id=:id', array(':id'=>$postId))[0]['user_id'];
               
                $postBody = file_get_contents("php://input");
                $postBody = json_decode($postBody);
                $commentBody = $postBody->commentbody;

                $userId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

                if (strlen($commentBody) > 160 || strlen($commentBody) < 1 || $commentBody == false) {
                        echo '{ "Error": "Incorrect Length!" }';
                        http_response_code(401);
                        die(); //added die
                }

                if (!$db->query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
                        echo '{ "Error": "Invalid ID!" }';
                        http_response_code(401);
                } else {
                        $db->query('UPDATE posts SET comments=comments+1 WHERE id=:postid', array(':postid'=>$postId));
                        $db->query('INSERT INTO comments VALUES (NULL, :comment, :userid, :receiver, NOW(), :postid)', array(':comment'=>$commentBody, ':userid'=>$userId, ':receiver'=>$r, ':postid'=>$postId));
                }
                echo '{';
                echo '"Commented":';
                echo $db->query('SELECT comments FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['comments'];
                echo '}';

        } else if ($_GET['url'] == "likes") {
                $postId = $_GET['id'];
                $token = $_COOKIE['SNID'];
                $likerId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

                if (!$db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {

                        if ($db->query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
                                $db->query('UPDATE posts SET dislikes=dislikes-1 WHERE id=:postid', array(':postid'=>$postId));
                                $db->query('DELETE FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
                        }
                        
                        $db->query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
                        $db->query('INSERT INTO post_likes VALUES (NULL, :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
                        $color1 = "#e61c5d";
                        $color2 = "#e1d4bf";
                        //Notify::createNotify("", $postId);
                } else {
                        $db->query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
                        $db->query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
                        $color1 = "#e1d4bf";
                        $color2 = "#e1d4bf";
                }

                echo "{";
                echo '"Likes":';
                echo $db->query('SELECT likes FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['likes'];
                echo ",";
                echo '"Color1": "'.$color1.'",';
                echo '"Color2": "'.$color2.'",';
                echo '"Dislikes":';
                echo $db->query('SELECT dislikes FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['dislikes'];
                echo "}";
        
        } else if ($_GET['url'] == "dislikes") {
                $postId = $_GET['id'];
                $token = $_COOKIE['SNID'];
                $dislikerId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

                if (!$db->query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$dislikerId))) {

                        if ($db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$dislikerId))) {
                                $db->query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
                                $db->query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$dislikerId));
                        }

                        $db->query('UPDATE posts SET dislikes=dislikes+1 WHERE id=:postid', array(':postid'=>$postId));
                        $db->query('INSERT INTO post_dislikes VALUES (NULL, :postid, :userid)', array(':postid'=>$postId, ':userid'=>$dislikerId));
                        $color1 = "#e61c5d";
                        $color2 = "#e1d4bf";
                        //Notify::createNotify("", $postId);
                } else {
                        $db->query('UPDATE posts SET dislikes=dislikes-1 WHERE id=:postid', array(':postid'=>$postId));
                        $db->query('DELETE FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$dislikerId));
                        $color1 = "#e1d4bf";
                        $color2 = "#e1d4bf";
                }

                echo "{";
                echo '"Likes":';
                echo $db->query('SELECT likes FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['likes'];
                echo ",";
                echo '"Color1": "'.$color1.'",';
                echo '"Color2": "'.$color2.'",';
                echo '"Dislikes":';
                echo $db->query('SELECT dislikes FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['dislikes'];
                echo "}";
        }       

}
else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
        if ($_GET['url'] == "auth") {
                if (isset($_GET['token'])) {
                        if ($db->query("SELECT token FROM login_tokens WHERE token=:token", array(':token'=>sha1($_GET['token'])))) {
                                $db->query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_GET['token'])));
                                echo '{ "Status": "Success" }';
                                http_response_code(200);
                        } else {
                                echo '{ "Error": "Invalid token" }';
                                http_response_code(400);
                        }
                } else {
                        echo '{ "Error": "Malformed request" }';
                        http_response_code(400);
                }
        }
} else {
        http_response_code(405);
}

// Helper functions
?>
