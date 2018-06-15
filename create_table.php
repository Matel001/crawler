<?php
  require_once 'connect.php';


  $date = new DateTime();
  if(isset($_SESSION['R'])){
      $tableName = "results_".$date->format('d_m_y_H_i_s');
  }
  else{
      $tableName = "links_".$date->format('d_m_y_H_i_s');
  }

  $title = "TITLE";
  $description = "DESCRIPTION";
  $keywords = "KEYWORDS";
  $url = "URL";

  #--
  $rootID="rootID";
  $lvl="Level";

try{
    $request="CREATE TABLE IF NOT EXISTS $tableName(
                                      ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                      $title TEXT,
                                      $description TEXT,
                                      $keywords TEXT,
                                      $url VARCHAR(255),
                                      #--
                                      $rootID INT,
                                      $lvl INT
                                      );";
    $db->exec($request);
    //echo("\nTable: ".$tableName." created.");
}
catch(PDOException $e){
  //echo $e->getMessage();
  echo("Wystąpił błąd podczas tworzenia tabeli.");
}
?>
