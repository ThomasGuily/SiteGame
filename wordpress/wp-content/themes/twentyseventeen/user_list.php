<?php /* Template Name: user_listes */ ?>
<?php
  try {
    global $wpdb;
  } catch (PDOException $e) {
    echo 'echec de connexion : ' . $e->getMessage();
  }

    global $id_page_edit_player;

  get_header();
?>
<html>
  <head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title> Base de données </title>
    <meta charset="utf-8">
  </head>
  <body>
    <div class="table-responsive">
      <table class="tablesorter {sortlist: [[0,0]]} table table-striped table-hover col-12">
        <thead class="thead-dark">
          <tr>
            <th scope = "col" width="2%"> ID </th>
            <th scope = "col"> Pseudo </th>
            <th scope = "col"> Nom </th>
            <th scope = "col"> Adresse mail </th>
            <th scope = "col"> Date d'inscription </th>
            <th scope = "col"> Téléphone </th>
            <th scope = "col"> Date de naissance </th>
            <th scope = "col" width="5%"> </th>
          </tr>
        </thead>
        <tbody id = "users_table">
          <?php
          $users_query = $wpdb->prepare("SELECT * FROM wp_users");
          $users = $wpdb -> get_results($users_query);
          foreach ($users as $user_infos) {
            $userid = $user_infos -> ID;
            $last_name_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "last_name");
            $last_name = $wpdb -> get_var($last_name_query);
            $first_name_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "first_name");
            $first_name = $wpdb -> get_var($first_name_query);
            $phone_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "phone_number");
            $phone = $wpdb -> get_var($phone_query);
            $birthdate_query = $wpdb -> prepare("SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s", $userid, "birthdate");
            $birthdate = $wpdb -> get_var($birthdate_query);
            ?>
            <tr>
              <td>
                <?php echo $userid ?>
              </td>
              <td>
                <?php echo $user_infos -> user_login ?>
              </td>
              <td>
                <?php echo "{$first_name} {$last_name}" ?>
              </td>
              <td>
                <?php echo $user_infos -> user_email ?>
              </td>
              <td>
                <?php echo $user_infos -> user_registered ?>
              </td>
              <td>
                <?php echo $phone ?>
              </td>
              <td>
                <?php echo $birthdate ?>
              </td>
              <td>
                  <a href="?page_id=<?php echo $id_page_edit_player;?>&id=<?php echo $userid; ?> ">
                  <input type="button" class="btn btn-dark" value="Edit" />
                </a>
              </td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
  </body>
</html>
