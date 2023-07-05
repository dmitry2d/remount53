<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package atlant-theme
 * @subpackage Atlant
 * @since twent Atlant Theme 1.0
 */
$timberContext = $GLOBALS['timberContext']; // @codingStandardsIgnoreFile
if (! isset($timberContext)) {
	throw new \Exception('Timber context not set in footer.');
}

$timberContext['content'] = ob_get_contents();

ob_end_clean();

$templates = ['page-plugin.twig'];

Timber::render($templates, $timberContext);
