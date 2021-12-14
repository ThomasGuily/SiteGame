<?php /* Template Name: player_inscriptions */ ?>

<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}


$userid = wp_get_current_user() -> ID;
$user_tournaments_query = $wpdb -> prepare("SELECT *
            FROM wp_tournament INNER JOIN wp_participate
            ON wp_tournament.id_tournament = wp_participate.id_tournament
            WHERE wp_participate.id_player = %d", $userid);
$user_tournaments = $wpdb -> get_results($user_tournaments_query);

get_header();
?>

<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title> Mes inscriptions </title>
  <meta charset="utf-8">
</head>
<body>

  <h1 align="center">Mes inscriptions</h1>

  <?php
  if(sizeof($user_tournaments)){ ?>
  <div id="table1" class="table-responsive">
    <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
      <thead class="thead-dark">
        <tr>
            <th scope = "col"> Tournoi </th>
            <th scope = "col"> Evènement </th>
            <th scope = "col"> Equipe(s) </th>
            <th scope = "col"> Paiement </th>
        </tr>
      </thead>
      <tbody>
          <?php
          foreach ($user_tournaments as $tournament) {
                ?>
                <tr>
                    <td>
                        <?php echo $tournament -> name?>
                    </td>
                    <td>
                        <?php echo $tournament -> event?>
                    </td>
                    <td>
                        <?php
                        $team_query = $wpdb -> prepare("SELECT wp_team.team_name
                        FROM wp_team INNER JOIN wp_in_team
                        ON wp_team.id_team = wp_in_team.id_team
                        WHERE wp_in_team.id_player = %d AND wp_team.id_tournament = %d", $userid, $tournament->id_tournament);
                        $team = $wpdb -> get_var($team_query);
                        echo $team
                        ?>
                    </td>
                    <td>
                        <?php echo 'PAIEMENT'?>
                    </td>
                </tr>
                <?php
            }
        ?>
      </tbody>
    </table>
  </div>
      <?php
  }
  else{
      ?>
      <hr>
      <h5 align="center">Vous n'êtes inscrits à aucun tournoi</h5>
      <?php
  }
  ?>
</body>
</html>
