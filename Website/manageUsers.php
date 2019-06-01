<?php

/**
 * manageUsers.php
 *
 * Layout for listing all users and providing UI for management functionalities based on the user priviledges
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


require_once 'db_config.php';

$title='Manage User Accounts';
//if the user that visits is the admin
if($user->hasPermissions(1)){
    //fetch all users
    $users = $user->getAllUserAccounts();
//tried to reverse sha1 but found out that it is impossible
//https://stackoverflow.com/questions/2235079/is-it-possible-to-reverse-a-sha1


//generate HTML to form the table that lists the user records
    $content = '
      <h2 >Manage Users</h2>
      <table>
      <tr>
      <th> User ID </th><th> User Name </th><th> User Email </th><th> On Newsletter? </th><th> User Role </th><th>Is Deleted</th>
      </tr><tr>';
      foreach ($users as $value) {
        $content = $content . '<td>'.
          $value['user_ID'] .' </td>
          <td>'. $value['user_name'] .'</td>
          <td>'.$value['user_email'] .'</td>
          <td> '. $value['on_newsletter'] .' </td>
           <td>'. $user->getUserRoleByNumber($value['user_role']) .'</td>
           <td>'. $user->isDeleted($value['user_ID']). '</td>
           <td> <a href="userProfile.php?userID='.$value["user_ID"].'">Profile</a>
           </tr>';
      }
    $content = $content .'
    </table>
    ';
  }
else{
  $content = '<h1>You are not allowed to view this page!<h1>';
}
require 'layout.php';
 ?>
