<?php
/**
 * addCategory.php
 *
 * All functionalities relating to adding a new article category.
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


require_once 'db_config.php';

$title='Add Category';
$error = '';


//viewing this page requires the user to be an admin or an author
if($user->hasPermissions(2)){
  //if the submit button is pressed so that the name to be changed, check the input
  if (isset($_POST['add_cat_button'])) {
    if (empty($_POST['cat_name'])) { //if name is empty add that to the error list
      $error ='<li>Name cannot be blank.</li>';
    }elseif (strlen($_POST['cat_name']) > 20 ) { //if name is bigger than 20 characters add that to the error list
      $error .= '<li> Category name cannot be bigger than 20 characters</li>';
    }
    if ($categoryQueries->hasSameName($_POST['cat_name']) ) { //if the name that was inserted is not unique among categories, add that to the error list
      $error .= '<li> There is already a category with the same name.</li>';
    }
    //if the error list is NOT empty, display all the errors in a list, and the form again
    if ($error != '') {
      $content = '
        <h2>Add Category</h2>
        <ul #id="errorList">'.$error.'</ul>
        <p>Category names can have up to 20 characters.</p>
      <form action="addCategory.php" method="POST">
        <label>New Category Name</label> <input type="text" name="cat_name" />
        <input type="submit" name="add_cat_button" value="Create">
      </form>
      ';
    }else{ //if the error list is empty, insert the new category to the database
      $categoryQueries->insertNewCategory($_POST['cat_name']);
      //let the user know of the success
      $content = '<p>Category succesfuly added!</p>
      <p><a href="index.php">Return to home.</a></p>';
    }
  }else { //if the submit button is not pressed show the input form
    $content = '
      <h2>Add Category</h2>
      <p>Category names can have up to 20 characters.</p>
    <form action="addCategory.php" method="POST">
      <label>New Category Name</label> <input type="text" name="cat_name" />
      <input type="submit" name="add_cat_button" value="Create">
    </form>
    ';
  }
}else{ //if the user has not priviledges, do not display the page
  $content = '<h1>You are not allowed to view this page!<h1>';
}
require 'layout.php';
 ?>
