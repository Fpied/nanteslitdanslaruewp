<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
<main class="main__home">
    <div class="hero">
        <?php the_post_thumbnail('full', ['class' => 'image__header']); ?>
        <div class="transparencetitle">
            <h1 class="title__home"><?php the_title(); ?></h1>
        </div>
    </div>

    <?php the_content(); ?>

</main>
<?php endwhile; ?>

<?php get_footer(); ?>