<?php

function nlr_setup(){
    add_theme_support('title-tag');

}

add_action('after_setup_theme', 'nlr_setup');

function nlr_styles(){
    wp_enqueue_style('nlr-style', get_template_directory_uri() . '/CSS/app.css');
    wp_enqueue_style('nlr-font', 'https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap');

}

add_action('wp_enqueue_scripts', 'nlr_styles');