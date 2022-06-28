<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════



namespace Payamito\UltimateMember;

use Payamito_OTP;
use PUM_Functions;
use PUM_Send;

if (!class_exists("Ajax")) {
    class Ajax
    {
        protected static $_instance = null;
        public $otp_count = 4;
        private $OTP = null;
        public $send = null;

        public static function get_instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        public function __construct()
        {
            add_action('wp_ajax_nopriv_payamito_um_validation', [$this, 'ajax']);
            add_action('wp_ajax_payamito_um_validation', [$this, 'ajax']);
        }

        public  function ajax()
        {
            $mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? sanitize_text_field($_REQUEST['mode']) : false;
            $form_id = isset($_REQUEST['form_id'])   && !empty($_REQUEST['form_id']) ? sanitize_text_field($_REQUEST['form_id']) : false;
            $field_value = isset($_REQUEST['field']) && !empty($_REQUEST['field']) ? sanitize_text_field($_REQUEST['field']) : false;
            $country_code = isset($_REQUEST['country'])  && !empty($_REQUEST['country']) ? sanitize_text_field($_REQUEST['country']) : false;
            $field_value = PUM_Functions::remove_0_from_mobile($field_value);
            if ($form_id === false) {
                die;
            }
            if ($country_code === false) {

                return $this->ajax_response(-1, self::message(6));
            }
            if (!payamito_verify_moblie_number($field_value)) {

                return $this->ajax_response(-1, self::message(0));
            }
            if (!PUM_Functions::verify_country($country_code, $form_id)) {

                return $this->ajax_response(-1, self::message(7));
            }

            if ($mode === false) {
                die;
            }

            if (isset($_SESSION['pum_first_send'])) {
                $mode = "send_otp";
            }
            switch ($mode) {
                case 'validation':
                    $this->validation($form_id, $field_value, $country_code);
                    break;
                case 'send_otp':
                    $this->send_otp($form_id, $field_value, $country_code);
                    break;
            }
        }

        public  function validation($form_id, $field_value, $country_code)
        {
            global $payamito_um_options, $wpdb;

            $user = null;
            $options = $payamito_um_options[$form_id];
            $meta_key = $options['meta_key'];
            $sql = $wpdb->prepare("SELECT DISTINCT `user_id`,`meta_key` FROM `wp_usermeta` WHERE `meta_value`=%s AND `meta_key`=%s", $field_value, $meta_key);
            $result = $wpdb->get_results($sql);

            if (count($result) == 0) {

                return $this->ajax_response(-1, self::message(8));
            }
            $OTP = isset($_REQUEST['otp'])   && !empty($_REQUEST['otp']) ? sanitize_text_field($_REQUEST['otp']) : false;
            if ($OTP === false) {

                return $this->ajax_response(-1, self::message(5));
            }
            $meta_key = $payamito_um_options[$form_id]['meta_key'];

            if (username_exists($field_value)) {
                $user = get_user_by('login', $field_value);
            }
            if (email_exists($field_value)) {
                $user = get_user_by('email', $field_value);
            } else {
                global $wpdb;
                $sql = $wpdb->prepare("SELECT DISTINCT `user_id` FROM {$wpdb->usermeta} WHERE `meta_key`=%s AND `meta_value`=%s", $meta_key, $field_value);
                $result = $wpdb->get_results($sql);
                $user_id = null;
                if (count($result) != 0) {
                    $user_id = $result[0]->user_id;
                    $validate = Payamito_OTP::payamito_validation_session($field_value . "_OTP", $OTP);
                    if ($validate === false) {

                        return $this->ajax_response(-1, self::message(5));
                    }
                    $after_login = get_post_meta($form_id, "_um_login_after_login", true);
                    $login_redirect_url = get_post_meta($form_id, "_um_login_redirect_url", true);
                    $redirect = "";
                    switch ($after_login) {

                        case 'redirect_admin':
                            $redirect = wp_redirect(admin_url());
                            break;
                        case 'redirect_url':
                            $redirect = $login_redirect_url;
                            break;
                        case 'refresh':
                            $redirect = UM()->permalinks()->get_current_url();
                            break;
                        case 'redirect_profile':
                        default:
                            $user = get_userdata($user_id);
                            $user = $user->data->user_login;
                            $redirect = esc_url((get_home_url() . '/wp-admin?um_user=' . $user));
                    }
                    $user = get_user_by("id", $user_id);
                    wp_set_auth_cookie($user_id);
                    wp_set_current_user($user->ID, $user->data->user_login);
                    update_user_caches($user);
                    unset($_SESSION[$field_value . "_OTP"]);
                    unset($_SESSION[$field_value . "T"]);
                    return $this->ajax_response(1, self::message(9), $redirect);
                }
            }
        }
        public  function send_otp($form_id, $field_value, $country_code)
        {
            global $payamito_um_options;

            $options = $payamito_um_options[$form_id];

            $message = $this->is_ready_send($options);
            if ($message == false) {

                return $this->ajax_response(-1, self::message(10));
            }
            $otp_options = get_option('payamito_um_otp');

            $resend_time = PUM_Functions::resent_time_check($field_value, $otp_options['resend_time']);
            if ($resend_time !== true) {

                return  $this->ajax_response(-1, sprintf(__("Please wait %s seconds", ' payamito-ultimate-member'), $resend_time));
            }
            $this->otp_count = $otp_options['count'];

            $send = $this->start_send($message, $field_value);

            if ($send['result'] == true) {

                if (!is_null($this->OTP)) {

                    Payamito_OTP::payamito_set_session($field_value . "_OTP", $this->OTP);
                }
                if (isset($_SESSION['pum_first_send'])) {

                    unset($_SESSION['pum_first_send']);

                    return $this->ajax_response(1, self::message(1), false);
                } else {
                    return $this->ajax_response(1, self::message(1));
                }
            } else {

                return $this->ajax_response(-1, $send['message']);
            }
        }
        public function start_send($message, $phone_number)
        {

            if (is_null($this->send)) {

                $this->send = PUM_Send::get_instance();
            }
            $result = [];
            if (!isset($message['type'])) {
                $result['message'] = __("Config error please check settings", 'payamito-ultimate-member');
                $result['result'] = false;
                return $result;
            }


            switch ($message['type']) {

                case 1:
                    $send_pattern = $this->set_pattern($message['message']);

                    $result = $this->send->Send_pattern($phone_number, $send_pattern, $message['pattern_id']);
                    break;

                case 2:

                    $result =   $this->send->Send($phone_number, $message['message']);
            }

            return $result;
        }
        public  function set_pattern($pattern)
        {
            $send_pattern = [];
            foreach ($pattern as $index => $item) {

                $send_pattern[$item[1]] = $this->get_tag_value($item[0]);
            }
            return $send_pattern;
        }
        /**
         * ajax response
         *The response to the OTP request is given in Ajax
         * @access public
         * @since 1.0.0
         * @static
         */
        public  function  ajax_response(int $type = -1, $message, $redirect = null)
        {
            wp_send_json(array('e' => $type, 'message' => $message, "re" => $redirect));
            die;
        }
        /**
         * ajax response message
         *
         * @access public
         * @since 1.0.0
         * @return array
         * @static
         */
        public static function message($key)
        {
            $messages = array(
                __('Mobile number is incorrect', ' payamito-ultimate-member'),
                __('OTP sent successfully', ' payamito-ultimate-member'),
                __('Failed to send OTP ', ' payamito-ultimate-member'),
                __('An unexpected error occurred. Please contact support ', ' payamito-ultimate-member'),
                __('Enter OTP number ', ' payamito-ultimate-member'),
                __(' OTP is Incorrect ', ' payamito-ultimate-member'),
                __('Contry code is empty', ' payamito-ultimate-member'),
                __('Sorry, we are not able to log in or register in your country', ' payamito-ultimate-member'),
                __('No user found', ' payamito-ultimate-member'),
                __('Login successfull', ' payamito-ultimate-member'),
                __("The message could not be sent due to incorrect settings. Please contact support", "payamito-ultimate-member")
            );
            return $messages[$key];
        }

        public  function is_ready_send($option)
        {
            $message = $this->set_message($option);

            if (is_null($message)) {

                return false;
            }

            return $message;
        }

        public  function set_message($option)
        {

            if ($option['pattern_active'] === true) {

                $pattern = $option['pattern'];

                $pattern_id = trim($option["pattern_id"]);

                if (is_array($pattern) && count($pattern) > 0 && is_numeric($pattern_id)) {

                    return array('type' => 1, 'message' => $pattern, 'pattern_id' => $pattern_id);
                } else {

                    return null;
                }
            } else {
                if (isset($option['text'])) {
                    $text = trim($option['text']);
                } else {
                    $text = '';
                }


                if ($text == '') {
                    return null;
                } else {
                    $message = $this->set_value($text);

                    return array('type' => 2, 'message' => $message);
                }
            }
        }
        public function set_value($text)
        {
            $tags = PUM_Functions::get_tags();

            $value = [];

            foreach ($tags as $index => $tag) {

                array_push($value, $this->get_tag_value($index));
            }

            $message = str_replace($tags, $value, $text);

            return $message;
        }
        public function get_tag_value($tag)
        {
            switch ($tag) {
                case 'OTP':
                case '{OTP}':
                    $value = Payamito_OTP::payamito_generate_otp($this->otp_count);
                    $this->OTP = (string) $value;
                    return $value;
                    break;

                case 'site_name':
                case '{site_name}':
                    $value = get_bloginfo('name');
                    return $value;
                    break;
            }
        }

        public function get_otp()
        {
            return $this->OTP;
        }
    }
}
