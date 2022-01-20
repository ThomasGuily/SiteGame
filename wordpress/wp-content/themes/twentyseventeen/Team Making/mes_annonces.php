<?php /* Template Name: Mes annonces */ ?>

<?php

try {
    global $wpdb;
} catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
}

global $id_page_main_team_making;
global $id_page_modif_annonce;

$user = wp_get_current_user();
if($user -> user_status == 1) {
    $statut_valid = true;
}

$annonces_query = $wpdb->prepare("SELECT * FROM wp_tm_annonces WHERE id_auteur = %d", $user->ID);
$annonces = $wpdb->get_results($annonces_query);

$typeAnnonceChoisi = $_POST['choixTypeAnnonce'];

if(isset($_POST['edit_action'])) {
    $id_annonce = $_POST['edit_action'];
    wp_redirect(WP_HOME.'/?page_id='.$id_page_modif_annonce.'&id_annonce='.$id_annonce);
}

if(isset($_POST['supp_action'])){
    $id_annonce = $_POST['supp_action'];
    $supp_query = $wpdb->delete('wp_tm_annonces', array( 'id' => $id_annonce));
    wp_redirect('?page_id=758'); //Refresh la page
}

get_header();

?>

<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Ajouter votre annonce</title>
    <meta charset="utf-8">
</head>
<body>
<h1 align="center">Mes annonces</h1>
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
        <input type="button" class="right" value="Retour" onclick="window.location.href='?page_id=<?php echo $id_page_main_team_making;?>'">
    </form>
    <br>
    <form method="post" style="margin-left:10px">
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
                <th scope = "col"> </th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($annonces as $annonce) {
                if($typeAnnonceChoisi == 0 || $typeAnnonceChoisi == $annonce->type_annonce){
                    ?>
                    <tr>
                        <td>
                            <?php
                            $team_query = $wpdb -> prepare("SELECT team_name FROM wp_team WHERE id_team = %d", $annonce -> id_team);
                            $team_name = $wpdb -> get_var($team_query);

                            if($annonce->type_annonce == 1){
                                echo 'Vous recherchez un joueur pour votre équipe '. '<font color="#ff8c00">'.$team_name."</font>";
                            }
                            if($annonce->type_annonce == 2){
                                echo 'Vous recherchez une équipe';
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
                            <button type="submit" name="edit_action" value="<?php echo $annonce->id;?>">Modifier</button>
                        </td>
                        <td>
                            <button type="submit" name="supp_action" value="<?php echo $annonce->id;?>">Supprimer</button>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    </form>
</div>
            <?php
            }
            else {
                ?>

<br>
<hr>
<div align="center">
    <h5>Vous n'avez pas encore posté d'annonces</h5>

    <br>
    <input type="button" class="right" value="Retour" onclick="window.location.href='?page_id=<?php echo $id_page_main_team_making;?>'">
</div>
            <?php
        }
?>
</body>
</html>
