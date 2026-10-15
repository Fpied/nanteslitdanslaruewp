<?php 
 
$id = 'nlr_banniere_' . get_the_ID();
$style = 'nlr_banniere_style';
$luminosite = get_theme_mod($id . '_luminosite', 50);
$image = get_theme_mod($id  . '_image', '');
$titre = get_theme_mod($id . '_titre', '');
$fond = get_theme_mod($style  . '_fond', '#000000');
$opacite = get_theme_mod($style  . '_opacite', 0);
$rotation = get_theme_mod($style  . '_rotation', 0);
$posX = get_theme_mod($style  . '_pos_x', 50);
$posY = get_theme_mod($style  . '_pos_y', 50);
$couleurTexte = get_theme_mod($style  . '_couleur_texte', '#ffffff');
$largeur = get_theme_mod($style  . '_largeur', 60);

if (!$image){ return; }

$imageUrl = wp_get_attachment_image_url($image, 'full');
?>
<div class="hero">
    <img src="<?php echo esc_url($imageUrl); ?>"
        class="image__header"
        alt=""
        style="filter: brightness(<?php echo absint($luminosite); ?>%)">
    <div class="transparencetitle"
     data-banniere="<?php echo esc_attr($style); ?>"
     style="--pos-x: <?php echo absint($posX); ?>%;
            top: <?php echo absint($posY); ?>%;
            --largeur: <?php echo absint($largeur); ?>%;
            --rotation: <?php echo intval($rotation); ?>deg;">
        <h1 class="title__home"
            style="color: <?php echo esc_attr($couleurTexte); ?>;
                background-color: color-mix(in srgb, <?php echo esc_attr($fond); ?> <?php echo absint($opacite); ?>%, transparent);">
            <?php echo esc_html($titre); ?>
        </h1>
    </div>
</div>

