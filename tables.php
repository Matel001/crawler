<?php
session_start();
require_once 'connect.php';

//$table = $_GET['tn'];//$_SESSION['tn'];
$request = $db->query("SHOW TABLES IN $db_name LIKE 'results%'");

$results = $request->fetchAll();
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
          <h1>Link details</h1>
        </header>
        <main>
            <article>
              <table>
                  <thead>
                    <tr>
                      <th>Nazwa tabeli</th>
                    </tr>
                  </thead>
                  <tbody>
              <?php foreach ($results as $result) {
                echo '<tr><td>
                    <a href ="result.php?tn='.$result[0].'">'.$result[0].'</a>
                  </td></tr>';
              } ?>
                 </tbody>
             </table>
             <button onclick="back()">Wróć</button>
             </article>
         </main>
   </div>
 </body>
</html>
