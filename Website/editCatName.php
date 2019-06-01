<?php



/**
 * editCatName.php
 *
 * Layout for editing a category name
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */




require_once 'db_config.php';

$title='Edit Category Name';
$error = '';

//check if usre is admin
if($user->hasPermissions(1)){
  //check if the submit button was pressed
  if (isset($_POST['edit_cat_name'])) {
    //make checks and not make changes if the text field is blank of if the name is bigger than 20 characters. the name also must be unique
    if (empty($_POST['cat_new_name'])) {
      $error ='<li>Name cannot be blank.</li>';
    }elseif (strlen($_POST['cat_new_name']) > 20 ) {
      $error = $error . '<li> Category name cannot be bigger than 20 characters</li>';
    }
    if ($categoryQueries->hasSameName($_POST['cat_new_name']) ) {
      $error = $error . '<li> There is already a category with the same name.</li>';
    }
//if there are any errors, show them to the user
    if ($error != '') {
      $content = '
        <h2>Edit Category Name</h2>
        <ul #id="errorList">'.$error.'</ul>
        <p>Category names can have up to 20 characters.</p>
      <form action="editCatName.php" method="POST">
        <label>Select which category you would like to rename: </label>';
        $content = $content . $categoryQueries->dropdownAllCategories();
        $content = $content .'
        <label>Insert New Name</label> <input type="text" name="cat_new_name" />
        <input type="submit" name="edit_cat_name" value="Change">
      </form>
      ';
    }else{ //if everything is fine, update the database record of the category to the new name
      $categoryQueries->editCategoryName((int)$_POST['categories'],$_POST['cat_new_name']);
      $content = '<p>Category name succesfuly changed!</p>
      <p><a href="index.php">Return to home.</a></p>';
    }
  }else { //show the form to change name when the user first visits the site
    //https://www.w3schools.com/php/php_file_upload.asp for image upload
    $content = '
      <h2>Edit Category Name</h2>
    <form action="editCatName.php" method="POST">
    <label>Select which category you would like to rename: </label>';
    $content = $content . $categoryQueries->dropdownAllCategories();
    $content = $content .'
    <label>Insert New Name</label> <input type="text" name="cat_new_name" />
    <input type="submit" name="edit_cat_name" value="Change">
  </form>
  ';
  }
}else{
  $content = '<h1>You are not allowed to view this page!<h1>';
}
require 'layout.php';
 ?>
