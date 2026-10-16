    <footer>
        
    <div class="info_footer">
        <div class="titre">
            <?php if (is_active_sidebar('footer-1')) : ?>
                <?php dynamic_sidebar('footer-1'); ?>
            <?php else : ?>
                <h2 class="title__footer">Nantes lit dans la rue</h2>
                <p>Des trucs comme ci des trucs comme ça</p>
            <?php endif; ?>
        </div>
        <div class="contact__footer">
            <?php if (is_active_sidebar('footer-2')) : ?>
                <?php dynamic_sidebar('footer-2'); ?>
            <?php else : ?>
                <h2 class="title__footer">Nous contacter</h2>
                <p>📍 1 bis boulevard de Berlin,<br>
                    2e étage, 44000 Nantes</p>
            <?php endif; ?>
        </div>
        <div class="plan__du__site">
            <?php if (is_active_sidebar('footer-3')) : ?>
                <?php dynamic_sidebar('footer-3'); ?>
            <?php else : ?>
                <h2 class="title__footer">Plan du site</h2>
                <?php wp_nav_menu([
                        'theme_location' => 'principal',
                        'container'      => false,
                        'menu_class'     => 'footer__menu',
                    ]); ?>
            <?php endif; ?>

        </div>
    </div>
            <div class="footer__bas">
                <?php dynamic_sidebar('legacy-mention'); ?>
            </div>
    </footer>
<div class="barre__bas">
    <?php $pageAgenda = get_page_by_path('agenda'); ?>
    <?php if (get_theme_mod('nlr_agenda_actif', true) && $pageAgenda && get_post_status($pageAgenda) === 'publish') : ?>
        <a href="<?php echo get_permalink($pageAgenda); ?>"><img src="<?php echo get_template_directory_uri(); ?>/asset/icone_agenda.svg" alt="voir l'agenda"></a>
    <?php endif; ?>
    <a href="#haut" class="chevron__haut"><img src="<?php echo get_template_directory_uri(); ?>/asset/fleche-vers-le-haut.png" alt="flèche vers le haut"></a>
</div>
<?php wp_footer(); ?>
</body>
</html>