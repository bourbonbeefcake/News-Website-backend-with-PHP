<?php

/**
 * commentReply.php
 *
 * Layout for the area where a user can write a reply comment to another comment
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require_once 'db_config.php';
require 'comment.class.php';

$content =null;
$title ='Reply to Comment';

//if there is a user logged in, the article and comment id is set in $_GET superglobal
if ($user->is_loggedin() && isset($_GET['commentID']) && isset($_GET['articleID'])) {
  //if the submit button is not pressed to post a comment then show the user the comment that he is commenting on and the text area to write his comment on
  if (!isset($_POST['post_comment'])) {
      $commentObj = $commentQueries->getSingleComment($_GET['commentID']);
      $content .=  '
      <h2>Reply to Comment</h2>
      <div id="wrapper">
      <ul>
      <li class="comment">
      <p>At: </p>
      <p>'.$commentObj->getCommDate().' </p>
      <p> <a href="userProfile.php?userID=' . $commentObj->getCommAuthor() .  '">'.$user->getUserNameByID($commentObj->getCommAuthor()).'</a> wrote: </p>
      <p>'.$commentObj->getCommContent().'</p><br></li></ul></div>';
      $content.=  '
      <form action="comment_reply.php?commentID='.$_GET['commentID']. '&articleID=' . $_GET['articleID'] . '" method="POST">
      <label>Reply</label> <textarea name="comment_body"></textarea>
      <input type="submit" name="post_comment" value="Post">
    </form>';
}else { //if the submit button is pressed
  //check that the text inserted is at least 1 character long. if it is not, then show the error and not post the comment
  if (strlen($_POST['comment_body']) < 1) {
    $content .= 'Sorry, need to write something in the text area.';
  }else {
    //if the text is longer than 1 character then create an object with all the relevant data and the text that the user wrote as content.
    $newCommentObj = new comment(null,$_SESSION['loggedin'],null,null,$_POST['comment_body'],$_GET['articleID'],$_GET['commentID']);
    //if the user is an admin or an author approve the comment instantly
    if ($user->hasPermissions(2)) {
      $commentQueries->addNewComment($newCommentObj,true);
      $content .= '<p>Comment Posted!</p><a href="article.php?articleID='.$_GET['articleID'].'">Back to article</a>';
    }else { //otherwise, insert it to the database but make it not visible until one of the staff approves it
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
