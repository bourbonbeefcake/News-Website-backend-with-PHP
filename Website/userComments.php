<?php



/**
 * userComments.php
 *
 * Displays the comments of the specified user
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */

require_once 'db_config.php';
require 'comment.class.php';

$title = 'User Comments';
$content ='';

//check if there is a user logged in and check if there is a user ID set in the $_GET superglobal
if ($user->is_loggedin() && isset($_GET['userID'])) {
  //if so, the title is username + "'s Comments"
  $title = $user->getUserNameByID($_GET['userID']). '\'s Comments';
  //content starts with the heading 2 username + "'s Comments"
  //then fetch all comments from the database and display them one by one.
  //the function handles also the case that there are no comments, in which case, displays appropriate message to the user
  $content .= '
  <h2>'.$user->getUserNameByID($_GET['userID']).'\'s Comments</h2>';
  $content .= '<div id="wrapper">' .  $commentQueries->displayCommentsByAuthor($_GET['userID'], $user) . '</div>';

}elseif(!$user->is_loggedin()){
  //check if a user is not logged in and refuse entry
  $content = '<h1>You are not allowed to view this page!<h1>';
}else {
  $content = '<h1>A problem occured. Please try again later.</h1>';
}
require 'layout.php';
 ?>
