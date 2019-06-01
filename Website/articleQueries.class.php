<?php

/**
 * articleQueries.class.php
 *
 * Contains all article related functions
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



  class articleQueries {
    //this class is made to keep all article-related querries and functions in one place.
    //This class is instantiated in the db_config.php file and when the file is required by a page, the instance is also included to that page along with all its functions.
    //The constructor receives the variable that holds the PDO object so that it can perform all the queries to the database.
      private $db;
      /////////////////////////////////////////////////////////////////////////////////////////////////////////

      function __construct($DB_con)
      {
        $this->db = $DB_con;
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //receives all articles from the database
      public function getAllArticles(){
          $queryForArticles = $this->db->prepare('SELECT * FROM articles');
          $queryForArticles->execute();
          $articles = $queryForArticles->fetchAll();
          return $articles;
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //receives an article's ID as an arguement and fetches it from the database. It returns is as an object (class article)
      public function getArticleByID($id){
        $queryForArticle = $this->db->prepare('SELECT * FROM articles WHERE article_ID = :id');

        $criteria = [
          'id' => $id
        ];
        $queryForArticle->execute($criteria);
        $article = $queryForArticle->fetch();
        $articleObj = new Article($article['article_ID'],$article['article_title'],$article['article_author'],$article['article_is_visible'],$article['article_date'],$article['article_content'],$article['article_category'],$article['articlePicture']);
        return $articleObj;
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //fetches all articles from the database with descending order. The array is returned.
      public function getAllLatest(){
        $stmt = $this->db->prepare('SELECT * FROM articles
          WHERE article_is_visible = "y"
          ORDER BY article_date DESC');

        $stmt->execute();
        $articles = $stmt->fetchAll();
        return $articles;
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets the author ID as an arguement and fetches all articles posted by that author from the database. It returns the array.
      public function getArticlesByAuthor($authorID){
        $stmt = $this->db->prepare('SELECT * FROM articles
          WHERE article_author = :id
          ORDER BY article_date DESC');

          $criteria = [
            'id' => $authorID
          ];

        $stmt->execute($criteria);
        $articles = $stmt->fetchAll();
        return $articles;
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets the object of the article to display, and TRUE if the article's content must be truncated, FALSE to display full content.
      //generates the HTML to form the article entry with the appropriate data
      //returns the content generated
      public function displayArticle($articleObject,$truncateContent){

        $articleContent = $articleObject->getContent();
        $userFunctions = new User($this->db);
        $categoryFunctions = new categoryQueries($this->db);
        $contentToReturn;

        if ($truncateContent === true) {
          $articleContent = $articleObject->truncateArticle();
        }

        $category_name = $categoryFunctions->getName($articleObject->getCategory());
        $author_name = $userFunctions->getUserNameByID($articleObject->getAuthor());


          $contentToReturn = '
          <div id="latestCustom">
          <img class="articleImg" src="data:image/jpeg;base64,'. $articleObject->getPicture().'"/>
          <a href="article.php?articleID='.$articleObject->getID().'"><h2>'. $articleObject->getTitle() .  '</h2></a>
          <p>Date: ' . $articleObject->getDate() .  '</p>';

          if ($userFunctions->isDeleted($articleObject->getAuthor())) {
            $contentToReturn .= '<p>Author: <del>'.$author_name.'</del></p>';
          }else {
            $contentToReturn .= '<p>Author: <a href="userProfile.php?userID=' . $articleObject->getAuthor() .  '">'.$author_name.'</a></p>';
          }

          $contentToReturn .= '
          <p>Category: <a href="category.php?categoryID=' . $articleObject->getCategory() .  '">'.$category_name.'</a></p>
          <p>' . $articleContent .  '</p>
          <hr>
          <br>
          </div>
          ';
          return $contentToReturn;
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets an article object as an arguement and if the user logged in is an admin or the article's author, the additional priviledges will be displayed
      //it generates the HTML that will form the article enrty and returns it
      public function displayArticleOptions($article){
        $content = $this->displaySocialButtons($article->getID());
        $user = new User($this->db);
        $categoryQueries = new categoryQueries($this->db);
        if ($user->is_loggedin()) {
          $content .= '<a href="commentAnArticle.php?articleID='.$article->getID().'">Add new Comment</a><br>';
          if ($user->hasPermissions(1) || $article->getAuthor() === $_SESSION['loggedin']) {
            $content .= '<a href="deleteArticle.php?articleID='.$article->getID().'">Delete Article</a><br>';
            $content .= '
            <form action="article.php?articleID='.$article->getID().'" method="POST">
            <p>Name can have up to 40 characters maximum.</p>
              <label>New Article Name:</label> <input type="text" name="new_article_name" />
              <label>Change Category:</label>';
              $content .= $categoryQueries->dropdownAllCategories();
              $content .= '
              <label>Edit Article Content</label> <textarea name="amend_article_content">'.$article->getContent().'</textarea>
              <input type="submit" name="edit_article" value="Save Changes">
            </form>';
          }
        }else{
          $content .= '<p>You need to be logged in to post a comment.</p>';
        }

        return $content;
      }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets an article ID as an arguement and deletes that article from the database
        function deleteArticle($articleID){
          $stmt = $this->db->prepare('DELETE FROM articles WHERE article_ID = :id LIMIT 1');

          $criteria = [
            'id' => $articleID
          ];
          $stmt->execute($criteria);
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets an article object as an arguement, and updates the article with the same ID in the database with the values that the object holds.
        function editArticle($articleObject){
            $stmt = $this->db->prepare('UPDATE articles SET article_title= :title, article_category =:category, article_content= :content WHERE article_ID= :id');

          $criteria = [
            'id' => $articleObject->getID(),
            'title' => $articleObject->getTitle(),
            'category' => $articleObject->getCategory(),
            'content' => $articleObject->getContent()
          ];
          $stmt->execute($criteria);
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets an article object as an arguement, and inserts a new article to the database with the info provided in the object
        function insertNewArticle($articleObject){
          $stmt = $this->db->prepare('INSERT INTO articles (article_title, article_content, article_author, article_is_visible, article_category, articlePicture)
          VALUES(:title, :content, :author, :isVisible, :category, :picture)');

        $criteria = [
          'title' => $articleObject->getTitle(),
          'category' => $articleObject->getCategory(),
          'content' => $articleObject->getContent(),
          'author' => $articleObject->getAuthor(),
          'isVisible' => $articleObject->isVisible(),
          'picture' => $articleObject->getCodedPicture()

        ];
        $stmt->execute($criteria);
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets a string and checks if any of the articles in the database has a title same with the string.
        //returns true if so, or false otherwise.
        function hasSameName($articleName){
          $articles = $this->getAllArticles();

          foreach ($articles as $article) {
            if ($article['article_title'] === $articleName) {
              return true;
            }
          }
          return false;
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets an article ID as an arguement and checks if that article exists in the database
        //returns true if so, or false otherwise.
        function doesArticleExists($articleID){
          $stmt = $this->db->prepare('SELECT * FROM articles WHERE article_ID = :id');

          $criteria = [
            'id' => $articleID
          ];

          $stmt->execute($criteria);
          $result=$stmt->fetch();
          if (!empty($result)) {
            return true;
          }else {
            return false;
          }
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets an article ID as an arguement and generates the link for that article. The article URL then passes to each of the social sharing buttons.
        //returns the social buttons.
        //https://simplesharebuttons.com/html-share-buttons/
        function displaySocialButtons($articleID){
          $link = 'http://v.je/article.php?articleID='.$articleID.'';
          $content = '<div id="share-buttons">';
          $content .= '<a href="http://www.facebook.com/sharer.php?u='.$link.'" target="_blank">
        <img src="./images/facebook-social-icon.png" alt="Facebook" />
        </a>
        <a href="https://plus.google.com/share?url='.$link.'" target="_blank">
        <img src="./images/google-social-icon.png" alt="Google" />
        </a>
        <a href="https://twitter.com/share?url='.$link.'" target="_blank">
        <img src="./images/twitter-social-icon.png" alt="Twitter" />
        </a>';

          $content .= '</div>';

          return $content;
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets a string as an arguement, and checks if any article in the database contains that string to its title.
        //it returns the results if any.
        function searchArticles($query){
          $stmt = $this->db->prepare('SELECT * FROM articles WHERE article_title like "%'.$query.'%"');

          $stmt->execute();
          return $stmt->fetchAll();
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
}
 ?>
