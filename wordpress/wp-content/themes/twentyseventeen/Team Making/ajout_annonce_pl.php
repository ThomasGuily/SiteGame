<?php /* Template Name: ajout annonce player */ ?>

<?php

try {
    global $wpdb;
} catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
}

global $id_page_mes_annonces;
global $id_page_main_team_making;

$user = wp_get_current_user();
$user_id = $user->ID;

$query1 = $wpdb->prepare("SELECT id_team FROM wp_in_team WHERE id_player = %d", $user_id);
$teams = $wpdb->get_results($query1);

if(isset($_POST['postAnnonce'])) {
    if(isset($_POST['choixTeam'])){
        //Pour remplir l'annonce, on a besoin de l'id_team qu'on a avec le post et l'id_tournament correspondant que l'on va rechercher dans la db
        $id_teamChoisi = $_POST['choixTeam'];

        $query = $wpdb->prepare("SELECT id_tournament FROM wp_team WHERE id_team = %d;", $id_teamChoisi);
        $result = $wpdb -> get_results($query);
        $id_tournoiChoisi = $result[0]->id_tournament;

        $commentaire = $_POST['commentaire'];
        $date = date("Y-m-d H-i-s");

        $wpdb -> insert('wp_tm_annonces', array(
            'id_auteur' => $user_id,
            'date' => $date,
            'type_annonce' => 1,
            'statut' => 0,
            'id_team' => $id_teamChoisi,
            'id_tournament' => $id_tournoiChoisi,
            'comment' => $commentaire
        ));

        $erreur = '';

        wp_redirect('?page_id='.$id_page_mes_annonces);
    }
    else {
        $erreur = 'Aucune équipe sélectionnée';
    }
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
<form method="post" id="formulaire" name="formulaire">
    <div class="container-fluid">
        <label>
            Vous êtes à la recherche d'un joueur pour compléter votre équipe multijoueur ? <br>
            Vous êtes au bon endroit !
        </label>
        <hr>
        <?php
        if(sizeof($teams)){

        ?>
            <label>Pour quelle équipe recherchez-vous un joueur ?</label>
            <br>
            <h4>Vos tournois multijoueurs sont : </h4>
            <br>

            <?php

            foreach($teams as $item){
                $query2 = $wpdb->prepare("SELECT id_tournament, team_name FROM wp_team WHERE id_team = %d;", $item->id_team);
                $teams2 = $wpdb -> get_results($query2);

                $query3 = $wpdb->prepare("SELECT name, event, number_players FROM wp_tournament WHERE id_tournament = %d",$teams2[0]->id_tournament);
                $tournaments = $wpdb->get_results($query3);

                //Pour chaque équipe du joueur
                foreach ($teams2 as $item2) {

                    $id_team = $item->id_team;
                    $id_tournament = $item2->id_tournament;
                    $team_name = $item2->team_name;
                    $tournament_name = $tournaments[0]->name;
                    $number_players = $tournaments[0]->number_players;
                    $event = $tournaments[0]->event;

                    if($number_players > 1) { ?>
            <input type="radio" name="choixTeam" value="<?php echo $id_team?>">
            <label style="font-size: large"> <b>Equipe <?php echo $team_name?> </b> </label>
                <p> <?php echo 'Nombre de joueurs : '.$number_players.'<br>'.
                        'Evenement : '.$event.'<br>'.
                        'Tournoi : '.$tournament_name.'<br>';
                    ?></p>
            <?php
                    }
                }
            }
            ?>
            <br>
            Veuillez écrire un message qui accompagnera votre annonce
            <br><br>
            <textarea id="commentaire" name="commentaire" rows="5"
                      maxlength="1000" placeholder="Votre message..."
                      required="required" form="formulaire"></textarea>
            <br>
            <div>
                <input type="submit" class="btn-dark" name="postAnnonce" value="Poster l'annonce">
                <?php
                if(isset($erreur)) {
                    echo '<font color="red">'.$erreur."</font>";
                }
                ?>
                <br><br>
                <input type="button" class="btn-dark" value="Annuler" onclick="window.location.href='?page_id=<?php echo $id_page_main_team_making;?>'">
            </div>
        <?php
        } else{
            ?>
            <div align="center">
                <h5>Vous ne faites partie d'aucune équipe multijoueur</h5>
                <br>
                <input type="button" class="right" value="Retour" onclick="window.location.href='?page_id=<?php echo $id_page_main_team_making;?>'">
            </div>
            <?php
        }
        ?>
    </div>
</form>
</body>
</html>
