<?php /* Template Name: interface_admin */ ?>

<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}

global $id_page_listes;
global $id_page_admin_add_player;
global $id_page_users_list;
get_header();

?>
<script>
function getEvent() {
  let evenement = document.getElementById("selectEvent");
  let today = new Date(), expires = new Date();
  expires.setTime(today.getTime() + (365*24*60*60*1000));
  document.cookie = "event = " + evenement.value + ";expires=" + expires.toGMTString();;
}
</script>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title>Inscription</title>
  <meta charset="utf-8">
</head>
<body>
  <div class="formulaire">
    <div class=" form-group col-12" >
      <label for="tournoi"> Sélection de l'évènement :</label>
      <?php
      $events_query = $wpdb->prepare("SELECT DISTINCT event FROM wp_tournament");
      $events = $wpdb -> get_results($events_query);
      ?>
      <select id="selectEvent" class="custom-select col-6">
        <?php foreach($events as $event) : ?>
          <option>
            <?php echo $event-> event; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class=" form-group col-12" >
      <a href="?page_id=<?php echo $id_page_listes;?>">
        <input type="button"  class="btn btn-dark col-xs-12 col-sm-12 col-md-6 col-lg-4" value="Infos de l'événement" onclick="getEvent()" />
      </a>
    </div>
    <div class=" form-group col-12" >
      <a href="?page_id=<?php echo $id_page_admin_add_player;?>">
        <input type="button"  class="btn btn-dark col-xs-12 col-sm-12 col-md-6 col-lg-4" value="Inscrire un joueur" />
      </a>
    </div>
    <div class=" form-group col-12" >
      <a href="?page_id=<?php echo $id_page_users_list;?>">
        <input type="button"  class="btn btn-dark col-xs-12 col-sm-12 col-md-6 col-lg-4" value="Infos des utilisateurs" onclick="getEvent()" />
      </a>
    </div>

  </div>
</body>
</html>
