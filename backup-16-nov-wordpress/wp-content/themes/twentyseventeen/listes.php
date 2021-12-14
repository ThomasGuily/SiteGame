<?php /* Template Name: listes */ ?>
<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}
get_header();
$event_selected = $_COOKIE["event"];
?>
<script type="text/javascript">
function close_table() {
  document.getElementById('teams_invisible').style.display= "none";
  document.getElementById('tournaments_invisible').style.display= "none";
  document.getElementById('users_invisible').style.display= "none";
}
function show_table_users(){
  close_table();
  document.getElementById('users_invisible').style.display="block";
}
function show_table_teams(){
  close_table();
  document.getElementById('teams_invisible').style.display="block";
}
function show_table_tournaments(){
  close_table();
  document.getElementById('tournaments_invisible').style.display="block";
}
</script>
<?php
$tournaments_query = $wpdb->prepare("SELECT * FROM wp_tournament WHERE event = %s", $event_selected);
$tournaments = $wpdb -> get_results($tournaments_query);
foreach ($tournaments as $tournament) {
  $id = $tournament -> id_tournament;
  $array[] = $id;
}
$users_id_query = $wpdb->prepare("SELECT DISTINCT id_player FROM wp_participate WHERE id_tournament IN (SELECT id_tournament FROM wp_tournament WHERE event = %s)", $event_selected);
$users_id = $wpdb -> get_results($users_id_query);
$teams_query = $wpdb->prepare("SELECT * FROM wp_team INNER JOIN wp_tournament ON wp_tournament.id_tournament = wp_team.id_tournament WHERE wp_tournament.event = %s", $event_selected) ;
$teams = $wpdb -> get_results($teams_query);
?>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title> Base de données </title>
  <meta charset="utf-8">
</head>
<body>
  <div class="container-fluid">
    <input type="button" class="btn btn-dark" value="Afficher les utilisateurs" onclick="javascript:show_table_users();" />
    <input type="button" class="btn btn-dark" value="Afficher les équipes" onclick="javascript:show_table_teams();" />
    <input type="button" class="btn btn-dark" value="Afficher les tournois" onclick="javascript:show_table_tournaments();" />
  </div>
  <div id="users_invisible" style="display:none;">
    <div class="table-responsive">
      <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
        <thead class="thead-dark">
          <tr>
            <th scope = "col" width="2%"> ID </th>
            <th scope = "col"> Pseudo </th>
            <th scope = "col"> Nom </th>
            <th scope = "col"> Adresse mail </th>
            <th scope = "col"> Date d'inscription </th>
            <th scope = "col"> Tournoi(s) </th>
            <th scope = "col"> Equipe(s) </th>
            <th scope = "col"> Paiement </th>
            <th scope = "col"> Téléphone </th>
            <th scope = "col"> Date de naissance </th>
            <th scope = "col" width="5%"> </th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($users_id as $user) {
            $userid = $user -> id_player;
            $user_query = $wpdb -> prepare("SELECT * FROM wp_users WHERE ID = %d", $userid);
            $user_infos = $wpdb -> get_row($user_query);
            $last_name_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "last_name");
            $last_name = $wpdb -> get_var($last_name_query);
            $first_name_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "first_name");
            $first_name = $wpdb -> get_var($first_name_query);
            $phone_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "phone_number");
            $phone = $wpdb -> get_var($phone_query);
            $birthdate_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "birthdate");
            $birthdate = $wpdb -> get_var($birthdate_query);
            ?>
            <tr>
              <td>
                <?php echo $userid ?>
              </td>
              <td>
                <?php echo $user_infos -> user_login ?>
              </td>
              <td>
                <?php echo "{$first_name} {$last_name}" ?>
              </td>
              <td>
                <?php echo $user_infos -> user_email ?>
              </td>
              <td>
                <?php echo $user_infos -> user_registered ?>
              </td>
              <td>
                <?php
                $user_tournaments_query = $wpdb -> prepare("SELECT wp_tournament.name
                  FROM wp_tournament INNER JOIN wp_participate
                  ON wp_tournament.id_tournament=wp_participate.id_tournament
                  WHERE wp_participate.id_player = %d AND wp_tournament.event = %s ORDER BY wp_tournament.name", $userid, $event_selected);
                  $user_tournaments = $wpdb -> get_results($user_tournaments_query);
                  foreach ($user_tournaments as $tournament) {
                    echo "{$tournament->name} ";
                  } ?>
                </td>
                <td>
                  <?php
                  $user_teams_query = $wpdb -> prepare("SELECT wp_team.team_name
                    FROM wp_team
                    INNER JOIN wp_in_team ON wp_in_team.id_team=wp_team.id_team
                    INNER JOIN wp_tournament on wp_team.id_tournament = wp_tournament.id_tournament
                    WHERE wp_in_team.id_player = %d AND wp_tournament.event = %s ORDER BY wp_team.team_name", $userid, $event_selected);
                    $user_teams = $wpdb -> get_results($user_teams_query);
                    foreach ($user_teams as $team) {
                      echo "{$team->team_name} ";
                    } ?>
                  </td>
                  <td>
                    <?php
                    $paid_query = $wpdb -> prepare("SELECT wp_usermeta.meta_value
                      FROM wp_users INNER JOIN wp_usermeta
                      ON wp_usermeta.user_id=wp_users.ID
                      WHERE wp_usermeta.user_id = %d AND wp_usermeta.meta_key = %s", $userid, 'paid');
                      $paid = $wpdb -> get_var($paid_query);
                      if ($paid  == 1) {
                        echo 'Payé';
                      }
                      else {
                        echo 'Non payé';
                      } ?>
                    </td>
                    <td>
                      <?php echo $phone ?>
                    </td>
                    <td>
                      <?php echo $birthdate ?>
                    </td>
                    <td>
                      <a href="?page_id=744&id=<?php echo $userid; ?> ">
                        <input type="button" class="btn btn-dark" value="Edit" />
                      </a>
                    </td>
                  </tr>
                  <?php
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
        <div id="tournaments_invisible" style="display:none;">
          <div class="table-responsive">
            <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
              <thead class="thead-dark">
                <tr>
                  <th scope = "col" width="2%"> ID </th>
                  <th scope = "col"> Nom </th>
                  <th scope = "col"> Nombre de joueurs par équipe </th>
                  <th scope = "col"> Nombre de joueurs/équipes inscrites </th>
                  <th scope = "col"> Plage horaire </th>
                  <th scope="col" width="5%"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($tournaments as $tournament) {
                  ?>
                  <tr>
                    <td>
                      <?php echo $tournament -> id_tournament ?>
                    </td>
                    <td>
                      <?php echo $tournament -> name ?>
                    </td>
                    <td>
                      <?php $num_by_team = $tournament -> number_players;
                      if ($num_by_team == 1) {
                        echo "Solo";
                      }
                      else {
                        echo $num_by_team;
                      }?>
                    </td>
                    <td>
                      <?php
                      $num_inscrits_query = $wpdb -> prepare("SELECT COUNT(*) FROM wp_participate WHERE id_tournament = %d", $tournament -> id_tournament);
                      $num_inscrits = $wpdb -> get_var($num_inscrits_query);
                      if ($num_by_team == 1) {
                        echo $num_inscrits;
                      }
                      else {
                        $num_teams_inscrites_query = $wpdb -> prepare("SELECT COUNT(*) FROM wp_team WHERE id_tournament = %d", $tournament -> id_tournament);
                        $num_teams_inscrites = $wpdb -> get_var($num_teams_inscrites_query);
                        echo $num_teams_inscrites, " (", $num_inscrits, " joueurs)";
                      }
                      ?>
                    </td>
                    <td>
                      <?php echo $tournament -> time_slot ?>
                    </td>
                    <td>
                      <input type="button" class="btn btn-dark" value="Edit" />
                    </td>
                  </tr>
                  <?php
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
        <div id="teams_invisible" style="display:none;">
          <div class="table-responsive">
            <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
              <thead class="thead-dark">
                <tr>
                  <th scope = "col" width="2%"> ID </th>
                  <th scope = "col"> Nom </th>
                  <th scope = "col"> Chef d'équipe </th>
                  <th scope = "col"> Tournoi </th>
                  <th scope = "col"> Membres de l'équipe </th>
                  <th scope = "col"> Date de création </th>
                  <th scope = "col" width="5%"> </th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($teams as $team) {
                  ?>
                  <tr>
                    <td>
                      <?php echo $team -> id_team ?>
                    </td>
                    <td>
                      <?php echo $team -> team_name ?>
                    </td>
                    <td>
                      <?php
                      $team_leader_query = $wpdb -> prepare("SELECT user_login FROM wp_users WHERE ID = %d", $team -> id_team_leader);
                      $team_leader = $wpdb -> get_var($team_leader_query);
                      echo $team_leader ?>
                    </td>
                    <td>
                      <?php
                      $tournament_query = $wpdb -> prepare("SELECT name FROM wp_tournament WHERE id_tournament = %d", $team -> id_tournament);
                      $tournament= $wpdb -> get_var($tournament_query);
                      echo $tournament ?>
                    </td>
                    <td>
                      <?php
                      $team_members_query = $wpdb -> prepare("SELECT wp_users.user_login
                        FROM wp_users
                        INNER JOIN wp_in_team
                        ON wp_in_team.id_player = wp_users.ID
                        WHERE wp_in_team.id_team = %d ORDER BY wp_users.user_login", $team -> id_team );
                        $team_members = $wpdb -> get_results($team_members_query);
                        foreach ($team_members as $team_member) {
                          echo "{$team_member -> user_login} ";
                        }
                        ?>
                      </td>
                      <td>
                        <?php echo $team -> creation ?>
                      </td>
                      <td>
                        <input type="button" class="btn btn-dark" value="Edit" />
                      </td>
                    </tr>
                    <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </body>
        </html>
