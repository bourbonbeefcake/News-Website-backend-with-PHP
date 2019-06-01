<?php


/**
 * deleteCategory.php
 *
 * Layout for deleting a category
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



require_once 'db_config.php';

$title='Delete Category';
$content = '';

//check if the user is an admin
if($user->hasPermissions(1)){
  //if the submit button for category deletion pressed
  if (isset($_POST['delete_cat_button'])) {
    //keep the category selection from the dropdown menu into $_SESSION superglobal, because the page is going to reload more than once and the $_POST data will be lost
      $_SESSION['category_to_delete'] = (int) $_POST['categories'];
//show the user the confirmation form
      $content = '
      <h2>Are you certain? This action is irrevocable. </h2>
      <form action="deleteCategory.php" method="POST">
      <input type="submit" name="confirm_deletion" value="Yes, delete.">
      <input type="submit" name="cancel_deletion" value="No, abort.">
    </form>';
    }else {
      //else show the user the form to select a category to delete
      //https://www.w3schools.com/php/php_file_upload.asp for image upload
      $content = '
        <h2>Delete Category</h2>
        <form action="deleteCategory.php" method="POST">
        <label>Select which category you would like to delete: </label>';

        $content .= $categoryQueries->dropdownAllCategories();
        $content .= '<input type="submit" name="delete_cat_button" value="Delete">
      </form>
      ';
    }
    //if deletion is confirmed, delete the category from the database. all articles are deleted as well.
    if (isset($_POST['confirm_deletion'])) {
      $categoryQueries->deleteCategory($_SESSION['category_to_delete']);
      $content = '<p>Category deleted!</p><a href="deleteCategory.php">Go back</a>';
      unset($_SESSION['category_to_delete']);
      //else if the deletion is canceled, redirect the user to the previous page
    }elseif (isset($_POST['cancel_deletion'])) {
      unset($_SESSION['category_to_delete']);
      $user->redirect('deleteCategory.php');
    }
    //unset the $_SESSION variable that held the ID of the category in any case
}else{
  $content = '<h1>You are not allowed to view this page!<h1>';
}
require 'layout.php';
 ?>
