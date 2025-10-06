<?php
namespace TMW\AffiliateManager;
if (!defined('ABSPATH')) { exit; }
class Admin_Menu {
    protected $logger; protected $banner;
    public function __construct(Logger $logger, Banner_Zones $banner) {
        $this->logger = $logger; $this->banner = $banner;
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'maybe_handle_posts']);
    }
    public function register_menu() {
        add_menu_page('TMW Affiliate Manager', 'TMW Affiliate', 'manage_options', 'tmw-am', [$this, 'render_dashboard'], 'dashicons-megaphone', 60);
        add_submenu_page('tmw-am', 'Dashboard', 'Dashboard', 'manage_options', 'tmw-am', [$this, 'render_dashboard']);
        add_submenu_page('tmw-am', 'Banner Zones', 'Banner Zones', 'manage_options', 'tmw-am-zones', [$this, 'render_zones']);
        add_submenu_page('tmw-am', 'Logs', 'Logs', 'manage_options', 'tmw-am-logs', [$this, 'render_logs']);
        add_submenu_page('tmw-am', 'Settings', 'Settings', 'manage_options', 'tmw-am-settings', [$this, 'render_settings']);
    }
    public function render_dashboard() { require \TMW_AM_DIR . 'admin/pages/dashboard.php'; }
    public function render_zones() { require \TMW_AM_DIR . 'admin/pages/banner-zones.php'; }
    public function render_logs() { require \TMW_AM_DIR . 'admin/pages/logs.php'; }
    public function render_settings() { require \TMW_AM_DIR . 'admin/pages/settings.php'; }
    public function maybe_handle_posts() {
        if (!isset($_POST['tmw_am_action'])) return;
        if (!current_user_can('manage_options')) return;
        check_admin_referer('tmw_am_nonce', 'tmw_am_nonce');
        switch (sanitize_text_field(wp_unslash($_POST['tmw_am_action']))) {
            case 'save_zones':
                $zones = isset($_POST['zones']) && is_array($_POST['zones']) ? $_POST['zones'] : [];
                $clean = [];
                foreach ($zones as $key => $data) {
                    $label = isset($data['label']) ? sanitize_text_field(wp_unslash($data['label'])) : '';
                    $html  = isset($data['html']) ? wp_unslash($data['html']) : '';
                    if (!current_user_can('unfiltered_html')) { $html = wp_kses_post($html); }
                    $clean[$key] = ['label' => $label, 'html' => $html];
                }
                update_option('tmw_am_banner_zones', $clean, false);
                $this->logger->info('Banner zones saved');
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-success is-dismissible"><p>TMW: Banner zones saved.</p></div>';
                });
                break;
        }
    }
}