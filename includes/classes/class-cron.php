<?php
namespace TMW\AffiliateManager;
if (!defined('ABSPATH')) { exit; }
class Cron {
    protected $logger;
    public function __construct(Logger $logger) {
        $this->logger = $logger;
        add_action('tmw_am_daily_event', [$this, 'daily_job']);
    }
    public function daily_job() { $this->logger->info('Cron daily_job executed'); }
}