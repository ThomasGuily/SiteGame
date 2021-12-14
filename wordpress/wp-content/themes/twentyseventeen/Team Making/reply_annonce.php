<?php /* Template Name: reply_annonce */ ?>

<?php

try {
    global $wpdb;
} catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
}

include("wp-includes/mailer/mailinit.php");

if(isset($_GET['id_annonce'])) {
    $user = wp_get_current_user();

    global $id_page_main_team_making;

    $annonce_query = $wpdb->prepare("SELECT * FROM wp_tm_annonces WHERE id = %d",$_GET['id_annonce']);
    $resultAnnonce = $wpdb->get_results($annonce_query);
    $annonce = $resultAnnonce[0];

    $auteur_query = $wpdb->prepare("SELECT user_login, user_email FROM wp_users WHERE ID = %d", $annonce->id_auteur);
    $auteur = $wpdb->get_results($auteur_query)[0];
    $mail_auteur = $auteur->user_email;
    $name_auteur = $auteur->user_login;

    if($annonce->type_annonce == 1) {
        $team_query = $wpdb->prepare("SELECT team_name FROM wp_team WHERE id_team = %d", $annonce->id_team);
        $team_name = $wpdb->get_var($team_query);
    }

    if($annonce->type_annonce == 2) {
        // On va chercher les équipes de l'utilisateur connecté
        $query1 = $wpdb->prepare("SELECT id_team FROM wp_in_team WHERE id_player = %d", $user->ID);
        $teams = $wpdb->get_results($query1);

        $team_query = $wpdb->prepare("SELECT team_name FROM wp_team WHERE id_team = %d", $annonce->id_team);
        $team_name = $wpdb->get_var($team_query);
    }

    $tournament_query = $wpdb->prepare("SELECT name FROM wp_tournament WHERE id_tournament= %d", $annonce->id_tournament);
    $tournament_name = $wpdb->get_var($tournament_query);
}

if(isset($_POST['replyAnnonce']) && isset($_POST['replyComment']) && $_GET['id_annonce']){

    $bodyMessage = 'Message vide';

    // Comme pour l'inscription, il faut changer le type de texte des mails, de textplain en un autre

    // Le replier qui demande à intégrer l'équipe
    if($annonce->type_annonce == 1){
        $bodyMessage =
            '<html>
                  <body>'.
            '<h4> Bonjour '. $name_auteur . ',</h4>' .
            $user->user_login.
            ' a réagi à votre annonce.'.
            '<br>'.
            'Il/Elle souhaite intégrer votre équipe '. $team_name. ' pour le tournoi de '. $tournament_name .'.'.
            '<br><br>'.
            'Vous pouvez prendre contact avec lui/elle via son adresse mail : '. $user->user_email.
            '<br><br>'.
            'Cordialement'.
            '<br><br>'.
            'Le Cercle Magellan'.
            '</body>
                  </html>';
    }

    // Le replier qui propose à l'annonceur d'intégrer son équipe
    if($annonce->type_annonce == 2){

        $id_team_replier = $_POST['id_team_replier'];
        $team_name_replier_query = $wpdb->prepare('SELECT team_name FROM wp_team WHERE id_team = %d', $id_team_replier);
        $team_name_replier = $wpdb->get_var($team_name_replier_query);

        $bodyMessage = '<html>
                  <body>'.
            '<h4> Bonjour '. $name_auteur . ',</h4>' .
            $user->user_login.
            ' a réagi à votre annonce.'.
            '<br>'.
            'Il/Elle vous invite à rejoindre son équipe '. $team_name_replier. ' pour le tournoi de '. $tournament_name .'.'.
            '<br><br>'.
            'Vous pouvez prendre contact avec lui/elle via son adresse mail : '. $user->user_email.
            '<br><br>'.
            'Cordialement'.
            '<br><br>'.
            'Le Cercle Magellan'.
            '</body>
                  </html>';
    }

    /////// Envoi du mail de réponse ///////
    // Initialisation du mailer
    $mailer = new Swift_Mailer($transport);
    //  Setup du mail
    $message_swift = (new Swift_Message('Game 22 - '. $user->user_login .' a réagi à votre annonce'))
        ->setFrom(['game22@magellan.fpms.ac.be'=>'Réponse pour votre annonce'])
        ->setTo($mail_auteur)
        ->setContentType("text/html")
        ->setBody($bodyMessage);

    // Envoi du mail
    $resultMail=$mailer->send($message_swift);

    $id_annonce = $_GET['id_annonce'];
    $replyComment = $_POST['replyComment'];
    $userReply_id = $user->ID;
    $replyDate = date("Y-m-d H-i-s");

    if($annonce->type_annonce == 1) {
        $wpdb -> update('wp_tm_annonces', array(
            'id_replier' => $userReply_id,
            'replyDate' => $replyDate,
            'replyComment' => $replyComment,
            'statut' => 1
        ),array('id'=>$id_annonce));
    }

    if($annonce->type_annonce == 2) {

        $id_team_replier = $_POST['id_team_replier'];

        $wpdb -> update('wp_tm_annonces', array(
            'id_replier' => $userReply_id,
            'replyDate' => $replyDate,
            'replyComment' => $replyComment,
            'id_team' => $id_team_replier,
            'statut' => 1
        ),array('id'=>$id_annonce));
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
<div class="container-fluid">
    <h5>Vous répondez à l'annonce de </h5>
    <h5 style="color: limegreen"><?php echo $name_auteur; ?></h5>
    <br>
    <?php
    if($annonce->type_annonce == 1){?>
    <label>Il participe au tournoi de <?php echo $tournament_name;?> avec son équipe <?php echo $team_name;?> <br>Il recherche un joueur pour compléter son équipe.</label>
    <?php
    }
    if($annonce->type_annonce == 2){?>
    <label>Il souhaite participer au tournoi de <?php echo $tournament_name;?> et recherche une équipe. </label>
    <?php } ?>
    <br>
    <label>Son message :</label>
    <p style="max-width: 50%;margin-left: 3%" ><?php echo $annonce->comment; ?></p>
    <hr>
    <form method="post" id="formulaire" name="formulaire">
        <?php
        if($annonce->type_annonce == 1){?>
        <label>Vous pouvez postuler pour intégrer son équipe<br>Ecrivez-lui : </label>
        <?php }
        if($annonce->type_annonce == 2){
            foreach($teams as $item){
                $query2 = $wpdb->prepare("SELECT id_tournament, team_name FROM wp_team WHERE id_team = %d;", $item->id_team);
                $teams2 = $wpdb -> get_results($query2);

                $query3 = $wpdb->prepare("SELECT name, event, number_players FROM wp_tournament WHERE id_tournament = %d",$teams2[0]->id_tournament);
                $tournaments = $wpdb->get_results($query3);

                $teamFind = false;
                //Pour chaque équipe du joueur
                foreach ($teams2 as $item2) {
                    if($teamFind){
                        break;
                    }
                    if($annonce->id_tournament == $item2->id_tournament){
                        $teamFind = true;
                        $id_team_replier = $item->id_team;  //Cela va être l'id_team de l'annonce qui est pour l'instant à -1 parce que l'auteur de l'annonce n'avait pas d'équipe.
                        $team_name_replier = $item2->team_name; //
                        $number_players = $tournaments[0]->number_players;
                        $event = $tournaments[0]->event;
                    }
                }
                if($teamFind){
                    break;
                }
            }
            if($teamFind){
                ?>
                <h5>Vous participez bien au tournoi <?php echo $tournament_name;?>, voici un résumé de votre équipe :  </h5>
                <br>
                <input type="hidden" name="id_team_replier" value="<?php echo $id_team_replier?>"/>
                <label style="font-size: large"> <b>Equipe <?php echo $team_name_replier?> </b> </label>
                <p> <?php echo 'Nombre de joueurs : '.$number_players.'<br>'.
                        'Evenement : '.$event.'<br>';
                    ?></p>
                <br>
                <label>Vous pouvez l'informer que vous souhaiteriez qu'il intégre votre équipe.<br>Ecrivez-lui : </label>
                <?php
            }else{
                ?>
                <div align="center">
                    <h6>Vous n'avez pas d'équipe qui participe à ce tournoi, vous ne pouvez pas répondre à ce type d'annonce</h6>
                    <br>
                    <input type="button" class="right" value="Retour" onclick="window.location.href='?page_id=<?php echo $id_page_main_team_making;?>'">
                </div>
                <?php
            }
        }
        if(($teamFind == true&&$annonce->type_annonce ==2) || $annonce->type_annonce==1){
            ?>
            <br>
            <textarea id="replyComment" name="replyComment" rows="5"
                      maxlength="1000" placeholder="Votre message..."
                      required="required" form="formulaire"></textarea>
            <br>
            <div>
                <input type="submit" class="btn-dark" name="replyAnnonce" value="Répondre à l'annonce">
                <br><br>
                <input type="button" class="btn-dark" value="Annuler" onclick="window.location.href='?page_id=<?php echo $id_page_main_team_making;?>'">
            </div>

            <?php
        }
        ?>
    </form>
</div>
</body>
</html>
