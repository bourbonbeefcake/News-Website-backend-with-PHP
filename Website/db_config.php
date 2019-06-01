<?php

/**
 * db_config.php
 *
 * Responsible for connecting to the database based on the credentials given by the developer
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */



//One of the most important files in the site, this file establishes the connection with the database
//It also keeps the session started or resumed, as it is required in every other page
//Additionaly, it requires all files that contain the supportive classes with very useful functions for every element of the website.
//These classes are instantiated and the PDO that contains the connection with the database is passed into them so that they can perform queries.
session_start();

$DB_host = "localhost";
$DB_user = "root";
$DB_pass = "";
$DB_name = "news_database";

try
{
     $pdo = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
     echo $e->getMessage();
}


require 'userQueries.class.php';
require 'categoryQueries.class.php';
require 'articleQueries.class.php';
require 'commentQueries.class.php';

$user = new User($pdo);
$categoryQueries = new categoryQueries($pdo);
$articleQueries = new articleQueries($pdo);
$commentQueries = new commentQueries($pdo);



?>
