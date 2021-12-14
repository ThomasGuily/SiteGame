<?php /* Template Name: team_making */ ?>

<?php

try {
    global $wpdb;
} catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
}

global $id_page_ajout_team;
global $id_page_ajout_player;
global $id_page_mes_annonces;
global $id_page_reply;


$user = wp_get_current_user();
if($user -> user_status == 1) {
    $statut_valid = true;
}

$annonces_query = $wpdb->prepare("SELECT * FROM wp_tm_annonces");
$annonces = $wpdb->get_results($annonces_query);

if(isset($_POST['choixTypeAnnonce'])){
    $typeAnnonceChoisi = $_POST['choixTypeAnnonce'];
}

get_header();
?>

<html>

<head>
    <title>Team Making</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta charset="utf-8">
</head>

<body>
    <div align="center">
        <label>Vous êtes à la recherche d'un joueur pour compléter votre équipe multijoueur ? <br>
            Vous n'avez pas d'équipe et vous souhaitez en trouver une qui puisse vous accueillir ? <br>
            Cet espace de Team making est fait pour vous ! <br>
        </label>
        <br><br>
        <?php if($statut_valid) { ?>
            <label style="color:green">Vous pouvez dès à présent ajouter une annonce pour trouver ce que vous souhaitez.</label>
            <br><br>
            <input type="button" style="height:50px;width:250px" value="Je recherche une équipe" onclick="window.location.href='?page_id=<?php echo $id_page_ajout_team;?>'">
            <input type="button" style="height:50px;width:250px" value="Je recherche un joueur" onclick="window.location.href='?page_id=<?php echo $id_page_ajout_player;?>'">
            <br><br>
            <input type="button" style="height:50px;width:250px" value="Mes annonces" onclick="window.location.href='?page_id=<?php echo $id_page_mes_annonces;?>'">
            <?php
        }
        else { ?>
            <label style="color:red">
                Votre compte n'est pas validé ou vous n'êtes pas connecté !
                <br>
                Vous ne pouvez pas ajouter une annonce pour l'instant, veuillez d'abord vous connecter.
            </label>
            <?php
        }
        ?>
    </div>
    <br><br>

    <?php
    if(sizeof($annonces)){
    ?>

    <div class="container-fluid">
        <form style="width: 50%" method="post" name="typeAnnonceForm">
            <select name = "choixTypeAnnonce" class="custom-select col-6" onchange="this.form.submit();">
                <option <?php
                        if($typeAnnonceChoisi == 0){
                            echo 'selected ';
                        }
                        ?>value="0">Toutes les annonces</option>
                <option <?php
                        if($typeAnnonceChoisi == 1){
                            echo 'selected ';
                        }
                        ?>value="1">Recherche d'un joueur</option>
                <option <?php
                        if($typeAnnonceChoisi == 2){
                            echo 'selected ';
                        }
                        ?>value="2">Recherche d'une équipe</option>
            </select>
        </form>
        <br>
        <div class="table-responsive">
            <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
                <thead class="thead-dark">
                <tr>
                    <th scope = "col"> Annonce </th>
                    <th scope = "col"> Tournoi </th>
                    <th scope = "col"> Evénement </th>
                    <th scope = "col"> Date </th>
                    <th scope = "col" style="max-width: 250px"> Message </th>
                    <th scope = "col"> </th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($annonces as $annonce) {
                    //Affichage en fonction du type d'annonce et on peut pas voir ses propres annonces
                    if(($typeAnnonceChoisi == 0 || $typeAnnonceChoisi == $annonce->type_annonce) && $user->ID != $annonce->id_auteur){
                        ?>
                        <tr>
                            <td>
                                <?php
                                $auteur_query = $wpdb -> prepare("SELECT user_login FROM wp_users WHERE ID = %d", $annonce -> id_auteur);
                                $auteur_name = $wpdb -> get_var($auteur_query);

                                $team_query = $wpdb -> prepare("SELECT team_name FROM wp_team WHERE id_team = %d", $annonce -> id_team);
                                $team_name = $wpdb -> get_var($team_query);

                                if($annonce->type_annonce == 1){
                                    echo $auteur_name.' recherche un joueur pour son équipe '. '<font color="#ff8c00">'.$team_name."</font>";
                                }
                                if($annonce->type_annonce == 2){
                                    echo $auteur_name.' recherche une équipe';
                                }
                                ?>
                            </td>

                            <td>
                                <?php
                                    $tournament_query = $wpdb->prepare("SELECT name FROM wp_tournament WHERE id_tournament = %d", $annonce->id_tournament);
                                    $tournament_name = $wpdb->get_var($tournament_query);
                                    echo $tournament_name;
                                ?>
                            </td>

                            <td>
                                <?php
                                    $event_query = $wpdb -> prepare("SELECT event FROM wp_tournament WHERE id_tournament = %d", $annonce -> id_tournament);
                                    $event_name = $wpdb -> get_var($event_query);
                                    echo $event_name;
                                ?>
                            </td>

                            <td>
                                <?php echo $annonce->date; ?>
                            </td>

                            <td style="max-width: 250px">
                                <?php echo $annonce->comment; ?>
                            </td>

                            <td>
                                <button type="submit" name="reply_action" class="btn-dark"
                                    <?php
                                    if(!$statut_valid){
                                        echo ' disabled="disabled" ';
                                    }
                                    ?>
                                        value="<?php echo $annonce->id;?>"
                                        onclick="window.location.href='<?php echo '?page_id='.$id_page_reply.'&id_annonce='.$annonce->id;?>'">
                                    Répondre</button>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
        <?php
    }
    else {
        ?>
        <hr>
        <div align="center">
            <h5>Aucune annonce n'a été postée</h5></div>
    <?php
    }
    ?>
</body>
</html>

