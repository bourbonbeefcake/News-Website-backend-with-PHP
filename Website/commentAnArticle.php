<?php

/**
 * commentAnArticle.php
 *
 * Layout for the area where a user can write a comment to an article
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require_once 'db_config.php';
require 'comment.class.php';
require 'article.class.php';

$content =null;
$title ='Add New Comment';
//check if a user is logged in and if there is an article ID set in the $_GET superglobal
if ($user->is_loggedin() && isset($_GET['articleID'])) {
  //if the submit button is not pressed, show the user the text area to write the comment in
  if (!isset($_POST['post_comment'])) {
    $content = $content .  '
    <h2>Comment Article</h2>
    <form action="commentAnArticle.php?articleID='.$_GET['articleID'].'" method="POST">
    <label>Reply</label> <textarea name="comment_body"></textarea>
    <input type="submit" name="post_comment" value="Post">
  </form>';
}else { //else check if the text that the user wrote is at least 1 character long. If not show the error and do not insert the comment
      if (strlen($_POST['comment_body']) < 1) {
        $content = $content . 'Sorry, need to write something in the text area.';
      }else {
        //if the text is longer than 1 character then create an object with all the relevant data and the text that the user wrote as content.
        $newCommentObj = new comment(null,$_SESSION['loggedin'],null,null,$_POST['comment_body'],$_GET['articleID'],null);
          //if the user is an admin or an author approve the comment instantly
        if ($user->hasPermissions(2)) {
          $commentQueries->addNewComment($newCommentObj,true);
          $content .= '<p>Comment Posted!</p><a href="article.php?articleID='.$_GET['articleID'].'">Back to article</a>';
        }else {
          //otherwise, insert it to the database but make it not visible until one of the staff approves it
          $commentQueries->addNewComment($newCommentObj,false);
          $content .= '<p>Comment posted! It will be visible as soon as the admins approve it. Thank you.</p><a href="article.php?articleID='.$_GET['articleID'].'">Back to article</a>';
        }

      }
}
}elseif(!$user->is_loggedin()){
  $content = '<h1>You need to be logged in to post a comment!</h1>';
}else {
    $content = '<h1>Something went wrong, please try again.</h1>';
}

require 'layout.php';
 ?>
