<?php

/**
 * restoreUser.php
 *
 * When the resoration of a soft deleted user is issued, it links to this page in order to execute the appropriate function
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


  require_once 'db_config.php';


  $title = 'Restore User';
  $content = '';
//check if the user visiting is an admin
  if ($user->hasPermissions(1)) {
    //if yes, restore the user by changing the "is_deleted" attribute to "n"
    $user->restoreUser($_GET['userID']);
    $content = '<h1>User restored!</h1>
    <p><a href="index.php">Back home</a></p>
    <p><a href="userProfile.php?userID='.$_GET['userID'].'">Back to restored user profile</a></p>';

  }else {
    $content = '<h1>You are not allowed to view this page!<h1>';
  }
require 'layout.php';
 ?>
