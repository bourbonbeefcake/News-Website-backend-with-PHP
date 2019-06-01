<!--
*
* sideBar.php
*
* Layout dynamically builds the sideBar depending on what role the logged in user has.
*
* @author     Triantafyllidis Antonios
* @copyright  2017 Triantafyllidis Antonios
*
-->
<nav>
  <ul>
<?php
require_once 'db_config.php';
//this page is required by layout.php and implements the side bar.
//it has been taken care of that the sidebar is only visible when someone has logged in
   function populateSideBar($perms)
  {
    //this function populates the sidebar with the permissions that the current logged in user has.
    if (!is_null($perms)) {
      foreach ($perms as $perm) {
        echo '<li><a href="'. $perm['link'] .'">'.$perm['name'].'</a></li>';
      }
    }
  }
  //shows a welcoming text for the user, with his name which is a link to his profile
  echo '<li><a href="userProfile.php?userID='. $_SESSION['loggedin'] .'"> Welcome '. $user->getUserName() .'! </a></li><hr>';
  //gets the user's permissions and then populates the database with them
  populateSideBar($user->getPermissions());
 ?>


  </ul>
</nav>
