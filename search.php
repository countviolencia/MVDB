<?php

session_start();
require __DIR__ . '/vendor/autoload.php';

$movietitle = $_POST["movietitle"];
echo "You searched for <h1>".$movietitle."</h1><br />";

// include "bootstrap.php"; // Load the class in if you're not using an autoloader
$search = new \Imdb\TitleSearch(); // Optional $config parameter
$results = $search->search($movietitle, [\Imdb\TitleSearch::MOVIE, \Imdb\Title::TV_SERIES]); // Optional second parameter restricts types returned


// $results is an array of Title objects
// The objects will have title, year and movietype available
//  immediately, but any other data will have to be fetched from IMDb
foreach ($results as $result) { /* @var $result \Imdb\Title */
    $imdbid = $result->imdbid();
    $title = $result->title();
    $year= $result->year();
    $movieType= $result->movietype();
    //echo $result->title() . ' ( ' . $result->year() . ')'.'=>'.$imdbid.'<br />';
    echo '<a href="movie.php?imdbid='.$imdbid.'">'.$title.'</a> -> '.$year.'   ' .$movieType.'<br />';
}

?>