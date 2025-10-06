<?php
namespace TMW\AffiliateManager;
if (!defined('ABSPATH')) { exit; }
class Banner_Zones {
    protected $logger;
    public function __construct(Logger $logger) {
        $this->logger = $logger;
        add_action('init', [$this, 'register']);
    }
    public function register() { $this->logger->info('Banner_Zones registered'); }
    public function get_zones(): array { return get_option('tmw_am_banner_zones', []); }
}