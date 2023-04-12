<?php
/**
 * Plugin Name: contact_form
 */
register_activation_hook(__FILE__, 'myPluginCreateTable');

function myPluginCreateTable() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = $wpdb->prefix . 'contact_form';
  $sql = "CREATE TABLE `$table_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sujet` varchar(220) DEFAULT NULL,
  `nom` varchar(220) DEFAULT NULL,
  `prénom` varchar(220) DEFAULT NULL,
  `email` varchar(220) DEFAULT NULL,
  `message` varchar(250) DEFAULT NULL,
  date_envoi TIMESTAMP NOT NULL DEFAULT CURRENT_DATE(),
  PRIMARY KEY(id)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
  ";

  if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}
register_deactivation_hook(__FILE__,'deletetable');
function deletetable(){
  global $wpdb;
  $wp_contact_form = $wpdb->prefix . 'contact_form';
  $sql = "DROP TABLE IF EXISTS $wp_contact_form";
  $wpdb->query($sql);
  delete_option("devnote_plugin-db_version");

}
// affichage de formulaire de contact 
function contact_form_shortcode(){
  submit_contact_form();
  ob_start();
  ?>
  <form method="post" action="">
  <label for="Sujet">Sujet<span>*</span></label><br>
  <input type="text" name="sujet"required><br>
  
  <label for="Nom">Nom<span>*</span></label><br>
  <input type="text" name="Nom"required><br>
  
  <label for="prénom">Prénom<span>*</span></label><br>
  <input type="text" name="Prénom"required><br>
  
  <label for="email">Email<span>*</span></label><br>
  <input type="email" name="email"required><br>
  
  <label for="message">message<span>*</span></label><br>
  <textarea name="message" rows="8" required></textarea><br><br>

  
  <input type="submit" name="submit_contact_form" value="Envoyer">
</from>

<?php
// Récupération du contenu du tampon de sortie
$content = ob_get_contents();
// Nettoyage du tampon de sortie
ob_end_clean();
// Retourne le contenu du shortcode
return $content;
}
add_shortcode('contact_form','contact_form_shortcode');



function submit_contact_form() {
  if ( isset( $_POST['submit_contact_form'] ) ) {
    $sujet = sanitize_text_field($_POST['sujet']);
    $nom = sanitize_text_field($_POST['Nom']);
    $prenom = sanitize_text_field($_POST['Prénom']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form';
    $wpdb->insert($table_name, array(
        'sujet' => $sujet,
        'nom' => $nom,
        'prénom' => $prenom,
        'email' => $email,
        'message' => $message
    ));
      array('%s', '%s','%s','%s','%s');

    wp_redirect(home_url('/contact_us/'));
    exit;
  }
 

}



function menu_page()
        {
            add_menu_page('contact_form', 'contact_form', 'manage_options', 'cf_responses_page', 'settings_page', 'dashicons-email-alt', 1);
        }
            add_action('admin_menu', 'menu_page');

    function settings_page()
        {
            if (!current_user_can('manage_options')) {
            return;
        }

            global $wpdb;
            $table_name = $wpdb->prefix . 'contact_form';
            $results = $wpdb->get_results("SELECT * FROM $table_name");

            echo '<div class="wrap bg-dark">';
            echo '<h1>' . esc_html('Contact Form Responses', 'contact_form') . '</h1>';
            echo '<p>' . esc_html('View and manage responses submitted through the contact form.') . '</p>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead>';
            echo '<tr>';
            echo '<th style="width: 2rem;">' . esc_html('id', 'contact_form') . '</th>';
            echo '<th>' . esc_html('sujet', 'contact_form') . '</th>';
            echo '<th>' . esc_html('nom', 'contact_form') . '</th>';
            echo '<th>' . esc_html('prénom', 'contact_form') . '</th>';
            echo '<th>' . esc_html('email', 'contact_form') . '</th>';
            echo '<th>' . esc_html('message', 'contact_form') . '</th>';
            echo '<th>' . esc_html__('date_envoi', 'contact_form') . '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
foreach ($results as $row) 
        {
            echo '<tr>';
            echo '<td>' . $row->id . '</td>';
            echo '<td>' . $row->sujet . '</td>';
            echo '<td>' . $row->nom . '</td>';
            echo '<td>' . $row->prénom . '</td>';
            echo '<td>' . $row->email . '</td>';
            echo '<td>' . $row->message . '</td>';
            echo '<td>' . $row->date_envoi	. '</td>';
            echo '</tr>';
        }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }



?>


