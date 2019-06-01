<?php

/**
 * article.class.php
 *
 * Class for the article object
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */


//this class is made to make objects that keep the attributes of an article and use getters and setters to easily manipulate data, locally before amending the database again.
  class Article
  {
    private $id;
    private $title;
    private $authorID;
    private $isVisible;
    private $date;
    private $content;
    private $category;
    private $picture;
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function __construct($id, $title, $authorID,$isVisible, $date, $content, $category, $picture)
    {
      $this->id = $id;
      $this->title = $title;
      $this->authorID = $authorID;
      $this->isVisible = $isVisible;
      $this->date = $date;
      $this->content = $content;
      $this->category = $category;
      $this->picture = $picture;
    }

    function truncateArticle()
    {
      if (strlen($this->content) > 100) {

          // truncate string
          $stringCut = substr($this->content, 0, 100);

          // make sure it ends in a word and not cut it in the middle
          $truncatedString = substr($stringCut, 0, strrpos($stringCut, ' ')).'... <a href="article.php?articleID='. $this->id .'">Read More</a>';
        }else{$truncatedString = $this->content;}
        return $truncatedString;
    }



//GETTERS                   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function getID()
    {
      return $this->id;
    }
    function getTitle()
    {
      return $this->title;
    }
    function getAuthor()
    {
      return $this->authorID;
    }
    function isVisible(){
      return $this->isVisible;
    }
    function getDate()
    {
      return $this->date;
    }
    function getContent()
    {
      return $this->content;
    }
    function getCategory()
    {
      return $this->category;
    }
//16777215 bytes for medium BLOB (16.7 MB)
//https://stackoverflow.com/questions/5775571/what-is-the-maximum-length-of-data-i-can-put-in-a-blob-column-in-mysql
    function getPicture()
    {
      return base64_encode($this->picture);
    }

    function getCodedPicture(){
      return $this->picture;
    }


//SETTERS                   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

function setID($id)
{
  $this->id = $id;
}

function setTitle($title)
{
  $this->title = $title;
}

function setAuthor($authorID)
{
  $this->authorID = $authorID;
}

function setVisible($isVisible)
{
  $this->isVisible = $isVisible;
}

function setDate($date)
{
  $this->date = $date;
}

function setContent($content)
{
  $this->content = $content;
}

function setCategory($category)
{
  $this->category = $category;
}

function setPicture($picture)
{
  $this->picture = $picture;
}

}
 ?>
