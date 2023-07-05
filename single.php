<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();
$post = Timber::get_post();
$context['post'] = $post;

$isService = $post->post_type === 'service';

if ($isService) {
	$sidebar['services'] = Timber::get_posts([
		'post_type' => 'service'
	]);

	$context['sidebar'] = Timber::get_sidebar('partials/sidebar-services.twig', $sidebar);
}

if (post_password_required( $post->ID)) {
	Timber::render('single-password.twig', $context );
} else {
	Timber::render(['single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single-' . $post->slug . '.twig', 'single.twig'], $context );
}
