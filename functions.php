<?php

function nlr_setup(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('align-wide');
    register_nav_menus(['principal' => "Menu principal"]);
    add_theme_support("responsive-embeds");
    add_theme_support('custom-logo');
}

add_action('after_setup_theme', 'nlr_setup');

function nlr_styles(){
    wp_enqueue_style('nlr-style', get_template_directory_uri() . '/CSS/app.css');
    wp_enqueue_style('nlr-font', 'https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap');
    wp_enqueue_script('nlr-calendrier', get_template_directory_uri() . '/JS/calendrier.js', [], null, true);
    wp_enqueue_script('nlr-chevron', get_template_directory_uri(). '/JS/chevron.js', [], null, true);
    wp_enqueue_script('nlr-animation', get_template_directory_uri(). '/JS/animation.js', [], null, true);
    wp_enqueue_script('nlr-header', get_template_directory_uri(). '/JS/header.js', [], null, true);
    wp_enqueue_script('nlr-lastpage', get_template_directory_uri(). '/JS/lastPage.js', [], null, true);
    wp_enqueue_script('nlr-password', get_template_directory_uri(). '/JS/password.js', [], null, true);
}

add_action('wp_enqueue_scripts', 'nlr_styles');

function nlr_filter_percent(){
    wp_enqueue_script('nlr-customizer', get_template_directory_uri() . '/JS/customizer.js', ['customize-controls', 'wp-color-picker'], null, true);
}

add_action('customize_controls_enqueue_scripts', 'nlr_filter_percent');

function nlr_preview_scripts(){
    wp_enqueue_script('nlr-banniere-preview', get_template_directory_uri() . '/JS/banniere-preview.js', ['customize-preview'], null, true);
}

add_action('customize_preview_init', 'nlr_preview_scripts');

function nlr_editor_scripts(){
    wp_enqueue_script('nlr-video-ombre', get_template_directory_uri() . '/JS/video-ombre.js', ['wp-hooks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-compose'], null, false);
}

add_action('enqueue_block_editor_assets', 'nlr_editor_scripts');

function nlr_video_ombre($contenu, $block){
    if (!in_array($block['blockName'], ['core/video', 'core/embed'])) {
        return $contenu;
    }

    $couleur = sanitize_hex_color($block['attrs']['ombreCouleur'] ?? '');
    $taille  = $block['attrs']['ombreTaille'] ?? null;
    $arrondi = $block['attrs']['ombreArrondi'] ?? null;

    // On construit la liste des variables CSS à ajouter
    $variables = '';
    if ($couleur) {
        $variables .= '--ombre-couleur: ' . $couleur . ';';
    }
    if ($taille !== null) {
        $variables .= ' --ombre-taille: ' . absint($taille) . 'px;';
    }
    if ($arrondi !== null) {
        $variables .= ' --ombre-arrondi: ' . absint($arrondi) . 'px;';
    }

    // Rien de réglé : on ne touche pas au bloc
    if ($variables === '') {
        return $contenu;
    }

    $balises = new WP_HTML_Tag_Processor($contenu);
    if ($balises->next_tag('figure')) {
        $style = $balises->get_attribute('style');
        $balises->set_attribute('style', $variables . ($style ? ' ' . $style : ''));
    }

    return $balises->get_updated_html();
}

add_filter('render_block', 'nlr_video_ombre', 10, 2);

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

    // NOUVEAU : deuxième type de contenu, les activités
    register_post_type('activite', [
        'labels' => [
            'name'          => 'Activités',
            'singular_name' => 'Activité',
            'add_new_item'  => 'Ajouter une activité',
        ],
        'public'       => true,
        'menu_icon'    => 'dashicons-groups',
        'supports'     => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);
}

add_action('init', 'nlr_evenements');

function nlr_documents(){
    register_post_type('document', [
        'labels' => [
            'name' => 'Documents',
            'singular_name' => 'Document',
            'add_new_item' => 'Ajouter un document',
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-media-document',
        'supports' => ['title'],
    ]);

    register_taxonomy('document_category', 'document', [
        'labels' => [
            'name' => 'Catégories de documents',
            'singular_name' => 'Catégorie de document',
            'add_new_item' => 'Ajouter une catégorie',
        ],
        'public' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'show_admin_column' => true,
    ]);
}

add_action('init', 'nlr_documents');



// -- Dossier privé des PDF ---

// Chemin du dossier privé sur le disque
function nlr_private_dir(){
    return wp_upload_dir()['basedir'] . '/nlr-private';
}

// Crée le dossier de ton .htaccess qui interdit tout accès direct
function nlr_protect_private_dir(){
    $dir = nlr_private_dir();
    wp_mkdir_p($dir);
    if(!file_exists($dir . '/.htaccess')) {
        file_put_contents($dir . '/.htaccess', "Require all denied\n");
    }
}

// Filtre : envoie le fichier déposé dans le dossier privé au lieu de la médiathèque
function nlr_private_upload_dir($dirs){
    $dirs['subdir'] = '/nlr-private';
    $dirs['path']   = $dirs['basedir'] . '/nlr-private';
    $dirs['url']    = $dirs['baseurl'] . '/nlr-private';
    return $dirs;

}

// Supprime un fichier du dossier privé
function nlr_delete_private_file($filename){
    if($filename){
        $path = nlr_private_dir() . '/' . basename($filename);
        if(file_exists($path)){
            unlink($path);
        }
    }
}

function nlr_document_box(){
    add_meta_box(
        'nlr_document',
        'Lien ou fichier PDF',
        'nlr_document_box_html',
        'document',
        'normal'
    );
}

add_action('add_meta_boxes', 'nlr_document_box');

// Autorise l'envoi de fichiers dans le formulaire d'un document
function nlr_document_form_enctype($post){
    if ($post->post_type === 'document') {
        echo ' enctype="multipart/form-data"';
    }
}

add_action('post_edit_form_tag', 'nlr_document_form_enctype');

function nlr_document_box_html($post){
    $url  = get_post_meta($post->ID, 'document_url', true);
    $file = get_post_meta($post->ID, 'document_file', true);
    wp_nonce_field('nlr_document_save', 'nlr_document_nonce');
    ?>
    <p>Remplir l'un ou l'autre : un lien vers un site, ou un fichier PDF.</p>
    <p>
        <label for="document_url">Lien vers un site :</label>
        <input type="url" id="document_url" name="document_url" class="widefat" placeholder="https://" value="<?php echo esc_attr($url); ?>">
    </p>
    <p>
        <label for="document_file">Fichier PDF :</label><br>
        <input type="file" id="document_file" name="document_file" accept="application/pdf">
    </p>
    <?php if ($file) : ?>
        <p>Fichier actuel : <strong><?php echo esc_html($file); ?></strong></p>
    <?php endif; ?>
    <?php
}

function nlr_document_save($post_id){
    if (!isset($_POST['nlr_document_nonce']) || !wp_verify_nonce($_POST['nlr_document_nonce'], 'nlr_document_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $url     = esc_url_raw(wp_unslash($_POST['document_url'] ?? ''));
    $oldFile = get_post_meta($post_id, 'document_file', true);

    if (!empty($_FILES['document_file']['name'])) {
        // Un nouveau PDF est déposé : on l'envoie dans le dossier privé
        nlr_protect_private_dir();
        add_filter('upload_dir', 'nlr_private_upload_dir');
        $upload = wp_handle_upload($_FILES['document_file'], [
            'test_form' => false,
            'mimes'     => ['pdf' => 'application/pdf'],
        ]);
        remove_filter('upload_dir', 'nlr_private_upload_dir');

        if (empty($upload['error'])) {
            nlr_delete_private_file($oldFile);
            update_post_meta($post_id, 'document_file', basename($upload['file']));
            $url = '';   // le PDF remplace le lien
        }
    } elseif ($url !== '' && $oldFile) {
        // Un lien est saisi sans nouveau PDF : le lien remplace l'ancien fichier
        nlr_delete_private_file($oldFile);
        delete_post_meta($post_id, 'document_file');
    }

    update_post_meta($post_id, 'document_url', $url);
}

add_action('save_post_document', 'nlr_document_save');

function nlr_download_document(){
    // 1. Pas une demande de téléchargement: WordPress continue normalement
    if(!isset($_GET['nlr_download'])){
        return;
    }

    // 2. Réservé aux personnes connectées
    if(!is_user_logged_in()){
        wp_die('Accès réservé aux bénévoles.', '', 403);
    }

    // 3. Le numéro du document, forcé en entier positif
    $id = absint($_GET['nlr_download']);

    // 4. Ce numéro doit correspondre à un document publié
    if(get_post_type($id) !== 'document' || get_post_status($id) !== 'publish'){
        wp_die('Document introuvable.', '', 404);
    }

    // 5. Le fichier doit exister dans le dossier privé
    $file = get_post_meta($id, 'document_file', true);
    $path = nlr_private_dir() . '/' . basename($file);
    if(!$file || !file_exists($path)){
        wp_die('Fichier introuvable.', '', 404);
    }

    // 6. Envoi du fichier
    nocache_headers();
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;


}

add_action('template_redirect', 'nlr_download_document');

function nlr_create_volunteer_role(){
    add_role('volunteer', 'Bénévole', [
        'read' => true,
    ]);
}

add_action('init', 'nlr_create_volunteer_role');

function nlr_hide_admin_bar($show){
    if(!current_user_can('edit_posts')) {
        return false;
    }
    return $show;
}

add_filter('show_admin_bar', 'nlr_hide_admin_bar');

function nlr_block_admin(){
    if(!current_user_can('edit_posts') && !wp_doing_ajax()) {
        wp_safe_redirect(home_url());
        exit;
    }
}

add_action('admin_init', 'nlr_block_admin');

function nlr_date_box(){
    add_meta_box(
        'nlr_date',
        'Date, horaires et lieu',          // NOUVEAU : titre plus juste
        'nlr_date_box_html',
        ['evenement', 'activite'],         // NOUVEAU : la boîte apparaît sur les deux types
        'side'
    );
}

add_action('add_meta_boxes', 'nlr_date_box');

function nlr_date_box_html($post){
    $date  = get_post_meta($post->ID, 'date_evenement', true);
    $debut = get_post_meta($post->ID, 'heure_debut', true);
    $fin   = get_post_meta($post->ID, 'heure_fin', true);
    $lieu  = get_post_meta($post->ID, 'lieu', true);   // NOUVEAU
    ?>
    <?php wp_nonce_field('nlr_date_save', 'nlr_date_nonce'); ?>
    <label for="date_evenement">Date :</label>
    <input type="date" id="date_evenement" name="date_evenement" value="<?php echo esc_attr($date); ?>">
    <p>
        <label for="heure_debut">Début :</label>
        <input type="time" id="heure_debut" name="heure_debut" value="<?php echo esc_attr($debut); ?>">
    </p>
    <p>
        <label for="heure_fin">Fin :</label>
        <input type="time" id="heure_fin" name="heure_fin" value="<?php echo esc_attr($fin); ?>">
    </p>
    <!-- NOUVEAU : champ lieu -->
    <p>
        <label for="lieu">Lieu :</label>
        <input type="text" id="lieu" name="lieu" value="<?php echo esc_attr($lieu); ?>">
    </p>
    <?php
}

function nlr_date_save($post_id){
    if (!isset($_POST['nlr_date_nonce']) || !wp_verify_nonce($_POST['nlr_date_nonce'], 'nlr_date_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['date_evenement'])) {
        update_post_meta($post_id, 'date_evenement', sanitize_text_field($_POST['date_evenement']));
    }
    if (isset($_POST['heure_debut'])) {
        update_post_meta($post_id, 'heure_debut', sanitize_text_field($_POST['heure_debut']));
    }
    if (isset($_POST['heure_fin'])) {
        update_post_meta($post_id, 'heure_fin', sanitize_text_field($_POST['heure_fin']));
    }
    // NOUVEAU : enregistrement du lieu
    if (isset($_POST['lieu'])) {
        update_post_meta($post_id, 'lieu', sanitize_text_field($_POST['lieu']));
    }
}

add_action('save_post_evenement', 'nlr_date_save');
add_action('save_post_activite', 'nlr_date_save');   // NOUVEAU : même sauvegarde pour les activités

function nlr_biblio_menu(){
    add_menu_page(
        'Bibliothèques de rue',   // titre de l'onglet du navigateur
        'Bibliothèques de rue',   // texte dans le menu
        'edit_posts',             // droit nécessaire pour y accéder
        'nlr-biblio',             // identifiant de la page dans l'URL
        'nlr_biblio_page',        // fonction qui affiche la page
        'dashicons-book',         // icône
        26                        // position dans le menu
    );
}

add_action('admin_menu', 'nlr_biblio_menu');

function nlr_customizer($wp_customize){
    
    // NOUVEAU : une section à part pour les couleurs
    $wp_customize->add_section('nlr_section_couleurs', ['title' => 'Couleurs',]);

    $wp_customize->add_setting('nlr_couleur_bandeau', [
        'default' => '#FFFC78',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'couleur_bandeau', [
        'label' => "Couleur du bandeau",              // CHANGÉ : « du » au lieu de « de la »
        'section' => 'nlr_section_couleurs',          // CHANGÉ : nouvelle section
        'settings' => 'nlr_couleur_bandeau',
    ]));

    $wp_customize->add_section('nlr_section_agenda', ['title' => 'Agenda' ]);

    $wp_customize->add_setting('nlr_agenda_actif', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('nlr_agenda_actif', [
        'label' => "Afficher le bouton agenda",
        'section' => 'nlr_section_agenda',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting('nlr_couleur_footer', [
        'default' => '#e63946',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'couleur_footer', [
        'label' => "Couleur de fond du pied de page",
        'section' => 'nlr_section_couleurs',
        'settings' => 'nlr_couleur_footer',
    ]));

    $wp_customize->add_setting('nlr_opacite_footer', [
        'default' => 100,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('nlr_opacite_footer', [
        'label' => "Opacité du fond du pied de page ",
        'section' => 'nlr_section_couleurs',
        'type' => 'range',
        'input_attrs' => ['min' => 0, 'max' => 100, 'step' => 1],
    ]);

    $wp_customize->add_section('nlr_section_fontsize', ['title' => 'Tailles de police',]);
    $wp_customize->add_setting('nlr_fontsize_h1', [
        'default' => 100,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('nlr_fontsize_h1', [
        'label' => "Taille des titres h1",
        'section' => 'nlr_section_fontsize',
        'type' => 'range',
        'input_attrs' => ['min' => 50, 'max' => 200, 'step' => 1],
    ]);
        $wp_customize->add_setting('nlr_fontsize_h2', [
        'default' => 100,
        'sanitize_callback' => 'absint',
    ]);
        $wp_customize->add_control('nlr_fontsize_h2', [
            'label' => "Taille des titres h2 ",
            'section' => 'nlr_section_fontsize',
            'type' => 'range',
            'input_attrs' => ['min' => 80, 'max' => 120, 'step' => 1],
        ]);
        $wp_customize->add_setting('nlr_fontsize_h3', [
        'default' => 100,
        'sanitize_callback' => 'absint',
    ]);
        $wp_customize->add_control('nlr_fontsize_h3', [
            'label' => "Taille des titres h3 ",
            'section' => 'nlr_section_fontsize',
            'type' => 'range',
            'input_attrs' => ['min' => 80, 'max' => 120, 'step' => 1],
        ]);

        $wp_customize->add_setting('nlr_fontsize_p', [
        'default' => 100,
        'sanitize_callback' => 'absint',
    ]);

        $wp_customize->add_control('nlr_fontsize_p', [
            'label' => "Taille des paragraphes ",
            'section' => 'nlr_section_fontsize',
            'type' => 'range',
            'input_attrs' => ['min' => 80, 'max' => 120, 'step' => 1],
        ]); 
        $wp_customize->add_setting('nlr_fontsize_a', [
        'default' => 100,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('nlr_fontsize_a', [
        'label' => "Taille des liens ",
        'section' => 'nlr_section_fontsize',
        'type' => 'range',
        'input_attrs' => ['min' => 80, 'max' => 120, 'step' => 1],
    ]);

    $wp_customize->add_setting('nlr_fontsize_menu', [
        'default' => 100,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('nlr_fontsize_menu', [
        'label' => "Taille du menu ",
        'section' => 'nlr_section_fontsize',
        'type' => 'range',
        'input_attrs' => ['min' => 50, 'max' => 120, 'step' => 1],
    ]);

    $wp_customize->add_setting('nlr_interligne',[
        'default' => 150,
        'sanitize_callback' => 'absint',

    ]);

    $wp_customize->add_control('nlr_interligne', [
        'label' => "Interligne du texte",
        'section' => 'nlr_section_fontsize',
        'type' => 'range',
        'input_attrs' => ['min' => 100, 'max' => 250, 'step'=> 5], 
    ]);


    $wp_customize->add_setting('nlr_logo_size', [
    'default' => 100,
    'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('nlr_logo_size', [
        'label' => "Taille du logo ",
        'section' => 'title_tagline',
        'type' => 'range',
        'input_attrs' => ['min' => 50, 'max' => 200, 'step' => 1],
    ]);
    $wp_customize->add_setting('nlr_voile_actions', [
        'default' => 24,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('nlr_voile_actions', [
        'label' => "Opacité du voile des actions ",
        'section' => 'nlr_section_couleurs',
        'type' => 'range',
        'input_attrs' => ['min' => 0, 'max' => 100, 'step' => 1],
    ]);

    $wp_customize->add_setting('nlr_couleur_ronds', [
        'default' => '#FF6900',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'couleur_ronds', [
        'label' => "Nos actions : couleur des ronds",
        'section' => 'nlr_section_couleurs',
        'settings' => 'nlr_couleur_ronds',
    ]));

    $wp_customize->add_setting('nlr_couleur_voile', [
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'couleur_voile', [
        'label' => "Nos actions : couleur du voile",
        'section' => 'nlr_section_couleurs',
        'settings' => 'nlr_couleur_voile',
    ]));

    $wp_customize->add_setting('nlr_couleur_texte_actions', [
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'couleur_texte_actions', [
        'label' => "Nos actions : couleur du texte",
        'section' => 'nlr_section_couleurs',
        'settings' => 'nlr_couleur_texte_actions',
    ]));
        $wp_customize->add_setting('nlr_couleur_rejoignez', [
        'default' => '#FF6900',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'couleur_rejoignez', [
        'label' => "Rejoignez-nous : couleur des images",
        'section' => 'nlr_section_couleurs',
        'settings' => 'nlr_couleur_rejoignez',
    ]));

    $wp_customize->add_setting('nlr_fond_rejoignez', [
        'default' => '#FF6900',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fond_rejoignez', [
        'label' => "Rejoignez-nous : couleur du fond",
        'section' => 'nlr_section_couleurs',
        'settings' => 'nlr_fond_rejoignez',
    ]));

    $style = 'nlr_banniere_style';

    $wp_customize->add_section($style, [
        'title' => 'Style des bannières',
    ]);

    $wp_customize->add_setting($style . '_couleur_texte', [
    'default' => '#ffffff',
    'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $style . '_couleur_texte', [
        'label' => "Couleur du texte",
        'section' => $style,
        'settings' => $style . '_couleur_texte',
    ]));

    $wp_customize->add_setting($style . '_largeur', [
        'default' => 60,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control($style . '_largeur', [
        'label' => "Largeur du cadre du texte ",
        'section' => $style,
        'type' => 'range',
        'input_attrs' => ['min' => 20, 'max' => 100, 'step' => 1],
    ]);

    $wp_customize->add_setting($style . '_fond', [
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $style . '_fond', [
        'label' => "Couleur du fond du texte",
        'section' => $style,
        'settings' => $style . '_fond',
    ]));

    $wp_customize->add_setting($style . '_opacite', [
        'default' => 0,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control($style . '_opacite', [
        'label' => "Opacité du fond du texte ",
        'section' => $style,
        'type' => 'range',
        'input_attrs' => ['min' => 0, 'max' => 100, 'step' => 1],
    ]);

    $wp_customize->add_setting($style . '_rotation', [
        'default' => 0,
        'sanitize_callback' => function($valeur){ return intval($valeur); },
    ]);
    $wp_customize->add_control($style . '_rotation', [
        'label' => "Rotation du texte ",
        'section' => $style,
        'type' => 'range',
        'input_attrs' => ['min' => -20, 'max' => 20, 'step' => 1],
    ]);

    $wp_customize->add_setting($style . '_pos_x', [
        'default' => 50,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_setting($style . '_pos_y', [
        'default' => 50,
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_setting('couleur_tableau_missions', array(
        'default'           => '#7f3300',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'couleur_tableau_missions', array(
        'label'   => 'Fond du tableau des missions',
        'section' => 'nlr_section_couleurs',
    )));

    $pages = get_pages();

    foreach($pages as $page){
        $id = "nlr_banniere_". $page->ID;

        $titreSection = 'Bannière : ' . $page->post_title;
        if ($page->ID == get_option('page_on_front')) {
            $titreSection .= ' (accueil)';
        }

        $wp_customize->add_section($id, [
            'title' => $titreSection,
            'active_callback' => function() use ($page) {
                return is_page($page->ID);
            },
        ]);
        
        $wp_customize->add_setting($id . '_image', [
            'default' => 0,
            'sanitize_callback' => 'absint',
        ]);
        $wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, $id . '_image', [
            'label' => "Image de la bannière",
            'section' => $id,
            'settings' => $id . '_image',
            'width' => 1920,
            'height' => 756,
            'flex_width' => false,
            'flex_height' => false,

        ]));

        $wp_customize->add_setting($id . '_luminosite', [
            'default' => 50,
            'sanitize_callback' => 'absint',
        ]);
        $wp_customize->add_control($id . '_luminosite', [
            'label' => "Luminosité de l'image ",
            'section' => $id,
            'type' => 'range',
            'input_attrs' => ['min' => 0, 'max' => 100, 'step' => 1],
        ]);

        $wp_customize->add_setting($id . '_titre', [
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control($id . '_titre', [
            'label' => "Texte de la bannière",
            'section' => $id,
            'type' => 'text',
        ]);

        $wp_customize->add_setting($id . '_chantier', [
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ]);
        $wp_customize->add_control($id . '_chantier', [
            'label' => "Afficher le bandeau << page en chantier >>",
            'section' => $id,
            'type' => 'checkbox',
        ]);

        
       
    }


}

add_action('customize_register', 'nlr_customizer');



function nlr_widgets(){
    $colonnes = [
        'footer-1' => 'Pied de page – colonne 1',
        'footer-2' => 'Pied de page – colonne 2',
        'footer-3' => 'Pied de page – colonne 3',
        'legacy-mention' => 'Mention légale',
    ];
    foreach ($colonnes as $id => $nom) {
        register_sidebar([
            'name'          => $nom,
            'id'            => $id,
            'before_widget' => '<div class="footer__widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="title__footer">',
            'after_title'   => '</h2>',
        ]);
    }
}

add_action('widgets_init', 'nlr_widgets');

function nlr_couleurs(){
    $bandeau = get_theme_mod('nlr_couleur_bandeau', '#FFFC78');
    $footer  = get_theme_mod('nlr_couleur_footer', '#e63946');
    $opacite = get_theme_mod('nlr_opacite_footer', 100);
    $h1 = get_theme_mod('nlr_fontsize_h1', 100);
    $h2      = get_theme_mod('nlr_fontsize_h2', 100);
    $h3      = get_theme_mod('nlr_fontsize_h3', 100);
    $p       = get_theme_mod('nlr_fontsize_p', 100);
    $a       = get_theme_mod('nlr_fontsize_a', 100);
    $logo    = get_theme_mod('nlr_logo_size', 100);
    $menu = get_theme_mod('nlr_fontsize_menu', 100);   
    $voile   = get_theme_mod('nlr_voile_actions', 25);
    $ronds        = get_theme_mod('nlr_couleur_ronds', '#FF6900');
    $voileCouleur = get_theme_mod('nlr_couleur_voile', '#000000');
    $texteActions = get_theme_mod('nlr_couleur_texte_actions', '#ffffff');
    $rejoignez = get_theme_mod('nlr_couleur_rejoignez', '#FF6900');
    $fondRejoignez = get_theme_mod('nlr_fond_rejoignez', '#FF6900');
    $colorTable = get_theme_mod('couleur_tableau_missions', '#7f3300');
    $interligne = get_theme_mod('nlr_interligne', 150);
    ?>
    <style>
        :root {
            --bandeau: <?php echo esc_attr($bandeau); ?>;
            --footer-fond: <?php echo esc_attr($footer); ?>;
            --footer-opacite: <?php echo absint($opacite); ?>%;
            --font-size-h1: <?php echo absint($h1) / 100; ?>;
            --font-size-h2: <?php echo absint($h2) / 100; ?>;
            --font-size-h3: <?php echo absint($h3) / 100; ?>;
            --font-size-p: <?php echo absint($p) / 100; ?>;
            --font-size-a: <?php echo absint($a) / 100; ?>;
            --logo-size: <?php echo absint($logo) / 100; ?>;
            --voile-actions: <?php echo absint($voile) / 100; ?>;
            --ronds-actions: <?php echo esc_attr($ronds); ?>;
            --voile-couleur: <?php echo esc_attr($voileCouleur); ?>;
            --texte-actions: <?php echo esc_attr($texteActions); ?>;
            --couleur-rejoignez: <?php echo esc_attr($rejoignez); ?>;
            --fond-rejoignez: <?php echo esc_attr($fondRejoignez); ?>;
            --font-size-menu: <?php echo absint($menu) / 100; ?>;
            --couleur-tableau-missions: <?php echo esc_attr($colorTable); ?>;
            --interligne: <?php echo absint($interligne) / 100; ?>;
        }
    </style>
    <?php
}

add_action('wp_head', 'nlr_couleurs');

function nlr_block_styles(){
    register_block_style(['core/video', 'core/embed'], [
        'name' => 'encadree',
        'label' => 'Vidéo encadrée',
    ]);
    // Style 2: la bande penchée (nouveau)
    register_block_style(['core/columns', 'core/group'], [
        'name' => 'fond-penche',
        'label' => 'Bande penchée',
    ]);
}

add_action('init', 'nlr_block_styles');

function nlr_biblio_page(){
        $biblio = get_option('nlr_biblio', []);

    // Enregistrement, si le formulaire a été envoyé
    if (isset($_POST['nlr_biblio_nonce'])
        && wp_verify_nonce($_POST['nlr_biblio_nonce'], 'nlr_biblio_save')
        && current_user_can('edit_posts')) {

        update_option('nlr_biblio_lieu', sanitize_text_field(wp_unslash($_POST['lieu_habituel'] ?? '')));

        foreach ($_POST['jeudis'] ?? [] as $date => $infos) {
            $date   = sanitize_text_field($date);
            $annule = !empty($infos['annule']);
            $lieu   = sanitize_text_field(wp_unslash($infos['lieu'] ?? ''));

            if ($annule || $lieu !== '') {
                $biblio[$date] = ['annule' => $annule, 'lieu' => $lieu];
            } else {
                unset($biblio[$date]);
            }
        }

        update_option('nlr_biblio', $biblio);
        echo '<div class="notice notice-success"><p>Modifications enregistrées.</p></div>';
    }

    $lieuHabituel = get_option('nlr_biblio_lieu', '');

    // Premier jeudi à afficher : aujourd'hui si on est jeudi, sinon le prochain
    $jeudi = new DateTime(current_time('Y-m-d'));
    if ($jeudi->format('N') != 4) {
        $jeudi->modify('next thursday');
    }
    // Limite : même fin que le calendrier, 12 mois après le mois en cours
    $fin = (new DateTime(current_time('Y-m-01')))->modify('+13 months');
    ?>
    <div class="wrap">
        <h1>Bibliothèques de rue</h1>
        <form method="post">
            <?php wp_nonce_field('nlr_biblio_save', 'nlr_biblio_nonce'); ?>

            <p>
                <label for="lieu_habituel">Lieu habituel :</label>
                <input type="text" id="lieu_habituel" name="lieu_habituel" class="regular-text" value="<?php echo esc_attr($lieuHabituel); ?>">
            </p>

            <table class="widefat striped">
                <thead>
                    <tr><th>Jeudi</th><th>Annulé</th><th>Lieu, si différent</th></tr>
                </thead>
                <tbody>
                <?php while ($jeudi < $fin) :
                    $date  = $jeudi->format('Y-m-d');
                    $infos = $biblio[$date] ?? [];
                ?>
                    <tr>
                        <td><?php echo esc_html(date_i18n('l j F Y', $jeudi->getTimestamp())); ?></td>
                        <td><input type="checkbox" name="jeudis[<?php echo esc_attr($date); ?>][annule]" value="1" <?php checked(!empty($infos['annule'])); ?>></td>
                        <td><input type="text" name="jeudis[<?php echo esc_attr($date); ?>][lieu]" value="<?php echo esc_attr($infos['lieu'] ?? ''); ?>" placeholder="<?php echo esc_attr($lieuHabituel); ?>"></td>
                    </tr>
                <?php
                    $jeudi->modify('+7 days');
                endwhile; ?>
                </tbody>
            </table>

            <?php submit_button('Enregistrer'); ?>
        </form>
    </div>
    <?php
}