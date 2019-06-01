<?php

/**
 * commentQueries.php
 *
 * Contains all queries and functions related to commenting in general
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



  class commentQueries {
    //This class is instantiated in the db_config.php file and when the file is required by a page, the instance is also included to that page along with all its functions.
    //The constructor receives the variable that holds the PDO object so that it can perform all the queries to the database.
      private $db;
      /////////////////////////////////////////////////////////////////////////////////////////////////////////

      function __construct($DB_con)
      {
        $this->db = $DB_con;
      }
      //fetch all comments, pending and not pending from the database, order ascenting and return the array
      public function getAllPendingComments(){
        $stmt = $this->db->prepare('SELECT * FROM comments
          WHERE comment_pending = "y"
          ORDER BY comment_created_at');

          $stmt->execute();
          $comments = $stmt->fetchAll();
          return $comments;
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets instances of the User class (userQueries.class.php) and one of the Comment (comment.class.php) as arguements
      //if there is a user logged in show the option for comment reply, and if the user is an admin or the author of the comment show additionaly the option to delete the comment
      //return the options
      public function displayCommentOptions($user,$comment){
        $content = null;
        $articleQueries = new articleQueries($this->db);
        if ($user->is_loggedin()) {
          $content = '<a href="comment_reply.php?commentID='.$comment->getCommID(). '&articleID=' . $comment->getCommArticle().'">Reply</a><br>';
          if ($user->hasPermissions(1) || $comment->getCommAuthor() === $_SESSION['loggedin'] || $_SESSION['loggedin'] === $articleQueries->getArticleByID($comment->getCommArticle())->getAuthor()) {
            $content .= '<a href="deleteComment.php?commentID='.$comment->getCommID(). '&articleID=' . $comment->getCommArticle().'">Delete Comment</a><br>';
            if ($user->hasPermissions(1) || $_SESSION['loggedin'] === $articleQueries->getArticleByID($comment->getCommArticle())->getAuthor()) {
              if ($comment->isPending() === 'y') {
                $content .= '<a href="approveComments.php?commentID='.$comment->getCommID(). '&articleID=' . $comment->getCommArticle().'">Approve Comment</a>';
              }
            }
          }
        }
        return $content;
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets the criteria that the database will be querried on, if the comments to fetch are pending or not, the id of the article they reside in, and the parent comment (so the nested). If the parent is 0 then the coments are not nested
      //returns the array
    //http://www.technabled.com/2009/06/how-to-multi-level-comments-in-php.html   accessed at 14/12
    public function queryForCommentsThat($commentPending, $commentArticleID, $commentParentCommentID){

      $stmt = $this->db->prepare('SELECT c.*, u.user_name FROM comments c
        INNER JOIN users u ON c.comment_author = u.user_ID
        WHERE comment_pending = :pending
        AND comment_article = :articleID
        AND comment_parent = :parent
        ORDER BY comment_created_at DESC');

        $criteria = [
          'pending' => $commentPending,
          'articleID' => $commentArticleID,
          'parent' => $commentParentCommentID
        ];

        $stmt->execute($criteria);
        return $stmt;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //gets the article ID of which we want to display the comments for and a user instance so that the function to have access to user functions
    //fetches the articles that are approved, the ones that reside into the article that has the id provided, and that they arent nested.
    //lists the comments one by one in a variable
    //returns that variable. if there were no comments, the variable contains a message that there are not comments to display yet

    //http://www.technabled.com/2009/06/how-to-multi-level-comments-in-php.html   accessed at 14/12
    public function displayComments($articleID, $user){

      $commentList = null;
      $stmt = $this->queryForCommentsThat("n", $articleID, 0);

      if ($stmt->rowCount() > 0) {
        $results = $stmt->fetchAll();
        foreach ($results as $commentStats) { //https://stackoverflow.com/questions/43960349/uncaught-error-call-to-a-member-function-function-on-array
          $commentObj = new comment($commentStats['comment_ID'],$commentStats['comment_author'],$commentStats['comment_created_at'],$commentStats['comment_pending'],$commentStats['comment_content'],$commentStats['comment_article'],$commentStats['comment_parent']);

          $commentList = $commentList . $this->getCompleteComment($commentObj, $user);
        }
    }else {
        $commentList = '<p>No comments to display yet.</p>';
      }
        return $commentList;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //gets the author id and the user object so that the function has access to user queries and functions
    //fetches all comments that were posted by the author with the provided id
    //returns a list with all comment objects or a single message explaining that there are no comments from that author
    public function displayCommentsByAuthor($authorID, $user){

      $commentList = null;
      $stmt = $this->getCommentsByAuthor($authorID);

      if ($stmt->rowCount() > 0) {
        $results = $stmt->fetchAll();
        foreach ($results as $commentStats) { //https://stackoverflow.com/questions/43960349/uncaught-error-call-to-a-member-function-function-on-array
          $commentObj = new comment($commentStats['comment_ID'],$commentStats['comment_author'],$commentStats['comment_created_at'],$commentStats['comment_pending'],$commentStats['comment_content'],$commentStats['comment_article'],$commentStats['comment_parent']);

          $commentList = $commentList . $this->getCompleteComment($commentObj, $user);
        }
    }else {
        $commentList = '<p>No comments to display yet.</p>';
      }
        return $commentList;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //RECURSIVE FUNCTION
    //gets a comment object and a user object
    //generates HTML to form the received comment's body and all its nested comments, if any.
    //it will invoke itself until there are no other nested comments for the comment requested.
    //then returns the whole comment body in a variable
    //http://www.technabled.com/2009/06/how-to-multi-level-comments-in-php.html   accessed at 14/12
    public function getCompleteComment($commentObj, $user){
      $completeComment = '
      <li class="comment">
        <p>At: </p>
        <p>'.$commentObj->getCommDate().' </p>';


        if ($user->isDeleted($commentObj->getCommAuthor())) {
          $completeComment .= '<p>Author: <del>'.$user->getUserNameByID($commentObj->getCommAuthor()).'</del></p>';
        }else {
          $completeComment .= '<p> <a href="userProfile.php?userID=' . $commentObj->getCommAuthor() .  '">'.$user->getUserNameByID($commentObj->getCommAuthor()).'</a> wrote: </p>';
        }

        $completeComment .= '<p>'.$commentObj->getCommContent().'</p><br>
        '.$this->displayCommentOptions($user,$commentObj).'
        ';

        $queryResults = $this->queryForCommentsThat('n',$commentObj->getCommArticle(),$commentObj->getCommID());

      if ($queryResults->rowCount() > 0) {
        $comments = $queryResults->fetchAll();
        $completeComment .= '<ul>';

        foreach ($comments as $comment) {
          $commentObj = new comment($comment['comment_ID'],$comment['comment_author'],$comment['comment_created_at'],$comment['comment_pending'],$comment['comment_content'],$comment['comment_article'],$comment['comment_parent']);
          $completeComment .= $this->getCompleteComment($commentObj, $user);
        }
        $completeComment .= '</ul>';
      }
      $completeComment .= '</li>';
      return $completeComment;
}

  /////////////////////////////////////////////////////////////////////////////////////////////////////////
  //gets a comment ID
  //queries the database for the comment with that ID and fetches it
  //puts all the details of the comment in a comment object and returns the object
  public function getSingleComment($commentID){
    $stmt = $this->db->prepare('SELECT c.*, u.user_name FROM comments c
      INNER JOIN users u ON c.comment_author = u.user_ID
      WHERE comment_ID = :id
      ORDER BY comment_created_at DESC');

      $criteria = [
        'id' => $commentID
      ];

      $stmt->execute($criteria);
      $commentInfo = $stmt->fetchAll();

      foreach ($commentInfo as $comment) {
          $commentObj = new comment($comment['comment_ID'],$comment['comment_author'],$comment['comment_created_at'],$comment['comment_pending'],$comment['comment_content'],$comment['comment_article'],$comment['comment_parent']);
      }
      return $commentObj;
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////
  //gets a user id as arguement
  //fetches all the comments posted by that author
  //returns the array of comments that was fetched
  public function getCommentsByAuthor($userID){
    $stmt = $this->db->prepare('SELECT c.*, u.user_name FROM comments c
      INNER JOIN users u ON c.comment_author = u.user_ID
      WHERE comment_author = :id
      ORDER BY comment_created_at DESC');

      $criteria = [
        'id' => $userID
      ];

      $stmt->execute($criteria);
      return $stmt;
  }

      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets a comment object from which the criteria of the insert query will be determined, and a boolean expression depending if the comment will automatically be approved or not.
      //if the comment_parent was assigned null in the comment object, make sure that you make that 0
      //execute the query and insert the comment to the database
  public function addNewComment($commentObj, $approve){
    $stmtNotApprove = $this->db->prepare('INSERT INTO comments(comment_author,comment_article,comment_parent,comment_content) VALUES(:author,:article,:parent,:content)');
    $stmtApprove = $this->db->prepare('INSERT INTO comments(comment_author,comment_article,comment_parent,comment_content,comment_pending) VALUES(:author,:article,:parent,:content,"n")');

    if (is_null($commentObj->getCommParent())) {
      $commentObj->setCommParent(0);
    }

    $criteria = [
      'author' => $commentObj->getCommAuthor(),
      'article' => $commentObj->getCommArticle(),
      'parent' => $commentObj->getCommParent(),
      'content' => $commentObj->getCommContent()
    ];

    if ($approve) {
      $stmtApprove->execute($criteria);
    }else {
      $stmtNotApprove->execute($criteria);
    }
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //gets a comment ID
    //deletes the comment with the given id from the database
    public function deleteComment($commentID){
      $stmt = $this->db->prepare('DELETE FROM comments WHERE comment_ID = :id LIMIT 1');

      $criteria = [
        'id' => $commentID
      ];

      $stmt->execute($criteria);
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //gets a comment ID
    //updates the comment_pending attribute to "n" so that the comment is considered approved.
    public function approveComment($commentID){
      $stmt = $this->db->prepare('UPDATE comments SET comment_pending = "n" WHERE comment_ID= :id');

      $criteria = [
        'id' => $commentID
      ];

      $stmt->execute($criteria);
    }
    }
