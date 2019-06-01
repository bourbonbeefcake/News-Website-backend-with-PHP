<?php


/**
 * logout.php
 *
 * Layout for user logout
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */

require_once 'db_config.php';

$title = 'Log Out';

//destroy and unset the user's session
$user->logout();

$content = '
<h2 >Logout</h2>
<p> You are now logged out</p>
<a href="login.php">Login</a><br>
<a href="index.php">Back to home</a>
';

require 'layout.php';
?>
