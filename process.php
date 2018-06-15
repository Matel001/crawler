<?php
session_start();
require_once 'connect.php';


//---------------------FUNKCJA--------------------------------------------------
function shear($from, $lvl, $shearR, $result){
  global $tab;
  global $tabId;

  for($i=$from; $i<count($result); $i++){ //pętla po kolei po wszystkich rekordach

    if($result[$i]['rootID']==$shearR && $result[$i]['Level']<=$lvl){
        //wstaw do tablicy

        $tab[$tabId]['ID']=$result[$i]['ID'];
        $tab[$tabId]['TITLE']=$result[$i]['TITLE'];
        $tab[$tabId]['DESCRIPTION']=$result[$i]['DESCRIPTION'];
        $tab[$tabId]['KEYWORDS']=$result[$i]['KEYWORDS'];
        $tab[$tabId]['URL']=$result[$i]['URL'];
        $tab[$tabId]['rootID']=$result[$i]['rootID'];
        $tab[$tabId]['Level']=$result[$i]['Level'];

        $tabId++;
        //szukaj dzieci jeśli istnieją
        if($result[$i]['Level']>1){
            shear($i+1, $lvl-1, $result[$i]['ID'], $result);
        }
    }
    //przerwij jeśli rootID jest większe od szukanego ID -> rekordy tak posortowane
    //że niczego dalej nie znajdzie
    if($result[$i]['rootID']>$shearR) break;

    $from++;
  }
}
//-----------------------------------------------------------------------------
function write_to_base($db, $table, $link){
  $data = json_decode($link, true);
  $query = $db->prepare("INSERT INTO $table VALUES (?,?,?,?,?,?,?)");

  $null = 'NULL';
  foreach($data as $d){
    foreach($d as $row){
      $query->bindParam(1, $null, PDO::PARAM_NULL);
      $query->bindParam(2, $row["TITLE"], PDO::PARAM_STR);
      $query->bindParam(3, $row["DESCRIPTION"], PDO::PARAM_STR);
      $query->bindParam(4, $row["KEYWORDS"], PDO::PARAM_STR);
      $query->bindParam(5, $row["URL"], PDO::PARAM_STR);
      $query->bindParam(6, $row["rootID"], PDO::PARAM_INT);
      $query->bindParam(7, $row["Level"], PDO::PARAM_INT);
      $query->execute();
    }

  }
}

if(isset($_SESSION['proc'])){
  $finish = array();
  //global $tab;
  $table = $_SESSION['tn'];
  $levels = $_SESSION['levels'];

  $shearRoot = 1;
  $tabId = 1;

//naprawa pierwszego rekordu -> wpisany link z klawiatury
  $query = $db->prepare("UPDATE $table SET rootID = :rID, Level = :lvl WHERE ID = :id");
  $query->bindValue(':id', 1, PDO::PARAM_INT);
  $query->bindValue(':rID', 0, PDO::PARAM_INT);
  $query->bindValue(':lvl', $levels+1, PDO::PARAM_INT);
  $query->execute();
//---------------------------------------

// pobieramy całą tablice danych
  $query = $db->query("SELECT * FROM $table");
  $result = $query->fetchAll(PDO::FETCH_ASSOC);

//link wpisany z klawiatury zawsze na 1 miejscu
  $tab[0]['ID']=$result[0]['ID'];
  $tab[0]['TITLE']=$result[0]['TITLE'];
  $tab[0]['DESCRIPTION']=$result[0]['DESCRIPTION'];
  $tab[0]['KEYWORDS']=$result[0]['KEYWORDS'];
  $tab[0]['URL']=$result[0]['URL'];
  $tab[0]['rootID']=$result[0]['rootID'];
  $tab[0]['Level']=$result[0]['Level'];

//sortowanie danych w schemacie dziadek->rodzic->dziecko
shear(1, $levels, $shearRoot, $result);

//?
$_SESSION['R']=true;


require_once 'create_table.php';
unset($_SESSION['R']);
$_SESSION['tn'] = $tableName;
$jsonw = "[".json_encode($tab, JSON_PRETTY_PRINT)."]";

//zapis do bazy pod nazwą "result..."
write_to_base($db, $tableName, $jsonw);

//zapis do jsona

/*  $create_file = 'resultt.json';
  if(!file_exists($create_file))
    file_put_contents($create_file, $jsonw);
  else {
    $file = fopen('resultt.json', 'w');
    fwrite($file, $jsonw);
    fclose($file);
  }
*/
header('Location: result.php');
}
else{
  header('Location: index.php');
  exit();
}
 ?>

 <!DOCTYPE html>
 <html lang="pl">
 <head>
     <meta charset="utf-8">
     <title>Wynik</title>
     <meta http-equiv="X-Ua-Compatible" content="IE=edge">

     <link rel="stylesheet" href="style.css"/>
     <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
 </head>
 <body>

     <div class="container">
         <header>
             <h1>Przetwarzanie</h1>
         </header>
         <main>
             <article>
               <p>Przetwarzam dane, proszę czekać.</p>
             </article>
         </main>

     </div>

 </body>
 </html>
