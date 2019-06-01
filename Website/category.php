<?php
/**
 * category.php
 *
 * Category layout
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require 'db_config.php';
require 'article.class.php';

$content = '';
$title ='';


//if a category ID is set in $_GET superglobal
if(isset($_GET['categoryID'])){
//get the articles of that category from the database
$articles = $categoryQueries->getItsArticles($_GET['categoryID']);

$content .= '<h2>Articles in: '. $categoryQueries->getName($_GET['categoryID']) . '</h2><hr>';
  //if there are articles in the category
   if(sizeof($articles) > 0){
     //loop through the array
   foreach ($articles as $article) {
     //generate HTML dynamically for each article and store it in content
     $content .= $articleQueries->displayArticle($articleQueries->getArticleByID($article['article_ID']),true);
   }
   }else{
     $content = '
     <h1>Nothing to display here yet<h1>';
   }
 }

  $title = 'Category: ' . $categoryQueries->getName($_GET['categoryID']);


require 'layout.php';
 ?>
