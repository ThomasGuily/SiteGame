<?php /* Template Name: CustomPageT1 */ ?>
<?php
$bdd = new PDO('mysql:host=localhost;dbname=sitegame', 'root', '');
?>
<html>
  <head>
    <title> Page de test </title>
    <meta charset="utf-8">  </head>
</html>

<?php
$reponse = $bdd-> query('SELECT * FROM wp_players WHERE ID = 2');
$donnees = $reponse -> fetch();
 ?>

 <html>
   <body>
     <?php echo $donnees['pseudo']; ?>
   </body>
 </html>
