<?php
  $config = require_once 'config.php';
try{
    $db = new PDO("mysql:host={$config['host']};dbname={$config['db_name']};charset=utf8", $config['user'], $config['password'],[
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    //echo "connected";
  }
  catch(PDOException $error){
    exit('Database error.');
  }
  ?>
