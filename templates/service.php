<?php
/**
 * Template Name: Базовый шаблон услуг
 * Template Post Type: service
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
// Скрипт ленивой загрузки галерей
add_action(
	'wp_enqueue_scripts',
	static fn () => wp_enqueue_script(
		'load-more',
		get_template_directory_uri() . '/assets/js/load-more.js', ['jquery'], in_footer: true
	)
);

$context = Timber::context();
$post = Timber::get_post();

// Пучаем информацию главной страницы
$indexPage = Timber::get_post([
	'post_type' => 'page',
	'name' => 'index',
	'numberposts' => -1,
	'hide_empty' => true,
]);

// Добавляем в пост промо-банеры и акции
$post->update('mainpage_actions', $indexPage->meta('mainpage_actions'));


$context['post'] = $post;

// Передаём в сайдбар услуги
$context['sidebar'] = Timber::get_sidebar('partials/sidebar-services.twig', $context);

Timber::render('single-service.twig', $context);
