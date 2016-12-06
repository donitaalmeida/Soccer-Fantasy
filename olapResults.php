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
    $searchtype = filter_input(INPUT_POST, "searchtype");
    try {
      $uname = "root";  //add your db username here
      $passwd = "password";  //add the password here
      $dbname ="soccerStarSchema";//add the dbname here
      //connection to database add your db user name and password here
      $con = new PDO("mysql:host=localhost;dbname=".$dbname,
                     $uname, $passwd);
      $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $vars = array();
      switch ($searchtype) {
        case "drill":
         $option = filter_input(INPUT_POST, "option");

         if($option == 'up'){
          $query = "SELECT stadium.country,club.club_name,
                    league.league_name, sum(total_goals) as 'goals', sum(games_played) as 'games_played',
                    sum(games_won) as 'games_won' , sum(games_lost) as 'games_lost'
                    from club, league, club_stats, stadium, calendar
                    where club.club_key = club_stats.club_key
                    and league.league_key = club_stats.league_key
                    and stadium.stadium_key = club_stats.stadium_key
                    and calendar.calendar_key = club_stats.calendar_key
                      group by stadium.country, club.club_name
                      having sum(games_played) >2";

         }
         else if($option == 'state'){
            $query = "SELECT stadium.state, stadium.country,club.club_name,
                    league.league_name, sum(total_goals) as 'goals', sum(games_played) as 'games_played',
                    sum(games_won) as 'games_won' , sum(games_lost) as 'games_lost'
                    from club, league, club_stats, stadium, calendar
                    where club.club_key = club_stats.club_key
                    and league.league_key = club_stats.league_key
                    and stadium.stadium_key = club_stats.stadium_key
                    and calendar.calendar_key = club_stats.calendar_key
                      group by stadium.state, club.club_name
                      having sum(games_played) >2";

         }
         else if($option == 'down'){
          $query = "SELECT stadium.city, stadium.state, stadium.country,club.club_name,
                    league.league_name, sum(total_goals) as 'goals', sum(games_played) as 'games_played',
                    sum(games_won) as 'games_won' , sum(games_lost) as 'games_lost'
                    from club, league, club_stats, stadium, calendar
                    where club.club_key = club_stats.club_key
                    and league.league_key = club_stats.league_key
                    and stadium.stadium_key = club_stats.stadium_key
                    and calendar.calendar_key = club_stats.calendar_key
                      group by stadium.city, club.club_name
                      having sum(games_played) >2";


         }
         $ps = $con->prepare($query);
         break;


        case "slice":
          $state = filter_input(INPUT_POST, "state1");
          $query = "SELECT stadium.state, stadium.country, calendar.month, club.club_name,
            league.league_name, sum(total_goals) as 'goals', sum(games_played) as 'games_played',
            sum(games_won) as 'games_won' , sum(games_lost) as 'games_lost'
            from club, league, club_stats, stadium, calendar
            where club.club_key = club_stats.club_key
            and league.league_key = club_stats.league_key
            and stadium.stadium_key = club_stats.stadium_key
            and calendar.calendar_key = club_stats.calendar_key
            and stadium.state = :state
              group by stadium.state, calendar.month, club.club_name
              having games_played >1";
          $ps = $con->prepare($query);
          $vars[':state'] = $state;
          break;

        case "dice":

        $statevar = filter_input(INPUT_POST, "state2");
        $months = filter_input(INPUT_POST, "month");
        $monthsarr = explode(",", $months);
        $states =  explode(",", $statevar);
          $query = "SELECT stadium.state, stadium.country, calendar.month, club.club_name,
            league.league_name, sum(total_goals) as 'goals', sum(games_played) as 'games_played',
            sum(games_won) as 'games_won' , sum(games_lost) as 'games_lost'
            from club, league, club_stats, stadium, calendar
            where club.club_key = club_stats.club_key
            and league.league_key = club_stats.league_key
            and stadium.stadium_key = club_stats.stadium_key
            and calendar.calendar_key = club_stats.calendar_key
            and (stadium.state = :state1 or stadium.state = :state2 ) and (calendar.month = :month1 or calendar.month = :month2 )
              group by stadium.state, calendar.month, club.club_name
              having games_played >1";
          $ps = $con->prepare($query);
          $vars[':state1'] = $states[0];
          $vars[':state2'] = $states[1];
          $vars[':month1'] = $monthsarr[0];
          $vars[':month2'] = $monthsarr[1];


        break;
      }
      $ps->execute($vars);
      $row =$ps->fetch(PDO::FETCH_ASSOC);
      $ps =  $con->prepare($query);
      $ps->execute($vars);
      $data = $ps->fetchAll(PDO::FETCH_ASSOC);
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
     print "</table>\n";
   }
   else{
     print "<label style='margin-left:170px;'>Sorry your search did not match our records! Please try again</label>";
   }
 } ?>

 </body>
</html>
