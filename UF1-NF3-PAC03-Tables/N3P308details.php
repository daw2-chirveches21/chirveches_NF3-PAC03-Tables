<?php
// function to generate ratings
function generate_ratings($rating) {
    $movie_rating = ''; 
    for ($i = 0; $i < $rating; $i++) {
        $movie_rating .= '<img src="star.png" width="10" height="10" alt="star"/>';               
    }

    return $movie_rating;
}


// take in the id of a director and return his/her full name
function get_director($director_id) {

    global $db;

    $query = 'SELECT 
            people_fullname 
       FROM
           people
       WHERE
           people_id = ' . $director_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $people_fullname;
}

// take in the id of a lead actor and return his/her full name
function get_leadactor($leadactor_id) {

    global $db;

    $query = 'SELECT
            people_fullname
        FROM
            people 
        WHERE
            people_id = ' . $leadactor_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $people_fullname;
}

// take in the id of a movie type and return the meaningful textual
// description
function get_movietype($type_id) {

    global $db;

    $query = 'SELECT 
            movietype_label
       FROM
           movietype
       WHERE
           movietype_id = ' . $type_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $movietype_label;
}

// function to calculate if a movie made a profit, loss or just broke even
function calculate_differences($takings, $cost) {

    $difference = $takings - $cost;

    if ($difference < 0) {     
        $color = 'red';
        $difference = '$' . abs($difference) . ' million';
    } elseif ($difference > 0) {
        $color ='green';
        $difference = '$' . $difference . ' million';
    } else {
        $color = 'blue';
        $difference = 'broke even';
    }

    return '<span style="color:' . $color . ';">' . $difference . '</span>';
}

//connect to MySQL
$db = mysqli_connect(gethostname(), 'root', 'root') or 
    die ('Unable to connect. Check your connection parameters.');
mysqli_select_db($db, 'moviesite') or die(mysqli_error($db));

// retrieve information
$query = 'SELECT
        movie_name, movie_year, movie_director, movie_leadactor,
        movie_type, movie_running_time, movie_cost, movie_takings
    FROM
        movie
    WHERE
        movie_id = ' . $_GET['movie_id'];
$result = mysqli_query($db, $query) or die(mysqli_error($db));

$row = mysqli_fetch_assoc($result);
$movie_name         = $row['movie_name'];
$movie_director     = get_director($row['movie_director']);
$movie_leadactor    = get_leadactor($row['movie_leadactor']);
$movie_year         = $row['movie_year'];
$movie_running_time = $row['movie_running_time'] .' mins';
$movie_takings      = $row['movie_takings'] . ' million';
$movie_cost         = $row['movie_cost'] . ' million';
$movie_health       = calculate_differences($row['movie_takings'],
                          $row['movie_cost']);

// display the information
echo <<<ENDHTML
<html>
 <head>
  <title>Details and Reviews for: $movie_name</title>
 </head>
 <body>
  <div style="text-align: center;">
   <h2>$movie_name</h2>
   <h3><em>Details</em></h3>
   <table cellpadding="2" cellspacing="2"
    style="width: 70%; margin-left: auto; margin-right: auto;">
    <tr>
     <td><strong>Title</strong></strong></td>
     <td>$movie_name</td>
     <td><strong>Release Year</strong></strong></td>
     <td>$movie_year</td>
    </tr><tr>
     <td><strong>Movie Director</strong></td>
     <td>$movie_director</td>
     <td><strong>Cost</strong></td>
     <td>$$movie_cost<td/>
    </tr><tr>
     <td><strong>Lead Actor</strong></td>
     <td>$movie_leadactor</td>
     <td><strong>Takings</strong></td>
     <td>$$movie_takings<td/>
    </tr><tr>
     <td><strong>Running Time</strong></td>
     <td>$movie_running_time</td>
     <td><strong>Health</strong></td>
     <td>$movie_health<td/>
    </tr>
   </table>
ENDHTML;

// retrieve reviews for this movie

$VarAscDesc='DESC';
$VarOrden=isset($_GET['orden']) ? $_GET['orden']: 'review_date';
$VarOrden2=isset($_GET['order2']) ? $_GET['order2']: 'ASC';

if($VarOrden2=='DESC'){
    $VarAscDesc='ASC';
}else{
    $VarAscDesc='DESC';
}
$query = 'SELECT
        review_movie_id, review_date, reviewer_name, review_comment,
        review_rating
    FROM
        reviews
    WHERE
        review_movie_id = ' . $_GET['movie_id'] . '
    ORDER BY
        ' .$VarOrden .' ' .$VarAscDesc;

$result = mysqli_query($db, $query) or die(mysqli_error($db));

$moviePeli = $_GET['movie_id'];
// display the reviews
echo <<< ENDHTML
   <h3><em>Reviews</em></h3>
   <table cellpadding="2" cellspacing="2"
    style="width: 90%; margin-left: auto; margin-right: auto;">
    <tr>
     <th style="width: 7em;"><a href="N3P308details.php?movie_id=$moviePeli&orden=review_date&order2=$VarAscDesc">Date</a></th>
     <th style="width: 10em;"><a href="N3P308details.php?movie_id=$moviePeli&orden=reviewer_name&order2=$VarAscDesc">Reviewer</a></th>
     <th><a href="N3P308details.php?movie_id=$moviePeli&orden=review_comment&order2=$VarAscDesc">Comments</a></th>
     <th style="width: 5em;"><a href="N3P308details.php?movie_id=$moviePeli&orden=review_rating&order2=$VarAscDesc">Rating</a></th>
    </tr>
ENDHTML;
$suma = 0;
$resul=0;
$cont=0;
$i=0;
while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['review_date'];
    $name = $row['reviewer_name'];
    $comment = $row['review_comment'];
    $ratingp = $row['review_rating'];
    $rating = generate_ratings($row['review_rating']);
        
    $suma = $suma + $ratingp;           

    $bg_color = $i % 2 === 0 ? "green" : "blue";
    
    echo <<<ENDHTML
    <tr>
      <td style="vertical-align:top; text-align: center;background-color:$bg_color;">$date</td>
      <td style="vertical-align:top;background-color:$bg_color;">$name</td>
      <td style="vertical-align:top;background-color:$bg_color;">$comment</td>
      <td style="vertical-align:top;background-color:$bg_color;">$rating</td>
    </tr>
ENDHTML;
$cont++;
$i++;

}
$resul= $suma / $cont;
echo "</table>La media es: ";
echo $resul;

echo <<<ENDHTML
  </div>
 </body>
</html>
ENDHTML;
?>
