<?php /* Template Name: creation_equipe */ ?>
<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}
$id_tournoi = $_COOKIE["tournament"];
if(isset($_POST['newteam'])) {
  date_default_timezone_set('Europe/Paris');
  $id_tournament = $_COOKIE["tournament"];
  $teamname = htmlspecialchars($_POST['teamname']);
  $password = htmlspecialchars($_POST['password']);
  $teampassword = wp_hash_password($_POST['teampassword']);
  $teampasswordconfirm = htmlspecialchars($_POST['teampasswordconfirm']);
  $date = date("Y-m-d H-i-s", );
  if(!empty($_POST['teamname']) AND !empty($_POST['teampassword']) AND !empty($_POST['teampasswordconfirm']) AND !empty($_POST['password'])) {
    $user = wp_get_current_user();
    $passwordconfirm = $user -> user_pass;
    $user_id = $user->ID;
    if(wp_check_password($password, $passwordconfirm, $user_id)) {
      if(wp_check_password($teampasswordconfirm, $teampassword)) {
        $wpdb -> insert('wp_team', array(
          'team_name' => $teamname,
          'team_password' => $teampassword,
          'creation' => $date,
          'id_team_leader' => $user -> ID,
          'id_tournament' => $id_tournament
        ));
        $team_id = $wpdb -> insert_id;
        $wpdb -> insert('wp_in_team', array(
          'id_player' => $user_id,
          'id_team' => $team_id
        ));
        $wpdb -> insert('wp_participate', array(
          'id_player' => $user_id,
          'id_tournament' => $id_tournament
        ));
        $erreur = "Création de l'équipe validée, vous allez être redirigé vers la page d'accueil !";
        wp_redirect( "WP_HOME" );
      }
      else {
        $erreur = "Les mots de passe de l'équipe ne correspondent pas !";
      }
    }
    else {
      $erreur = "Votre mot de passe est incorrect !";
    }
  }
  else {
    $erreur = "Tous les champs doivent être complétés !";
  }
}
get_header();
?>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title> Création équipe </title>
  <meta charset="utf-8">
</head>
<body>
  <div class="container-fluid">
    <div class="heading">
      <h2 class="titre"> Création équipe</h2>
    </div>
    <div class="formulaire">
      <form method="POST" action="">
        <div class="form-group col-12">
          <label for="teamname"> Nom d'équipe :</label>
          <input type="varchar" id="teamname" name="teamname" value="<?php if(isset($teamname)) { echo $teamname; } ?>" />
        </div>
        <div class="form-group col-12">
          <label for="teampassword" > Mot de passe de l'équipe :</label>
          <input type="password" id="teampassword" name="teampassword" />
        </div>
        <div class="form-group col-12">
          <label for="teampasswordconfirm"> Confirmation du mot de passe de l'équipe :</label>
          <input type="password" id="teampasswordconfirm" name="teampasswordconfirm" />
        </div>
        <div class="form-group col-12">
          <label for="password" > Votre mot de passe :</label>
          <input type="password" id="password" name="password" />
        </div>
        <div class="form-group col-12">
          <input type="submit" class="btn btn-dark" name="newteam" value="Valider la création de l'équipe !" />
        </div>
      </form>
    </div>
    <?php
    if(isset($erreur)) {
      echo '<font color="red">'.$erreur."</font>";
    }
    ?>
  </div>
</body>
</html>
