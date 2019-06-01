<?php

/**
 * latestArticles.php
 *
 * Layout for page that shows all latest articles
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */

require_once 'db_config.php';
require 'article.class.php';


$title = 'Latest Articles';
$content = '<h2>Latest Articles</h2><hr>';

//get all the latest articles
$articles = $articleQueries->getAllLatest();
//loop through the array retrieved
    foreach ($articles as $article) {
    //make a list of all the articles in the array by generating html for each
      $content .= $articleQueries->displayArticle($articleQueries->getArticleByID($article['article_ID']), true);
    }


require 'layout.php';
 ?>
