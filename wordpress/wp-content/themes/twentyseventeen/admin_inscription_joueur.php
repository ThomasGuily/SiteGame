<?php /* Template Name: admin_inscription_joueur */ ?>
<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}
//Chargement du package pour l'envoi du mail de validation
include("wp-includes/mailer/mailinit.php");
if(isset($_POST['inscription'])) {
  date_default_timezone_set('Europe/Paris');
  $valid = 0;
  $username = htmlspecialchars($_POST['username']);
  $lastname = htmlspecialchars($_POST['lastname']);
  $firstname = htmlspecialchars($_POST['firstname']);
  $mail = htmlspecialchars($_POST['mail']);
  $birthdate= htmlspecialchars($_POST['birthdate']);
  $phonenumber= htmlspecialchars($_POST['phonenumber']);
  $password = wp_hash_password($_POST['password']);
  $passwordconfirm = htmlspecialchars($_POST['passwordconfirm']);
  $date = date("Y-m-d H-i-s", );
  if(!empty($_POST['username']) AND !empty($_POST['mail']) AND !empty($_POST['lastname']) AND !empty($_POST['firstname']) AND !empty($_POST['birthdate']) AND !empty($_POST['phonenumber']) AND  !empty($_POST['password']) AND !empty($_POST['passwordconfirm'])) {
    if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $mailquery = $wpdb -> prepare("SELECT COUNT(*) FROM wp_users WHERE user_email = %s", $mail);
      $mailexist = $wpdb -> get_var($mailquery);
      if($mailexist == 0) {
        if(wp_check_password($passwordconfirm, $password)) {
          $wpdb -> insert('wp_users', array(
            'user_login' => $username,
            'user_nicename' => strtolower($username),
            'user_email' => $mail,
            'user_registered' => $date,
            'display_name' => $username,
            'user_pass' => $password,
            'user_status' => 1
          ));
          $user_id = $wpdb -> insert_id;
          update_user_meta($user_id, 'first_name', $firstname);
          update_user_meta($user_id, 'last_name', $lastname);
          update_user_meta($user_id, 'birthdate', $birthdate);
          update_user_meta($user_id, 'phone_number', $phonenumber);
          update_user_meta($user_id, 'paid', FALSE);
          update_user_meta($user_id,'code_validation', $codeValidation);
          $result = wp_update_user(array('ID'=>$user_id, 'role'=> 'joueur'));
          $erreur = "Le compte a bien été créé !";
          $valid = 1;
        } else {
          $erreur = "Les mots de passes ne correspondent pas!";
        }
      } else {
        $erreur = "Adresse mail déjà utilisée!";
        $mailexist = 0;
      }
    } else {
      $erreur = "Adresse mail non valide!";
    }
  } else {
    $erreur = "Tous les champs doivent être complétés !";
  }
  echo '<script type="text/javascript">window.alert("'.$erreur.'");</script>';
  if ($valid == 1){
    $a = "?page_id=752&id=";
    $b = $a. $user_id;
    wp_redirect($b );
  }
}
get_header();
?>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title>Inscription</title>
  <meta charset="utf-8">
</head>
<body>
  <div class="container-fluid">
    <div class="heading">
      <h2 class="titre"> Inscription GAME 22</h2>
    </div>
    <div class="formulaire">
      <form method="POST" action="">
        <div class="form-group col-12">
          <label for="username"> Nom d'utilisateur :</label>
          <input type="varchar" id="username" name="username" value="<?php if(isset($username)) { echo $username; } ?>" />
        </div>
        <div class="form-group col-12">
          <label for="firstname"> Prénom :</label>
          <input type="varchar" id="firstname" name="firstname" value="<?php if(isset($firstname)) { echo $firstname; } ?>" />
        </div>
        <div class="form-group col-12">
          <label for="lastname"> Nom de famille :</label>
          <input type="varchar" id="lastname" name="lastname" value="<?php if(isset($lastname)) { echo $lastname; } ?>" />
        </div>
        <div class="form-group col-12">
          <label for="birthdate"> Date de naissance : </label>
          <input type="date" id="birthdate" name="birthdate" min="1900-01-01" max="2008-01-01" value="<?php if(isset($birthdate)) { echo $birthdate; } ?>" />
        </div>
        <div class="form-group col-12">
          <label for="mail"> Adresse mail :</label>
          <input type="varchar" id="mail" name="mail" value="<?php if(isset($mail)) { echo $mail; } ?>" />
        </div>
        <div class="form-group col-12">
          <label for="phonenumber"> Numéro de téléphone :</label>
          <input type="varchar" id="phonenumber" name="phonenumber" value="<?php if(isset($phonenumber)) { echo $phonenumber; } ?>" />
        </div>
        <div class="form-group col-12">
          <label for="password" >Mot de passe :</label>
          <input type="password" id="password" name="password" />
        </div>
        <div class="form-group col-12">
          <label for="passwordconfirm"> Confirmation du mot de passe :</label>
          <input type="password" id="passwordconfirm" name="passwordconfirm" />
        </div>
        <div class="form-group col-12">
          <input type="submit" class="btn btn-dark" name="inscription" value="Inscrire à un tournoi !" />
        </div>
      </form>
    </div>
  </div>
</body>
</html>
