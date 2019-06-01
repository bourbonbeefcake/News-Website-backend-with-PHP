<?php
/**
 * addArticle.php
 *
 * All functionalities relating to adding a new article.
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


require_once 'db_config.php';
require 'article.class.php';

$title='Add Article';
$error = '';
$content = '';
$imageUploadOk = 1;

//https://stackoverflow.com/questions/3967515/how-to-convert-an-image-to-base64-encoding to get an image from file. Non coded.
$path = 'images/default-no-image-icon.png';
$imageData = file_get_contents($path);

if($user->hasPermissions(2)){
  if (isset($_POST['add_art_button'])) {

    if (empty($_POST['art_isVisible'])) { //because this is not implemented, it will remain checked always so that all new articles that are posted are visible
      $_POST['art_isVisible'] = 'y';
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //checks for image uploading
    //it will ONLY check the image if the user has selected one. If not it will use the default picture.
    if (!empty($_FILES['image_to_upload']['tmp_name'])) {
      $check = getimagesize($_FILES["image_to_upload"]["tmp_name"]);
      if($check !== false) {
          $imageUploadOk = 1;
      } else {
        $error .= "<li>File is not an image.</li>";
          $iamgeUploadOk = 0;
      }

      if ($_FILES["image_to_upload"]["size"] > 500000) {
        $error .= "<li>Image file can be up to 500 KB.</li>";
      $imageUploadOk = 0;
    }

    if ($imageUploadOk == 0) {
      $error .= "<li>The Image file was NOT uploaded.</li>";
    }else {
      $imageData = file_get_contents($_FILES['image_to_upload']['tmp_name']);
    }
  }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //checks for title
      if (empty($_POST['art_title'])) {
          $error .='<li>Title cannot be blank.</li>';
      }elseif (strlen($_POST['art_title']) > 40) {
          $error .= '<li> Article Title cannot be bigger than 40 characters</li>';
      }
      if ($articleQueries->hasSameName($_POST['art_title']) ) {
          $error .= '<li> There is already an article with the same name.</li>';
      }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //checks for content
    if (empty($_POST['art_content'])) {
        $error .='<li>Content cannot be blank.</li>';
    }elseif (strlen($_POST['art_content']) < 100) {
        $error .= '<li> Article content cannot be shorter than 100 characters</li>';
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//when the user has done something wrong, the contents of what he already wrote, will not be deleted.
//That would be very inconvenient for someone who took hours to write a decent article.
    if ($error !== '') {
      $content = '
        <h2>Add Article</h2>
        <ul #id="errorList">'.$error.'</ul>
        <form action="addArticle.php" method="POST" enctype="multipart/form-data">
        <label>Article Image:</label><input type="file" name="image_to_upload" />
        <label>Article Title:</label> <input type="text" name="art_title" value="'.$_POST['art_title'].'" />
        <label>Article Category:</label>';
        $content .= $categoryQueries->dropdownAllCategories();
        $content = $content . '<label>Article Content</label><textarea name="art_content">'.$_POST['art_content'].'</textarea>
        <label>Visible?</label> <input type="checkbox" name="art_isVisible" disabled checked />
        <input type="submit" name="add_art_button" value="Post" />
        </form>
      ';
    }else {
      //creating an instance of Article that will store all the information before getting inserted in the database
      $articleObj = new Article(null, $_POST['art_title'], $_SESSION['loggedin'], $_POST['art_isVisible'], null, $_POST['art_content'], $_POST['categories'], $imageData);

      $articleObj->setTitle($_POST['art_title']);
      $articleQueries->insertNewArticle($articleObj);
      $user->sendMailToSubscribedUsers($articleObj);
      $content = '<h1>Article Posted!</h1>
      <p><a href="latestArticles.php">Go to Latest Articles</a> <a href="index.php">Go Home</a></p>
      ';

    }

  }else {

    //https://www.w3schools.com/php/php_file_upload.asp for image upload
    //https://stackoverflow.com/questions/4526273/what-does-enctype-multipart-form-data-mean for knowing what enctype="multipart/form-data" means
    $content = '
      <h2>Add Article</h2>
      <form action="addArticle.php" method="POST" enctype="multipart/form-data">
      <label>Article Image:</label><input type="file" name="image_to_upload" />
      <label>Article Title:</label> <input type="text" name="art_title" />
      <label>Article Category:</label>';
      $content = $content . $categoryQueries->dropdownAllCategories();
      $content = $content . '<label>Article Content</label><textarea name="art_content"></textarea>
      <label>Visible?</label> <input type="checkbox" name="art_isVisible" disabled checked />
      <input type="submit" name="add_art_button" value="Post" />
      </form>
    ';
  }
}else{
  $content = '<h1>You are not allowed to view this page!<h1>';
}
require 'layout.php';
 ?>
