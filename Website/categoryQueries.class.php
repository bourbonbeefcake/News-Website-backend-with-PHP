<?php

/**
 * categoryQueries.class.php
 *
 * Contains all category related functions
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



  class categoryQueries {
    //this class is made to keep all category-related querries and functions in one place.
    //This class is instantiated in the db_config.php file and when the file is required by a page, the instance is also included to that page along with all its functions.
    //The constructor receives the variable that holds the PDO object so that it can perform all the queries to the database.
    private $db;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //gets an ID as an arguement and fetches the name of that category from the database
    //returns the name of the category
    function getName($id){
      $categoryName = $this->db->prepare('SELECT category_name FROM categories WHERE category_ID = :cate_id');

      $criteria = [
         'cate_id' => $id
       ];

       $categoryName ->execute($criteria);
       $buff = $categoryName->fetch();
       return $buff['category_name'];
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //fetches all categories from the database and generates HTML code to construct a dropdown menu with all $categories
    //it then returns the dropdown menu
    function dropdownAllCategories(){
      $content = null;
      $categories = $this->getAllCategories();

      $content = '
      <select name="categories">';
      //list the categories
      foreach ($categories as $row) {
      $content = $content . '<option value="'.$row['category_ID'].'">' . $row['category_name'] . '</option>';  //https://www.w3schools.com/tags/att_option_value.asp
      }
      $content = $content . '</select>';

      return $content;
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////
//THIS FUNCTION IS NOT USED. IT IS IMPLEMENTED FOR FUTURE WORK
//THE WHOLE IDEA, IS TO USE THIS TO GENERATE A DROPDOWN MENU WITH THE CATEGORIES SO THAT ONE CATEGORY TO NOT BE LISTED FOR DELETION, PROTECTING IT THAT WAY.
//THE NOT LISTED CATEGORY, IS THE GENERIC ONE THAT WILL BE USED TO HOLD ALL ARTICLES FROM CATEGORIES THAT ARE DELETED.
//gets a category ID as an arguement
//fetches all categories from the database and generates HTML code to construct a dropdown menu with all categories EXCEPT the category that has the ID given as parameter
//returns the dropdown menu
function dropdownAllCategoriesExcept($catID){
  $content = null;
  $categories = $this->getAllCategories();

  $content = '
  <select name="categories">';
  //list the categories
  foreach ($categories as $row) {
    if ($row['category_ID'] === $catID) {
      unset($row);  //https://stackoverflow.com/questions/4727350/delete-row-from-php-array
    }else {
      $content = $content . '<option value="'.$row['category_ID'].'">' . $row['category_name'] . '</option>';  //https://www.w3schools.com/tags/att_option_value.asp
    }
  }
  $content = $content . '</select>';

  return $content;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////

//gets a category ID and a string as arguements
//sets the category's name with the given ID,  to the string given
    function setName($id, $name){
      $criteria = [
         'cate_id' => $id,
         'name' => $name
       ];

      $updateCatName = $this->db->prepare('UPDATE categories SET category_name = :name WHERE category_ID= :cate_id');
      $updateCatName->execute($criteria);
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////
//gets a category ID as an arguement
//fetches the articles that belong to the category with the given ID
//returns the array with the articles fetched

    function getItsArticles($id){
      $criteria = [
         'cate_id' => $_GET['categoryID']
       ];
      $queryForArticles = $this->db->prepare('SELECT a.*, c.* FROM articles a
        INNER JOIN categories c ON c.category_ID = a.article_category
        WHERE a.article_category = :cate_id AND a.article_is_visible = "y"' );

       $queryForArticles->execute($criteria);
       return $queryForArticles->fetchAll();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
//fetches all categories from the database and returns the array
    function getAllCategories(){
      $results = $this->db->prepare('SELECT * FROM categories');
      $results->execute();
      return $results;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
//gets a string as arguement
//inserts a new category to the database with the given name
    function insertNewCategory($catName){
      $stmt = $this->db->prepare('INSERT INTO categories (category_name) VALUES (:name)');

      $criteria = [
        'name' => $catName
      ];
      $stmt->execute($criteria);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //gets a string as arguement
    //return true if there is a category in the database with name equal to the string given
    function hasSameName($newCatName){

      $categories = $this->getAllCategories();

      foreach ($categories as $category) {
        if ($category['category_name'] === $newCatName) {
          return true;
        }
      }
      return false;
    }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets a category id as arguement
      //deletes the category with that id from the database
      function deleteCategory($categoryID){
        $stmt = $this->db->prepare('DELETE FROM categories WHERE category_ID = :id LIMIT 1');

        $criteria = [
          'id' => $categoryID
        ];
        $stmt->execute($criteria);
      }

      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets a category id and a string as arguements
      //updates the name of the category with that ID to be the string given
      function editCategoryName($categoryID, $catNewName){
        $stmt = $this->db->prepare('UPDATE categories SET category_name= :name WHERE category_ID= :id');

        $criteria = [
          'id' => $categoryID,
          'name' => $catNewName
        ];
        $stmt->execute($criteria);
      }
  }
 ?>
