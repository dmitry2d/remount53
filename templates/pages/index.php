<?php
/**
 * Template Name: Шаблон главной страницы
 * Template Post Type: page
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
$context = Timber::context();

$post = new Timber\Post();
$context['post'] = $post;

// Получаем галереи (сданные объекты)
$context['galleries'] = Timber::get_posts([
	'post_type' => 'gallery',
	'meta_query' => [
		[
			'key' => 'show_gallery_on_mainpage',
			'value' => true,
		],
	],
	'meta_key' => 'date',
	'orderby' => 'meta_value_num',
	'order' => 'DESC',
	'numberposts' => 10,
]);

return Timber::render('page-index.twig', $context);
