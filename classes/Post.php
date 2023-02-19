<?php
require_once("Mail.php");
ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);
class Post {

        public static function createPost($postbody, $loggedInUserId, $profileUserId, $location_link, $location) {

                if (strlen($postbody) > 160 || strlen($postbody) < 1) {
                        die('Incorrect length!');
                }
                
                $recipients = DB::query('SELECT * FROM users');
                $date = date("Y-m-d G:i:s");
                $topics = self::getTopics($postbody);
                
                if ($loggedInUserId == $profileUserId) {
                        DB::query('INSERT INTO posts VALUES (NULL, :postbody, :posted_at, :userid, 0, 0, 0, NULL, :location_link, :location, :topics)', array(':postbody'=>$postbody, ':posted_at'=>$date, ':userid'=>$profileUserId, ':location_link'=>$location_link, ':location'=>$location, ':topics'=>$topics));
                    
                        if (count(Notify::createNotify($postbody)) != 0) {
                                foreach (Notify::createNotify($postbody) as $key => $n) {
                                                $s = $loggedInUserId;
                                                $newest = DB::query('SELECT id FROM `posts` ORDER BY id DESC');
                                                $r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];
                                                if ($r != 0) {
                                                        DB::query('INSERT INTO notifications VALUES (NULL, :type, :receiver, :sender, :post_id, :extra)', array(':type'=>$n["type"], ':post_id'=>$newest[0]["id"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
                                                }
                                }
                        }
                        $fullname = DB::query('SELECT fullname FROM users WHERE users.id=:loggedInUserId', array(':loggedInUserId'=>$loggedInUserId))[0]['fullname'];
                        $latest = DB::query('SELECT id FROM `posts` ORDER BY id DESC');
                        foreach($recipients as $r){
                            Mail::sendMail('Safety-Pin Emergency Notification', 'New Emergency Post By '.$fullname.': '.$postbody.'<br> Link: https://safety-pin.ga/specific.php?id='.$latest[0]['id'].'', $r['email']);
                        }
                        $urls = 'https://safety-pin.ga/specific.php?id='.$latest[0]['id'].'';
                        $content = array(
                            "en" => 'New Emergency Post By '.$fullname.': '.$postbody.''
                        );
                        $headings = array(
                            "en" => 'Safety-Pin Emergency Notification'
                        );
                        $fields = array(
                            'app_id' => "bf35c756-d339-4516-8616-68ac6c050407",
                            'included_segments' => array(
                                'Subscribed Users'
                            ),
                            'data' => array(
                                "foo" => "bar"
                            ),
                            'contents' => $content,
                            'headings' => $headings,
                            'url' => $urls
                        );
                        
                        $fields = json_encode($fields);
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json; charset=utf-8',
                            'Authorization: Basic MjViNTQ1NTItYTg1Ny00ZjU0LWExNDQtOWNhZWIxOTFmZTZh'
                        ));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_HEADER, FALSE);
                        curl_setopt($ch, CURLOPT_POST, TRUE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                        
                        $response = curl_exec($ch);
                        curl_close($ch);
                        
                        return $response;
                } else {
                        die('Incorrect user!');
                }
        }

        public static function createImgPost($postbody, $loggedInUserId, $profileUserId, $location_link, $location) {

                if (strlen($postbody) > 160) {
                        die('Incorrect length!');
                }
                
                $recipients = array();
                $recipients = DB::query('SELECT * FROM users');
                $date = date("Y-m-d G:i:s");
                $topics = self::getTopics($postbody);

                if ($loggedInUserId == $profileUserId) {
                        DB::query('INSERT INTO posts VALUES (NULL, :postbody, :posted_at, :userid, 0, 0, 0, NULL, :location_link, :location, :topics)', array(':postbody'=>$postbody, ':posted_at'=>$date, ':userid'=>$profileUserId, ':location_link'=>$location_link, ':location'=>$location, ':topics'=>$topics));

                        if (count(Notify::createNotify($postbody)) != 0) {
                                foreach (Notify::createNotify($postbody) as $key => $n) {
                                                $s = $loggedInUserId;
                                                $newest = DB::query('SELECT id FROM `posts` ORDER BY id DESC');
                                                $r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];
                                                if ($r != 0) {
                                                        DB::query('INSERT INTO notifications VALUES (NULL, :type, :receiver, :sender, :post_id, :extra)', array(':type'=>$n["type"], ':post_id'=>$newest[0]["id"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
                                                }
                                }
                        }

                        $fullname = DB::query('SELECT fullname FROM users WHERE users.id=:loggedInUserId', array(':loggedInUserId'=>$loggedInUserId))[0]['fullname'];
                        $latest = DB::query('SELECT id FROM `posts` ORDER BY id DESC');
                        foreach($recipients as $r){
                            Mail::sendMail('Safety-Pin Emergency Notification', 'New Emergency Post By '.$fullname.': '.$postbody.'<br> Link: https://safety-pin.ga/specific.php?id='.$latest[0]['id'].'', $r['email']);
                        }
                        $postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid'=>$loggedInUserId))[0]['id'];
                        return $postid;
                        curl_close($ch);
                } else {
                        die('Incorrect user!');
                }
        }
        
        public static function notifAll($postbody, $loggedInUserId, $profileUserId){
            if ($loggedInUserId == $profileUserId){
                    $fullname = DB::query('SELECT fullname FROM users WHERE users.id=:loggedInUserId', array(':loggedInUserId'=>$loggedInUserId))[0]['fullname'];
                    $latest = DB::query('SELECT id FROM `posts` ORDER BY id DESC');
                    $urls = 'https://safety-pin.ga/specific.php?id='.$latest[0]['id'].'';
                    $content = array(
                        "en" => 'New Emergency Post By '.$fullname.': '.$postbody.''
                    );
                    $headings = array(
                        "en" => 'Safety-Pin Emergency Notification'
                    );
                    $fields = array(
                        'app_id' => "bf35c756-d339-4516-8616-68ac6c050407",
                        'included_segments' => array(
                            'Subscribed Users'
                        ),
                        'data' => array(
                            "foo" => "bar"
                        ),
                        'contents' => $content,
                        'headings' => $headings,
                        'url' => $urls
                    );
                    
                    $fields = json_encode($fields);
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic MjViNTQ1NTItYTg1Ny00ZjU0LWExNDQtOWNhZWIxOTFmZTZh'
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    
                    $response = curl_exec($ch);
                    curl_close($ch);
                    
                    return $response;
            } else {
                        die('Incorrect user!');
            }
        }

        public static function likePost($postId, $likerId) {

                if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
                        DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
                        DB::query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
                        //Notify::createNotify("", $postId);
                } else {
                        DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
                        DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
                }

        }

        public static function dislikePost($postId, $dislikerId) { //added function for dislike

                if (!DB::query('SELECT user_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$dislikerId))) {
                        DB::query('UPDATE posts SET dislikes=dislikes+1 WHERE id=:postid', array(':postid'=>$postId));
                        DB::query('INSERT INTO post_dislikes VALUES (\'\', :postid, :userid)', array(':postid'=>$postId, ':userid'=>$dislikerId));
                        //Notify::createNotify("", $postId);
                } else {
                        DB::query('UPDATE posts SET dislikes=dislikes-1 WHERE id=:postid', array(':postid'=>$postId));
                        DB::query('DELETE FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$dislikerId));
                }

        }


        public static function getTopics($text) {

                $text = explode(" ", $text);

                $topics = "";

                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "#") {
                                $topics .= substr($word, 1).",";
                        }
                }

                return $topics;
        }

        public static function link_add($text) {

                $text = explode(" ", $text);
                $newstring = "";

                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "@") {
                                $newstring .= "<a href='profile.php?username=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
                        } else if (substr($word, 0, 1) == "#") {
                                $newstring .= "<a href='topics.php?topic=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
                        } else {
                                $newstring .= htmlspecialchars($word)." ";
                        }
                }

                return $newstring;
        }

        public static function displayPosts($userid, $username, $loggedInUserId) {
                $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
                $posts = "";

                foreach($dbposts as $p) {

                        if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {

                                $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='like' value='Like'>
                                        <span>".$p['likes']." likes</span>
                                ";
                                if ($userid == $loggedInUserId) {
                                        $posts .= "<input type='submit' name='deletepost' value='x' />";
                                }
                                $posts .= "
                                </form><hr /></br />
                                ";

                        } else {
                                $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                <input type='submit' name='unlike' value='Unlike'>
                                <span>".$p['likes']." likes</span>
                                ";
                                if ($userid == $loggedInUserId) {
                                        $posts .= "<input type='submit' name='deletepost' value='x' />";
                                }
                                $posts .= "
                                </form><hr /></br />
                                ";
                        }

                        if (!DB::query('SELECT post_id FROM post_dislikes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {

                                $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='dislike' value='Dislike'>  
                                        <span>".$p['dislikes']." dislikes</span>
                                ";
                                if ($userid == $loggedInUserId) {
                                        $posts .= "<input type='submit' name='deletepost' value='x' />";
                                }
                                $posts .= "
                                </form><hr /></br />
                                ";

                        } else {
                                $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                <input type='submit' name='undislike' value='Undislike'>
                                <span>".$p['dislikes']." dislikes</span>
                                ";
                                if ($userid == $loggedInUserId) {
                                        $posts .= "<input type='submit' name='deletepost' value='x' />";
                                }
                                $posts .= "
                                </form><hr /></br />
                                ";
                        }
                }

                return $posts;
        }

}
?>
