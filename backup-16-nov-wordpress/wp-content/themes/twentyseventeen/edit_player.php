<?php /* Template Name: edit_player */ ?>
<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}
get_header();
function delete_from_tournament($wpdb, $id_tournament, $id_user) {
  $tour_query = $wpdb->prepare("SELECT * FROM wp_tournament WHERE id_tournament = %d", $id_tournament);
  $tour = $wpdb -> get_row($tour_query);
  if ($tour -> number_players >= 1) {
    $team_query = $wpdb -> prepare("SELECT wp_team.id_team
      FROM wp_team INNER JOIN wp_in_team
      ON wp_team.id_team = wp_in_team.id_team
      WHERE wp_in_team.id_player = %d AND wp_team.id_tournament = %d", $id_user, $id_tournament);
    $team = $wpdb -> get_var($team_query);
    $delete_from_team_query = $wpdb -> prepare("DELETE FROM wp_in_team WHERE id_player = %d AND id_team = %d", $id_user, $team);
    $wpdb -> query($delete_from_team_query);
  }
  $delete_query = $wpdb -> prepare("DELETE FROM wp_participate WHERE id_player = %d AND id_tournament = %d", $id_user, $id_tournament);
  $wpdb -> query($delete_query);
}
$userid = $_GET['id'];
$user_query = $wpdb -> prepare("SELECT * FROM wp_users WHERE ID = %d", $userid);
$user_infos = $wpdb -> get_row($user_query);
$last_name_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "last_name");
$last_name = $wpdb -> get_var($last_name_query);
$first_name_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "first_name");
$first_name = $wpdb -> get_var($first_name_query);
$phone_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "phone_number");
$phone = $wpdb -> get_var($phone_query);
$birthdate_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "birthdate");
$birthdate = $wpdb -> get_var($birthdate_query);
$current_user = wp_get_current_user();
$passwordconfirm = $current_user -> user_pass;
$events_query = $wpdb -> prepare("SELECT DISTINCT event FROM wp_tournament");
$events = $wpdb -> get_results($events_query);
if(isset($_POST['modification'])) {
  date_default_timezone_set('Europe/Paris');
  $username = htmlspecialchars($_POST['username']);
  $lastname = htmlspecialchars($_POST['lastname']);
  $firstname = htmlspecialchars($_POST['firstname']);
  $mail = htmlspecialchars($_POST['mail']);
  $birthdate= htmlspecialchars($_POST['birthdate']);
  $phonenumber= htmlspecialchars($_POST['phonenumber']);
  $password = htmlspecialchars($_POST['password1']);
  if(!empty($_POST['password1'])) {
    if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $mailquery = $wpdb -> prepare("SELECT COUNT(*) FROM wp_users WHERE user_email = %s", $mail);
      $mailexist = $wpdb -> get_var($mailquery);
      if($mailexist == 0 || $mail == $user_infos->user_email) {
        if(wp_check_password($password, $passwordconfirm)) {
          $wpdb -> update('wp_users', array(
            'user_login' => $username,
            'user_nicename' => strtolower($username),
            'user_email' => $mail,
            'user_registered' => $date,
            'display_name' => $username,
            'user_registered' => $user_infos -> user_registered
          ), array( 'ID' => $userid ));
          update_user_meta($userid, 'first_name', $firstname);
          update_user_meta($userid, 'last_name', $lastname);
          update_user_meta($userid, 'birthdate', $birthdate);
          update_user_meta($userid, 'phone_number', $phonenumber);
          $erreur = "Modification effectuée!";
        } else {
          $erreur = "Mot de passe incorrect!";
        }
      } else {
        $erreur = "Adresse mail déjà utilisée!";
        $mailexist = 0;
      }
    } else {
      $erreur = "Adresse mail non valide!";
    }
  } else {
    $erreur = "Veuillez indiquer votre mot de passe !";
  }
  echo '<script type="text/javascript">window.alert("'.$erreur.'");</script>';
}
else if(isset($_POST['validate'])) {
  $password2 = htmlspecialchars($_POST['password2']);
  if(!empty($_POST['password2'])) {
    if(wp_check_password($password2, $passwordconfirm)) {
      update_user_meta($userid, 'paid', 1);
      $erreur = "Validation du paiement de {$user_infos->user_login} effectuée";
    }
    else {
      $erreur = "Mot de passe incorrect";
    }
  } else {
    $erreur = "Veuillez entrer votre mot de passe";
  }
  echo '<script type="text/javascript">window.alert("'.$erreur.'");</script>';
}
else if(isset($_POST['tournament'])) {
  $password4 = htmlspecialchars($_POST['password4']);
  $id_tour = htmlspecialchars($_POST['tournamentSelection']);
  $tour_query = $wpdb->prepare("SELECT name FROM wp_tournament WHERE id_tournament = %d", $id_tour);
  $tour = $wpdb -> get_var($tour_query);
  if(!empty($_POST['password4'])) {
    if(wp_check_password($password4, $passwordconfirm)) {
      delete_from_tournament($wpdb, $id_tour, $userid);
      $erreur = "Désinscription de {$user_infos->user_login} du tournoi {$tour->name} effectuée";
    }
    else {
      $erreur = "Mot de passe incorrect";
    }
  } else {
    $erreur = "Veuillez entrer votre mot de passe";
  }
  echo '<script type="text/javascript">window.alert("'.$erreur.'");</script>';
}
else if(isset($_POST['event'])) {
  $password3 = htmlspecialchars($_POST['password3']);
  $event = htmlspecialchars($_POST['eventSelection']);
  if(!empty($_POST['password3'])) {
    if(wp_check_password($password3, $passwordconfirm)) {
      $user_tournaments_query = $wpdb -> prepare("SELECT wp_tournament.id_tournament
        FROM wp_tournament INNER JOIN wp_participate
        ON wp_tournament.id_tournament=wp_participate.id_tournament
        WHERE wp_participate.id_player = %d AND wp_tournament.event = %s", $userid, $event);
      $user_tournaments = $wpdb -> get_results($user_tournaments_query);
      foreach ($user_tournaments as $tournament) {
        delete_from_tournament($wpdb, $tournament->id_tournament, $userid);
      }
      $erreur = "Désinscription de {$user_infos->user_login} de l'évenement {$event} effectuée";
    }
    else {
      $erreur = "Mot de passe incorrect";
    }
  } else {
    $erreur = "Veuillez entrer votre mot de passe";
  }
  echo '<script type="text/javascript">window.alert("'.$erreur.'");</script>';
}

?>
<script type="text/javascript">
  function close() {
    document.getElementById('edit_user').style.display= "none";
    document.getElementById('delete_from_event').style.display= "none";
    document.getElementById('delete_from_tournament').style.display= "none";
    document.getElementById('validate_payment').style.display= "none";
  }
  function show_edit_user(){
    close();
    document.getElementById('edit_user').style.display="block";
  }
  function show_delete_from_event(){
    close();
    document.getElementById('delete_from_event').style.display="block";
  }
  function show_delete_from_tournament(){
    close();
    document.getElementById('delete_from_tournament').style.display="block";
  }
  function show_validate_payment(){
    close();
    document.getElementById('validate_payment').style.display="block";
  }
  function alert() {
    alert('Modification VALIDEE');
  }
</script>

<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title><?php echo $user_infos->user_login?></title>
  <meta charset="utf-8">
</head>
<body>
  <h2>
    Modification de <?php echo $user_infos->user_login?>
  </h2>
  <div class=" form-group col-12" >
    <input type="button" class="btn btn-dark" value="Modification des informations personnelles" onclick="javascript:show_edit_user();" />
    <input type="button" class="btn btn-dark" value="Validation du paiement" onclick="javascript:show_validate_payment();"/>
    <input type="button" class="btn btn-dark" value="Désinscription d'un tournoi" onclick="javascript:show_delete_from_tournament();"/>
    <input type="button" class="btn btn-dark" value="Suppression d'un événement" onclick="javascript:show_delete_from_event();"/>
  </div>
  <div id="edit_user" style="display:none;">
    <div class="formulaire">
      <form method="POST" id = "form1" action="">
        <div class="form-group col-12">
          <label for="username"> Nom d'utilisateur :  </label>
          <br>
          <input type="varchar" id="username" name="username" value ="<?php echo $user_infos->user_login ?>"/>
        </div>
        <div class="form-group col-12">
          <label for="firstname"> Prénom : </label>
          <br>
          <input type="varchar" id="firstname" name="firstname" value = " <?php echo $first_name ?>"/>
        </div>
        <div class="form-group col-12">
          <label for="lastname"> Nom de famille : </label>
          <br>
          <input type="varchar" id="lastname" name="lastname" value ="<?php echo $last_name ?>"/>
        </div>
        <div class="form-group col-12">
          <label for="birthdate"> Date de naissance : </label>
          <br>
          <input type="date" id="birthdate" name="birthdate" min="1900-01-01" max="2008-01-01" value = "<?php echo $birthdate ?>"/>
        </div>
        <div class="form-group col-12">
          <label for="mail"> Adresse mail :</label>
          <br>
          <input type="varchar" id="mail" name="mail" value="<?php echo $user_infos->user_email ?>" />
        </div>
        <div class="form-group col-12">
          <label for="phonenumber"> Numéro de téléphone :  </label>
          <br>
          <input type="varchar" id="phonenumber" name="phonenumber" value = "<?php echo $phone ?>"/>
        </div>
        <div class="form-group col-12">
          <label for="password" >Votre mot de passe :</label>
          <input type="password" id="password" name="password1" />
        </div>
        <div class="form-group col-12">
          <input type="submit" class="btn btn-dark" form="form1" name="modification" value="Valider la modification !" />
        </div>
      </form>
    </div>
  </div>
  <div id="delete_from_event" style="display:none;">
    <div class="formulaire">
      <form method="POST" id = "form3" action="">
        <div class="form-group col-12">
          <label for="event"> Sélection de l'évènement :</label> <br>
          <?php
          $events_query = $wpdb->prepare("SELECT DISTINCT event FROM wp_tournament");
          $events = $wpdb -> get_results($events_query);
          ?>
          <select name="eventSelection" class="custom-select col-6">
            <?php foreach($events as $event) : ?>
              <option id="<?php echo $event-> event; ?>" value="<?php echo $event-> event;?>" >
                <?php echo $event-> event; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group col-12">
          <label for="password" >Votre mot de passe :</label>
          <input type="password" id="password" name="password3" />
        </div>
        <div class="form-group col-12">
          <input type="submit" class="btn btn-dark" name="event" form="form3" value="Désinscrire <?php echo $user_infos->user_login?>  !" />
        </div>
      </form>
    </div>
  </div>
  <div id="delete_from_tournament" style="display:none;">
    <div class="formulaire">
      <form method="POST" id = "form4" action="">
        <div class="form-group col-12">
          <label for="event"> Sélection du tournoi :</label> <br>
          <?php
          $user_tournaments_query = $wpdb -> prepare("SELECT wp_tournament.name, wp_tournament.id_tournament  FROM wp_tournament INNER JOIN wp_participate ON wp_tournament.id_tournament=wp_participate.id_tournament WHERE wp_participate.id_player = %d ORDER BY wp_tournament.name", $userid);
          $user_tournaments = $wpdb -> get_results($user_tournaments_query); ?>
          <select name="tournamentSelection" class="custom-select col-6">
            <?php foreach($user_tournaments as $tour) : ?>
              <option value="<?php echo $tour->id_tournament;?>" >
                <?php echo $tour->name; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group col-12">
          <label for="password" >Votre mot de passe :</label>
          <input type="password" id="password" name="password4" />
        </div>
        <div class="form-group col-12">
          <input type="submit" class="btn btn-dark" name="tournament" form="form4" value="Désinscrire <?php echo $user_infos->user_login?> du tournoi!" />
        </div>
      </form>
    </div>
  </div>
  <div id="validate_payment" style="display:none;">
    <h3> Prix à payer : </h3>
    <div class="formulaire">
      <form method="POST" id = "form2" action="">
        <div class="form-group col-12">
          <label for="password" >Votre mot de passe :</label>
          <input type="password" id="password" name="password2" />
        </div>
        <div class="form-group col-12">
          <input type="submit" class="btn btn-dark" name="validate" form="form2" value="Valider le paiement de <?php echo $user_infos->user_login?> !" />
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
