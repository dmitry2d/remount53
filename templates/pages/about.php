<?php
/**
 * Template Name: Шаблон страницы "О компании"
 * Template Post Type: page
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
$context = Timber::context();

$post = new Timber\Post();

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
$context['sidebar'] = Timber::get_sidebar('partials/sidebar-services.twig', [
	'services' => $context['services']
]);

return Timber::render(['page-about.twig'], $context);
