<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════




// don't call the file directly


if (!defined('ABSPATH')) {
    die();
}
if (!class_exists('PUM_Functions')) :

    class PUM_Functions
    {

        public static  function get_forms()
        {
            $final = [];
            $fields = [];
            $forms = get_posts(apply_filters(
                "payamito_um_array_get_form_filter",
                array(
                    'post_type' => 'um_form',
                    'post_status' => 'publish',
                    'meta_key' => array('_um_custom_fields', '_um_register_template', '')
                )
            ));
            if (count($forms)) {
                foreach ($forms as $index => $form) {
                    $final[$index]["ID"]     = $form->ID;
                    $final[$index]["title"]  = $form->post_title;
                    $form_fields = get_post_meta($form->ID, '_um_custom_fields', true);
                    if (is_array($fields)) {
                        foreach ($form_fields as $item) {
                            if (isset($item['metakey']) && !empty($item['metakey'])) {

                                $fields[$item['metakey']] = $item['title'];
                            }
                        }
                    }
                    $final[$index]['fields'] = $fields;
                    $final[$index]["mode"]   = get_post_meta($form->ID, '_um_mode', true);
                    $fields=[];
                }

                return apply_filters("payamito_um_forms",  $final);
            }
        }
        public function get_tags(){
            return['OTP','site_name'];
        }
        public static function form_type()
        {
            $types = ['register', 'login'];
            return $types;
        }
      

        public static function get_mobile($field)
        {
            $field  = (array) $field;
            $mobile = rgar($field, "payamito_um_verify_mobile");
            $mobile = str_replace('.', '_', $mobile);
            $mobile = "input_{$mobile}";
            $mobile = !rgempty($mobile) ? sanitize_text_field(rgpost($mobile)) : '';
            return $mobile;
        }

        public static function resent_time_check($mobile, $time = 0)
        {

            if (!isset($_SESSION[$mobile . 'T'])) {
                return true;
            }
            $period_send = (int)$time;
            $time_send = (int)$_SESSION[$mobile . "T"];
            $R = time() - $time_send;
            if ($R < $period_send) {
                return ($period_send - $R);
            }
            return true;
        }
        public static function get_meta_keys()
        {
            global $wpdb;
            $final = array();
            $sql = "SELECT DISTINCT `meta_key`  FROM $wpdb->usermeta ";
            $results = $wpdb->get_results($sql, ARRAY_A);
            foreach ($results as $index => $result) {
                $final[$result['meta_key']] = $result['meta_key'];
            }
            return $final;
        }
        public static function verify_country($country, $form_id)
        {
            global $payamito_um_options;
            $options = $payamito_um_options[$form_id];
            if (is_array($options['allowed_countries']) &&  count($options['allowed_countries'])==0 ) {
                return true;
            }
           
            $country_selected = array_search($country, self::countrie_codes());

            if (!$country_selected) {
                return false;
            }
            $allowed_countries = is_null($options['allowed_countries'])?[]:$options['allowed_countries'] ;
            if(in_array($country_selected,$allowed_countries)){
                return true;
            }
            return false;
        }
       public static function remove_0_from_mobile($mobile){
           if(trim(empty($mobile))){
               return "";
           }
        $is_0=$mobile[0];
        if($is_0=='0'){
            $mobile=substr_replace($mobile,"",0,1);
        }
        return $mobile;
       }
        public static function get_countries()
        {
            $countries = array(
                'AF' => __('Afghanistan', 'payamito-ultimate-member'),
                'AX' => __('Åland Islands', 'payamito-ultimate-member'),
                'AL' => __('Albania', 'payamito-ultimate-member'),
                'DZ' => __('Algeria', 'payamito-ultimate-member'),
                'AS' => __('American Samoa', 'payamito-ultimate-member'),
                'AD' => __('Andorra', 'payamito-ultimate-member'),
                'AO' => __('Angola', 'payamito-ultimate-member'),
                'AI' => __('Anguilla', 'payamito-ultimate-member'),
                'AQ' => __('Antarctica', 'payamito-ultimate-member'),
                'AG' => __('Antigua and Barbuda', 'payamito-ultimate-member'),
                'AR' => __('Argentina', 'payamito-ultimate-member'),
                'AM' => __('Armenia', 'payamito-ultimate-member'),
                'AW' => __('Aruba', 'payamito-ultimate-member'),
                'AU' => __('Australia', 'payamito-ultimate-member'),
                'AT' => __('Austria', 'payamito-ultimate-member'),
                'AZ' => __('Azerbaijan', 'payamito-ultimate-member'),
                'BS' => __('Bahamas', 'payamito-ultimate-member'),
                'BH' => __('Bahrain', 'payamito-ultimate-member'),
                'BD' => __('Bangladesh', 'payamito-ultimate-member'),
                'BB' => __('Barbados', 'payamito-ultimate-member'),
                'BY' => __('Belarus', 'payamito-ultimate-member'),
                'BE' => __('Belgium', 'payamito-ultimate-member'),
                'PW' => __('Belau', 'payamito-ultimate-member'),
                'BZ' => __('Belize', 'payamito-ultimate-member'),
                'BJ' => __('Benin', 'payamito-ultimate-member'),
                'BM' => __('Bermuda', 'payamito-ultimate-member'),
                'BT' => __('Bhutan', 'payamito-ultimate-member'),
                'BO' => __('Bolivia', 'payamito-ultimate-member'),
                'BQ' => __('Bonaire, Saint Eustatius and Saba', 'payamito-ultimate-member'),
                'BA' => __('Bosnia and Herzegovina', 'payamito-ultimate-member'),
                'BW' => __('Botswana', 'payamito-ultimate-member'),
                'BV' => __('Bouvet Island', 'payamito-ultimate-member'),
                'BR' => __('Brazil', 'payamito-ultimate-member'),
                'IO' => __('British Indian Ocean Territory', 'payamito-ultimate-member'),
                'BN' => __('Brunei', 'payamito-ultimate-member'),
                'BG' => __('Bulgaria', 'payamito-ultimate-member'),
                'BF' => __('Burkina Faso', 'payamito-ultimate-member'),
                'BI' => __('Burundi', 'payamito-ultimate-member'),
                'KH' => __('Cambodia', 'payamito-ultimate-member'),
                'CM' => __('Cameroon', 'payamito-ultimate-member'),
                'CA' => __('Canada', 'payamito-ultimate-member'),
                'CV' => __('Cape Verde', 'payamito-ultimate-member'),
                'KY' => __('Cayman Islands', 'payamito-ultimate-member'),
                'CF' => __('Central African Republic', 'payamito-ultimate-member'),
                'TD' => __('Chad', 'payamito-ultimate-member'),
                'CL' => __('Chile', 'payamito-ultimate-member'),
                'CN' => __('China', 'payamito-ultimate-member'),
                'CX' => __('Christmas Island', 'payamito-ultimate-member'),
                'CC' => __('Cocos (Keeling) Islands', 'payamito-ultimate-member'),
                'CO' => __('Colombia', 'payamito-ultimate-member'),
                'KM' => __('Comoros', 'payamito-ultimate-member'),
                'CG' => __('Congo (Brazzaville)', 'payamito-ultimate-member'),
                'CD' => __('Congo (Kinshasa)', 'payamito-ultimate-member'),
                'CK' => __('Cook Islands', 'payamito-ultimate-member'),
                'CR' => __('Costa Rica', 'payamito-ultimate-member'),
                'HR' => __('Croatia', 'payamito-ultimate-member'),
                'CU' => __('Cuba', 'payamito-ultimate-member'),
                'CW' => __('Cura&ccedil;ao', 'payamito-ultimate-member'),
                'CY' => __('Cyprus', 'payamito-ultimate-member'),
                'CZ' => __('Czech Republic', 'payamito-ultimate-member'),
                'DK' => __('Denmark', 'payamito-ultimate-member'),
                'DJ' => __('Djibouti', 'payamito-ultimate-member'),
                'DM' => __('Dominica', 'payamito-ultimate-member'),
                'DO' => __('Dominican Republic', 'payamito-ultimate-member'),
                'EC' => __('Ecuador', 'payamito-ultimate-member'),
                'EG' => __('Egypt', 'payamito-ultimate-member'),
                'SV' => __('El Salvador', 'payamito-ultimate-member'),
                'GQ' => __('Equatorial Guinea', 'payamito-ultimate-member'),
                'ER' => __('Eritrea', 'payamito-ultimate-member'),
                'EE' => __('Estonia', 'payamito-ultimate-member'),
                'ET' => __('Ethiopia', 'payamito-ultimate-member'),
                'FK' => __('Falkland Islands', 'payamito-ultimate-member'),
                'FO' => __('Faroe Islands', 'payamito-ultimate-member'),
                'FJ' => __('Fiji', 'payamito-ultimate-member'),
                'FI' => __('Finland', 'payamito-ultimate-member'),
                'FR' => __('France', 'payamito-ultimate-member'),
                'GF' => __('French Guiana', 'payamito-ultimate-member'),
                'PF' => __('French Polynesia', 'payamito-ultimate-member'),
                'TF' => __('French Southern Territories', 'payamito-ultimate-member'),
                'GA' => __('Gabon', 'payamito-ultimate-member'),
                'GM' => __('Gambia', 'payamito-ultimate-member'),
                'GE' => __('Georgia', 'payamito-ultimate-member'),
                'DE' => __('Germany', 'payamito-ultimate-member'),
                'GH' => __('Ghana', 'payamito-ultimate-member'),
                'GI' => __('Gibraltar', 'payamito-ultimate-member'),
                'GR' => __('Greece', 'payamito-ultimate-member'),
                'GL' => __('Greenland', 'payamito-ultimate-member'),
                'GD' => __('Grenada', 'payamito-ultimate-member'),
                'GP' => __('Guadeloupe', 'payamito-ultimate-member'),
                'GU' => __('Guam', 'payamito-ultimate-member'),
                'GT' => __('Guatemala', 'payamito-ultimate-member'),
                'GG' => __('Guernsey', 'payamito-ultimate-member'),
                'GN' => __('Guinea', 'payamito-ultimate-member'),
                'GW' => __('Guinea-Bissau', 'payamito-ultimate-member'),
                'GY' => __('Guyana', 'payamito-ultimate-member'),
                'HT' => __('Haiti', 'payamito-ultimate-member'),
                'HM' => __('Heard Island and McDonald Islands', 'payamito-ultimate-member'),
                'HN' => __('Honduras', 'payamito-ultimate-member'),
                'HK' => __('Hong Kong', 'payamito-ultimate-member'),
                'HU' => __('Hungary', 'payamito-ultimate-member'),
                'IS' => __('Iceland', 'payamito-ultimate-member'),
                'IN' => __('India', 'payamito-ultimate-member'),
                'ID' => __('Indonesia', 'payamito-ultimate-member'),
                'IR' => __('Iran', 'payamito-ultimate-member'),
                'IQ' => __('Iraq', 'payamito-ultimate-member'),
                'IE' => __('Ireland', 'payamito-ultimate-member'),
                'IM' => __('Isle of Man', 'payamito-ultimate-member'),
                'IL' => __('Israel', 'payamito-ultimate-member'),
                'IT' => __('Italy', 'payamito-ultimate-member'),
                'CI' => __('Ivory Coast', 'payamito-ultimate-member'),
                'JM' => __('Jamaica', 'payamito-ultimate-member'),
                'JP' => __('Japan', 'payamito-ultimate-member'),
                'JE' => __('Jersey', 'payamito-ultimate-member'),
                'JO' => __('Jordan', 'payamito-ultimate-member'),
                'KZ' => __('Kazakhstan', 'payamito-ultimate-member'),
                'KE' => __('Kenya', 'payamito-ultimate-member'),
                'KI' => __('Kiribati', 'payamito-ultimate-member'),
                'KW' => __('Kuwait', 'payamito-ultimate-member'),
                'KG' => __('Kyrgyzstan', 'payamito-ultimate-member'),
                'LA' => __('Laos', 'payamito-ultimate-member'),
                'LV' => __('Latvia', 'payamito-ultimate-member'),
                'LB' => __('Lebanon', 'payamito-ultimate-member'),
                'LS' => __('Lesotho', 'payamito-ultimate-member'),
                'LR' => __('Liberia', 'payamito-ultimate-member'),
                'LY' => __('Libya', 'payamito-ultimate-member'),
                'LI' => __('Liechtenstein', 'payamito-ultimate-member'),
                'LT' => __('Lithuania', 'payamito-ultimate-member'),
                'LU' => __('Luxembourg', 'payamito-ultimate-member'),
                'MO' => __('Macao', 'payamito-ultimate-member'),
                'MK' => __('North Macedonia', 'payamito-ultimate-member'),
                'MG' => __('Madagascar', 'payamito-ultimate-member'),
                'MW' => __('Malawi', 'payamito-ultimate-member'),
                'MY' => __('Malaysia', 'payamito-ultimate-member'),
                'MV' => __('Maldives', 'payamito-ultimate-member'),
                'ML' => __('Mali', 'payamito-ultimate-member'),
                'MT' => __('Malta', 'payamito-ultimate-member'),
                'MH' => __('Marshall Islands', 'payamito-ultimate-member'),
                'MQ' => __('Martinique', 'payamito-ultimate-member'),
                'MR' => __('Mauritania', 'payamito-ultimate-member'),
                'MU' => __('Mauritius', 'payamito-ultimate-member'),
                'YT' => __('Mayotte', 'payamito-ultimate-member'),
                'MX' => __('Mexico', 'payamito-ultimate-member'),
                'FM' => __('Micronesia', 'payamito-ultimate-member'),
                'MD' => __('Moldova', 'payamito-ultimate-member'),
                'MC' => __('Monaco', 'payamito-ultimate-member'),
                'MN' => __('Mongolia', 'payamito-ultimate-member'),
                'ME' => __('Montenegro', 'payamito-ultimate-member'),
                'MS' => __('Montserrat', 'payamito-ultimate-member'),
                'MA' => __('Morocco', 'payamito-ultimate-member'),
                'MZ' => __('Mozambique', 'payamito-ultimate-member'),
                'MM' => __('Myanmar', 'payamito-ultimate-member'),
                'NA' => __('Namibia', 'payamito-ultimate-member'),
                'NR' => __('Nauru', 'payamito-ultimate-member'),
                'NP' => __('Nepal', 'payamito-ultimate-member'),
                'NL' => __('Netherlands', 'payamito-ultimate-member'),
                'NC' => __('New Caledonia', 'payamito-ultimate-member'),
                'NZ' => __('New Zealand', 'payamito-ultimate-member'),
                'NI' => __('Nicaragua', 'payamito-ultimate-member'),
                'NE' => __('Niger', 'payamito-ultimate-member'),
                'NG' => __('Nigeria', 'payamito-ultimate-member'),
                'NU' => __('Niue', 'payamito-ultimate-member'),
                'NF' => __('Norfolk Island', 'payamito-ultimate-member'),
                'MP' => __('Northern Mariana Islands', 'payamito-ultimate-member'),
                'KP' => __('North Korea', 'payamito-ultimate-member'),
                'NO' => __('Norway', 'payamito-ultimate-member'),
                'OM' => __('Oman', 'payamito-ultimate-member'),
                'PK' => __('Pakistan', 'payamito-ultimate-member'),
                'PS' => __('Palestinian Territory', 'payamito-ultimate-member'),
                'PA' => __('Panama', 'payamito-ultimate-member'),
                'PG' => __('Papua New Guinea', 'payamito-ultimate-member'),
                'PY' => __('Paraguay', 'payamito-ultimate-member'),
                'PE' => __('Peru', 'payamito-ultimate-member'),
                'PH' => __('Philippines', 'payamito-ultimate-member'),
                'PN' => __('Pitcairn', 'payamito-ultimate-member'),
                'PL' => __('Poland', 'payamito-ultimate-member'),
                'PT' => __('Portugal', 'payamito-ultimate-member'),
                'PR' => __('Puerto Rico', 'payamito-ultimate-member'),
                'QA' => __('Qatar', 'payamito-ultimate-member'),
                'RE' => __('Reunion', 'payamito-ultimate-member'),
                'RO' => __('Romania', 'payamito-ultimate-member'),
                'RU' => __('Russia', 'payamito-ultimate-member'),
                'RW' => __('Rwanda', 'payamito-ultimate-member'),
                'BL' => __('Saint Barth&eacute;lemy', 'payamito-ultimate-member'),
                'SH' => __('Saint Helena', 'payamito-ultimate-member'),
                'KN' => __('Saint Kitts and Nevis', 'payamito-ultimate-member'),
                'LC' => __('Saint Lucia', 'payamito-ultimate-member'),
                'MF' => __('Saint Martin (French part)', 'payamito-ultimate-member'),
                'SX' => __('Saint Martin (Dutch part)', 'payamito-ultimate-member'),
                'PM' => __('Saint Pierre and Miquelon', 'payamito-ultimate-member'),
                'VC' => __('Saint Vincent and the Grenadines', 'payamito-ultimate-member'),
                'SM' => __('San Marino', 'payamito-ultimate-member'),
                'ST' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'payamito-ultimate-member'),
                'SA' => __('Saudi Arabia', 'payamito-ultimate-member'),
                'SN' => __('Senegal', 'payamito-ultimate-member'),
                'RS' => __('Serbia', 'payamito-ultimate-member'),
                'SC' => __('Seychelles', 'payamito-ultimate-member'),
                'SL' => __('Sierra Leone', 'payamito-ultimate-member'),
                'SG' => __('Singapore', 'payamito-ultimate-member'),
                'SK' => __('Slovakia', 'payamito-ultimate-member'),
                'SI' => __('Slovenia', 'payamito-ultimate-member'),
                'SB' => __('Solomon Islands', 'payamito-ultimate-member'),
                'SO' => __('Somalia', 'payamito-ultimate-member'),
                'ZA' => __('South Africa', 'payamito-ultimate-member'),
                'GS' => __('South Georgia/Sandwich Islands', 'payamito-ultimate-member'),
                'KR' => __('South Korea', 'payamito-ultimate-member'),
                'SS' => __('South Sudan', 'payamito-ultimate-member'),
                'ES' => __('Spain', 'payamito-ultimate-member'),
                'LK' => __('Sri Lanka', 'payamito-ultimate-member'),
                'SD' => __('Sudan', 'payamito-ultimate-member'),
                'SR' => __('Suriname', 'payamito-ultimate-member'),
                'SJ' => __('Svalbard and Jan Mayen', 'payamito-ultimate-member'),
                'SZ' => __('Swaziland', 'payamito-ultimate-member'),
                'SE' => __('Sweden', 'payamito-ultimate-member'),
                'CH' => __('Switzerland', 'payamito-ultimate-member'),
                'SY' => __('Syria', 'payamito-ultimate-member'),
                'TW' => __('Taiwan', 'payamito-ultimate-member'),
                'TJ' => __('Tajikistan', 'payamito-ultimate-member'),
                'TZ' => __('Tanzania', 'payamito-ultimate-member'),
                'TH' => __('Thailand', 'payamito-ultimate-member'),
                'TL' => __('Timor-Leste', 'payamito-ultimate-member'),
                'TG' => __('Togo', 'payamito-ultimate-member'),
                'TK' => __('Tokelau', 'payamito-ultimate-member'),
                'TO' => __('Tonga', 'payamito-ultimate-member'),
                'TT' => __('Trinidad and Tobago', 'payamito-ultimate-member'),
                'TN' => __('Tunisia', 'payamito-ultimate-member'),
                'TR' => __('Turkey', 'payamito-ultimate-member'),
                'TM' => __('Turkmenistan', 'payamito-ultimate-member'),
                'TC' => __('Turks and Caicos Islands', 'payamito-ultimate-member'),
                'TV' => __('Tuvalu', 'payamito-ultimate-member'),
                'UG' => __('Uganda', 'payamito-ultimate-member'),
                'UA' => __('Ukraine', 'payamito-ultimate-member'),
                'AE' => __('United Arab Emirates', 'payamito-ultimate-member'),
                'GB' => __('United Kingdom (UK)', 'payamito-ultimate-member'),
                'US' => __('United States (US)', 'payamito-ultimate-member'),
                'UM' => __('United States (US) Minor Outlying Islands', 'payamito-ultimate-member'),
                'UY' => __('Uruguay', 'payamito-ultimate-member'),
                'UZ' => __('Uzbekistan', 'payamito-ultimate-member'),
                'VU' => __('Vanuatu', 'payamito-ultimate-member'),
                'VA' => __('Vatican', 'payamito-ultimate-member'),
                'VE' => __('Venezuela', 'payamito-ultimate-member'),
                'VN' => __('Vietnam', 'payamito-ultimate-member'),
                'VG' => __('Virgin Islands (British)', 'payamito-ultimate-member'),
                'VI' => __('Virgin Islands (US)', 'payamito-ultimate-member'),
                'WF' => __('Wallis and Futuna', 'payamito-ultimate-member'),
                'EH' => __('Western Sahara', 'payamito-ultimate-member'),
                'WS' => __('Samoa', 'payamito-ultimate-member'),
                'YE' => __('Yemen', 'payamito-ultimate-member'),
                'ZM' => __('Zambia', 'payamito-ultimate-member'),
                'ZW' => __('Zimbabwe', 'payamito-ultimate-member'),
            );
            return apply_filters("payamito_um_countries", $countries);
        }
        public static function  countrie_codes()
        {
            return apply_filters("payamito_countrie_codes", array(
                'BD' => '+880',
                'BE' => '+32',
                'BF' => '+226',
                'BG' => '+359',
                'BA' => '+387',
                'BB' => '+1246',
                'WF' => '+681',
                'BL' => '+590',
                'BM' => '+1441',
                'BN' => '+673',
                'BO' => '+591',
                'BH' => '+973',
                'BI' => '+257',
                'BJ' => '+229',
                'BT' => '+975',
                'JM' => '+1876',
                'BV' => '',
                'BW' => '+267',
                'WS' => '+685',
                'BQ' => '+599',
                'BR' => '+55',
                'BS' => '+1242',
                'JE' => '+441534',
                'BY' => '+375',
                'BZ' => '+501',
                'RU' => '+7',
                'RW' => '+250',
                'RS' => '+381',
                'TL' => '+670',
                'RE' => '+262',
                'TM' => '+993',
                'TJ' => '+992',
                'RO' => '+40',
                'TK' => '+690',
                'GW' => '+245',
                'GU' => '+1671',
                'GT' => '+502',
                'GS' => '',
                'GR' => '+30',
                'GQ' => '+240',
                'GP' => '+590',
                'JP' => '+81',
                'GY' => '+592',
                'GG' => '+441481',
                'GF' => '+594',
                'GE' => '+995',
                'GD' => '+1473',
                'GB' => '+44',
                'GA' => '+241',
                'SV' => '+503',
                'GN' => '+224',
                'GM' => '+220',
                'GL' => '+299',
                'GI' => '+350',
                'GH' => '+233',
                'OM' => '+968',
                'TN' => '+216',
                'JO' => '+962',
                'HR' => '+385',
                'HT' => '+509',
                'HU' => '+36',
                'HK' => '+852',
                'HN' => '+504',
                'HM' => '',
                'VE' => '+58',
                'PR' => array(
                    '+1787',
                    '+1939',
                ),
                'PS' => '+970',
                'PW' => '+680',
                'PT' => '+351',
                'SJ' => '+47',
                'PY' => '+595',
                'IQ' => '+964',
                'PA' => '+507',
                'PF' => '+689',
                'PG' => '+675',
                'PE' => '+51',
                'PK' => '+92',
                'PH' => '+63',
                'PN' => '+870',
                'PL' => '+48',
                'PM' => '+508',
                'ZM' => '+260',
                'EH' => '+212',
                'EE' => '+372',
                'EG' => '+20',
                'ZA' => '+27',
                'EC' => '+593',
                'IT' => '+39',
                'VN' => '+84',
                'SB' => '+677',
                'ET' => '+251',
                'SO' => '+252',
                'ZW' => '+263',
                'SA' => '+966',
                'ES' => '+34',
                'ER' => '+291',
                'ME' => '+382',
                'MD' => '+373',
                'MG' => '+261',
                'MF' => '+590',
                'MA' => '+212',
                'MC' => '+377',
                'UZ' => '+998',
                'MM' => '+95',
                'ML' => '+223',
                'MO' => '+853',
                'MN' => '+976',
                'MH' => '+692',
                'MK' => '+389',
                'MU' => '+230',
                'MT' => '+356',
                'MW' => '+265',
                'MV' => '+960',
                'MQ' => '+596',
                'MP' => '+1670',
                'MS' => '+1664',
                'MR' => '+222',
                'IM' => '+441624',
                'UG' => '+256',
                'TZ' => '+255',
                'MY' => '+60',
                'MX' => '+52',
                'IL' => '+972',
                'FR' => '+33',
                'IO' => '+246',
                'SH' => '+290',
                'FI' => '+358',
                'FJ' => '+679',
                'FK' => '+500',
                'FM' => '+691',
                'FO' => '+298',
                'NI' => '+505',
                'NL' => '+31',
                'NO' => '+47',
                'NA' => '+264',
                'VU' => '+678',
                'NC' => '+687',
                'NE' => '+227',
                'NF' => '+672',
                'NG' => '+234',
                'NZ' => '+64',
                'NP' => '+977',
                'NR' => '+674',
                'NU' => '+683',
                'CK' => '+682',
                'XK' => '',
                'CI' => '+225',
                'CH' => '+41',
                'CO' => '+57',
                'CN' => '+86',
                'CM' => '+237',
                'CL' => '+56',
                'CC' => '+61',
                'CA' => '+1',
                'CG' => '+242',
                'CF' => '+236',
                'CD' => '+243',
                'CZ' => '+420',
                'CY' => '+357',
                'CX' => '+61',
                'CR' => '+506',
                'CW' => '+599',
                'CV' => '+238',
                'CU' => '+53',
                'SZ' => '+268',
                'SY' => '+963',
                'SX' => '+599',
                'KG' => '+996',
                'KE' => '+254',
                'SS' => '+211',
                'SR' => '+597',
                'KI' => '+686',
                'KH' => '+855',
                'KN' => '+1869',
                'KM' => '+269',
                'ST' => '+239',
                'SK' => '+421',
                'KR' => '+82',
                'SI' => '+386',
                'KP' => '+850',
                'KW' => '+965',
                'SN' => '+221',
                'SM' => '+378',
                'SL' => '+232',
                'SC' => '+248',
                'KZ' => '+7',
                'KY' => '+1345',
                'SG' => '+65',
                'SE' => '+46',
                'SD' => '+249',
                'DO' => array(
                    '+1809',
                    '+1829',
                    '+1849',
                ),
                'DM' => '+1767',
                'DJ' => '+253',
                'DK' => '+45',
                'VG' => '+1284',
                'DE' => '+49',
                'YE' => '+967',
                'DZ' => '+213',
                'US' => '+1',
                'UY' => '+598',
                'YT' => '+262',
                'UM' => '+1',
                'LB' => '+961',
                'LC' => '+1758',
                'LA' => '+856',
                'TV' => '+688',
                'TW' => '+886',
                'TT' => '+1868',
                'TR' => '+90',
                'LK' => '+94',
                'LI' => '+423',
                'LV' => '+371',
                'TO' => '+676',
                'LT' => '+370',
                'LU' => '+352',
                'LR' => '+231',
                'LS' => '+266',
                'TH' => '+66',
                'TF' => '',
                'TG' => '+228',
                'TD' => '+235',
                'TC' => '+1649',
                'LY' => '+218',
                'VA' => '+379',
                'VC' => '+1784',
                'AE' => '+971',
                'AD' => '+376',
                'AG' => '+1268',
                'AF' => '+93',
                'AI' => '+1264',
                'VI' => '+1340',
                'IS' => '+354',
                'IR' => '+98',
                'AM' => '+374',
                'AL' => '+355',
                'AO' => '+244',
                'AQ' => '',
                'AS' => '+1684',
                'AR' => '+54',
                'AU' => '+61',
                'AT' => '+43',
                'AW' => '+297',
                'IN' => '+91',
                'AX' => '+35818',
                'AZ' => '+994',
                'IE' => '+353',
                'ID' => '+62',
                'UA' => '+380',
                'QA' => '+974',
                'MZ' => '+258',
            ));
        }
    }
endif;
