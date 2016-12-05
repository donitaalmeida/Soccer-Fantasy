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


    $pfname = filter_input(INPUT_POST, "player_fname");
    $plname = filter_input(INPUT_POST, "player_lname");
    $pcountry = filter_input(INPUT_POST, "player_country");
    $p_height = filter_input(INPUT_POST, "player_height");
    $p_dob = filter_input(INPUT_POST, "player_dob");
    $p_dob = explode("/", $p_dob);
    $age = (date("md", date("U", mktime(0, 0, 0, $p_dob[0], $p_dob[1], $p_dob[2]))) > date("md")
    ? ((date("Y") - $p_dob[2]) - 1)
    : (date("Y") - $p_dob[2]));
    $tid = 22;
    $cid = 3;
    
    try{

      $uname = "raghu";  //add your db username here
      $passwd = "raghu";  //add the password here
      $dbname ="soccerfantasy";//add the dbname here
      //connection to database add your db user name and password here
      $con = new PDO("mysql:host=localhost;dbname=".$dbname,
                     $uname, $passwd);
      $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $vars = array(); 

      $query = "select max(player_id) from player";
      $ps = $con->prepare($query);
      $ps->execute();
      $pid = $ps->fetchColumn();
      $pid = $pid + 1;

       $query = "insert into player(player_id, player_age,player_fname,player_lname,country,height,club_id,team_id) values(:pid, :age , :fname , :lname , :country , :height , :cid , :tid)";
      $ps = $con->prepare($query);
      $vars[':pid'] = $pid;
      $vars[':age'] = $age;
      $vars[':fname'] = $age;
      $vars[':lname'] = $age;
      $vars[':country'] = $age;
      $vars[':height'] = $age;
      $vars[':cid'] = $cid;
      $vars[':tid'] = $tid;
      $ps->execute($vars);
      
 }
   catch(PDOException $ex) {
      echo 'ERROR: '.$ex->getMessage();
   }

?>

 </body>
</html>
