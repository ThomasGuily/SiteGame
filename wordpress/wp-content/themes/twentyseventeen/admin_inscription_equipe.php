<?php /* Template Name: admin_inscription_equipe */ ?>

<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}
$id_tournament = $_COOKIE["tournament"];
if(isset($_POST['teams'])) {
  $team_id = htmlspecialchars($_POST['selectteam']);
  $teampassword = htmlspecialchars($_POST['teampassword']);
  if(!empty($_POST['teampassword'])) {
    $query = $wpdb->prepare("SELECT team_password FROM wp_team WHERE id_team = %d", $team_id);
    $teampasswordconfirm = $wpdb->get_var($query);
    if(wp_check_password($teampassword, $teampasswordconfirm)) {
      $user = wp_get_current_user();
      $user_id = $user->ID;
      $wpdb -> insert('wp_in_team', array(
        'id_player' => $user_id,
        'id_team' => $team_id
      ));
      $wpdb -> insert('wp_participate', array(
        'id_player' => $user_id,
        'id_tournament' => $id_tournament
      ));
      $erreur = "Inscription à l'équipe validée, vous allez être redirigé vers la page d'accueil!";
      wp_redirect( "WP_HOME" );
    } else {
      $erreur = "Le mot de passe de l'équipe est incorrect!";
    }
  } else {
    $erreur = "Merci de renseigner le mot de passe de l'équipe à laquelle vous souhaitez vous inscrire!";
  }
}
get_header();
?>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title> Equipes </title>
  <meta charset="utf-8">
</head>
<body>
  <div class="container-fluid">

    <a href="?page_id=720">
      <input type="button"  class="btn btn-dark" value="Créer une nouvelle équipe" />
    </a>
  </div>

  <div class="container-fluid">
    <div class="heading">
      <h2 class="titre"> Rejoindre une équipe</h2>
    </div>
    <div class="formulaire">
      <form method="POST" action="">
        <div class="form-group col-12">
          <label for="team"> Equipes existantes :</label>
          <?php
          $teams_query = $wpdb -> prepare("SELECT * FROM wp_team WHERE id_tournament = %d", $id_tournament);
          $teams = $wpdb -> get_results($teams_query);
          ?>
          <select name = "selectteam" class="custom-select col-6">
            <?php foreach($teams as $team) : ?>
              <option id="<?php echo $team->id_team; ?>" value="<?php echo $team->id_team?>" >
                <?php echo $team->team_name;?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group col-12">
          <label for="teampassword" >Mot de passe de l'équipe :</label>
          <input type="password" id="teampassword" name="teampassword" />
        </div>
        <input type="submit" class="btn btn-dark" name="teams" value="Rejoindre l'équipe !" />
      </form>
    </div>
  </div>
  <?php
  if(isset($erreur)) {
    echo '<font color="red">'.$erreur."</font>";
  }
  ?>
</div>
</body>
</html>
