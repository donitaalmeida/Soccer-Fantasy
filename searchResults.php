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
      $dbname ="soccer_latest";//add the dbname here
      //connection to database add your db user name and password here
      $con = new PDO("mysql:host=localhost;dbname=".$dbname,
                     $uname, $passwd);
      $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $vars = array();
      switch ($searchtype) {
        case "clubmem":
          $clubname = filter_input(INPUT_POST, "clubname");
          $query = "SELECT player.player_fname as 'First Name',player.player_lname as 'Last Name',player.player_age as 'Age',player.height as 'Height',player.country as 'Country', player.goals as 'Total goals'
                    FROM club,player
                    WHERE club.club_id=player.club_id
                    AND UPPER(club.club_name) LIKE UPPER(:clubname)";
          $ps = $con->prepare($query);
          $vars[':clubname'] = $clubname;
          break;

        case "clubgoal":
          $goalno = filter_input(INPUT_POST, "goalno");
          $query ="SELECT club.club_name as 'Club Name', club.country as 'Country',CONCAT(manager.first_name,' ',manager.last_name) as 'Manager', sum(player.goals) as 'Total Goals'
                  FROM club,player,manager
                  WHERE player.club_id=club.club_id
                  AND manager.manager_id=club.manager_id
                  GROUP BY club.club_id
                  HAVING SUM(player.goals)>:goalno";
          $ps = $con->prepare($query);
          $vars[':goalno'] = $goalno;
          break;

        case "playerstat":
        $query = "SELECT p.player_fname as FirstName, p.player_lname as LastName, p.player_age as Age, t.team_name as TeamName, p.goals as Goals
                  FROM player p,national_team t
                  WHERE p.team_id = t.team_id
                  order by goals desc LIMIT 10";
          $ps = $con->prepare($query);
          break;

        case "tourstat":
          $tname = filter_input(INPUT_POST, "tourname");
          $tname = "%" . $tname . "%";
          $query = " SELECT game_name as Game, stadium_name as Stadium, nt1.team_name as 'Team 1', nt2.team_name as 'Team 2', game_score as 'Team1 Score', opponent_score as 'Team2 Score'
                    FROM game g inner join tournament_game tg on g.game_id = tg.game_id inner join tournament t on t.tournament_id = tg.tournament_id inner join national_team nt1 on nt1.team_id = tg.team1 inner join national_team nt2 on nt2.team_id = tg.team2 inner join stadium s on s.stadium_id = g.venue
                    WHERE tournament_name like :name";
          $ps = $con->prepare($query);
          $vars[':name'] = $tname;
          break;

        case "playerdet":
          $playername = filter_input(INPUT_POST, "playername");
          $playername = "%" . $playername . "%";
          $height = filter_input(INPUT_POST, "height");
          $age = filter_input(INPUT_POST, "age");
          $query ="SELECT p.player_fname as 'First Name', p.player_lname as 'Last Name', p.player_age as 'Player Age', p.height as 'Height', p.country as 'Country', t.team_name as 'Team Name', c.club_name as 'Club Name', p.goals as Goals
                  FROM player p left outer join national_team t on p.team_id = t.team_id left outer join club c on p.club_id = c.club_id ";

          if($playername!=null||$height!=null||$age!=null){
            $query.=" WHERE ";
            if($playername!=null){
              $query.= "( player_fname like :playername || player_lname like :playername ) AND ";
              $vars[':playername'] = $playername;
            }
            if($height!=null){
              $query.="height >= :height AND ";
              $vars[':height'] = $height;
            }
            if($age!=null){
              $query.="player_age < :age AND ";
              $vars[':age'] = $age;
            }
          }
          $query.= "1=1 GROUP BY player_id ORDER BY Goals desc";
          $ps = $con->prepare($query);
          break;

        case "teamstat":
          $query = "SELECT nt.team_name as 'Team Name', count(tg.game_id) as 'Games Played',count(distinct case when (nt.team_id = tg.team1 and  tg.game_score > tg.opponent_score) || (nt.team_id = tg.team2 and tg.opponent_score > tg.game_score) then tg.game_id else null end) as 'Games Won', count(distinct case when (nt.team_id = tg.team1 and  tg.game_score < tg.opponent_score) || (nt.team_id = tg.team2 and tg.opponent_score < tg.game_score) then tg.game_id else null end) as 'Games Lost', count(distinct case when ( tg.game_score = tg.opponent_score) then tg.game_id else null end) as 'Games Draw' FROM
          national_team nt left outer join tournament_game tg on ( nt.team_id = tg.team1 || nt.team_id =  tg.team2) group by nt.team_id
          order by count(tg.game_id) desc";
          $ps = $con->prepare($query);
          break;

        case "teamintour":
          $query = "SELECT DISTINCT national_team.team_name as 'Team Name',tournament.tournament_name as 'Tournament' from national_team, tournament_game, tournament WHERE (national_team.team_id=tournament_game.team1 OR national_team.team_id=tournament_game.team2) AND tournament_game.tournament_id=tournament.tournament_id ORDER BY tournament.tournament_name";
          $ps = $con->prepare($query);
          break;
          case "teaminleague":
          $query = "SELECT DISTINCT club.club_name as 'Club Name',league.league_name as 'League' from club, league_game, league WHERE (club.club_id=league_game.home_club OR club.club_id=league_game.away_club) AND league_game.league_id=league.league_id ORDER BY league.league_name";
          $ps = $con->prepare($query);
          break;

          case "leaguestat":
          $query = " SELECT league_name as 'leagueName',game_name as 'game', stadium_name as 'stadium', c1.club_name as 'clubName', c2.club_name as 'club2Name', club_score as 'clubScore', opponent_score as 'opponentScore'
                FROM game g inner join league_game lg on g.game_id = lg.game_id
                inner join league l on l.league_id = lg.league_id
                inner join club c1 on c1.club_id = lg.home_club
                inner join club c2 on c2.club_id = lg.away_club
                inner join stadium s on s.stadium_id = g.venue
                WHERE league_name like :name";
                  $leaguename = filter_input(INPUT_POST, "leaguename");
                $ps = $con->prepare($query);
                  $vars[':name'] = $leaguename;
            break;

        case "compteam":
          $team1="%".filter_input(INPUT_POST, "team1")."%";
          $team2="%".filter_input(INPUT_POST, "team2")."%";
          $query ="SELECT distinct g.game_name as 'Game', g.game_type as 'Game Type', s.stadium_name as 'Stadium', s.country as 'Country', t.game_score as 'Team1 Score',t.opponent_score as 'Team2 Score'
          FROM tournament_game t, game g, stadium s
          WHERE t.game_id=g.game_id
          AND s.stadium_id= g.venue AND t.team1 IN
            (SELECT national_team.team_id FROM national_team WHERE national_team.team_name LIKE :team1 OR national_team.team_name LIKE :team2)
                AND t.team2 IN
                    (SELECT national_team.team_id FROM national_team  WHERE national_team.team_name LIKE :team1 OR national_team.team_name LIKE :team2)";
          $ps = $con->prepare($query);
            $vars[':team1'] = $team1;
            $vars[':team2'] = $team2;
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
