<?php
/**
 * The template for displaying index page
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
$context = Timber::context();
$context['posts'] = new Timber\PostQuery();

$templates = ['index.twig'];

if (is_home()) {
	array_unshift($templates, 'front-page.twig', 'home.twig');
}

Timber::render($templates, $context);
