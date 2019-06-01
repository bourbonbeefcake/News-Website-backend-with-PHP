<?php


/**
 * deleteUser.php
 *
 * Layout for deleting a user (soft delete)
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require_once 'db_config.php';

$title='Delete User';
$content = '';


//check if the ID for the user to delete is set and if the requested user exist
if (isset($_GET['userID']) && $user->doesUserExist($_GET['userID'])) {
  //check if the current user is an admin OR if the account that is trying to delete is his own
  if($user->hasPermissions(1) || $_GET['userID'] === $user->getUserID()){
    //if he confirms deletion
    if (isset($_POST['confirm_deletion'])) {
      $user->deleteUser($_GET['userID']); //delete the user
      //if a logged in user deletes their own account, log them out
      if ($_GET['userID'] === $user->getUserID()) {
        $user->redirect('logout.php');
      }

      $content .= '<p>This account has been deleted. To restore, please contant the administration.</p><a href="index.php">Return to home.</a>';
      //if deletion is canceled, redirect back to the user profile
    }elseif(isset($_POST['cancel_deletion'])) {
      $user->redirect('userProfile.php?userID='.$_GET['userID']);
    }else { //show the confirmation form if non of the buttons is set
      $content .= '
      <h2>Are you certain? </h2>
      <form action="deleteUser.php?userID='.$_GET['userID'].'" method="POST">
      <input type="submit" name="confirm_deletion" value="Yes, delete.">
      <input type="submit" name="cancel_deletion" value="No, abort.">
    </form>';
    }
  }else{ //show this if the user does not permissions to view the page
    $content = '<h1>You are not allowed to view this page!<h1>';
  }
}else { //show this if the $_GET['userID'] is not set or if the requested user does not exist
  $content = '<h1>Either a technical problem occured or the requested user does not exist.</h1>';
}

  require 'layout.php';
 ?>
