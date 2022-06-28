<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════


namespace Payamito\UltimateMember;

use PUM_Functions;

/**
 * Register an options panel.
 *
 * @package Payamito
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class  Settings
{
	/**
	 * Holds the options panel controller.
	 *
	 * @var object
	 */
	protected $panel;
	protected static $_instance = null;
	public $forms;

	/**
	 * Get things started.
	 */

	public static function get_instance()
	{
		if (is_null(self::$_instance)) {

			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __Construct()
	{
		add_filter('payamito_add_section', [$this, 'register_settings'], 1);
		add_action('kianfr_' . 'payamito' . '_save_before', [$this, 'option_save'], 10, 1);
	}

	public function option_save($options)
	{
		$options_save = [];
		foreach ($options['payamito_um'] as $key => $option) {

			$key = str_replace("_form", "", $key);
			$options_save[$key]['active'] = $option['active'] == '1' ? true : false;
			$options_save[$key]['mobile_field'] = $option['mobile_field'];
			$options_save[$key]['meta_key'] = $option['meta_key'];
			$options_save[$key]['default_country'] = $option['default_country'];
			$options_save[$key]['allowed_countries'] = $option['allowed_countries'];
			$options_save[$key]['pattern_active'] = $option['pattern_active'] == '1' ? true : false;
			$options_save[$key]['pattern_id'] = $option['pattern_id'];
			$options_save[$key]['pattern'] = $option['pattern_repeater'];
		}

		update_option('payamito_um', $options_save);

		$options_save = [];
		$options_save['resend_time'] = $options['payamito_um_otp']['again_send_time'];
		$options_save['text']  = $options['payamito_um_otp']['otp_sms'];
		$options_save['count'] = $options['payamito_um_otp']['number_of_code'];
		$options_save['title'] = $options['payamito_um_otp']['otp_title'];
		$options_save['placeholder'] = $options['payamito_um_otp']['otp_placeholder'];
		$options_save['validate_bg_color'] = empty($options['payamito_um_otp']['validate_bg_color']) ? "#ffffff" : $options['payamito_um_otp']['validate_bg_color'];
		$options_save['validate_color'] = empty($options['payamito_um_otp']['validate_color']) ? "#000000" : $options['payamito_um_otp']['validate_color'];

		update_option('payamito_um_otp', $options_save);
	}
	public function register_settings($section)
	{

		$this->form = PUM_Functions::get_forms();
		$this->countries = PUM_Functions::get_countries();
		if (!class_exists('UM')) {
			$settings = [
				'title'  => esc_html__('Ultimate Member', 'payamito-ultimate-member'),
				'fields' => [
					array(
						'type'    => 'heading',
						'content' => esc_html__('There is no active form. Create at least one form', 'payamito-ultimate-member'),
					),
				],
			];
		} else {
			$settings = array(
				'title'  => esc_html__('Ultimate Member', 'payamito-ultimate-member'),
				'fields' => array(
					array(
						'id'            => 'payamito_um_otp',
						'type'          => 'accordion',
						'title'   => esc_html__('Validation ', 'payamito-ultimate-member'),
						'accordions'    => array(
							array(
								'title'   => esc_html__('Validation ', 'payamito-ultimate-member'),
								'fields'    => array(
									array(
										'id'   => 'number_of_code',
										'title' => esc_html__('Number of OTP code', 'payamito-ultimate-member'),
										'desc' => esc_html__('Number of OTP code that you want send for user', 'payamito-ultimate-member'),
										'type' => 'select',

										'options' => apply_filters("again_send_number", array(
											"4" => "4",
											"5" => "5",
											"6" => "6",
											"7" => "7",
											"8" => "8",
											"9" => "9",
											"10" => "10",
										)),
									),
									array(
										'id'   => 'again_send_time',
										'title' => esc_html__('Send Again', 'payamito-ultimate-member'),
										'desc' => esc_html__('When you want the user to re-request OTP.', 'payamito-ultimate-member'),
										'type' => 'select',

										'options' => apply_filters("again_send_time", array(
											"30" => "30",
											"60" => "60",
											"90" => "90",
											"120" => "120",
											"300" => "300",
										)),
									),
									array(
										'id'    => 'otp_title',
										'type'  => 'text',
										'title' => esc_html__('OTP field title', 'payamito-ultimate-member'),
										'default' => 'OTP',

									),
									array(
										'id'    => "otp_placeholder",
										'type'  => 'text',
										'title' => esc_html__('OTP field Placeholder', 'payamito-ultimate-member'),
										'default' => 'OTP',

									),
									array(
										'id'    => 'validate_bg_color',
										'type'  => 'color',
										'title' => esc_html__('Validate backgrund color', 'payamito-ultimate-member'),
										"default" => "#000000"
									),
									array(
										'id'    => 'validate_color',
										'type'  => 'color',
										'title' => esc_html__('Validate color', 'payamito-ultimate-member'),
										"default" => "#ffffff"
									),
								)
							),
						)
					),

					array(
						'id'            => 'payamito_um',
						'type'          => 'tabbed',
						'title'  => esc_html__('Message', 'payamito-ultimate-member'),
						'tabs'      => $this->tabs(),
					),
				)
			);
		}


		array_push($section, $settings);

		return $section;
	}

	public function tabs()
	{
		$tabs = [];
		array_push($tabs, $this->register_tab());

		array_push($tabs, $this->login_tab());

		return apply_filters('payamito_um_tabs', $tabs);
	}

	public function register_tab()
	{
		$register_tab = array(
			'title'     => esc_html__('Register', 'payamito-ultimate-member'),
			'fields'    => array(
				array(
					'type'    => 'heading',
					'content' => esc_html__('Register Form', 'payamito-ultimate-member'),
				),
			),
		);
		$forms = PUM_Functions::get_forms();
		if (is_array($forms) && count($forms) != 0) {
			foreach ($forms as $form) {
				if ($form['mode'] == 'register') {
					array_push($register_tab['fields'], $this->set_form_field('register', $form));
				}
			}
		}


		return apply_filters('payamito_um_register_tab', $register_tab);
	}

	public function login_tab()
	{
		$login_tab = array(
			'title'     => esc_html__('Login', 'payamito-ultimate-member'),
			'fields'    => array(
				array(
					'type'    => 'heading',
					'content' => esc_html__('Login Forms', 'payamito-ultimate-member'),
				),
			),
		);
		$forms = PUM_Functions::get_forms();
		if (is_array($forms) && count($forms) != 0) {
			foreach ($forms as $form) {
				if ($form['mode'] == 'login') {
					array_push($login_tab['fields'], $this->set_form_field('login', $form));
				}
			}
		}

		return apply_filters('payamito_um_login_tab', $login_tab);
	}


	public function get_tags()
	{
		$tags = [
			"OTP" => esc_html__("OTP", 'payamito-ultimate-member'),
			"site_name" => esc_html__("Site name", 'payamito-ultimate-member'),
		];
		ksort($tags, SORT_STRING);
		return $tags;
	}

	/**
	 * print tags for modal
	 *
	 */

	public  function option_set_pattern()
	{
		return array(
			'id'     => 'pattern_repeater',
			'type'   => 'repeater',
			'title'      => payamito_dynamic_text('pattern_Variable_title'),
			'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
			'help'       => payamito_dynamic_text('pattern_Variable_help'),
			'class' => "awesome-support-payamito-repeater pattern_background",
			'dependency' => array("pattern_active", '==', 'true'),
			'fields' => array(
				array(
					'id'          => 0,
					'type'        => 'select',
					'placeholder' =>  esc_html__("Select tag", "payamito-ultimate-member"),
					'class' => 'pattern_background',
					'options'     => $this->get_tags(),
				),
				array(
					'id'    => 1,
					'type'  => 'number',
					'placeholder' =>  esc_html__("Your tag", "payamito-ultimate-member"),
					'class' => 'pattern_background',
					'default' => '0',
				),
			)
		);
	}
	public function option_get_admin_phone_number()
	{
		return array(
			'id'     => 'admin_phone_number_repeater',
			'type'   => 'repeater',
			'title' => esc_html__("phone number", "payamito-ultimate-member"),
			'max' => '20',

			'dependency' => array("register_active", '==', 'true'),
			'fields' => array(
				array(
					'id'    => 'admin_phone_number',
					'type'  => 'text',
					'placeholder' => esc_html__("Phone number ", "payamito-ultimate-member"),
					'class' => 'awesome-support-payamito-phone-number ',
					'attributes'  => array(
						'type'      => 'tel',
						'maxlength' => 11,
						'minlength' => 11,
						"pattern" => "[0-9]{3}-[0-9]{3}-[0-9]{4}"
					),
				),
			),
		);
	}

	public function set_form_field($form_type, $form)
	{

		if (!is_numeric($form['ID'])) {

			return [];
		}

		return	array(
			'id'            => $form['ID'] . '_form',
			'type'          => 'accordion',
			'accordions'    => array(
				array(
					'title'     => esc_html__(ucfirst($form['title']), 'payamito-ultimate-member'),
					'fields'    => array(
						array(
							'id'   => 'active',
							'title' => esc_html__('Active', 'payamito-ultimate-member'),
							'desc' => esc_html__('Are you want send sms to admin ', 'payamito-ultimate-member'),
							'type' => 'switcher',
						),
						array(
							'id'          => 'mobile_field',
							'type'        => 'select',
							'title' => esc_html__('Mobile number ', 'payamito-ultimate-member'),
							'options'     => $form['fields'],
							'chosen' => true,
							'default' => isset($form['fields']['phone_number']) ? $form['fields']['phone_number'] : "",

						),
						$this->mobile_or_metakey($form, $form_type),
						array(
							'id'   => 'default_country',
							'title' => __('Default Country ', 'payamito-ultimate-member'),
							'type' => 'select',
							'options' => $this->countries,
							'default' => "iran",
							'chosen' => true,
						),
						array(
							'id'       => 'allowed_countries',
							'title'     => __('Allowed countries', 'payamito-ultimate-member'),
							'type'     => 'select',
							'desc'   =>  __('Select one or more user roles', 'payamito-ultimate-member'),
							'options'  => $this->countries,
							'chosen' => true,
							'multiple' => true,
						),
						array(
					        'type'       => 'notice',
					        'style'      => 'warning',
					        'content'    => esc_html__( '"notice" send pattern need to help', 'payamito-ultimate-member' ),
					        'class' => 'pattern_background',
				        ),
						array(
							'id'    =>  "pattern_active",
							'type'  => 'switcher',
							'title'      => payamito_dynamic_text('pattern_active_title'),
							'desc'       => payamito_dynamic_text('pattern_active_desc'),
							'help'       => payamito_dynamic_text('pattern_active_help'),
							'class' => 'pattern_background',
						),
						array(
							'id'   => 	"pattern_id",
							'type'    => 'text',
							'title'      => payamito_dynamic_text('pattern_ID_title'),
							'desc'       => payamito_dynamic_text('pattern_ID_desc'),
							'help'       => payamito_dynamic_text('pattern_ID_help'),
							'class' => 'pattern_background',
							'dependency' => array("pattern_active", '==', 'true'),
						),
						$this->option_set_pattern(),
						array(
							'id'   =>  "text",
							'title'      => payamito_dynamic_text('send_content_title'),
							'desc'       => payamito_dynamic_text('send_content_desc'),
							'help'       => payamito_dynamic_text('send_content_help'),
							'default' => esc_html__('کاربر گرامی کد تایید ثبت نام {OTP} می باشد. ', 'payamito-ultimate-member'),
							'class' => 'pattern_background',
							'type' => 'textarea',
							'dependency' => array("pattern_active", '!=', 'true'),
						),

					)
				),
			)
		);
	}
	public function mobile_or_metakey($form, $form_type)
	{
		if ($form_type == 'login') {
			$meta_key = array(
				'id'          => 'meta_key',
				'type'        => 'select',
				'title' => esc_html__('Meta Keys ', 'payamito-ultimate-member'),
				'options' => PUM_Functions::get_meta_keys(),
				'chosen' => true,

			);
			return $meta_key;
		}
		return array(
			'type'    => 'submessage',
			'style'   => 'normal',
			'content' => '',
		);
	}
}
