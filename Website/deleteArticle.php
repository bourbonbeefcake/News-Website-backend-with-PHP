<?php

/**
 * deleteArticle.php
 *
 * Layout for deleting an article 
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require_once 'db_config.php';
require 'article.class.php';
$title='Delete Article';
$content = '';


//if there is an article id set
if (isset($_GET['articleID'])) {
  //get the article with that ID
    $article = $articleQueries->getArticleByID($_GET['articleID']);
    //check if the user is either an admin or if he is the author of the article with the ID provided
  if($user->hasPermissions(1) || $article->getAuthor() === $user->getUserID()){
    //if the deletion is confirmed
    if (isset($_POST['confirm_deletion'])) {
      //delete the article from the database and let the user know that it was succesful. All comments of the article are deleted as well.
      $articleQueries->deleteArticle($article->getID());
      $content = $content . '<p>Article Deleted!</p><a href="index.php">Return to home.</a>';
      //else if the deletion is canceled
    }elseif(isset($_POST['cancel_deletion'])) {
      //redirect the user back to the article page
      $user->redirect('article.php?articleID='. $article->getID());
    }else {
      //if the user just landed to that page, show him the confirmation form
      $content = $content . '
      <h2>Are you certain? This action is irrevocable. </h2>
      <form action="deleteArticle.php?articleID='.$article->getID().'" method="POST">
      <input type="submit" name="confirm_deletion" value="Yes, delete.">
      <input type="submit" name="cancel_deletion" value="No, abort.">
    </form>';
    }
  }else{
    $content = '<h1>You are not allowed to view this page!<h1>';
  }
}else {
  $content = '<h1>A problem occured. Make sure you are not tampering with links.</h1>';
}

  require 'layout.php';
 ?>
