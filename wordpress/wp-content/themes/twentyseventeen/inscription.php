<?php /* Template Name: inscription */ ?>
<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}
//Chargement du package pour l'envoi du mail de validation
include("wp-includes/mailer/mailinit.php");

$inscription_bool = true;

if($inscription_bool) {

if(isset($_POST['inscription'])) {
  date_default_timezone_set('Europe/Paris');
  $valid = 0;
  $username = htmlspecialchars($_POST['username']);
  $lastname = htmlspecialchars($_POST['lastname']);
  $firstname = htmlspecialchars($_POST['firstname']);
  $mail = htmlspecialchars($_POST['mail']);

  $day = htmlspecialchars($_POST['dayBirthdate']);
  $month = htmlspecialchars($_POST['monthBirthdate']);
  $year = htmlspecialchars($_POST['yearBirthdate']);

  if(!empty($_POST['dayBirthdate'])
      AND !empty($_POST['monthBirthdate'])
      AND !empty($_POST['yearBirthdate'])) {

      if(($day == 31 AND $month%2 == 0) OR ($day == 30 AND $month == 2)){
          $erreur = "Il n'y a pas 30 ou 31 jours dans ce mois";
      }else{
            $strBirthdate = $year."-".$month."-".$day;
            $birthdate = date("Y-n-j", strtotime($strBirthdate));
          }
          // $birthdate= htmlspecialchars($_POST['birthdate']);
  }
  else {
      $erreur = "Date incomplète";
  }

  $phonenumber= htmlspecialchars($_POST['phonenumber']);
  $password = wp_hash_password($_POST['password']);
  $passwordconfirm = htmlspecialchars($_POST['passwordconfirm']);
  $date = date("Y-m-d H-i-s");
  if(!empty($_POST['username'])
      AND !empty($_POST['mail'])
      AND !empty($_POST['lastname'])
      AND !empty($_POST['firstname'])
      AND isset($birthdate)
      AND !empty($_POST['phonenumber'])
      AND !empty($_POST['password'])
      AND !empty($_POST['passwordconfirm'])) {
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
                'user_pass' => $password
              ));

            /////// Envoi du mail de validation ///////
            // Génération du code de validation aléatoire
            $caracteres = '0123456789abcdefghijklmnopqrstuvwxyz';
            $longueurMax = strlen($caracteres);
            $codeValidation = '';
            $longueur = 8; //Taille du code
            for ($i = 0; $i < $longueur; $i++)
            {
                $codeValidation .= $caracteres[rand(0, $longueurMax - 1)];
            }
            // Initialisation du mailer
            $mailer = new Swift_Mailer($transport);
            //  Setup du mail
            $message_swift = (new Swift_Message('Game 22 -  Finalisez votre inscription'))
                ->setFrom(['game22@magellan.fpms.ac.be'=>'Organisation Game 22']);

            global $id_page_validation; //id wordpress de la page de validation

            $user_id = $wpdb -> insert_id;
            $urlValidation = WP_SITEURL.'?page_id='.$id_page_validation.'&user_id='.$user_id.'&code='.$codeValidation;
            $message_swift->setContentType("text/html");

            $message_swift->setBody(
                  '<html>
                  <body>'.
                    '<h4> Bonjour '. $username . ',</h4>' .
                      'Nous avons bien reçu votre inscription sur notre site du Game.'.
                      '<br>'.
                      'Veuillez valider votre adresse mail en cliquant sur le lien suivant : '.
                      '<br><br>'.
                      "<a class = btn-dark href='$urlValidation'>Lien de validation</a>".
                      '<br><br>'.
                      'Cordialement'.
                      '<br><br>'.
                      'Le Cercle Magellan'.
                  '</body>
                  </html>'
            );

            // Déclaration du destinataire
            $message_swift->setTo($mail);
            // Envoi du mail
            $resultMail=$mailer->send($message_swift);

            update_user_meta($user_id, 'first_name', $firstname);
            update_user_meta($user_id, 'last_name', $lastname);
            update_user_meta($user_id, 'birthdate', $birthdate);
            update_user_meta($user_id, 'phone_number', $phonenumber);
            update_user_meta($user_id, 'code_validation', $codeValidation);
            //update_user_meta($user_id, 'paid', FALSE);
            $result = wp_update_user(array('ID'=>$user_id, 'role'=> 'joueur'));
            $erreur = "Votre compte a bien été créé !";
            $valid = 1;
        } else {
          $erreur = "Vos mots de passes ne correspondent pas!";
        }
      } else {
        $erreur = "Adresse mail déjà utilisée!";
        $mailexist = 0;
      }
    } else {
      $erreur = "Votre adresse mail n'est pas valide!";
    }
  } else {
      if(isset($birthdate)){
          $erreur = "Tous les champs doivent être complétés !";
      }
  }
  echo '<script type="text/javascript">window.alert("'.$erreur.'");</script>';
  if ($valid == 1) {
    wp_redirect( "WP_HOME" );
  }
}
}
else{
    $erreur = "Site en maintenance, impossible de s'inscrire pour l'instant. <br> Veuillez réessayer plus tard";
}
get_header();
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
<script>
    webshims.setOptions('waitReady', false);
    webshims.setOptions('forms-ext', {type: 'date'});
    webshims.setOptions('forms-ext', {type: 'time'});
    webshims.polyfill('forms forms-ext');
</script>

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
          <label> Date de naissance : </label>
	  <br>

            <select name = "dayBirthdate" class="custom-select col-3">
                <option selected disabled>Jour</option>
                <?php
                for($i=1; $i<32; $i++){
                    ?>
                    <option <?php if($day == $i) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
                    <?php
                }
                ?>
            </select>
            <select name = "monthBirthdate" class="custom-select col-3">
                <option selected disabled>Mois</option>
                <?php
                for($i=1; $i<12+1; $i++){
                    ?>
                    <option <?php if($month == $i) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
                    <?php
                }?>
            </select>
            <select name = "yearBirthdate" class="custom-select col-3">
                <option selected disabled>Année</option>
                <?php
                for($i=2008; $i>1900; $i--){
                    ?>
                    <option <?php if($year == $i) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
                    <?php
                }?>
            </select>
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
          <input type="submit"
			<?php if(!$inscription_bool) {
                           echo 'disabled="disabled"';
                        } ?>
		 class="btn btn-dark" name="inscription" value="Valider l'inscription !" />
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
