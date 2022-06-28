<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://payamito.com/
 * @since             1.0.0
 * @package           Payamito
 * Plugin Name:       Payamito sms ultimate member
 * Description:       Payamito sms ultimate member Version
 * Version:           1.2.1
 * Core Version       2.0.0
 * Author:            payamito
 * Author URI:        https://payamito.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:      payamito-ultimate-member
 * Domain Path:       /languages
 */

// don't call the file directly

if ( ! defined( 'ABSPATH' ) ) {

	die('direct access abort ');
}
if ( ! defined( 'PAYAMITO_UM_PLUGIN_FILE' ) ) {
    
	define( 'PAYAMITO_UM_PLUGIN_FILE', __FILE__ );
}


require_once __DIR__.'/Define-constants.php';
require_once __DIR__.'/inc/Autoloader.php';

register_activation_hook(__FILE__, 'payamito_um_activate');
register_deactivation_hook(__FILE__, 'payamito_um_deactivate');

if (!function_exists("payamito_um_set_locale")) {
    function payamito_um_set_locale()
    {
        $dirname = str_replace('//', '/', wp_normalize_path(dirname(__FILE__))) ;
        $mo = $dirname . '/languages/' . 'payamito-ultimate-member-' . get_locale() . '.mo';
        load_textdomain('payamito-ultimate-member', $mo);
    }
}
payamito_um_set_locale();
//add_action("admin_init",["PUM_Updater","init"]);

function payamito_um_activate(){

    do_action("payamito_um_activate");
    require_once PAYAMITO_UM_DIR . '/inc/functions.php';
    require_once PAYAMITO_UM_DIR . '/inc/class-install.php';
    
    Payamito\UltimateMember\Install::install();
    require_once payamito_um_load_core().'/includes/class-payamito-activator.php';
    Payamito_Activator::activate();
}
function payamito_um_deactivate(){

    do_action("payamito_um_deactivate");
    require_once payamito_um_load_core().'/includes/class-payamito-deactivator.php';
    Payamito_Deactivator::deactivate();
}
if(!class_exists('PayamitoUltimateMember')){

    include_once PAYAMITO_UM_DIR.'/inc/payamito-um.php';
}

/**
 * @return object|PayamitoUltimateMember|null
 */
function payamito_um()
{
    return PayamitoUltimateMember::get_instance();
}

payamito_um();