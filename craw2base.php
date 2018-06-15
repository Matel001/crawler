<?php
session_start();
require_once 'create_table.php';

$_SESSION['tn'] = $tableName;
$start = $_POST['link']; //startowy link
$_SESSION['link'] = $start;
//prosta walidacja wpisanego url'a
if($start != ''){
  if (!preg_match('/^(https|http)\:\/\/www\.*[a-zA-Z0-9\.]*\.([a-zA-z]{2}|[a-zA-Z]{3})$/', $start)){
        $_SESSION['link_e'] = "Podany URL jest błędny. Adres URL musi być napisany wg wzoru: http(s)://www.example.com";
        header('Location: index.php');
      }
}
else{
  $_SESSION['link_e'] = "Proszę podać adres URL!";
  header('Location: index.php');
  //echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
  exit();
}
echo $start;

$table = $tableName;

$how_many_levels = $_POST['levels'];//"głębokość" przeszukiwania linków
$_SESSION['levels'] = $how_many_levels;
$links_to_craw = 1; //ilość linków w 1 poziomie

// 2 tablice przechowujące linki: przeszukane i obecnie przeszukiwany
$already_crawled = array();
$crawling = array($start);

$rootID = 1;
$next_lvl_links = 0;
//$all_links = 1;

while($how_many_levels){
  while($links_to_craw){

    follow_links($db, $crawling[0], $rootID, $how_many_levels);
    //czyszczenie tablicy przeszukanych linków po każdym linku: zapobiega nieskończonym pętlom
    $already_crawled[]="";
    $links_to_craw--;
    $rootID++;
  }
  $links_to_craw = $next_lvl_links;
  $next_lvl_links = 0;
  $how_many_levels--;
}

$_SESSION['proc']=true;
header('Location: process.php');
//echo "<script type='text/javascript'> document.location = 'result.php'; </script>";



//---------FUNKCJE------------------
//poprawa kodowania
//----------------------------------------------------------------------
function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
     return mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
    // return iconv(mb_detect_encoding($content, 'UTF-8, ISO-8859-2, ISO-8859-1, ASCII'), true), "UTF-8", $content);
}
//-----------------------------------------------------------------------

function write_to_base($db, $table, $link){

  $data = json_decode($link, true);
  $query = $db->prepare("INSERT INTO $table VALUES (?,?,?,?,?,?,?)");

  $null = 'NULL';
  foreach($data as $row){
    $query->bindParam(1, $null, PDO::PARAM_NULL);
    $query->bindParam(2, $row["Title"], PDO::PARAM_STR);
    $query->bindParam(3, $row["Description"], PDO::PARAM_STR);
    $query->bindParam(4, $row["Keywords"], PDO::PARAM_STR);
    $query->bindParam(5, $row["URL"], PDO::PARAM_STR);
    $query->bindParam(6, $row["rootID"], PDO::PARAM_INT);
    $query->bindParam(7, $row["Level"], PDO::PARAM_INT);
    $query->execute();
  }
}


function get_details($url) {
	try{
		// zmiana user-agenta.
		$options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: Bot\n"));
		// stream
		$context = stream_context_create($options);
		// DomDocument class
		$doc = new DOMDocument();
		//file_get_contents() pobieramy stronę do obiektu domdocument
		@$doc->loadHTML(file_get_contents_utf8($url, false, $context));
		// pobieramy tytuł strony
		if($doc->getElementsByTagName("title")->length == 0){
			$title = "";
		} else{
			$title = $doc->getElementsByTagName("title");
      //usuwwamy białe znaki przed i po stringu
			$title = trim($title->item(0)->nodeValue);
		}

		$description = "";
		$keywords = "";
		// tablica z meta tagami
		$metas = $doc->getElementsByTagName("meta");
		// szukamy description i keywords
		for ($i = 0; $i < $metas->length; $i++) {
			$meta = $metas->item($i);
			// Get the description and the keywords.
			if (strtolower($meta->getAttribute("name")) == "description")
				$description = trim($meta->getAttribute("content"));
			if (strtolower($meta->getAttribute("name")) == "keywords")
				$keywords = trim($meta->getAttribute("content"));
		}
	}
	catch(Exception $e){
		echo "Wystąpił problem podszas pobierania strony <br/>";
	}
	// zwracamy tablicę ze szczegółami linka
  return array('Title'=>$title, 'Description'=>$description, 'Keywords'=>$keywords, 'URL' => $url);
	//$JSON = "Title: ".$title."Description: ".$description."Keywords: ".$keywords."URL: ".$url;

}

function normalize_link($l, $url){
  //parse_url()-> dokleja część z linka
  //["scheme"] : http(s)
  //["host"] : DOMENA
  //["path"] : LOKALIZACJA
  if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
    //zaczynające się od "/"
    //doklejamy http(s)://DOMENA
    $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
  } else if (substr($l, 0, 1) == "#") {
    //odsyłacze z tej samej strony
    //doklejamy całość: http(s)://DOMENA/LOKALIZACJA
    $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].(isset($url["path"])?$url['path']:'').$l;
  } else if (substr($l, 0, 2) == "//") {
    //zaczynające się od "//"
    //doklejamy http(s):
    $l = parse_url($url)["scheme"].":".$l;
  } else if (substr($l, 0, 2) == "./") {
    //zaczynające się od "./"
    //doklejamy http(s)://DOMENA/LOKALIZACJA (+ link bez początkowej ".")
    $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
  } else if (substr($l, 0, 3) == "../") {
    //zaczynające się od "../"
    //doklejamy http(s)://DOMENA/
    $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
  } else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
    //pozostałe linki, które nie mają na początku http(s) (linki w tym samym folderze)
    //doklejamy http(s)://DOMENA/
    $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
  } else if (substr($l, 0, 11) == "javascript:") {
    //wywołanie skryptu js'a -> pomijamy
    $links_count--;
    //$all_links--;
    //continue;
  }
  //zwracamy przetworzony link
  return $l;
}

function follow_links($db, $url, $root, $lvl) {
	// Give our function access to our crawl arrays.
	global $already_crawled;
	global $crawling;
  global $next_lvl_links;
  $json = [];
  global $table;
  //global $all_links;
  global $links_count;

  // The array that we pass to stream_context_create() to modify our User Agent.
  $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: Bot\n"));
  // Create the stream context.
  $context = stream_context_create($options);
  // DomDocument class
  $doc = new DOMDocument();
  //file_get_contents() pobieramy stronę do obiektu domdocument
  @$doc->loadHTML(file_get_contents_utf8($url, false, $context));

	// tablica z wszystkimi linkami na stronie
	$linklist = $doc->getElementsByTagName("a");

  //ilość linków na stronie
  $links_count = $linklist->length;
  //sumowanie ilości linków w poziomie
  $next_lvl_links += $links_count;
  //$all_links += $links_count;


  // Pętla po wszystkich znalezionych linkach
	foreach ($linklist as $link) {
    // Przetwarzanie linków do pełnej postaci
		$l =  $link->getAttribute("href");

    $l = normalize_link($l, $url);

		// Dodaj link do tablicy jeśli go tam nie ma
		if (!in_array($l, $already_crawled)) {
				$already_crawled[] = $l;
				$crawling[] = $l;
				//pobierz szczegóły linku
        $json = get_details($l);

        $json['rootID'] = $root;
        $json['Level'] = $lvl;

        $json = "[".json_encode($json, JSON_PRETTY_PRINT)."]";
//----------------------------------------------------------------------
        write_to_base($db, $table, $json);
//----------------------------------------------------------------------
    }
	}//koniec foreach przetwarzający linki

  //usuwamy właśnie przetworzony link
	array_shift($crawling);

}
