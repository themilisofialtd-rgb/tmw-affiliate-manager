<?php
if (!defined('ABSPATH')) { exit; }

function tmw_display_banner_zone($zone_id) {
    $zones = get_option('tmw_am_banner_zones', []);
    $zone  = isset($zones[$zone_id]) ? $zones[$zone_id] : null;
    $html = '';
    if ($zone && !empty($zone['html'])) {
        $html = apply_filters('tmw_am_banner_zone_html', $zone['html'], $zone_id, $zone);
    } else {
        $html = '<!-- TMW: no banner assigned for zone: ' . esc_html($zone_id) . ' -->';
    }
    echo $html;
}

add_shortcode('tmw_banner_zone', function ($atts) {
    $atts = shortcode_atts(['id' => 'desktop_global'], $atts, 'tmw_banner_zone');
    ob_start();
    tmw_display_banner_zone($atts['id']);
    return ob_get_clean();
});
