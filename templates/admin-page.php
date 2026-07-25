<?php

/**
 * TGS Error Logger - Admin Page Template
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

$reader = TGS_Error_Logger_Reader::instance();
$blogs = $reader->get_all_blogs();
$modules = TGS_Error_Logger::get_modules();
$levels = TGS_Error_Logger::get_levels();

// Get filters
$selected_blog = isset($_GET['blog_id']) ? intval($_GET['blog_id']) : get_current_blog_id();
$selected_module = isset($_GET['module']) ? sanitize_text_field($_GET['module']) : 'system';
$selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : current_time('Y-m-d');
$selected_level = isset($_GET['level']) ? sanitize_text_field($_GET['level']) : '';
$search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$security_only = isset($_GET['security_only']) && $_GET['security_only'] === '1';

// Pagination
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 50; // 50 logs per page

// Build filters
$filters = [];
if ($selected_level) {
    $filters['level'] = $selected_level;
}
if ($search_term) {
    $filters['search'] = $search_term;
}
if ($security_only) {
    $filters['security_only'] = true;
}

// Get logs
$logs = [];
$available_files = [];
$stats = null;
$total_logs = 0;

if ($selected_blog) {
    if ($selected_module === 'all') {
        // Get logs from all modules
        foreach (array_keys($modules) as $module_key) {
            $module_logs = $reader->read_logs($selected_blog, $module_key, $selected_date, $filters);
            // Add module info to each log
            foreach ($module_logs as &$log) {
                $log['module_name'] = $modules[$module_key];
            }
            $logs = array_merge($logs, $module_logs);
        }

        // Sort by timestamp descending
        usort($logs, function($a, $b) {
            return $b['unix_timestamp'] - $a['unix_timestamp'];
        });

        // Total before pagination
        $total_logs = count($logs);

        // Apply pagination
        $offset = ($page - 1) * $per_page;
        $logs = array_slice($logs, $offset, $per_page);

        // Build combined stats
        $stats = [
            'total' => $total_logs,
            'by_level' => [],
            'security_issues' => 0,
            'by_file' => []
        ];

        // Get all logs again for stats (without pagination)
        $all_logs_for_stats = [];
        foreach (array_keys($modules) as $module_key) {
            $module_logs = $reader->read_logs($selected_blog, $module_key, $selected_date, $filters);
            $all_logs_for_stats = array_merge($all_logs_for_stats, $module_logs);
        }

        foreach ($all_logs_for_stats as $log) {
            // Count by level
            if (!isset($stats['by_level'][$log['level']])) {
                $stats['by_level'][$log['level']] = 0;
            }
            $stats['by_level'][$log['level']]++;

            // Count security issues
            if (!empty($log['security_issue'])) {
                $stats['security_issues']++;
            }

            // Count by file
            if (!empty($log['file'])) {
                $file = basename($log['file']);
                if (!isset($stats['by_file'][$file])) {
                    $stats['by_file'][$file] = 0;
                }
                $stats['by_file'][$file]++;
            }
        }

        // Sort by_file by count
        arsort($stats['by_file']);

    } else {
        // Single module
        $all_logs = $reader->read_logs($selected_blog, $selected_module, $selected_date, $filters);
        $total_logs = count($all_logs);

        // Apply pagination
        $offset = ($page - 1) * $per_page;
        $logs = array_slice($all_logs, $offset, $per_page);

        $available_files = $reader->get_available_log_files($selected_blog, $selected_module);
        $stats = $reader->get_summary_stats($selected_blog, $selected_module, $selected_date);
    }
}

// Calculate total pages
$total_pages = ceil($total_logs / $per_page);

?>

<div class="wrap tgs-error-logger-wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-warning" style="font-size: 30px; color: #d63638;"></span>
        Nhật ký lỗi - Error Logs
    </h1>

    <hr class="wp-header-end">

    <!-- Filter Form -->
    <div class="tgs-error-logger-filters" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <form method="get" action="">
            <input type="hidden" name="page" value="tgs-error-logger">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                <!-- Chọn cửa hàng -->
                <?php if (is_multisite() && count($blogs) > 1): ?>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <span class="dashicons dashicons-store"></span> Cửa hàng:
                    </label>
                    <select name="blog_id" style="width: 100%;">
                        <?php foreach ($blogs as $blog): ?>
                            <option value="<?php echo esc_attr($blog['blog_id']); ?>" <?php selected($selected_blog, $blog['blog_id']); ?>>
                                [<?php echo esc_html($blog['blog_id']); ?>]
                                <?php echo esc_html($blog['tgs_site_code'] ?: $blog['blogname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Chọn module -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <span class="dashicons dashicons-admin-plugins"></span> Module:
                    </label>
                    <select name="module" style="width: 100%;">
                        <option value="all" <?php selected($selected_module, 'all'); ?>>-- Tất cả --</option>
                        <?php foreach ($modules as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($selected_module, $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Chọn ngày -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <span class="dashicons dashicons-calendar-alt"></span> Ngày:
                    </label>
                    <select name="date" style="width: 100%;">
                        <?php if (!empty($available_files)): ?>
                            <?php foreach ($available_files as $file): ?>
                                <option value="<?php echo esc_attr($file['date']); ?>" <?php selected($selected_date, $file['date']); ?>>
                                    <?php echo esc_html($file['date']); ?>
                                    (<?php echo size_format($file['size']); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?php echo esc_attr($selected_date); ?>">
                                <?php echo esc_html($selected_date); ?>
                            </option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Level -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <span class="dashicons dashicons-flag"></span> Mức độ:
                    </label>
                    <select name="level" style="width: 100%;">
                        <option value="">-- Tất cả --</option>
                        <?php foreach (array_keys($levels) as $level): ?>
                            <option value="<?php echo esc_attr($level); ?>" <?php selected($selected_level, $level); ?>>
                                <?php echo esc_html(strtoupper($level)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tìm kiếm -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <span class="dashicons dashicons-search"></span> Tìm kiếm:
                    </label>
                    <input type="text" name="search" value="<?php echo esc_attr($search_term); ?>"
                           placeholder="Message, file, user..." style="width: 100%;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button type="submit" class="button button-primary">
                    <span class="dashicons dashicons-filter"></span> Lọc
                </button>
                <a href="?page=tgs-error-logger" class="button">
                    <span class="dashicons dashicons-image-rotate"></span> Reset
                </a>

                <label style="margin-left: 15px;">
                    <input type="checkbox" name="security_only" value="1" <?php checked($security_only); ?>>
                    <strong style="color: #d63638;">Chỉ lỗi bảo mật</strong>
                </label>

                <?php if ($selected_blog && $selected_module && $selected_module !== 'all' && $selected_date): ?>
                    <a href="<?php echo wp_nonce_url(admin_url('admin-ajax.php?action=tgs_error_logger_download_log&blog_id=' . $selected_blog . '&module=' . $selected_module . '&date=' . $selected_date), 'tgs_error_logger_nonce', 'nonce'); ?>"
                       class="button button-secondary"
                       target="_blank"
                       style="margin-left: auto;">
                        <span class="dashicons dashicons-download"></span> Tải xuống
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <?php if (!empty($stats)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <!-- Total -->
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h3 style="margin: 0 0 10px; color: #2271b1; font-size: 14px;">
                    <span class="dashicons dashicons-chart-bar"></span> Tổng lỗi
                </h3>
                <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: <?php echo $stats['total'] > 0 ? '#d63638' : '#2271b1'; ?>;">
                    <?php echo number_format($stats['total']); ?>
                </p>
                <p style="margin: 0; color: #646970; font-size: 13px;">lỗi trong ngày này</p>
            </div>

            <!-- By Level -->
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h3 style="margin: 0 0 10px; color: #2271b1; font-size: 14px;">
                    <span class="dashicons dashicons-flag"></span> Theo mức độ
                </h3>
                <?php foreach ($stats['by_level'] as $level => $count): ?>
                    <div style="margin: 5px 0; display: flex; justify-content: space-between; font-size: 13px;">
                        <span class="tgs-level-badge tgs-level-<?php echo esc_attr($level); ?>">
                            <?php echo esc_html(strtoupper($level)); ?>
                        </span>
                        <strong><?php echo number_format($count); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Security Issues -->
            <div style="background: <?php echo $stats['security_issues'] > 0 ? '#fef2f2' : '#fff'; ?>; padding: 20px; border: 1px solid <?php echo $stats['security_issues'] > 0 ? '#fca5a5' : '#ccd0d4'; ?>; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h3 style="margin: 0 0 10px; color: #d63638; font-size: 14px;">
                    <span class="dashicons dashicons-shield-alt"></span> Vấn đề bảo mật
                </h3>
                <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #d63638;">
                    <?php echo number_format($stats['security_issues']); ?>
                </p>
                <p style="margin: 0; color: #646970; font-size: 13px;">
                    <?php if ($stats['security_issues'] > 0): ?>
                        <strong style="color: #d63638;">⚠️ Cần kiểm tra ngay!</strong>
                    <?php else: ?>
                        Không có vấn đề
                    <?php endif; ?>
                </p>
            </div>

            <!-- Top Files -->
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h3 style="margin: 0 0 10px; color: #2271b1; font-size: 14px;">
                    <span class="dashicons dashicons-media-code"></span> File lỗi nhiều nhất
                </h3>
                <?php $top_files = array_slice($stats['by_file'], 0, 5, true); ?>
                <?php if (!empty($top_files)): ?>
                    <?php foreach ($top_files as $file => $count): ?>
                        <div style="margin: 5px 0; display: flex; justify-content: space-between; font-size: 12px;">
                            <span style="color: #646970; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                <?php echo esc_html($file); ?>
                            </span>
                            <strong style="margin-left: 10px;"><?php echo number_format($count); ?></strong>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #787c82; font-size: 13px; margin: 0;">Không có dữ liệu</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Error Logs Table -->
    <div style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <div style="padding: 15px; border-bottom: 1px solid #ccd0d4; display: flex; justify-content: space-between; align-items: center;">
            <strong style="font-size: 14px;">
                Danh sách lỗi: <?php echo number_format($total_logs); ?> kết quả
                <?php if ($total_pages > 1): ?>
                    <span style="color: #646970; font-weight: normal;">
                        (Trang <?php echo $page; ?>/<?php echo $total_pages; ?>)
                    </span>
                <?php endif; ?>
            </strong>

            <?php if ($total_logs > $per_page): ?>
                <div style="font-size: 13px; color: #646970;">
                    Hiển thị <?php echo number_format($offset + 1); ?>-<?php echo number_format(min($offset + $per_page, $total_logs)); ?>
                    trong <?php echo number_format($total_logs); ?> logs
                </div>
            <?php endif; ?>
        </div>

        <div style="overflow-x: auto;">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 100px;">Thời gian</th>
                        <th style="width: 80px;">Level</th>
                        <?php if ($selected_module === 'all'): ?>
                        <th style="width: 150px;">Module</th>
                        <?php endif; ?>
                        <th style="width: 120px;">User</th>
                        <th>Message</th>
                        <th style="width: 200px;">File/Line</th>
                        <th style="width: 100px;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="<?php echo $selected_module === 'all' ? '7' : '6'; ?>" style="text-align: center; padding: 60px;">
                                <span class="dashicons dashicons-yes-alt" style="font-size: 48px; color: #46b450;"></span>
                                <p style="margin: 10px 0 0; color: #787c82; font-size: 14px;">
                                    ✅ Không có lỗi nào - Hệ thống hoạt động tốt!
                                </p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="<?php echo !empty($log['security_issue']) ? 'tgs-security-row' : ''; ?>">
                                <td>
                                    <strong style="color: #2271b1; font-size: 12px;">
                                        <?php echo esc_html(date('H:i:s', strtotime($log['timestamp']))); ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="tgs-level-badge tgs-level-<?php echo esc_attr($log['level']); ?>">
                                        <?php echo esc_html(strtoupper($log['level'])); ?>
                                    </span>
                                    <?php if (!empty($log['security_issue'])): ?>
                                        <div style="margin-top: 3px;">
                                            <span class="dashicons dashicons-shield-alt" style="color: #d63638; font-size: 14px;" title="<?php echo esc_attr($log['security_issue']); ?>"></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <?php if ($selected_module === 'all'): ?>
                                <td>
                                    <span style="font-size: 11px; background: #f0f0f1; padding: 3px 6px; border-radius: 3px; display: inline-block;">
                                        <?php echo esc_html($log['module_name'] ?? 'unknown'); ?>
                                    </span>
                                </td>
                                <?php endif; ?>
                                <td>
                                    <strong style="font-size: 12px;"><?php echo esc_html($log['user_login']); ?></strong>
                                    <div style="color: #646970; font-size: 11px;"><?php echo esc_html($log['ip_address']); ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 13px; line-height: 1.5; margin-bottom: 5px;">
                                        <?php echo esc_html($log['message']); ?>
                                    </div>
                                    <?php if (!empty($log['context'])): ?>
                                        <details style="font-size: 11px; color: #646970; margin-top: 5px;">
                                            <summary style="cursor: pointer; color: #2271b1;">▶ Context data</summary>
                                            <pre style="background: #f6f7f7; padding: 8px; margin-top: 5px; border-radius: 3px; overflow-x: auto;"><?php echo esc_html(json_encode($log['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                                        </details>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($log['file'])): ?>
                                        <code style="font-size: 11px; display: block; background: #f0f0f1; padding: 3px 6px; border-radius: 3px; word-break: break-all;">
                                            <?php echo esc_html(basename($log['file'])); ?>:<?php echo esc_html($log['line']); ?>
                                        </code>
                                        <?php if (!empty($log['function'])): ?>
                                            <div style="font-size: 11px; color: #646970; margin-top: 3px;">
                                                <?php echo esc_html($log['function']); ?>()
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($log['trace'])): ?>
                                            <details style="font-size: 10px; color: #646970; margin-top: 3px;">
                                                <summary style="cursor: pointer;">▶ Trace</summary>
                                                <div style="margin-top: 3px;"><?php echo esc_html($log['trace']); ?></div>
                                            </details>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: #c3c4c7;">--</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code style="font-size: 10px;">
                                        <?php echo esc_html($log['ip_address']); ?>
                                    </code>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div style="padding: 15px; border-top: 1px solid #ccd0d4; display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #646970; font-size: 13px;">
                    Trang <?php echo $page; ?> / <?php echo $total_pages; ?>
                </div>

                <div class="tablenav">
                    <div class="tablenav-pages">
                        <?php
                        $base_url = remove_query_arg('paged');

                        // First page
                        if ($page > 1) {
                            echo '<a class="button" href="' . esc_url(add_query_arg('paged', 1, $base_url)) . '">« Đầu</a> ';
                            echo '<a class="button" href="' . esc_url(add_query_arg('paged', $page - 1, $base_url)) . '">‹ Trước</a> ';
                        }

                        // Page numbers
                        $range = 2; // Show 2 pages before and after current
                        $start = max(1, $page - $range);
                        $end = min($total_pages, $page + $range);

                        for ($i = $start; $i <= $end; $i++) {
                            if ($i == $page) {
                                echo '<span class="button button-primary" style="margin: 0 2px;">' . $i . '</span> ';
                            } else {
                                echo '<a class="button" href="' . esc_url(add_query_arg('paged', $i, $base_url)) . '" style="margin: 0 2px;">' . $i . '</a> ';
                            }
                        }

                        // Last page
                        if ($page < $total_pages) {
                            echo '<a class="button" href="' . esc_url(add_query_arg('paged', $page + 1, $base_url)) . '">Sau ›</a> ';
                            echo '<a class="button" href="' . esc_url(add_query_arg('paged', $total_pages, $base_url)) . '">Cuối »</a>';
                        }
                        ?>
                    </div>
                </div>

                <div style="color: #646970; font-size: 13px;">
                    <?php echo number_format($offset + 1); ?>-<?php echo number_format(min($offset + $per_page, $total_logs)); ?> / <?php echo number_format($total_logs); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.tgs-level-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    line-height: 1.4;
}

.tgs-level-critical {
    background: #dc2626;
    color: #fff;
    border: 1px solid #b91c1c;
}

.tgs-level-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.tgs-level-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.tgs-level-notice {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.tgs-level-info {
    background: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}

.tgs-security-row {
    background: #fef2f2 !important;
    border-left: 3px solid #d63638 !important;
}

.wp-list-table td {
    vertical-align: top;
    padding: 12px 10px;
}

.dashicons {
    vertical-align: middle;
}

details summary {
    user-select: none;
}

details[open] summary {
    margin-bottom: 5px;
}

.tablenav {
    display: inline-block;
}

.tablenav-pages .button {
    margin: 0 2px;
    min-width: 32px;
    text-align: center;
}

.tablenav-pages .button-primary {
    cursor: default;
    pointer-events: none;
}
</style>
