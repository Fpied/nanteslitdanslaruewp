
<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
<main class="main__page">
    <?php get_template_part('template-parts/banniere'); ?>
    <div class="contenu__page contenu__page--wide">
        <?php the_content(); ?>
        <?php if(is_user_logged_in()) : ?>
            <h2 class="title_benevole">Ressources bénévoles</h2>

            <?php $categories = get_terms([
                'taxonomy' => 'document_category',
                'hide_empty' => false,
            ]); ?>
            <div class="resources">
                <?php foreach($categories as $category) : ?>
                    <section class="resources__box">
                        <h3 class="title_section"><?php echo esc_html($category->name); ?></h3>
                        <?php
                        $documents = get_posts([
                            'post_type' => 'document',
                            'numberposts' => -1,
                            'tax_query' => [
                                [
                                    'taxonomy' => 'document_category',
                                    'field' => 'term_id',
                                    'terms' => $category->term_id,
                                ],
                            ],
                        ]);
                        if($documents) : ?>
                            <ul>
                                <?php foreach($documents as $document) :
                                        $file = get_post_meta($document->ID, 'document_file', true); // AJOUT : nom du PDF
                                        $url = get_post_meta($document->ID, 'document_url', true); // Ajout du lien vers un site externe

                                        if($file){
                                            $link = add_query_arg('nlr_download', $document->ID, home_url('/'));
                                        } else {
                                            $link = $url;
                                        }
                                    ?>
                                    <li>
                                        <a href="<?php echo esc_url($link); ?>" target="_blank">
                                            <?php echo esc_html(get_the_title   ($document)); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p>Aucun document disponible pour cette catégorie.</p>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            </div>
            <a class="button_deconnexion__benevole" href="<?php echo esc_url(wp_logout_url(get_permalink())); ?>">Se déconnecter</a>
        <?php else : ?>
            <?php wp_login_form(['label_username' => 'Nom d\'utilisateur']); ?>
        <?php endif; ?>

    </div>
    
</main>
<?php endwhile; ?>

<?php get_footer(); ?>