<?php

/**
 * index.php
 *
 * Home page, just requires layout.php as it contains the whole page design
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */

require_once 'db_config.php';
  $title = 'Northampton News - Home';
  $content = '<h1>Welcome to Northampton News!</h1>';
  require 'layout.php';
?>
