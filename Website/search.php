<?php

/**
 * search.php
 *
 * Executes functions and displays articles that match the string given in the search box
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


require 'db_config.php';
require 'article.class.php';

$title ='Search for Articles';
$content = '<h2>Search results</h2><hr>';

//if a search query was submitted in the search box
if (isset($_POST['search_box'])) {
  //search for a title in the articles that contains the string entered
  $articles = $articleQueries->searchArticles($_POST['search_box']);

  if (!empty($articles)) {
    //if there were articles returned, list them all
    foreach ($articles as $article) {

      $content .= $articleQueries->displayArticle($articleQueries->getArticleByID($article['article_ID']), true);
    }
  }else {
    //if no articles were returned, let the user know
    $content = '<h1>There were no results for your query. Please try again.</h1>';
  }
}
require 'layout.php';
 ?>
