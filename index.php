<?php
session_start();
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Crawler na zaliczenie</title>
    <link rel="stylesheet" href="style.css"/>
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <header>
        <h1>Crawler</h1>
      </header>
        <main>
          <article>
          <form action="craw2base.php" method="post">
            <label>Link:
              <input type="text" placeholder="http(s)//www.example.com" name="link" value="<?php
            if(isset($_SESSION['link'])){
              echo $_SESSION['link'];
              unset($_SESSION['link']);
            }
      ?>"></label><br/>
      <?php
        if(isset($_SESSION['link_e'])){
          echo '<div class = "error">'.$_SESSION['link_e'].'</div>';
          unset($_SESSION['link_e']);
        }
      ?>
            <label>Poziom przeszukiwań (domyślnie 1):
              <input id="lvl"  type="text" name="levels" value="1">
            </label><br/>
            <input type="submit" value="Zacznij przeszukiwać">
          </form>
          <br/><a href="tables.php">Sprawdź poprzedne przeszukiwania</a>

        </article>
  </main>

  </div>
  </body>
</html>
