<?php /* Template Name: ajout annonce team */ ?>

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

if(isset($_POST['choixEvent'])) {
    $eventChoisi = $_POST['choixEvent'];
}

if(isset($_POST['postAnnonce'])) {

    if(isset($_POST['choixTournament'])){
        //Pour remplir l'annonce, on a besoin de l'id_team qu'on a avec le post et l'id_tournament correspondant que l'on va rechercher dans la db
        $id_tournoiChoisi = $_POST['choixTournament'];

        $commentaire = $_POST['commentaire'];
        $date = date("Y-m-d H-i-s");

        $wpdb -> insert('wp_tm_annonces', array(
            'id_auteur' => $user_id,
            'date' => $date,
            'type_annonce' => 2,
            'statut' => 0,
            'id_team' => -1,
            'id_tournament' => $id_tournoiChoisi,
            'comment' => $commentaire
        ));

        $erreur = '';

        wp_redirect('?page_id='.$id_page_mes_annonces);
    }
    else {
        $erreur = 'Aucun tournoi sélectionné';
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
            Vous souhaitez participer à un tournoi multijoueur mais vous n'avez pas d'équipe ? <br>
            Vous êtes au bon endroit !
        </label>
        <hr>
        <label>Dans quel tournoi souhaiteriez-vous jouer ?</label>
        <br>
        <br>

        <div class="form-group col-12">
            <label for="event"> Sélection de l'événement : </label>
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
    </div>
</form>
</body>
</html>
