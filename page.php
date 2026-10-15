<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
<main class="main__page">
    <?php get_template_part('template-parts/banniere'); ?>
    <div class="contenu__page">
        <?php the_content(); ?>

    </div>
    
</main>
<?php endwhile; ?>

<?php get_footer(); ?>