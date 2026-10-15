<?php
$evenements = new WP_Query([
    'post_type'      => 'evenement',
    'posts_per_page' => 3,
    'meta_key'       => 'date_evenement',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [[
        'key'     => 'date_evenement',
        'value'   => current_time('Y-m-d'),
        'compare' => '>=',
        'type'    => 'DATE',
    ]],
]);

if ($evenements->have_posts()) : ?>
<section class="cartes">
    <?php while ($evenements->have_posts()) : $evenements->the_post();
        $timestamp = strtotime(get_post_meta(get_the_ID(), 'date_evenement', true));
        $debut = get_post_meta(get_the_ID(), 'heure_debut', true);
        $fin   = get_post_meta(get_the_ID(), 'heure_fin', true);
        $lieu = get_post_meta(get_the_ID(), 'lieu', true);
    ?>
        <a href="<?php the_permalink(); ?>" class="carte">
            <div class="carte__affiche">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium'); ?>
                <?php endif; ?>
            </div>
            <div class="carte__infos">
                <h3 class="carte__titre"><?php the_title(); ?></h3>
                <p class="carte__date"><?php echo esc_html(date_i18n('l j F', $timestamp)); ?></p>
                <?php if ($debut) : ?>
                    <p class="carte__heure">
                        <?php echo esc_html($debut); ?><?php if ($fin) : ?> - <?php echo esc_html($fin); ?><?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if($lieu) : ?>
                    <p class="carte__lieu"><?php echo esc_html($lieu); ?></p>
                <?php endif; ?>
            </div>
        </a>
    <?php endwhile; wp_reset_postdata(); ?>
</section>
<?php endif; ?>