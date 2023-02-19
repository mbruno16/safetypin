<?php
ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);
class Comment {
        public static function createComment($commentBody, $postId, $userId) {
                $date = date("Y-m-d G:i:s");
                $r = DB::query('SELECT user_id FROM posts WHERE id=:id', array(':id'=>$postId))[0]['user_id'];

                if (strlen($commentBody) > 160 || strlen($commentBody) < 1) {
                        die('Incorrect length!');
                }

                if (!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
                        echo 'Invalid post ID';
                } 
                else {
                        DB::query('INSERT INTO comments VALUES (\'\', :comment, :userid, :receiver, :posted_at, :postid)', array(':comment'=>$commentBody, ':userid'=>$userId, ':receiver'=>$r, ':posted_at'=>$date, ':postid'=>$postId));
                }

        }

        public static function displayComments($postId) {

                $comments = DB::query('SELECT comments.comment, users.fullname FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array(':postid'=>$postId));
                foreach($comments as $comment) {
                        echo $comment['comment']." ~ ".$comment['fullname']."<hr />";
                }
        }
}
?>
