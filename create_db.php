<?php
  $config = require_once 'config.php';

try{
  $createDB = new PDO("mysql:host={$config['host']};charset=utf8",$config['user'], $config['password'],[
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  $createDB->exec($create_db);
  echo "DB created.";
}
catch(PDOException $e){
  echo $e->getMessage();
  exit('Problems with database create.');
}
?>
