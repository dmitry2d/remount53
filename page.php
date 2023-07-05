<?php
/**
 * The template for displaying pages.
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
$context = Timber::context();

$post = new Timber\Post();
$context['post'] = $post;

Timber::render(['page-' . $post->post_name . '.twig', 'page.twig'], $context);
