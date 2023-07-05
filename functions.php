<?php
/**
 * Atlant theme (using Timber)
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$composer_autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($composer_autoload)) {
	require_once $composer_autoload;

	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if (! class_exists('Timber')) {
	add_action(
		'admin_notices',
		fn() => print '<div class="error"><p>Timber не включен или не установлен. Активируйте плагин тут <a href="'
		        . esc_url(admin_url('plugins.php#timber'))
		        . '">' . esc_url(admin_url('plugins.php'))
		        . '</a></p></div>'
	);

	add_filter(
		'template_include',
		function ($template) {
			return get_template_directory() . '/static/no-timber.html';
		}
	);

	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = ['templates', 'views', 'views/pages'];

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class AtlantTheme extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action('after_setup_theme', [$this, 'theme_supports']);
		add_filter('timber/context', [$this, 'add_to_context']);
		add_filter('timber/twig', [$this, 'add_to_twig']);
		add_action('init', [$this, 'register_post_types']);
		add_action('init', [$this, 'register_taxonomies']);
		add_action('init', [$this, 'register_theme_options']);

		// Добавляем шаблоны на основе призаков (slug, type и т.д.)
		add_filter('template_include', [$this, 'matchTemplates']);

		// Обработчик формы обратной связи
		add_action('wp_ajax_discount_form', [$this, 'discountForm']);
		add_action('wp_ajax_nopriv_discount_form', [$this, 'discountForm']);

		// Обработчик формы в модалке
		add_action('wp_ajax_modal_form', [$this, 'modalForm']);
		add_action('wp_ajax_nopriv_modal_form', [$this, 'modalForm']);

		add_action('wp_enqueue_scripts', [$this, 'addDiscountFormScript']);
		add_action('wp_enqueue_scripts', [$this, 'addModalForm']);

		// Конфигурация PhpMailer для отправки почты
		add_action('phpmailer_init', [$this, 'phpMailerConfig']);

		parent::__construct();
	}

	function matchTemplates($template): string
	{
		if (is_page('index')) {
			return get_template_directory() . '/templates/pages/index.php';
		}

		if (is_page('gallery')) {
			return get_template_directory() . '/templates/pages/gallery.php';
		}

		if (is_page('about')) {
			return get_template_directory() . '/templates/pages/about.php';
		}

		// Шаблон для записей типа service
		global $post;
		if (isset($post->post_type) && $post->post_type === 'service'){
			return get_template_directory() . '/templates/service.php';
		}

		return $template;
	}

	function phpMailerConfig(PHPMailer\PHPMailer\PHPMailer $mailer): void
	{
		$mailer->isSMTP();
		$mailer->CharSet = "UTF-8";
		$mailer->SMTPAuth = true;
		$mailer->Host = 'smtp.yandex.ru';
		$mailer->Username = 'login';
		$mailer->Password = 'password';
		$mailer->Port = 465;
		$mailer->SMTPSecure = 'ssl';
		$mailer->setFrom('login@yandex.ru', 'Имя отправителя');
	}

	function addDiscountFormScript(): void
	{
		wp_enqueue_script(
			'discount-form',
			get_template_directory_uri() . '/assets/js/discount-form.js',
			['jquery'],
			false,
			true
		);

		wp_localize_script(
			'discount-form',
			'ajax',
			['ajaxurl' => admin_url('admin-ajax.php')]
		);
	}

	function addModalForm(): void
	{
		wp_enqueue_script(
			'modal-form',
			get_template_directory_uri() . '/assets/js/modal-form.js',
			in_footer: true
		);

		wp_enqueue_style(
			'modal-form',
			get_template_directory_uri() . '/assets/css/modal-form.css',
		);
	}

	// Обработчик формы на получение скидки
	function discountForm()
	{
		$errors = [];

		// Проверяем наличие нонса (CSRF token)
		if (! isset($_POST['_discount_form_nonce'])
		    || ! wp_verify_nonce($_POST['_discount_form_nonce'], 'discount_form_action')
		) {
			wp_send_json_error('Ошибка отправки формы!');
			wp_die();
		}

		// Получаем данные
		$name = $_POST['name'] ?? '';
		$phone = $_POST['phone'] ?? '';
		$agree = $_POST['agree'] ?? '';

		// Валидация данных
		if (empty($name) || empty($phone)) {
			wp_send_json_error('Пожалуйста заполните все поля!');
			wp_die();
		}

		if (empty($agree)) {
			wp_send_json_error('Примите политику конфиденциальности перед отправкой формы!');
			wp_die();
		}

		if (! preg_match("/^(([a-zA-Z' -]{2,30})|([а-яА-ЯЁё' -]{2,30}))$/u", $name)) {
			$errors['name'] = 'Введите корректное имя!';
		}

		$cleanedPhone = preg_replace('/[^[:digit:]]/', '', $phone);
		if (! preg_match('/^[0-9]{11}+$/', $cleanedPhone)) {
			$errors['phone'] = 'Неверный формат телефона!';
		}

		if (count($errors) > 0) {
			wp_send_json_error($errors, 400);
			wp_die();
		}

		// Отправляем письмо
		$headers   = [];
		$to        = 'i@twent.ru';
		$subject   = 'Новая заявка на получение скидки';
		$message   = "Имя: $name\nТелефон: $phone";
		$sent      = wp_mail($to, $subject, $message, $headers);

		// Было ли письмо успешно отправлено
		if ($sent) {
			wp_send_json_success('Заявка успешно отправлена.');
			wp_reset_query();
			wp_reset_postdata();
		} else {
			wp_send_json_error('Ошибка отправки сообщения. Попробуйте позже.', 400);
		}

		wp_die();
	}

	// Обработчик формы на получение скидки
	function modalForm()
	{
		$errors = [];

		// Проверяем наличие нонса (CSRF token)
		if (! isset($_POST['_modal_form_nonce'])
		    || ! wp_verify_nonce($_POST['_modal_form_nonce'], 'modal_form_action')
		) {
			wp_send_json_error('Ошибка отправки формы!');
			wp_die();
		}

		// Получаем данные
		$name = $_POST['name'] ?? '';
		$phone = $_POST['phone'] ?? '';
		$agree = $_POST['agree'] ?? '';

		// Валидация данных
		if (empty($name) || empty($phone)) {
			wp_send_json_error('Пожалуйста заполните все поля!');
			wp_die();
		}

		if (empty($agree)) {
			wp_send_json_error('Примите политику конфиденциальности перед отправкой формы!');
			wp_die();
		}

		if (! preg_match("/^(([a-zA-Z' -]{2,30})|([а-яА-ЯЁё' -]{2,30}))$/u", $name)) {
			$errors['name'] = 'Введите корректное имя!';
		}

		$cleanedPhone = preg_replace('/[^[:digit:]]/', '', $phone);
		if (! preg_match('/^[0-9]{11}+$/', $cleanedPhone)) {
			$errors['phone'] = 'Неверный формат телефона!';
		}

		if (count($errors) > 0) {
			wp_send_json_error($errors, 400);
			wp_die();
		}

		// Отправляем письмо
		$headers   = [];
		$to        = 'i@twent.ru';
		$subject   = 'Новый запрос обратного звонка сайте';
		$message   = "Имя: $name\nТелефон: $phone";
		$sent      = wp_mail($to, $subject, $message, $headers);

		// Было ли письмо успешно отправлено
		if ($sent) {
			wp_send_json_success('Заявка успешно отправлена.');
			wp_reset_query();
			wp_reset_postdata();
		} else {
			wp_send_json_error('Ошибка отправки сообщения. Попробуйте позже.', 400);
		}

		wp_die();
	}

	// Функция форматирования денежных значений
	public function roubles($number): string
	{
		$formatted = number_format($number, 0, ',', ' ');
		return "{$formatted} ₽";
	}

	/** This is where you can register custom post types. */
	public function register_post_types()
	{
	}

	/** This is where you can register custom taxonomies. */
	public function register_taxonomies()
	{
	}

	public function register_theme_options(): void
	{
		if (function_exists('acf_add_options_page')) {
			acf_add_options_page([
				'page_title' => 'Главные настройки сайта',
				'menu_title' => 'Настройки сайта',
				'menu_slug'  => 'theme-general-settings',
//				'capability' => 'edit_posts',
				'redirect'   => false
			]);

//			acf_add_options_sub_page([
//				'page_title'    => 'Дополнительные настройки сайта',
//				'menu_title'    => 'Дополнительные настройки',
//				'parent_slug'   => 'theme-general-settings',
//			]);
		};
	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['menu'] = new Timber\Menu();
		// Опции сайта
		$context['options'] = get_fields('options');
		$context['site'] = $this;

		// Услуги для мобильного меню и главной страницы
		$context['services'] = Timber::get_posts([
			'post_type' => 'service',
			'orderby' => 'menu_order',
			'order' => 'asc',
			'numberposts' => -1,
			//'select' => 'ID,post_name,menu_order',
		]);

		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links');

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag');

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support('post-thumbnails');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			[
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			]
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			[
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			]
		);

		add_theme_support( 'menus');
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig($twig) {
		$twig->addExtension(new Twig\Extension\StringLoaderExtension());

		// Регистрация функции для форматирования денежных значений
		$twig->addFunction(new Timber\Twig_Function(
			'roubles',
			[$this, 'roubles']
		));

		return $twig;
	}

}

new AtlantTheme();
