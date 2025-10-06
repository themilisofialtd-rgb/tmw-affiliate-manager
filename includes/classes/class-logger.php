<?php
namespace TMW\AffiliateManager;
if (!defined('ABSPATH')) { exit; }
class Logger {
    protected $log_file;
    public function __construct() { $this->log_file = trailingslashit(\TMW_AM_LOG_DIR) . 'tmw.log'; }
    public function info($m, array $c = []) { $this->write('INFO', $m, $c); }
    public function error($m, array $c = []) { $this->write('ERROR', $m, $c); }
    protected function write($l, $m, array $c = []) {
        $line = sprintf('[%s] %s: %s %s', gmdate('Y-m-d H:i:s'), $l, $m, $c ? wp_json_encode($c) : '');
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) { error_log('TMW-AM ' . $line); }
        if (is_writable(\TMW_AM_LOG_DIR)) { @file_put_contents($this->log_file, "TMW-AM {$line}\n", FILE_APPEND); }
    }
}