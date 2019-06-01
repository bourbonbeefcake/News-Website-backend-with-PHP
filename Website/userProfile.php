<?php


/**
 * userProfile.php
 *
 * Displays the profile of the specified user
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */

require_once 'db_config.php';

$content ='';
$title ='';


//function that will return a disabled checked checkbox if the parameter is 'on' and a disabled not-checked checkbox in any other case
function chkboxForNewsletter($state)
{
  if ($state === 'on') {
    return '<input type="checkbox" name="newsletter" value="on" checked/>';
  }else {
    return '<input type="checkbox" name="newsletter"/>';
  }
}

//check if there is a user logged in and check if there is a user ID set in the $_GET superglobal
if(isset($_GET['userID']) && isset($_SESSION['loggedin'])){
  //check if the user is not deleted
  if (!$user->isDeleted($_GET['userID'])) {
    //only then fetch all his details
    $details = $user->getUserDetailsByID($_GET['userID']);
    //loop through the details
    foreach ($details as $attribute) {
      //set the title of the page as the user's name
      $title = $attribute['user_name'];
      //heading as well
      $content = '<h2 >User Profile: '. $attribute['user_name'] .'</h2>';
      //if the user is either an admin or an author, show the link that will list all their articles and the link that will list all their comments
      if ($attribute['user_role'] === '1' || $attribute['user_role'] === '2') {
        $content .= '<a href="userArticles.php?userID='.$_GET['userID'].'">User Articles</a>
        &nbsp;
        <a href="userComments.php?userID='.$_GET['userID'].'">User Comments</a>';
      }else { //if this is a simple user only show the link to access the comments only since a simple user cannot have articles
        $content .= '<a href="userComments.php?userID='.$_GET['userID'].'">User Comments</a>';
      }
      //if the user is an admin or the owner of this profile
      if ($user->hasPermissions(1)|| $_SESSION['loggedin'] === $_GET['userID']) {
        //show the option to delete this account
        $content .= '&nbsp;&nbsp;<a href="deleteUser.php?userID='.$_GET['userID'].'">Delete User</a>';
      }
      //show the button to submit changes to that user
      $content .='<form action="editUserDetails.php?userID='.$_GET['userID'].'" method="POST">';
      //check again if the current user is an admin or the profile's owner
      if ($user->hasPermissions(1) || $_SESSION['loggedin'] === $_GET['userID']) {
        //show the text box that allows to insert a different name
        $content .='<label>Change name: </label><input type="text" name="change_name" />';
      }
      //show the current mail of this user
      $content .= '<label>User Email: ' . $attribute['user_email'] .'</label>';
      //check again for user authorization
      if ($user->hasPermissions(1) || $_SESSION['loggedin'] === $_GET['userID']) {
        //also the field that allows the input of a different email
        //and the newsletter checkbox
        $content .='
        <label>Change Email: </label><input type="text" name="email_change"/>
        <label>On Newletter?: </label>'.chkboxForNewsletter($attribute['on_newsletter']).'';
      }
      //show the uer's role
        $content .='<label>User Role: '.  $user->getUserRoleByNumber($attribute['user_role']).'</label>';
        //additionally if the user that views the profile is an admin, show the dropdown menu with available roles to change the role
      if ($user->hasPermissions(1)) {
        $content.='<select name="roles">';
        $results = $user->getAllRoles();
        foreach ($results as $row) {
        $content .= '<option value="'.$row['role_id'].'">' . $row['role_name'] . '</option>';
        }
        $content.='</select>';
      }
      //check for user authorization to create the submit button
      if ($user->hasPermissions(1) || $_SESSION['loggedin'] === $_GET['userID']) {

        $content .= '
        <input type="submit" name="save_prof_changes" value="Save Changes">
        ';
      }
      $content .= '</form>';
    }
  }else {
    //if user is deleted, show the relevant message, and if the admin is seeing this, show him the link that will redirect him to the restore page, to restore this user
    $title = 'User Deleted';
    $content = '<h1>This user is deleted.</h1>';
    if ($user->hasPermissions(1)) {
      $content .= '<p>Need to restore? <a href="restoreUser.php?userID='.$_GET['userID'].'">Restore</a></p>';
    }
  }

}else {
  $title = 'Error';
  $content = '<h1>You need to login to view other user profiles!</h1>';
}
require 'layout.php';
 ?>
