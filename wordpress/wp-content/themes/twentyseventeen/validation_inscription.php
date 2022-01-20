<?php /* Template Name: validation_inscription */ ?>


<?php
try {
    global $wpdb;
} catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
}
?>

<?php
if(isset($_GET['user_id']) && isset($_GET['code'])) {
    $user_id = $_GET['user_id'];
    $code = $_GET['code'];
    $code_query = $wpdb->prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", [$user_id, 'code_validation']);
    $code_db = $wpdb->get_var($code_query);
}
get_header();
?>

<html>
<head>
    <title>Validation de votre inscription</title>
    <meta charset="utf-8">
</head>
<body>
<h6 align="center">
    <?php
    if($code_db == $code && $code != null){
        echo "Votre adresse mail a bien été confirmé. <br><br> Vous pouvez désormais vous inscrire à un tournoi de nos événements.";
        $wpdb->update('wp_users', array('user_status'=>1), array('ID'=>$user_id));
    }
    else
    {
        if(!isset($_GET['user_id'])){
            echo "Erreur d'identification : Mauvais lien de validation.<br><br>";
        }
        elseif(!isset($_GET['code'])) {
            echo "Impossible de confirmer votre adresse mail : Mauvais code de confirmation<br><br>";
        }
        echo "Veuillez contacter le support du site";
    }
        ?>
</h6>
</body>
</html>

