<?php

function nlr_setup(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(['principal' => "Menu principal"]);

}

add_action('after_setup_theme', 'nlr_setup');

function nlr_styles(){
    wp_enqueue_style('nlr-style', get_template_directory_uri() . '/CSS/app.css');
    wp_enqueue_style('nlr-font', 'https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap');

}

add_action('wp_enqueue_scripts', 'nlr_styles');

function nlr_evenements(){
    register_post_type('evenement', [
        'labels' => [
            'name'          => 'Événements',
            'singular_name' => 'Événement',
            'add_new_item'  => 'Ajouter un événement',
        ],
        'public'       => true,
        'menu_icon'    => 'dashicons-calendar-alt',
        'supports'     => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);
}

add_action('init', 'nlr_evenements');

function nlr_date_box(){
    add_meta_box(
        'nlr_date',                  // identifiant de la boîte
        'Date de l\'événement',      // titre affiché
        'nlr_date_box_html',         // fonction qui dessine le contenu
        'evenement',                 // sur quel type de contenu
        'side'                       // emplacement : la colonne de droite
    );
}

add_action('add_meta_boxes', 'nlr_date_box');

function nlr_date_box_html($post){
    $date = get_post_meta($post->ID, 'date_evenement', true);
    ?>
    <?php wp_nonce_field('nlr_date_save', 'nlr_date_nonce'); ?>
    <label for="date_evenement">Date :</label>
    <input type="date" id="date_evenement" name="date_evenement" value="<?php echo esc_attr($date); ?>">
    <?php
}

function nlr_date_save($post_id){
    // le jeton est-il présent et valide ?
    if (!isset($_POST['nlr_date_nonce']) || !wp_verify_nonce($_POST['nlr_date_nonce'], 'nlr_date_save')) {
        return;
    }
    // est-ce une sauvegarde automatique ? on ne touche à rien
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // la personne a-t-elle le droit de modifier cet événement ?
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    // tout est bon : on enregistre la date, nettoyée
    if (isset($_POST['date_evenement'])) {
        update_post_meta($post_id, 'date_evenement', sanitize_text_field($_POST['date_evenement']));
    }
}

add_action('save_post_evenement', 'nlr_date_save');