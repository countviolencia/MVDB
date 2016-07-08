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
$pageTitle = $movieTitle; //this is the page title refered to in header.php
$director = $title->director();
$runtime = $title->runtime();
$plot = $title->plot();
//include header
include "header.php";

//these were the initial calls
//echo $movieTitle.'<br />';
//echo $movieType.'<br />';
//echo $rating.'<br />';
//echo $genre.'<br />';
//echo $plotOutline.'<br />';
//echo '<img src="'.$movieImage.'" alt="'.$movieTitle.'">';

//formatted view with css
echo '<div class="row">';
    echo '<div class="column column-30">';
        echo '<img src="'.$movieImage.'" alt="'.$movieTitle.'" class="thumbnail">';
    echo '</div>';//end of first outer coulmn
    echo '<div class="column column-70">';
        echo '<div class="row">';
            echo $movieTitle.'<br />';
        echo '</div>';//end of title row
        echo '<div class="row">';
            echo $rating.'<br />';
        echo '</div>';//end ofrating row
        echo '<div class="row">';
            echo $genre.'<br />';
        echo '</div>';//end of genre row
        echo '<div class="row">';
            echo $runtime.' Minutes<br />';
        echo '</div>';//end of runtime row
        echo '<div class="row">';
            echo $movieType.'<br />';
        echo '</div>';//end of movie type row
        echo '<div class="row">';
            echo $plotOutline.'<br />';
        echo '</div>';//end of plot outline row
    echo '</div>';// end 2nd outer column
echo '</div>';//end of row 1


echo '<div class="row">';
    echo '<div class="column">';
          if (!empty($plot)) {
            //++$rows;
            echo '<tr><td valign=top><b>Plot:</b></td><td><ul>';
            for ($i = 0; $i < count($plot); $i++) {
            echo "<li>".$plot[$i]."</li>\n";
            }
            echo "</ul></td></tr>\n";
            }
        flush();
    echo '</div>';//end of plot column
    echo '<div class="column">';
          if (!empty($director)) {
        // ++$rows;
            echo '<TR><TD valign=top><B>Director:</B></TD><TD>';
            echo "<table align='left' border='' style=''><tr><th style=''>Name</th><th style=''>Role</th></tr>";
            for ($i = 0; $i < count($director); $i++) {
            echo '<tr><td width=200>';
            echo "<a href='person.php?mid=".$director[$i]["imdb"]."'>";
            echo $director[$i]["name"].'</a></td><td>';
            echo $director[$i]["role"]."</td></tr>";
            }
            echo "</table></td></tr>\n";
        }
    echo '</div>';// end of directors column
echo '</div>';//end of row two




   


include "footer.php"; 
?>