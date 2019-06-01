<?php
/**
 * approveComments.php
 *
 * Page that shows all pending comments and allows for rejection or approval
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


require_once 'db_config.php';
require 'comment.class.php';
require 'article.class.php';

$content ='<h2>Approve Comments</h2>';
$title ='Approve Comments';

  //get all pending comments from the database
    $comments = $commentQueries->getAllPendingComments();
    //loop through the array of comments returned from the db
    foreach ($comments as $comment) {
      //get each comment as an object with all its details
      $commentObj = $commentQueries->getSingleComment($comment['comment_ID']);
      //get the article that the current comment belongs to
      $article = $articleQueries->getArticleByID($commentObj->getCommArticle());
      //check for user permissions, only authors or higher allowed
      if ($user->is_loggedin() && $user->hasPermissions(2)) {
        //if the user logged in is the author of the article of the comment in check
        if($_SESSION['loggedin'] === $article->getAuthor()){
          //if a comment ID is set in $_GET superglobal
          if (isset($_GET['commentID'])) {
            //approve it
            $commentQueries->approveComment($commentObj->getCommID());
            //unset the ID so that when the page reloads it wont try again to delete a comment that doesnt exist
            unset($_GET['commentID']);
            //and then reload the page
            $user->redirect('approveComments.php');
          }else { //if there is no comment ID set, display the comment and its options
            $content = $content .'
              <div id="wrapper">
              <ul>
              <li class="comment">
                <p>At: </p>
                <p>'.$commentObj->getCommDate().' </p>
                <p> <a href="userProfile.php?userID=' . $commentObj->getCommAuthor() .  '">'.$user->getUserNameByID($commentObj->getCommAuthor()).'</a> wrote: </p>
                <p>'.$commentObj->getCommContent().'</p><br>
                <p>In article: <a href="article.php?articleID='.$article->getID().'">'.$article->getTitle().'</a></p>
                '.$commentQueries->displayCommentOptions($user,$commentObj).'
                </ul>
                </div>';
          }

      }
    }else {
      $content = '<h1>You are not allowed to view this page.</h1>';
    }
  }
  require 'layout.php';
 ?>
