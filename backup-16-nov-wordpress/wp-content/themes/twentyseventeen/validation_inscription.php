<?php /* Template Name: validation_inscription */ ?>


<?php
try {
    global $wpdb;
} catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
}
?>

<?php
$user_id = $_GET['id'];
$code = $_GET['code'];
$code_query = $wpdb->prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", [$user_id,'code_validation'] );
$code_db = $wpdb->get_var($code_query);
?>

<html>
<head>
    <title>Validation de votre inscription</title>
    <meta charset="utf-8">
</head>
<body>
    <?php
    if($code_db == $code){
        echo "Votre adresse mail a bien été confirmé " . " Vous pouvez désormais vous inscrire à un tournoi de notre événement";
        $wpdb->update('wp_users', array('user_status'=>1), array('ID'=>$user_id));
    }
    else
    {
        echo "Impossible de confirmer votre adresse mail : Mauvais code de confirmation, contacter le support du site";
    }
        ?>
<!--Mettre un bouton de redirection accueil-->
</body>
</html>

