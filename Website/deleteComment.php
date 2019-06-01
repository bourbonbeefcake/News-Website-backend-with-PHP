<?php


/**
 * deleteComment.php
 *
 * Layout for deleting a comment
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require_once 'db_config.php';
require 'comment.class.php';
require 'article.class.php';

$content =null;
$title ='Delete Comment';

//check if a user is logged, if a comment and article ID are set in $_GET superglobal, and if the requested article excist
if ($user->is_loggedin() && isset($_GET['commentID']) && isset($_GET['articleID']) && $articleQueries->doesArticleExists($_GET['articleID'])) {
//using the IDs provided, get objects both of the article and the comment
  $commentObj = $commentQueries->getSingleComment($_GET['commentID']);
  $articleObj = $articleQueries->getArticleByID($_GET['articleID']);
//check if user is an admin or the author of the comment or the author of the article
  if ($user->hasPermissions(1) || $commentObj->getCommAuthor() === $_SESSION['loggedin'] || $articleObj->getAuthor() === $_SESSION['loggedin']) {
//then make checks to see if the deletion is confirmed
    if (isset($_POST['confirm_deletion'])) {
      //if so, delete the comment from the database and all its nested comments
      $commentQueries->deleteComment($_GET['commentID']);
      $content = $content . '<p>Comment Deleted!</p><a href="article.php?articleID='.$_GET['articleID'].'">Back to article</a>';
    }elseif (isset($_POST['cancel_deletion'])) {
      $user->redirect('article.php?articleID='.$_GET['articleID']);
    }else {
      $content = $content . '
      <h2>Are you certain? This action is irrevocable. </h2>
      <form action="deleteComment.php?commentID='.$commentObj->getCommID(). '&articleID=' . $articleObj->getID().'" method="POST">
      <input type="submit" name="confirm_deletion" value="Yes, delete.">
      <input type="submit" name="cancel_deletion" value="No, abort.">
    </form>';
    }
  }else {
    $content = '<h1>You are not allowed to view this page!<h1>';
  }
}elseif (!$user->is_loggedin()) {
  $content = '<h1>You are not allowed to view this page!<h1>';
}else {
  $content = '<h1>Either a technical problem occured or the requested article does not exist.</h1>';
}


require 'layout.php';
 ?>
