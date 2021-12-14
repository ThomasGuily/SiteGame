<?php /* Template Name: inscription_tournoi */ ?>
<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}
$event = get_the_title();
if(isset($_POST['inscription'])) {
  $id_current_tournament = htmlspecialchars($_POST['selecttournament']);
  setcookie("tournament", $id_current_tournament,  time() + 3600, null, null, false, true);
  $password = htmlspecialchars($_POST['password']);
  if(!empty($_POST['password'])) {
    $user = wp_get_current_user();
    $passwordconfirm = $user -> user_pass;
    $userid = $user -> ID;
    if($user -> user_status == 1) {
      if(wp_check_password($password, $passwordconfirm, $user->ID)) {
        $new_ts_query = $wpdb->prepare("SELECT time_slot FROM wp_tournament WHERE id_tournament = %d", $id_current_tournament);
        $new_ts = $wpdb->get_var($new_ts_query);
        $participate_query = $wpdb->prepare("SELECT id_tournament FROM wp_participate WHERE id_player = %d", $userid);
        $participate = $wpdb -> get_results($participate_query);
        $rowcount = 0;
        foreach ($participate as $tournament) {
          $ts_query = $wpdb-> prepare ("SELECT time_slot FROM wp_tournament WHERE id_tournament = %d AND event = %s", $tournament -> id_tournament, $event);
          $ts = $wpdb -> get_var($ts_query);
          $rowcount = $rowcount +1;
          $array[] = $ts;
        }
        if ($rowcount == 0 || !in_array($new_ts, $array)) {
          $player_query = $wpdb->prepare("SELECT number_players FROM wp_tournament WHERE id_tournament = %d", $id_current_tournament);
          $players = $wpdb->get_var($player_query);
          if ($players == 1) {
            $erreur = "Inscription validée, vous allez être redirigé vers la page d'accueil !";
            echo '<script type="text/javascript">window.alert("'.$erreur.'");</script>';
            $wpdb -> insert('wp_participate', array(
              'id_player' => $userid,
              'id_tournament' => $id_current_tournament
            ));
            wp_redirect( "?WP_HOME" );
          }
          else if ($players > 1) {
            $erreur = "Vous allez être redirigé vers la page de sélection d'équipe pour ce tournoi !";
            wp_redirect( "?page_id=718" );
          }
        }
        else {
          $erreur = "Vous êtes déjà inscrit à un tournoi sur cette plage horaire !";
        }
      }else {
        $erreur = "Votre mot de passe est incorrect !";
      }
    }
    else {
      $erreur = "Veuillez valider votre compte ! Vérifiez vos mails afin de trouver le lien de validation";
    }
  } else {
    $erreur = "Veuillez remplir tous les champs !";
  }
  echo '<script type="text/javascript">window.alert("'.$erreur.'");</script>';
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
      <h2 class="titre"> Inscription tournoi</h2>
    </div>
    <div class="container-fluid">
      <p> Il existe deux plages horaires possibles pour les tournois: il n'est possible de s'inscrire qu'à un seul tournoi pour chaque plage horaire. </p>
    </php>
    <div class="container-fluid">
      <h4>Liste des tournois:</h4>
      <?php
      $tournaments_query = $wpdb->prepare("SELECT id_tournament, name, number_players, time_slot FROM wp_tournament WHERE event = %s", $event);
      $tournaments = $wpdb -> get_results($tournaments_query);
      foreach($tournaments as $tournament){
        ?>
        <span><h5> <?php echo $tournament -> name; ?> : </h5>
          <?php if ($tournament -> number_players == 1) {?>
            <p> Le tournoi se fait en solo et se déroule sur la plage horaire <?php echo $tournament -> time_slot ?> </p> <?php
          }
          else if ($tournament -> number_players > 1) {
            ?> <p> Le tournoi se fait en équipe de <?php echo $tournament -> number_players ?> joueurs et se déroule sur la plage horaire <?php echo $tournament -> time_slot ?></p> <?php
          }
        }
        ?>
      </div>
      <div class="formulaire">
        <form method="POST" action="">
          <div class="form-group col-12">
            <label for="tournoi"> Sélection du tournoi :</label>
            <?php
            $tournaments_query = $wpdb->prepare("SELECT id_tournament, name, number_players FROM wp_tournament WHERE event = %s", $event);
            $tournaments = $wpdb -> get_results($tournaments_query);
            ?>
            <select name = "selecttournament" class="custom-select col-6">
              <?php foreach($tournaments as $tournament) : ?>
                <option id="<?php echo $tournament->id_tournament; ?>" value="<?php echo $tournament->id_tournament?>" >
                  <?php echo $tournament->name;
                  if ($tournament -> number_players == 1) {
                    ?>: Tournoi en solo<?php
                  }
                  else if ($tournament -> number_players > 1) {
                    ?>: Tournoi en équipe de <?php echo $tournament -> number_players ?> joueurs <?php
                  }
                  ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group col-12">
            <label for="password" >Votre mot de passe :</label>
            <input type="password" id="password" name="password" />
          </div>
          <input type="submit" class="btn btn-dark" name="inscription" value="Valider l'inscription !" />
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
