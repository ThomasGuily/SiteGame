<?php /* Template Name: Script - searchBar */ ?>

<?php
try {
    global $wpdb;
} catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
}

$event_selected = $_COOKIE["event"];
global $id_page_edit_player;
$roles = wp_get_current_user()->roles;

foreach ($roles as $role){
    if($role == "administrator"){
        $user_isAdmin = true; // Sécurité pour éviter d'accéder à la page de script de recherche sans les droits admin
    }
}

if(isset($wpdb) && $user_isAdmin){  // J'ai écrit cette ligne parce que pour avoir accès la database, j'ai été obligé de faire en sorte que ce fichier php soit une page "script" wordpress

    if(isset($_GET['username'])){

        $username = (String) trim($_GET['username']);

        $username_query = $wpdb->prepare("SELECT DISTINCT id_player FROM wp_participate 
                                                INNER JOIN wp_users ON wp_users.ID = wp_participate.id_player 
                                                WHERE wp_participate.id_tournament IN (SELECT id_tournament FROM wp_tournament WHERE event = %s) 
                                                AND wp_users.user_login LIKE %s",$event_selected,"$username%");
        $users = $wpdb->get_results($username_query);

        foreach ($users as $user) {
                $user_id = $user -> id_player;
                $user_query = $wpdb -> prepare("SELECT * FROM wp_users WHERE ID = %d", $user_id);
                $user_infos = $wpdb -> get_row($user_query);
                $last_name_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $user_id, "last_name");
                $last_name = $wpdb -> get_var($last_name_query);
                $first_name_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $user_id, "first_name");
                $first_name = $wpdb -> get_var($first_name_query);
                $phone_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $user_id, "phone_number");
                $phone = $wpdb -> get_var($phone_query);
                $birthdate_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $user_id, "birthdate");
                $birthdate = $wpdb -> get_var($birthdate_query);
                ?>
                <tr>
                    <td>
                        <?php echo $user_id ?>
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
                  WHERE wp_participate.id_player = %d AND wp_tournament.event = %s ORDER BY wp_tournament.name", $user_id, $event_selected);
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
                    WHERE wp_in_team.id_player = %d AND wp_tournament.event = %s ORDER BY wp_team.team_name", $user_id, $event_selected);
                        $user_teams = $wpdb -> get_results($user_teams_query);
                        foreach ($user_teams as $team) {
                            echo "{$team->team_name} ";
                        } ?>
                    </td>
                    <td>
                        <?php
                        $paid_query = $wpdb -> prepare("SELECT id_tournament, paid FROM wp_participate WHERE id_player = %d", $user_id);
                        $paid = $wpdb -> get_results($paid_query);

                        foreach($paid as $p){
                            $NT_query = $wpdb->prepare("SELECT name FROM wp_tournament WHERE id_tournament = %d", $p->id_tournament);
                            $NT = $wpdb->get_var($NT_query);
                            if ($p->paid  == 1) {
                                echo $NT. " a été payé.<br>";
                            }else{
                                echo $NT. " n'a été payé.<br>";
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo $phone ?>
                    </td>
                    <td>
                        <?php echo $birthdate ?>
                    </td>
                    <td>
                        <a href="?page_id=<?php echo $id_page_edit_player;?>&id=<?php echo $user_id; ?> ">
                            <input type="button" class="btn btn-dark" value="Edit" />
                        </a>
                    </td>
                </tr>
                <?php
            }
    }

    if(isset($_GET['teamName'])) {

        $teamName = (String) trim($_GET['teamName']);

        $teams_query = $wpdb->prepare("SELECT * FROM wp_team INNER JOIN wp_tournament ON wp_tournament.id_tournament = wp_team.id_tournament WHERE wp_tournament.event = %s AND wp_team.team_name LIKE %s", $event_selected, "$teamName%");
        $teams = $wpdb->get_results($teams_query);

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
    }

    if(isset($_GET['tournamentName'])) {

        $tournamentName = (String) trim($_GET['tournamentName']);

        $tournaments_query = $wpdb->prepare("SELECT * FROM wp_tournament WHERE event = %s AND name LIKE %s", $event_selected, "$tournamentName%");
        $tournaments = $wpdb->get_results($tournaments_query);
        foreach ($tournaments as $tournament) {
            $id = $tournament->id_tournament;
            $array[] = $id;
        }

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
    }
}


