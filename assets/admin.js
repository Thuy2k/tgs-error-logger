/**
 * TGS Error Logger - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Auto-refresh logs every 30 seconds if checkbox is enabled
        var autoRefreshInterval = null;

        $('#tgs-auto-refresh').on('change', function() {
            if ($(this).is(':checked')) {
                autoRefreshInterval = setInterval(function() {
                    location.reload();
                }, 30000);
            } else {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
            }
        });

        // Clear logs confirmation
        $('.tgs-clear-logs').on('click', function(e) {
            e.preventDefault();

            if (!confirm('Bạn có chắc muốn xóa log cũ? Hành động này không thể hoàn tác.')) {
                return;
            }

            var days = prompt('Xóa log cũ hơn bao nhiêu ngày?', '90');
            if (!days || isNaN(days)) {
                return;
            }

            $.ajax({
                url: tgsErrorLogger.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'tgs_error_logger_clear_logs',
                    nonce: tgsErrorLogger.nonce,
                    days: parseInt(days)
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi xóa logs.');
                }
            });
        });

        // Highlight search terms
        var searchTerm = new URLSearchParams(window.location.search).get('search');
        if (searchTerm) {
            highlightSearchTerm(searchTerm);
        }
    });

    function highlightSearchTerm(term) {
        if (!term) return;

        var regex = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');

        $('table tbody td').each(function() {
            var $td = $(this);
            var html = $td.html();

            // Skip if already has HTML tags (avoid breaking structure)
            if (html.indexOf('<') !== -1) return;

            var highlighted = html.replace(regex, '<mark style="background: #ff0; padding: 2px 4px; border-radius: 2px;">$1</mark>');
            $td.html(highlighted);
        });
    }

})(jQuery);
