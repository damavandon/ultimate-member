<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════



/**
 * PayamitoUltimateMember setup
 *
 * @package Payamito
 * @since   1.0.0
 */

// don't call the file directly
if (!defined('ABSPATH')) {

	die('direct access abort ');
}

final class PayamitoUltimateMember
{

	/**
	 * PayamitoUltimateMember version.
	 *
	 * @var string
	 */
	public $version = '1.2.0';

	/**
	 * The single instance of the class.
	 *
	 * @var PayamitoUltimateMember
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Form instance.
	 *
	 * @var object
	 */
	public $form;

	/**
	 * Send instance.
	 *
	 * @var object
	 */
	public $send;

	/**
	 * Ajax instance.
	 *
	 * @var object
	 */
	public $ajax;


	/**
	 * Submit instance.
	 *
	 * @var object
	 */
	public $submit;

	/**
	 * Plugin slag.
	 *
	 * @var string
	 */
	public static $slug = 'payamito_um';

	public $core_version='1.1.3';

	/**
	 * Main PayamitoUltimateMember Instance.
	 *
	 * Ensures only one instance of PayamitoUltimateMember is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see payamito_um()
	 * @return PayamitoUltimateMember - Main instance.
	 */
	public static function get_instance()
	{
		if (is_null(self::$_instance)) {

			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone()
	{
		wc_doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'payamito-ultimate-member'), '1.0.0');
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup()
	{
		wc_doing_it_wrong(__FUNCTION__, __('Unserializing instances of this class is forbidden.', 'payamito-ultimate-member'), '1.0.0');
	}

	public function __construct()
	{
		$this->includes();

		add_action('plugins_loaded', [$this, 'required'], 99);
		$this->init_hooks();

		$this->ajax = Payamito\UltimateMember\Ajax::get_instance();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes()
	{
		require_once PAYAMITO_UM_DIR . '/inc/functions.php';
		require_once PAYAMITO_UM_DIR . '/inc/class-updater.php';
		require_once PAYAMITO_UM_DIR . '/inc/class-plugins-required.php';
		require_once PAYAMITO_UM_DIR . '/inc/class-functions.php';
		require_once PAYAMITO_UM_DIR . '/inc/class-send.php';
		require_once PAYAMITO_UM_DIR . '/inc/admin/class-settings.php';
		require_once PAYAMITO_UM_DIR . '/inc/class-ultimate-member.php';
		require_once PAYAMITO_UM_DIR . '/inc/class-ajax.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */

	public function init_hooks()
	{
		$this->init();
		register_activation_hook(PAYAMITO_UM_PLUGIN_FILE, array('Payamito\UltimateMember\Install', 'install'));
	}

	public function init()
	{
		$this->load_core();

		Payamito\UltimateMember\Settings::get_instance();

		$this->um = Payamito\UltimateMember\Ultimate_Member::get_instance();

		$this->get_options();
	}

	public function load_core()
	{
			require_once payamito_um_load_core().'/payamito.php';
			run_payamito();
	}

	public function required()
	{
		if (!class_exists('TGM_Plugin_Activation')) {

			require_once PAYAMITO_UM_DIR . '/inc/lib/class-tgm-plugin-activation.php';
		}
		new Payamito\UltimateMember\Required();
	}
	private function get_options()
	{

		global $payamito_um_options;

		$payamito_um_options = get_option('payamito_um');
	}
}
