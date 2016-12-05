<!DOCTYPE html>
<html>
  <head>
    <title>
      Results
    </title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style type="text/css">

.responstable {
  margin: 1em 0;
  width: 100%;
  overflow: hidden;
  background: #FFF;
  color: #024457;
  border-radius: 10px;
  border: 1px solid #167F92;
}
.responstable tr {
  border: 1px solid #D9E4E6;
}
.responstable tr:nth-child(odd) {
  background-color: #EAF3F3;
}
.responstable th {
  display: none;
  border: 1px solid #FFF;
  background-color: #afdb43;
  color: #FFF;
  padding: 1em;
}
.responstable th:first-child {
  display: table-cell;
  text-align: center;
}
.responstable th:nth-child(2) {
  display: table-cell;
}
.responstable th:nth-child(2) span {
  display: none;
}
.responstable th:nth-child(2):after {
  content: attr(data-th);
}
@media (min-width: 480px) {
  .responstable th:nth-child(2) span {
    display: block;
  }
  .responstable th:nth-child(2):after {
    display: none;
  }
}
.responstable td {
  display: block;
  word-wrap: break-word;
  max-width: 7em;
}
.responstable td:first-child {
  display: table-cell;
  text-align: center;
  border-right: 1px solid #D9E4E6;
}
@media (min-width: 480px) {
  .responstable td {
    border: 1px solid #D9E4E6;
  }
}
.responstable th, .responstable td {
  text-align: left;
  margin: .5em 1em;
}
@media (min-width: 480px) {
  .responstable th, .responstable td {
    display: table-cell;
    padding: 1em;
  }
}

</style>
  </head>
  <body >




    <?php

    try {
      $uname = "root";  //add your db username here
      $passwd = "password";  //add the password here
      $dbname ="soccerstarschema";//add the dbname here
      //connection to database add your db user name and password here
      $con = new PDO("mysql:host=localhost;dbname=".$dbname,
                     $uname, $passwd);
      $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query1 ="Select  club.club_manager, club.club_name, sum(games_won) as 'games_won',  sum(total_goals) as 'total_goals'
              from club, league, club_stats
              where club.club_key = club_stats.club_key and league.league_key =
              club_stats.league_key and league.league_name = 'Laliga'
              group by club.club_key
              order by games_won desc limit 1";
      $query2="Select player.first_name,player.last_name,player.club_name,player.position,player.age, player_stats.total_goals
                FROM player_stats,player where player.player_key = player_stats.player_key
                order by player_stats.total_goals desc limit 1";

    $query3="Select player.first_name,player.last_name,player.club_name,player.position,player.age, player_stats.total_goals
            FROM player_stats,player where player.player_key = player_stats.player_key
            order by player_stats.total_goals desc limit 10";

  $query4="Select club.club_manager, club.club_name,  sum(games_won) as 'games_won'
          from club, league, club_stats
          where club.club_key = club_stats.club_key and league.league_key =
          club_stats.league_key and league.league_name = 'Laliga'
          group by club.club_key
          order by games_won desc";

  $ps = $con->prepare($query1);
  $ps->execute();
  $row =$ps->fetch(PDO::FETCH_ASSOC);
  $ps =  $con->prepare($query1);
  $ps->execute();
  $data = $ps->fetchAll(PDO::FETCH_ASSOC);
  print '<h1>Manager of the season</h1>';
  printTable($row,$data);
//-------------------------------
$ps = $con->prepare($query2);
$ps->execute();
$row =$ps->fetch(PDO::FETCH_ASSOC);
$ps =  $con->prepare($query2);
$ps->execute();
$data = $ps->fetchAll(PDO::FETCH_ASSOC);
print '<h1>Player of the season</h1>';
printTable($row,$data);
//-------------------------------------
$ps = $con->prepare($query3);
$ps->execute();
$row =$ps->fetch(PDO::FETCH_ASSOC);
$ps =  $con->prepare($query3);
$ps->execute();
$data = $ps->fetchAll(PDO::FETCH_ASSOC);
print '<h1>Players Ranking</h1>';
printTable($row,$data);
//------------------------------------
$ps = $con->prepare($query4);
$ps->execute();
$row =$ps->fetch(PDO::FETCH_ASSOC);
$ps =  $con->prepare($query4);
$ps->execute();
$data = $ps->fetchAll(PDO::FETCH_ASSOC);
print '<h1>Managers Ranking</h1>';
printTable($row,$data);
}
        catch(PDOException $ex) {
          echo 'ERROR: '.$ex->getMessage();
        }
 function printTable($row, $data) {
   if((is_array($row) || is_object($row))&&(is_array($data) || is_object($data))){
     print "<table class='responstable' >\n";
     print "<tr>\n";
     foreach ($row as $field => $value) {
       print "<th>$field</th>\n";
     }
     print "</tr>\n";
     foreach ($data as $row){
       print "<tr>\n";
       foreach ($row as $name => $value) {
         print "<td>$value</td>\n";
       }
       print "</tr>\n";
     }
     print "</table><br><br>\n";
   }
   else{
     print "<label style='margin-left:170px;'>No records!</label>";
   }
 } ?>

 </body>
</html>
