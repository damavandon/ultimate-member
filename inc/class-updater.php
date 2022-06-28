<?php

if (!class_exists('PUM_Updater')) {
    class PUM_Updater
    {
        public static $e;
        public static function init()
        {
            if (!class_exists("Puc_v4_Factory")) {
                include_once PAYAMITO_UM_DIR . '/inc/lib/plugin-update-checker-master/plugin-update-checker.php';
            }
            self::update_cheker();
        }

        public static function update_cheker()
        {
            $slug = 'payamito-sms-ultimate-member';
            $server = sprintf('https://updater.payamito.com/?action=get_metadata&slug=%s', $slug);
            $bootstrap_path = PAYAMITO_UM_PLUGIN_FILE;
            
            try {
                Puc_v4_Factory::buildUpdateChecker($server, $bootstrap_path, $slug);
            } catch (Exception $e) {
                self::$e = $e->getMessage();
                add_action('admin_notices', [__CLASS__, 'exception']);
            }
        }
        public static function exception()
        {
            printf( '<div class="notice notice-error">%s</div>', self::$e);

        }
    }
}
