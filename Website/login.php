<?php

/**
 * login.php
 *
 * Layout for user login
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


require_once 'db_config.php';

$title = 'Log in';
//check if there is a logged in user
if($user->is_loggedin())
{
  //if there is ask them if they want to log out instead
  $content = '
      <h2 >Login</h2>
      <p>You are already logged in! Wanna log out?</p>
      <a href="logout.php">Logout</a>';
  }else{
    //if there is no user logged in, normally show them the login form
    $content =
      '
        <h2 >Login</h2>
      <form action="login.php" method="POST">
        <label>Username:</label> <input type="text" name="username" />
        <label>Password:</label> <input type="password" name="password" />
        <input type="submit" name="submit" value="Login">
      </form>
        <p class="loginP">Not having an account? <a href="register.php">Register</a></p>
      ';
  }
//if they submit the form
if(isset($_POST['submit']))
{
  //try to create a new session and log the user in
  //if it is successful redirect them to the index page after creating the session
  if($user->login($_POST['username'],$_POST['password']))
  {
    $user->redirect('index.php');
  }
  //else post a failure message and do not create the session
  else
  {
    $content =
      '
        <h2 >Login</h2>
        <p>Wrong Credentials! Try again</p>
      <form action="login.php" method="POST">
        <label>Username:</label> <input type="text" name="username" />
        <label>Password:</label> <input type="password" name="password" />
        <input type="submit" name="submit" value="Login">
      </form>
        <p class="loginP">Not having an account? <a href="register.php">Register</a></p>
      ';
  }
}

  require 'layout.php';
?>
