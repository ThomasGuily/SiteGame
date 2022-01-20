<?php /* Template Name: listes */ ?>
<?php
try {
  global $wpdb;
} catch (PDOException $e) {
  echo 'echec de connexion : ' . $e->getMessage();
}

// Attention pour l'id de la page du script de la barre de recherche vu qu'il y a du javascript,
// j'ai pas tenté de faire une variable globale pour cet id.
// Il faut le changer manuellement et c'est le seul normalement

get_header();
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script type="text/javascript">

function close_table() {
  document.getElementById('teams_invisible').style.display= "none";
  document.getElementById('tournaments_invisible').style.display= "none";
  document.getElementById('users_invisible').style.display= "none";
}
function show_table_users(){
  close_table();
  document.getElementById('users_invisible').style.display="block";
}
function show_table_teams(){
  close_table();
  document.getElementById('teams_invisible').style.display="block";
}
function show_table_tournaments(){
  close_table();
  document.getElementById('tournaments_invisible').style.display="block";
}


function searchUsers(username){
    jQuery('#users_table').html('');  // On vide le contenu html du tableau sinon il y a duplication des lignes

    // jQuery.ajax permet d'envoyer une requête GET ou POST vers une autre page php
    // Il va recevoir un output sous la forme d'un html si l'username a donné un résultat auprès de la bd
    // Alors on met le html output dans l'élément table
    // Si il n'y a pas de résultat, on masque le tableau et on affiche aucun user
    jQuery.ajax({
        type: 'GET',
        url: '?page_id=775',
        data: 'username=' + encodeURIComponent(username),
        success: function (output) {
            if(output.length-2){  // Super bizarre mais un string null a une longueur de 2 et n'est pas null
                document.getElementById('table1').style.display= "block";
                document.getElementById('message_null_users').style.display= "none";
                jQuery('#users_table').html('').append(output);
            }
            else {
                document.getElementById('table1').style.display= "none";
                document.getElementById('message_null_users').style.display= "block";
            }
        }
        // Faire la gestion des erreurs plus tard avec un throw exception dans le php
    })
}

function searchTeams(teamName){
    jQuery('#teams_table').html('');
    jQuery.ajax({
        type: 'GET',
        url: '?page_id=775',
        data: 'teamName=' + encodeURIComponent(teamName),
        success: function (output) {
            if(output.length-2){  // Super bizarre mais un string null a une longueur de 2 et n'est pas null
                document.getElementById('table2').style.display= "block";
                document.getElementById('message_null_teams').style.display= "none";
                jQuery('#teams_table').html('').append(output);
            }
            else {
                document.getElementById('table2').style.display= "none";
                document.getElementById('message_null_teams').style.display= "block";
            }
        }
        // Faire la gestion des erreurs plus tard avec un throw exception dans le php
    })
}

function searchTournaments(tournamentName){
    jQuery('#tournaments_table').html('');
    jQuery.ajax({
        type: 'GET',
        url: '?page_id=775',
        data: 'tournamentName=' + encodeURIComponent(tournamentName),
        success: function (output) {
            if(output.length-2){  // Super bizarre mais un string null a une longueur de 2 et n'est pas null
                document.getElementById('table3').style.display= "block";
                document.getElementById('message_null_tournaments').style.display= "none";
                jQuery('#tournaments_table').html('').append(output);
            }
            else {
                document.getElementById('table3').style.display= "none";
                document.getElementById('message_null_tournaments').style.display= "block";
            }
        }
        // Faire la gestion des erreurs plus tard avec un throw exception dans le php
    })
}


// Permet de remplir les tableaux au chargement de la page
// en appelant une première fois les fonctions avec une champ vide = tous les éléments
window.onload = function(){
    let emptySearch = ''
    searchUsers(emptySearch);
    searchTeams(emptySearch);
    searchTournaments(emptySearch);
}

// jQuery.ready est appelée à chaque fois qu'une barre de recherche est modifiée
jQuery(document).ready(function () {
    jQuery('#input_user').keyup(function () {
        let username = jQuery(this).val().toLowerCase();
        //console.log(username);
        searchUsers(username);
    });

    jQuery('#input_team').keyup(function () {
        let teamName = jQuery(this).val().toLowerCase();
        //console.log(teamName);
        searchTeams(teamName);
    });

    jQuery('#input_tournament').keyup(function () {
        let tournamentName = jQuery(this).val().toLowerCase();
        //console.log(tournamentName);
        searchTournaments(tournamentName);
    });
});


</script>

<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title> Base de données </title>
  <meta charset="utf-8">
</head>
<body>
<div class="container-fluid">
    <table>
        <tbody>
          <tr>
              <td> <input type="button" class="btn btn-dark" value="Afficher les utilisateurs" onclick="javascript:show_table_users();" /> </td>
              <td> <input type="button" class="btn btn-dark" value="Afficher les équipes" onclick="javascript:show_table_teams();" /> </td>
              <td> <input type="button" class="btn btn-dark" value="Afficher les tournois" onclick="javascript:show_table_tournaments();" /> </td>
          </tr>
        </tbody>
    </table>
    <div id="users_invisible" style="display:none;">
        <div class = "container-fluid">
            <input id="input_user" name="input_user" type="text" placeholder="Search..">
        </div>
        <br>
        <label id="message_null_users" align="center" style="display:none;">Aucun utilisateur trouvé</label>
        <div id="table1" class="table-responsive">
            <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
                <thead class="thead-dark">
                <tr>
                    <th scope = "col" width="2%"> ID </th>
                    <th scope = "col"> Pseudo </th>
                    <th scope = "col"> Nom </th>
                    <th scope = "col"> Adresse mail </th>
                    <th scope = "col"> Date d'inscription </th>
                    <th scope = "col"> Tournoi(s) </th>
                    <th scope = "col"> Equipe(s) </th>
                    <th scope = "col"> Paiement </th>
                    <th scope = "col"> Téléphone </th>
                    <th scope = "col"> Date de naissance </th>
                    <th scope = "col" width="5%"> </th>
                </tr>
                </thead>
                <tbody id = "users_table">

                </tbody>
            </table>
        </div>
      </div>
    <div id="teams_invisible" style="display:none;">
        <div class = "container-fluid">
            <input id="input_team" name="input_team" type="text" placeholder="Search..">
        </div>
        <br>
        <label id="message_null_teams" align="center" style="display:none;">Aucune équipe trouvée</label>
        <div id="table2" class="table-responsive">
            <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
                <thead class="thead-dark">
                  <tr>
                      <th scope = "col" width="2%"> ID </th>
                      <th scope = "col"> Nom </th>
                      <th scope = "col"> Chef d'équipe </th>
                      <th scope = "col"> Tournoi </th>
                      <th scope = "col"> Membres de l'équipe </th>
                      <th scope = "col"> Date de création </th>
                      <th scope = "col" width="5%"> </th>
                  </tr>
                  </thead>
                <tbody id = "teams_table">

                </tbody>
            </table>
        </div>
    </div>
    <div id="tournaments_invisible" style="display:none;">
        <div class = "container-fluid">
            <input id="input_tournament" name="input_tournament" type="text" placeholder="Search..">
        </div>
        <br>
        <label id="message_null_tournaments" align="center" style="display:none;">Aucun tournoi trouvé</label>
        <div id="table3" class="table-responsive">
            <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
                <thead class="thead-dark">
                <tr>
                  <th scope = "col" width="2%"> ID </th>
                  <th scope = "col"> Nom </th>
                  <th scope = "col"> Nombre de joueurs par équipe </th>
                  <th scope = "col"> Nombre de joueurs/équipes inscrites </th>
                  <th scope = "col"> Plage horaire </th>
                  <th scope="col" width="5%"></th>
                </tr>
              </thead>
                <tbody id = "tournaments_table">

                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
