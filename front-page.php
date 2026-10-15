<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
<main class="main__home">
    <?php get_template_part('template-parts/banniere'); ?>

    <?php the_content(); ?>

</main>
<?php endwhile; ?>

<?php get_footer(); ?>