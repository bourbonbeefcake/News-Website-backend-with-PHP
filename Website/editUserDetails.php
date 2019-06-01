<?php


/**
 * editUserDetails.php
 *
 * Layout for editing a user's account details
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require_once 'db_config.php';

$content ='';
$title ='Edit User Details';
$error = '';
//check if the button to amend the changes to the database is pressed
if (isset($_POST['save_prof_changes'])) {
//if it is check if the user is the admin or the owner of the profile he tries to amend to
  if ($user->hasPermissions(1) || $user->getUserID() === $_GET['userID']) {

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//checks for user name
//if the username is not empty, make the following checks: User name must be smaller than 20 characters, contain only letters and numbers, start with a letter, be unique
    if (!empty($_POST['change_name'])) {
      if (strlen($_POST['change_name']) > 20 ) {
          $error .= '<li> Name cannot be bigger than 20 characters</li>';
        }
      if (!preg_match("/^[A-Za-z][A-Za-z0-9]{1,21}$/",$_POST['change_name'])) {
          $error .= '<li> Name must start with a letter and contain only letters and numbers.</li>';
      }
      if ($user->hasSameName($_POST['change_name'])) {
        $error .= '<li> Please use a different name as this already exists!</li>';
      }
    }else {
      //if the username field is empty, keep the username that the user already has
      $_POST['change_name'] = $user->getUserNameByID($_GET['userID']);
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//checks for email
//if the username is not empty make the following checks: email must be unique and follow the email pattern
    if (!empty($_POST['email_change'])) {
      if (!filter_var($_POST['email_change'], FILTER_VALIDATE_EMAIL)) {
        $error .= '<li> Invalid Email Format</li>';
      }
      if ($user->hasSameEmail($_POST['email_change'])) {
        $error .= '<li> Please use a different email address as this already exists!</li>';
      }
    }else {
      //if the email field is empty, keep the email that the user already has
      $_POST['email_change'] = $user->getUserEmailByID($_GET['userID']);
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//if there are any errors let the user know
    if ($error != '') {
      $content = '
        <h2>Edit User Details</h2>
        <ul #id="errorList">'.$error.'</ul>
        <a href="userProfile.php?userID='.$_GET['userID'].'">Return to profile</a>
        ';
    }else{
      //if everything is fine, update the username and email to the content of the POST fields in superglobal
      $user->changeName($_POST['change_name'], $_GET['userID']);
      $user->changeEmail($_POST['email_change'], $_GET['userID']);
      //if the user has selected the checkbox for newsletter
      if (isset($_POST['newsletter'])) {
        //put the "on" value to the relevant attribute in the database
        $user->changeNewsletter('on',$_GET['userID']);
      }else {
        //if it was not selected, then put the "off" value in the relevant attribute field in tha database
        $user->changeNewsletter('off',$_GET['userID']);
      }
      //additionally check if the user is an admin, and give the option for him to change the user's role as well
      if ($user->hasPermissions(1)) {
        $user->changeRole((int)$_POST['roles'], $_GET['userID']);
      }

      $content = '<h1>Profile succesfuly updated!</h1>
      <a href="userProfile.php?userID='.$_GET['userID'].'">Back to Profile</a><br>
      <a href="index.php">Back to home</a>';

    }
  }else {
    $content = '<h1>You are not allowed to view this page!<h1>';
  }
}else {
  $content = '<h1>User not found. No changes were made.<h1>';
}


require 'layout.php';
 ?>
