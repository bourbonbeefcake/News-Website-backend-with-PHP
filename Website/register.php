<?php

/**
 * register.php
 *
 * Layout for user registration
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



    require 'db_config.php';

  $title = 'Register';
  $error = '';
if (!$user->is_loggedin()) {
  //if the user submits the form
  if(isset($_POST['submit'])){
//loop through all the values inserted
    foreach ($_POST as $key => $value) {
      if (empty($_POST[$key])) {
        //check them ALL and none of them must be empty.
        //if any of them pass the error text into the error array
        $error .='<li>' . ucfirst($key). ' is required.</li>';
      }
    }
    //preg_match pattern is set to accept a String that starts with any letter of the alphabet, continue with any letters or numbers, be longer than 1 character long and shorter than 20.
    if (!preg_match("/^[A-Za-z][A-Za-z0-9]{1,19}$/",$_POST['username'])) {
        $error .= '<li> Name must start with a letter and contain only letters and numbers. Minimum 2 character and maximum 20</li>';
    }
    //confirm password field must match the password in the password field
    if ($_POST['password'] !== $_POST['confirmPassword']) {
      $error .= '<li> Password is not the same in both fields!</li>';
    }
    //password must be smaller than 16 characters and biger than 6
    if ((strlen($_POST['password']) < 6) || (strlen($_POST['password']) > 16)) {
      $error .= '<li> Passwords can be 6 characters at the minimum and 16 at the maximum.</li>';
    }
    //email must be a valid email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $error .= '<li> Invalid Email Format</li>';
    }
    //user name must be unique
    if ($user->hasSameName($_POST['username'])) {
      $error .= '<li> Please use a different name as this already exists!</li>';
    }
    //email must be unique
    if ($user->hasSameEmail($_POST['email'])) {
      $error .= '<li> Please use a different email address as this already exists!</li>';
    }

    //making sure that the checkbox will return "off" value to the database when it is null and not throw an exception
    if (!isset($_POST['newsletter'])) {
      $_POST['newsletter'] = 'off';
    }
    //if there was a single error do not amend the database, and post the errors that are contained in the array
    if ($error != '') {
      $content = '
        <h2 >Register</h2>
        <ul #id="errorList">'.$error.'</ul>
      <form action="register.php" method="POST">
        <label>*Username:</label> <input type="text" name="username" />
        <label>*Password:</label> <input type="password" name="password" />
        <label>*Confirm Password:</label> <input type="password" name="confirmPassword" />
        <label>*Email:</label> <input type="text" name="email" />
        <label>I would like to be informed when a new article is posted</label> <input type="checkbox" name="newsletter"/>
        <input type="submit" name="submit" value="Register">
      </form>';
    }else{
      //no errors, so ammend the database by inserting the new user
      $user->register($_POST['username'],$_POST['password'],$_POST['email'],$_POST['newsletter']);
      $content = '<p>You have succesfully registered! Now you can <a href="login.php">login</a>!</p>';
    }


  }else{
//when the user visits the page, show him the register form
    $content = '
      <h2 >Register</h2>
      <p>All mandatory fields are indicated with *</p>
    <form action="register.php" method="POST">
      <label>*Username:</label> <input type="text" name="username" />
      <label>*Password:</label> <input type="password" name="password" />
      <label>*Confirm Password:</label> <input type="password" name="confirmPassword" />
      <label>*Email:</label> <input type="text" name="email" />
      <label>I would like to be informed when a new article is posted</label> <input type="checkbox" name="newsletter"/>
      <input type="submit" name="submit" value="Register">
    </form>
    ';}
}else {
  $content = '<h1>You are already logged in!</h1>';
}

    //finally require the layout file
    require 'layout.php';
?>
