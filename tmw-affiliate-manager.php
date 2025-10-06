<?php
/**
 * Plugin Name: TMW Affiliate Manager
 * Plugin URI: https://github.com/themilisofialtd-rgb/tmw-affiliate-manager
 * Description: Foundation build for the TMW Affiliate Manager — banner zones, cron scheduler, and logger system.
 * Version: 1.0.2
 * Author: Adultwebmaster69
 * Author URI: https://top-models.webcam
 * Text Domain: tmw-affiliate-manager
 */
if (!defined('ABSPATH')) { exit; }

define('TMW_AM_VERSION', '1.0.2');
define('TMW_AM_FILE', __FILE__);
define('TMW_AM_DIR', plugin_dir_path(__FILE__));
define('TMW_AM_URL', plugin_dir_url(__FILE__));
define('TMW_AM_LOG_DIR', TMW_AM_DIR . 'logs/');

spl_autoload_register(function ($class) {
    $prefix = 'TMW\\AffiliateManager\\';
    $base_dir = TMW_AM_DIR . 'includes/classes/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) { return; }
    $relative_class = substr($class, $len);
    $relative_path = str_replace('\\', '-', $relative_class);
    $relative_path = str_replace('_', '-', $relative_path);
    $file = $base_dir . 'class-' . strtolower($relative_path) . '.php';
    if (is_readable($file)) { require $file; }
});

require_once TMW_AM_DIR . 'includes/helpers.php';

register_activation_hook(__FILE__, function () {
    if (!file_exists(TMW_AM_LOG_DIR)) { wp_mkdir_p(TMW_AM_LOG_DIR); }
    if (!file_exists(TMW_AM_LOG_DIR . '.htaccess')) {
        file_put_contents(TMW_AM_LOG_DIR . '.htaccess', "Require all denied\nDeny from all\n");
    }
    if (!file_exists(TMW_AM_LOG_DIR . 'index.html')) {
        file_put_contents(TMW_AM_LOG_DIR . 'index.html', '');
    }
    if (!get_option('tmw_am_banner_zones')) {
        update_option('tmw_am_banner_zones', [
            'desktop_global' => ['label' => 'Desktop Global', 'html' => '<!-- paste desktop banner HTML here -->'],
            'mobile_global' => ['label' => 'Mobile Global', 'html' => '<!-- paste mobile banner HTML here -->'],
        ], false);
    }
    if (!wp_next_scheduled('tmw_am_daily_event')) {
        wp_schedule_event(time() + 60, 'daily', 'tmw_am_daily_event');
    }
});

register_deactivation_hook(__FILE__, function () {
    $timestamp = wp_next_scheduled('tmw_am_daily_event');
    if ($timestamp) { wp_unschedule_event($timestamp, 'tmw_am_daily_event'); }
});

add_action('plugins_loaded', function () {
    $logger = new TMW\AffiliateManager\Logger();
    $banner = new TMW\AffiliateManager\Banner_Zones($logger);
    $cron   = new TMW\AffiliateManager\Cron($logger);
    $admin  = new TMW\AffiliateManager\Admin_Menu($logger, $banner);

    $GLOBALS['tmw_am'] = (object)[
        'logger' => $logger,
        'banner' => $banner,
        'cron'   => $cron,
        'admin'  => $admin,
    ];
});

add_action('tmw_am_daily_event', function () {
    if (!empty($GLOBALS['tmw_am']->logger)) {
        $GLOBALS['tmw_am']->logger->info('Daily cron tick: tmw_am_daily_event');
    }
});
