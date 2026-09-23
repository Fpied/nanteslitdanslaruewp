<?php
$evenements = new WP_Query([
    'post_type'      => 'evenement',
    'posts_per_page' => 5,
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
<section class="agenda">
    <h2 class="agenda__titre">L'agenda</h2>
    <?php while ($evenements->have_posts()) : $evenements->the_post();
        $timestamp = strtotime(get_post_meta(get_the_ID(), 'date_evenement', true));
    ?>
        <article class="agenda__item">
            <div class="agenda__date">
                <span class="agenda__jour"><?php echo esc_html(date_i18n('l', $timestamp)); ?></span>
                <span class="agenda__num"><?php echo esc_html(date_i18n('d', $timestamp)); ?></span>
                <span class="agenda__mois"><?php echo esc_html(date_i18n('F', $timestamp)); ?></span>
            </div>
            <a class="agenda__lien" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </article>
    <?php endwhile; wp_reset_postdata(); ?>
</section>
<?php endif; ?>