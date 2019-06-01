<?php


/**
 * userArticles.php
 *
 * Displays articles of the specified user (author or admin)
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


require_once 'db_config.php';
require 'comment.class.php';
require 'article.class.php';

$content ='';

//check if the user is logged in and if there is a user ID set in the $_GET superglobal
if ($user->is_loggedin() && isset($_GET['userID'])) {
  //the title will be the user's name + "'s Articles"
  $title = $user->getUserNameByID($_GET['userID']). '\'s Articles';
  //start with a heading 2 username + "'s Articles'"
  $content .= '<h2>'.$user->getUserNameByID($_GET['userID']).'\'s Articles</h2>';
  //fetch all articles that belong to the requested user
  $articles = $articleQueries->getArticlesByAuthor($_GET['userID']);
  //if the user has posted any articles
     if(sizeof($articles) > 0){
       //loop throug the array of articles
     foreach ($articles as $article) {
       //generate html to form each article element and save each to the content
       $content .= $articleQueries->displayArticle($articleQueries->getArticleByID($article['article_ID']),true);
     }
     }else{
       //if there are no articles returned, display a message
       $content .= '
       <p>No articles to display yet.</p>';
     }

}else {
  $content = '<h1>You are not allowed to view this page!<h1>';
}
require 'layout.php';
 ?>
