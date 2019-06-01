<!--
*
* navbar.php
*
* this file implements the navigation bar and its menus.
* the categories menu queries the database about categories and lists them
* for the login menu, a check was implemented. If a user is logged in the link becomes "Logout". If no one is logged in the link becomes "Login". Respectively, each link redirects to the relevant page.
*
* @author     Triantafyllidis Antonios
* @copyright  2017 Triantafyllidis Antonios
*
-->
<nav>
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="latestArticles.php">Latest Articles</a></li>
    <li><a href="#">Select Category</a>
      <ul>

<?php

require_once 'db_config.php';


$results = $categoryQueries->getAllCategories();

foreach ($results as $key => $row) {
echo '<li><a href="category.php?categoryID='. $row['category_ID'] .'">' . $row['category_name'] . '</a></li>';
}
  ?>
      </ul>
    </li>
    <li><a href="contact.php">Contact us</a></li>
  </ul>

  <?php if(isset($_SESSION['loggedin'])){
    echo'<a href="logout.php">Logout</a>';
  }else {
    echo'<a href="login.php">Login</a>';
  } ?>
</nav>
