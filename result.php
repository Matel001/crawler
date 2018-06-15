<?php
session_start();
require_once 'connect.php';

if(isset($_GET['tn'])){
	$table = $_GET['tn'];
	unset($_GET['tn']);
}
else{
  $table = $_SESSION['tn'];
}
  $request = $db->query("SELECT * from $table");

  $results = $request->fetchAll();
//-----------------FUNKCJA
//ustala margines zależnie od poziomu linku -> tworzy prowizoryczne drzewko
//przy wyświetlaniu
//wyświetla rekordy odpowiednio sformatowane w css
  function show($results){
    global $table;
    $modifier = 0;
    $lvl = $results[0]['Level'];

    for($i=$lvl; $i>0; $i--){
      $margin[$i]= $modifier+15;
      $modifier+=30;
    }
    foreach($results as $result){
      echo '<div class= "item" style="margin-left:'.$margin[$result['Level']].'px;">';

      echo '<p class="id">'.$result['ID'].'</p>
      <p class="title">'.$result['TITLE'].'</p>
      <a class="url" target="_blank" href="'.$result['URL'].
      '">'.$result['URL'].'</a>';

      echo '<a class="det" href = "details.php?id='.$result['ID'].'&tn='.$table.'">&gt;&gt;DETAILS&lt;&lt;</a></div>';
    }
  }

?>
<script type='text/javascript'>
function back(){
  document.location = 'index.php'; }
</script>

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
            <h1>Crawled links</h1>
            <?php echo "<h3>".$table."</h3>"; ?>
        </header>

        <main>
          <button id= "resultB" onclick="back()">Wróć</button>
            <article id="results">

                <?php show($results);
                echo "<br/>";
                 ?>

            </article>
        </main>

    </div>

</body>
</html>
