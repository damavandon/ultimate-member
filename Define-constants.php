<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

// don't call the file directly
if (!defined('ABSPATH')) {

    die('direct access abort ');
}

if (!defined('PAYAMITO_UM_BASENAME')) {

    defined('PAYAMITO_UM_BASENAME') || define('PAYAMITO_UM_BASENAME',__DIR__);
}
if (!defined('PAYAMITO_UM_DIR')) {

    define('PAYAMITO_UM_DIR', PAYAMITO_UM_BASENAME);
}
if (!defined('PAYAMITO_UM_URL')) {

    define('PAYAMITO_UM_URL',  plugin_dir_url( __FILE__));
}
if (!defined('PAYAMITO_UM_VER')) {

    define('PAYAMITO_UM_VER', '1.2.1');
}
if (!defined('PAYAMITO_UM_COR_DIR')) {

    define('PAYAMITO_UM_COR_DIR', PAYAMITO_UM_DIR.'/inc/core/payamito-core');
}
if (!defined('PAYAMITO_UM_CORE_VER')) {
    define('PAYAMITO_UM_CORE_VER', '2.0.0');
}
