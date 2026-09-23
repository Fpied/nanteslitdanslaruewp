<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/asset/LogoNLDLR-01.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header class="header">
    <a href="<?php echo home_url('/'); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/asset/LogoNLDLR-01.png" alt="Logo Nantes Lit dans la rue" class="logo__header">
    </a>
        <input class="menu_hamburger_checkbox" type="checkbox" id="votre_id" tabindex="0">
        <label class="label__hamburger" for="votre_id" aria-label="Menu"></label>
            <nav class="nav__header">
                <label for="votre_id" class="label__close" aria-label="Fermer">X</label>
                <?php wp_nav_menu([
                    'theme_location' => 'principal',
                    'container'      => false,
                    'menu_class'     => 'nav__header__ul',
                ]); ?>
            </nav>
    </header>

