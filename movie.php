<?php
//session_start() 
require __DIR__ . '/vendor/autoload.php';

$imdbid = $_GET['imdbid'];

//echo $imdbid;


$title = new \Imdb\Title($imdbid );
$movieTitle = $title->title();
$rating = $title->rating();
$plotOutline = $title->plotoutline();
$movieType = $title->movietype();
$genre = $title->genre();
$movieImage = $title->photo();
$pageTitle = $movieTitle;
//include header
include "header.php";

echo $movieTitle.'<br />';
echo $movieType.'<br />';
echo $rating.'<br />';
echo $genre.'<br />';
echo $plotOutline.'<br />';
echo '<img src="'.$movieImage.'" alt="'.$movieTitle.'">';

include "footer.php"; 
?>