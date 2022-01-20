<?php /* Template Name: Modification d'une annonce */ ?>

<?php

try {
    global $wpdb;
} catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
}

$user = wp_get_current_user();
global $page_id_mes_annonces;

if(isset($_POST['choixEvent'])) {
    $eventChoisi = $_POST['choixEvent'];
}

if(isset($_GET['id_annonce'])){
    $id_annonce = $_GET['id_annonce'];

    $query_get_annonce = $wpdb->prepare("SELECT * FROM game.wp_tm_annonces WHERE id=%d",$id_annonce);
    $annonce = $wpdb->get_results($query_get_annonce)[0]; //l'élément à car il n'y en a d'office qu'un

    if($user->ID== $annonce->id_auteur){
        $type_annonce = $annonce->type_annonce;
    }
    else{
        $type_annonce = 0; // Ce statut signifie qu'il y a une erreur avec l'id de l'utilisateur
    }
}

if(isset($_POST['modifAnnonce'])) {

    $date = date("Y-m-d H-i-s");   //Lors de la modification c'est la nouvelle date qui est prise en compte
    $commentaire = $_POST['commentaire'];

    if($type_annonce == 1){
        //Pour remplir l'annonce, on a besoin de l'id_team qu'on a avec le post et l'id_tournament correspondant que l'on va rechercher dans la db
        $id_teamChoisi = $_POST['choixTeam'];

        $query = $wpdb->prepare("SELECT id_tournament FROM wp_team WHERE id_team = %d;", $id_teamChoisi);
        $result = $wpdb -> get_results($query);
        $id_tournoiChoisi = $result[0]->id_tournament;

        $wpdb -> update('wp_tm_annonces', array(
            'date' => $date,
            'statut' => 0,   //Attention, je sais pas encore comment je vais gérer le statut,
            // je pense que si l'annonce a eu un réponse on ne peut plus la modifier mais c'est pas encore fait ça
            'id_team' => $id_teamChoisi,
            'id_tournament' => $id_tournoiChoisi,
            'comment' => $commentaire,
        ),array('id'=>$id_annonce));
        wp_redirect('?page_id='.$page_id_mes_annonces);
    }

    if($type_annonce == 2){
        if(isset($_POST['choixTournament'])){
            //Pour remplir l'annonce, on a besoin de l'id_team qu'on a avec le post et l'id_tournament correspondant que l'on va rechercher dans la db
            $id_tournoiChoisi = $_POST['choixTournament'];
            $erreur_modif_annonce_tm = '';

            $wpdb -> update('wp_tm_annonces', array(
                'date' => $date,
                'statut' => 0, //Attention, je sais pas encore comment je vais gérer le statut,
                // je pense que si l'annonce a eu un réponse on ne peut plus la modifier mais c'est pas encore fait ça
                'id_tournament' => $id_tournoiChoisi,
                'comment' => $commentaire
            ),array('id'=>$id_annonce));
            wp_redirect('?page_id='.$page_id_mes_annonces);
        }
        else {
            $erreur_modif_annonce_tm = 'Aucun tournoi sélectionné';
        }
    }
}
get_header();
?>

<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Modifier votre annonce</title>
    <meta charset="utf-8">
</head>
<body>

<?php if(isset($_GET['id_annonce'])) {
    if($type_annonce){
        ?>
    <form method="post" id="formulaire" name="formulaire">
        <div class="container-fluid">
        <?php
        if($type_annonce == 1){
            $query1 = $wpdb->prepare("SELECT id_team FROM wp_in_team WHERE id_player = %d", $user->ID);
            $teams = $wpdb -> get_results($query1);
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
                        <input type="radio" name="choixTeam" <?php if($annonce->id_team == $id_team){ echo 'checked="checked"';}?> value="<?php echo $id_team?>">
                        <label style="font-size: large"> <b>Equipe <?php echo $team_name?> </b> </label>
                        <p> <?php echo 'Nombre de joueurs : '.$number_players.'<br>'.
                                'Evenement : '.$event.'<br>'.
                                'Tournoi : '.$tournament_name.'<br>';
                            ?></p>
                        <?php
                    }
                }
            }
        }
        if($type_annonce == 2){
            $query_event = $wpdb->prepare("SELECT event,name FROM wp_tournament WHERE id_tournament = %d", $annonce->id_tournament);
            $result = $wpdb->get_results($query_event);
            $old_event = $result[0]->event;
            $old_tournament_name = $result[0]->name;
            if(!isset($eventChoisi)) {
                $eventChoisi = $old_event;
            }
            ?>
            <h5>Vous aviez choisi</h5>
            <br>
            <label> Tournoi : <?php echo $old_tournament_name;?>
                <br><br> Event : <?php echo $old_event;?> </label>
            <hr>
            <div class="form-group col-12">
                <label for="event"> Changer d'événement : </label>
                <?php
                $events_query = $wpdb->prepare("SELECT DISTINCT event FROM wp_tournament");
                $events = $wpdb->get_results($events_query);
                ?>
                <form method="post" name="eventForm">
                    <select name = "choixEvent" class="custom-select col-6" onchange="if(this.value !== 0) { this.form.submit(); }">
                        <option selected disabled>Choisissez votre événement</option>
                        <?php foreach($events as $event) : ?>
                        <option id="<?php echo $event->event; ?>" name="event" value="<?php echo $event->event?>" >
                            <?php echo $event->event ?>
                            <?php endforeach; ?>
                        </option>
                    </select>
                </form>
            </div>
            <div class="form-group col-12">
                <?php
                if(isset($eventChoisi)){
                    $tournaments_query = $wpdb->prepare("SELECT id_tournament, name, number_players FROM wp_tournament WHERE event = %s", $eventChoisi);
                    $tournaments = $wpdb -> get_results($tournaments_query);
                    ?>
                    <label>Vous allez participer au <?php echo $eventChoisi;?></label>
                    <br>
                    <label for="tournoi"> Sélection du tournoi multijoueur : </label>
                    <select name = "choixTournament" class="custom-select col-6">
                        <option selected disabled> Choisissez votre tournoi</option>
                        <?php foreach($tournaments as $tournament) :
                            if ($tournament -> number_players > 1) { ?>
                                <option id="<?php echo $tournament->id_tournament; ?>" value="<?php echo $tournament->id_tournament?>" >
                                    <?php echo $tournament->name;
                                    ?>: Tournoi en équipe de <?php echo $tournament -> number_players ?> joueurs
                                </option>
                                <?php
                            }
                        endforeach; ?>
                    </select>
                    <?php
                }
                ?>
            </div>

            <?php
        }
        ?>
            <textarea id="commentaire" name="commentaire" rows="5"
                      maxlength="1000" placeholder="Votre message..."><?php echo $annonce->comment;?></textarea>
            <br>
            <div>
                <input type="submit" class="btn-dark" name="modifAnnonce" value="Modifier votre annonce">
                <?php
                if(isset($erreur_modif_annonce_tm)) {
                    echo '<font color="red">'.$erreur_modif_annonce_tm."</font>";
                }
                ?>
                <br><br>
                <input type="button" class="btn-dark" value="Annuler" onclick="window.location.href='?page_id=<?php echo $page_id_mes_annonces;?>'">
            </div>
        </div>
    </form>
        <?php
    }
    else{
        ?> <label style="color: red">Il semblerait que cette annonce ne vous appartienne pas, retournez sur la page de vos annonces et réessayez<br>Si le problème persiste, contacter le support du site</label>
        <?php
    }
}

else{
?><label style="color: red">Link error, retournez sur la page de vos annonces et réessayez<br>Si le problème persiste, contacter le support du site</label>
<?php
}
?>
</body>
</html>
