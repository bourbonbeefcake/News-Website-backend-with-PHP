<?php

/**
 * manageArticles.php
 *
 * Layout for listing all articles and providing UI for management functionalities based on the user priviledges
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */

require_once 'db_config.php';
require 'article.class.php';

$title='Manage Articles';

//if the user is an admin or author
if($user->hasPermissions(2)){
  //separatelly check for the admin, and show him all the articles in he database
  if ($user->getUserRole() === 1) {
    $articles = $articleQueries->getAllArticles();
    //separatelly check for the author and show him only HIS articles
  }elseif($user->getUserRole() === 2) {
    $articles = $articleQueries->getArticlesByAuthor($_SESSION['loggedin']);
  }

//generate HTML to form the table with all the records
    $content = '
      <h2>Manage Articles</h2>
      <table>
      <tr>
      <th>Article ID</th><th> Article Title </th><th> Article Date </th><th> Article Author </th><th> Is Visible? </th><th> Article Category </th>
      </tr><tr>';
      foreach ($articles as $article) {
        $articleObject = $articleQueries->getArticleByID($article['article_ID']);
        $content = $content . '<td>'.
          $articleObject->getID() .' </td>
          <td>'.$articleObject->getTitle() .'</td>
          <td> '. $articleObject->getDate() .' </td>
           <td><a href="userProfile.php?userID=' . $articleObject->getAuthor() .  '">'.$user->getUserNameByID($articleObject->getAuthor()).'</a></td>
           <td> ' . $articleObject->isVisible(). '</td>
           <td><a href="category.php?categoryID=' . $articleObject->getCategory() .  '">'.$categoryQueries->getName($articleObject->getCategory()).'</a></td>
           <td><a href="article.php?articleID=' . $articleObject->getID() .  '">View Details</a></td>
           </tr>';
      }
    $content = $content .'
    </table>
    ';
  }
else{
  $content = '<h1>You are not allowed to view this page!<h1>';
}
require 'layout.php';
 ?>
