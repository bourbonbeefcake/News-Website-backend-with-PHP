<?php

/**
 * article.php
 *
 * Article layout
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require_once 'db_config.php';
require 'article.class.php';
require 'comment.class.php';

$content ='';
$title ='Articles: ';

//gets all the article IDs that are in the database and stores them in an array
$queryForNumOfArticles = $pdo->prepare('SELECT article_ID FROM articles');
$queryForNumOfArticles->execute();

//checks if there is an article with the selected ID in the database and if the $_GET['articleID'] is set. Only then it will try to fetch the requested article
if(isset($_GET['articleID']) && $articleQueries->doesArticleExists($_GET['articleID'])){

  $articleObj = $articleQueries->getArticleByID($_GET['articleID']);

  $title .= $articleObj->getTitle();

  $content .= '<article>';
   $content .= $articleQueries->displayArticle($articleObj, false); //true to truncate article content, false to show full content.

   $content .= $articleQueries->displayArticleOptions($articleObj); //display the article options for the current user depending on his role


   $content .= '<div id="wrapper">' .  $commentQueries->displayComments($articleObj->getID(), $user) . '</div>';
$content .= '</article>';

if (isset($_POST['edit_article'])) {
  if($user->is_loggedin()){
    // if the logged user has admin role, or if he is the author of the article
    if ($user->hasPermissions(1) || $articleObj->getAuthor() === $_SESSION['loggedin']) {
      if ($_POST['new_article_name'] !== '') {
        if (strlen($_POST['new_article_name']) <= 40) {
          $articleObj->setTitle($_POST['new_article_name']);
        }
      }
      if ($_POST['categories'] !== $articleObj->getCategory()) {
        $articleObj->setCategory($_POST['categories']);
      }
      if ($_POST['amend_article_content'] !== '') {
        $articleObj->setContent($_POST['amend_article_content']);
      }
      $articleQueries->editArticle($articleObj);
      $user->redirect('article.php?articleID='.$articleObj->getID().'');
    }
  }
}

}else if(!$articleQueries->doesArticleExists($_GET['articleID'])){
    $content = '<h1>The article you requested does not exist</h1>';
}else{
  $content = '<h1> Something went wrong. Please try again.</h1>';
}
  require 'layout.php';

 ?>
