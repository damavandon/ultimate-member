<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════


// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {

	die('direct access abort ');
}

if(function_exists('pum_autoload') &&  is_callable('pum_autoload')){

    spl_autoload_register('pum_autoload');
}

    function pum_autoload($class_name){

        $namespace='Payamito\UltimateMember';
        if ( 0 !== strpos( $class_name, $namespace ) ) {
            return;
        }
    
        $class_name = str_replace( $namespace, '', $class_name );
        $class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );
    
        $path = PAYAMITO_UM_DIR . $class_name . '.php';

        include_once $path;
    
    }