<?php
session_start();
require_once 'connect.php';

$table = $_GET['tn'];//$_SESSION['tn'];
$id = $_GET['id'];
unset($_GET['tn']);
unset($_GET['id']);
$request = $db->query("SELECT * from $table WHERE ID= $id");

$results = $request->fetchAll();
?>

<script>
  function goBack() {
      window.history.back();
  }
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
                       <th>ID</th><th>Title</th><th>Description</th><th>Keywords</th><th>URL</th>
                     </tr>
                   </thead>
                   <tbody>
               <?php foreach ($results as $result) {
                 echo "<tr><td>{$result['ID']}</td><td>{$result['TITLE']}</td><td>{$result['DESCRIPTION']}</td><td>{$result['KEYWORDS']}</td><td>";
                 echo '<a target="_blank" href="'.$result['URL'].'">'.$result['URL'].'</a></td></tr>';
               } ?>
                  </tbody>
              </table>
              <button onclick="goBack()">Wróć</button>

              </article>
          </main>

    </div>

  </body>
</html>
