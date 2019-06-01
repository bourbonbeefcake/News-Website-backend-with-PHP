<?php

/**
 * comment.class.php
 *
 * Class for the comment object
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



//this class is made to make objects that keep the attributes of a comment and use getters and setters to easily manipulate data, locally before amending the database again.

  class comment{

    private $id;
    private $author;
    private $date;
    private $pending;
    private $content;
    private $article;
    private $parent; //if 0, this is not a nested comment
    function __construct($id,$author,$date,$pending,$content,$article,$parent)
    {
      $this->id = $id;
      $this->author = $author;
      $this->date = $date;
      $this->pending = $pending;
      $this->content = $content;
      $this->article = $article;
      $this->parent = $parent;
    }

    public function getCommID(){
      return $this->id;
    }
    public function getCommAuthor(){
      return $this->author;
    }
    public function getCommDate(){
      return $this->date;
    }
    public function isPending(){
      return $this->pending;
    }
    public function getCommContent(){
      return $this->content;
    }
    public function getCommArticle(){
      return $this->article;
    }
    public function getCommParent(){
      return $this->parent;
    }


    public function setCommID($id){
      $this->id = $id;
    }
    public function setCommAuthor($author){
      $this->author = $author;
    }
    public function setCommDate($date){
      $this->date = $date;
    }
    public function setPending($isPending){
      $this->pending = $isPending;
    }
    public function setCommContent($content){
      $this->content = $content;
    }
    public function setCommArticle($article){
      $this->article = $article;
    }
    public function setCommParent($parent){
      $this->parent = $parent;
    }


  }
 ?>
