<?php
/**
 * Template Name: Шаблон галереи
 * Template Post Type: page
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
$context = Timber::context();

$post = new Timber\Post();
$context['post'] = $post;

// Передаём в сайдбар услуги
$context['sidebar'] = Timber::get_sidebar('partials/sidebar-services.twig', [
	'services' => $context['services']
]);

// Получаем таксономии gallery_category в заданном порядке
$context['gallery_categories'] = Timber::get_terms([
	'taxonomy'   => 'gallery_category',
	'orderby'    => 'term_order',
	'hide_empty' => true,
]);

// Получаем все галереи и группируем их по таксономиям
$galleries = Timber::get_posts([
	'post_type' => 'gallery',
	'meta_key' => 'date',
	'orderby' => 'meta_value_num',
	'order' => 'DESC',
	'numberposts' => -1,
]);

$groupedGalleries = [];
foreach ($galleries as $gallery) {
	$terms = Timber::get_terms([
		'taxonomy' => 'gallery_category',
		'object_ids' => $gallery->ID,
	]);

	foreach ($terms as $term) {
		$groupedGalleries[$term->slug][] = $gallery;
	}
}

// Добавляем группы галерей в контекст
$context['galleries'] = $groupedGalleries;

// Добавляем года к категориям галерей
foreach ($groupedGalleries as $key => $category) {
	$uniqueYears[$key] = [];

	foreach ($category as $gallery) {
		$galleryYear = date('Y', strtotime($gallery->custom['date']));

		$uniqueYears[$key][$galleryYear] = true;
	}

	$uniqueYearsArray[$key] = array_keys($uniqueYears[$key]);

	// Сохраняем года в категорию
	$context['years'][$key] = $uniqueYearsArray[$key];
}

unset($uniqueYears);

return Timber::render('page-gallery.twig', $context);
