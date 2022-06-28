<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════



namespace Payamito\UltimateMember;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Payamito_OTP;

/**
 * ultimate member classe
 *
 * @package AbzarWp
 * @since   1.0.0
 */


use PUM_Functions;

defined('ABSPATH') || exit;

if (!class_exists("Ultimate_Member")) {

    class Ultimate_Member
    {


        private static $_instance = null;
        public $form = null;
        public $sended = false;
        public $message = "";
        /**
         * Main Plugin Instance.
         *
         * Ensures only one instance of Payamito_Um is loaded or can be loaded.
         *
         * @since 1.0
         * @static
         * @return Payamito_Um - Main instance.
         */
        public static function get_instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        public function __construct()
        {
            add_action("um_after_form_fields", [$this, "configuration_otp_system"], 11, 1);
            add_action('um_after_login_fields', [$this, 'add_login_otp'], 9999, 1);
            add_action('um_submit_form_errors_hook__registration', [$this, 'submit_form_registration_validation'], 999, 1);
            add_action('um_registration_complete', [$this, 'update_payamito_um_mobile_field'], 10, 2);
            add_action('wp_enqueue_scripts', [$this, 'scripts']);
            add_action('show_user_profile', [$this, 'extra_user_profile_fields']);
            add_action('edit_user_profile', [$this, 'extra_user_profile_fields']);
            add_action('personal_options_update', [$this, 'save_extra_user_profile_fields']);
            add_action('edit_user_profile_update', [$this, 'save_extra_user_profile_fields']);
        }
        function extra_user_profile_fields($user)
        {
            global $wpdb;
            $verifired_by_payamito = get_user_meta($user->ID, 'payamito_um_OTP', true);
            if ($verifired_by_payamito == false || $verifired_by_payamito = "") {
                return;
            }
            $sql = "SELECT `meta_value`,`meta_key`,`umeta_id`,`user_id` FROM {$wpdb->usermeta}  WHERE `meta_value` LIKE '9%' AND LENGTH(`meta_value`)=10;";
            $mobiles = $wpdb->get_results($sql);
            $umeta_ids = [];
            if (count($mobiles) == 0) {
                return;
            }
?>
            <h3><?php _e("Payamito ultimate member phone numbers ", "payamito-ultimate-member"); ?></h3>

            <table class="form-table">
                <?php foreach ($mobiles as $index => $mobile) {
                    if ($mobile->user_id == $user->ID) { ?>
                        <tr>
                            <th><label for="phone_number"><?php printf(esc_attr__("Phone number ", 'payamito-ultimate-member'), ($index + 1)); ?></label></th>
                            <td>
                                <?php if (current_user_can("edit_users")) { ?>
                                    <input type="text" name="<?php echo esc_attr("phone_number_" . $mobile->umeta_id) ?>" value="<?php echo esc_attr($mobile->meta_value); ?>" class="regular-text" /><br />
                                <?php } else { ?>
                                    <input type="text" name="<?php echo esc_attr("phone_number_" . $mobile->umeta_id) ?>" value="<?php echo esc_attr($mobile->meta_value); ?>" class="regular-text" /><br />
                                <?php } ?>
                                <?php array_push($umeta_ids, $mobile->umeta_id) ?>
                            </td>
                        </tr>
                        <?php ?>
                        <input type="hidden" name="umeta_id" value="<?php echo esc_attr(implode('-', $umeta_ids)); ?>" /><br />
            </table>
    <?php }
                }
            }
            function save_extra_user_profile_fields($user_id)
            {
                global $wpdb;

                if (empty($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
                    return;
                }

                if (!current_user_can('edit_user', $user_id)) {
                    return false;
                }
                $umeta_ids = explode('-', sanitize_text_field($_POST['umeta_id']));

                foreach ($umeta_ids as $id) {

                    $mobile = isset($_POST['phone_number_' . $id]) ? $_POST['phone_number_' . $id] : false;
                    if ($mobile == false) {
                        continue;
                    }
                    $mobile = PUM_Functions::remove_0_from_mobile($mobile);

                    $wpdb->update($wpdb->usermeta, ['meta_value' => $mobile], ['umeta_id' => $id], array('%s', '%d'));
                }
            }
            public  function scripts()
            {

                $otp_options = get_option('payamito_um_otp');
                if ($otp_options == false) {
                    return;
                }

                wp_enqueue_script('payamito-um-form', PAYAMITO_UM_URL . '/assets/js/form.js', array('jquery'), false, true);
                wp_enqueue_style('payamito-um-form', PAYAMITO_UM_URL . '/assets/css/form.css',[],false,'all');


                wp_localize_script('payamito-um-form', 'payamito_um_form', [
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'resend_time' => $otp_options['resend_time'],
                    'nonce' => wp_create_nonce('payamito_um'),
                    "OTP_Success" => __("Send OTP success", "payamito-ultimate-member"),
                    "OTP_Fail" => __("Send OTP failed", "payamito-ultimate-member"),
                    'Send' => __("Send request failed please contact with support team ", "payamito-ultimate-member"),
                    'OTP_Wrong' => __("OTP is wrong", "payamito-ultimate-member"),
                    'OTP_Correct' => __("OTP is wrong", "payamito-ultimate-member"),
                    'invalid' => __("Mobile number is incorrct", "payamito-ultimate-member"),
                    'error' => __("Error", "payamito-ultimate-member"),
                    'success' => __("Success", "payamito-ultimate-member"),
                    "warning" => __("Warning", "payamito-ultimate-member"),
                    'enter' => __('Enter OTP number ', 'payamito-ultimate-member'),
                    'second' => __('Second', 'payamito-ultimate-member'),
                    
                ]);
            }
            public function OTP_error()
            {

                wp_enqueue_style('payamito-um-otp-erorr', PAYAMITO_UM_URL . "/assets/css/otp-erorr.css");
            }

            public function submit_form_registration_validation($args)
            {
                if (isset(UM()->form()->errors)) {
                    if (is_array(UM()->form()->errors)) {
                        if (count(UM()->form()->errors) > 0) {
                            return;
                        }
                    }
                }

                global $payamito_um_options;
                $form_id = $args['form_id'];
                $options = $payamito_um_options[$form_id];

                if ($options['active'] == false) {
                    return;
                }
                $field = $options['mobile_field'];
                $mobile = sanitize_text_field($args[$field]);

                $OTP = isset($args['payamito_um_OTP']) ? sanitize_text_field($args['payamito_um_OTP']) : '';
                $country_code = isset($args['country']) ? sanitize_text_field($args['country']) : '';
                if (empty($country_code)) {

                    UM()->form()->add_error($field, __("Contry code is empty", "payamito-ultimate-member"));
                    return;
                }
                if (!PUM_Functions::verify_country($country_code, $form_id)) {
                    UM()->form()->add_error($field, __("Sorry, we are not able to log in or register in your country", "payamito-ultimate-member"));
                }

                if (empty(payamito_verify_moblie_number($mobile))) {
                    UM()->form()->add_error($field, __("Mobile number must be validate!", "payamito-ultimate-member"));
                    if (empty($this->message)) {

                        $this->message = esc_html__('Mobile number must be validate! ', 'payamito-ultimate-member');
                    }
                }
                global $wpdb;
                $mobile = PUM_Functions::remove_0_from_mobile($mobile);
                $sql = $wpdb->prepare("SELECT DISTINCT `user_id` FROM {$wpdb->usermeta} WHERE `meta_key`=%s AND `meta_value`=%s", $field, $mobile);
                $result = $wpdb->get_results($sql);
                if (count($result) != 0) {
                    wp_enqueue_style('payamito-um-app', PAYAMITO_UM_URL . '/assets/css/app.css');
                    UM()->form()->add_error($field, __("Mobile number already in use!", "payamito-ultimate-member"));
                    $this->sended = false;
                } else {
                    $this->first_send($options, $mobile);
                }
                if (empty($OTP)) {
                    UM()->form()->add_error("payamito_um_OTP", __('Unable to verify Mobile number', 'payamito-ultimate-member'));
                    if (empty($this->message)) {
                        $this->message = esc_html__('OTP is empty. Unable to verify Mobile number ', 'payamito-ultimate-member');
                    }
                    return;
                }
                $validate = Payamito_OTP::payamito_validation_session($mobile . "_OTP", $OTP);
                if ($validate === false) {
                    UM()->form()->add_error("payamito_um_OTP", __('Unable to verify Mobile number', 'payamito-ultimate-member'));
                    if (empty($this->message)) {

                        $this->message = esc_html__('OTP is incorrect. Unable to verify Mobile number ', 'payamito-ultimate-member');
                    }
                    return;
                } else {
                    wp_enqueue_style('payamito-um-app', PAYAMITO_UM_URL . '/assets/css/app.css');
                    return;
                }
            }

            public function update_payamito_um_mobile_field($user_id, $args)
            {
                global $payamito_um_options;

                $options = $payamito_um_options[$args['form_id']];
                $field = $options['mobile_field'];

                $mobile = PUM_Functions::remove_0_from_mobile(sanitize_text_field($args[$field]));
                $ts = update_user_meta($user_id, $field, $mobile);
                unset($_SESSION[$mobile . "_OTP"]);
                unset($_SESSION[$mobile . "T"]);
            }

            public function first_send($options, $mobile)
            {
                if (isset($_SESSION['first_send'])) {
                    $this->sended = true;
                    return false;
                }
                $message = payamito_um()->ajax->is_ready_send($options);
                $send = payamito_um()->ajax->start_send($message, $mobile);
                if ($send['result'] === true) {
                    $this->sended = true;

                    if (!is_null(payamito_um()->ajax->get_otp())) {
                        Payamito_OTP::payamito_set_session($mobile . "_OTP", payamito_um()->ajax->get_otp());
                    }
                    $_SESSION['first_send'] = true;
                    $this->message = esc_html__('We sent OTP code to verify your phone number', 'payamito-ultimate-member');
                } else {
                    $this->message = esc_html($send['message']);
                }
            }
            public function configuration_otp_system($form)
            {
                global $payamito_um_options;
                $form_id = $form['form_id'];
                
                $options = isset($payamito_um_options[$form_id]) ? $payamito_um_options[$form_id] : false;
                if(!is_array( $options) || $options==false ){
                    return ;
                }
                $allowed_countries =is_array($options['allowed_countries'])? array_map('strtolower', $options['allowed_countries']):[];
                if ($options === false) {
                    return;
                }
                if (!isset($options['active']) || $options['active'] == false) {
                    return;
                }
                $otp_options = get_option('payamito_um_otp');
                if (count($_POST) == 0) {

                    $_SESSION['pum_first_send'] = true;

                    wp_enqueue_style('payamito-um-app', PAYAMITO_UM_URL . '/assets/css/app.css');
                    if (isset($_SESSION['first_send'])) {

                        unset($_SESSION['first_send']);
                    }
                }
                wp_enqueue_script('payamito-um-countries-js', PAYAMITO_UM_URL . '/assets/js/countries.js', array('jquery'), false, true);
                wp_enqueue_style('payamito-um-countries-css', PAYAMITO_UM_URL . '/assets/css/countries.css');
                wp_localize_script('payamito-um-countries-js', 'payamito_countries', [
                    'allowed_countries' =>  $allowed_countries,
                    'default_country'=>strtolower($options['default_country']) 
                ]);

    ?>
    <input type="hidden" id="payamito_um_otp_field" value="<?php echo esc_attr($options['mobile_field'] . '-' . $form_id)  ?>" />
    <div id="payamito_um_otp_container" style="<?php ($form['mode'] == 'login' || count($_POST) == 0) ?  esc_attr_e("display:none") : esc_attr_e('display:block'); ?> ">
        <?php if ($form['mode'] == 'login' || $this->sended === true) { ?>
            <div id="<?php echo  esc_html("um_field" . $form["form_id"] . "payamito_um_OTP")  ?>" class="um-field_payamito_um_OTP um-field-number um-field-type_number " data-key="payamito_um_otp">
                <div class="um-field-label"><label for="payamito_um_OTP"><?php echo esc_html($otp_options['title']) ?> </label>
                    <div class="um-clear"></div>
                </div>
                <div class="um-field-area">
                    <input type="text" autocomplete="off" class="um-form-field " name="payamito_um_OTP" id="payamito_um_OTP" placeholder="<?php echo esc_html($otp_options['placeholder']) ?>" data-validate="OTP_validation" data-key="OTP" value="">
                    <?php if (!empty($this->message)) { ?>
                        <div class="um-field-error payamito-um-dispaly-none"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span><?php esc_html($this->message);  ?></div>
                    <?php } ?>
                    <button type="button" style="margin-top: 2px; padding: 11px !important ;background-color: #3ba1da;color: white;width: 25%;" id="payamito_um_send_otp" class="um-button  payamito_um_send_otp"><?php esc_html_e("Resend OTP", "payamito-ultimate-member") ?></button>
                </div>
            </div>
        <?php } ?>
    </div>
<?php
                $this->form = $form;
            }
            public function add_login_otp($form)
            {
                global $payamito_um_options;

                $form_id = $form['form_id'];

                $options = $payamito_um_options[$form_id];

                if (!isset($options['active']) || $options['active'] == false) {
                    return;
                }
                if ($form['mode'] !== 'login') {
                    return;
                }
                $otp_options = get_option('payamito_um_otp');

?>
    <div class="">
        <br />
        <input class="um-button payamito-um-validate-otp " id="payamito_um_login_btn" style="<?php echo esc_attr(sprintf("color:%s;background-color:%s", $otp_options['validate_color'], $otp_options['validate_bg_color']))  ?>" value="<?php esc_attr_e('Login With OTP', 'payamito-ultimate-member'); ?>" type="button" />
    </div>

<?php
            }
        }
    }
?>
