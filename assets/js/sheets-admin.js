/**
 * Google Sheets Admin JavaScript
 * スプレッドシート同期管理画面の機能
 */

(function($) {
    'use strict';

    /**
     * Google Sheets Admin Controller
     */
    const GISheetsAdmin = {
        /**
         * 初期化
         */
        init() {
            console.log('[GI Sheets Admin] Initializing...');
            
            if (typeof giSheetsAdmin === 'undefined') {
                console.error('[GI Sheets Admin] giSheetsAdmin object not found');
                return;
            }
            
            this.bindEvents();
            console.log('[GI Sheets Admin] Initialized successfully');
        },

        /**
         * イベントバインディング
         */
        bindEvents() {
            // 接続テストボタン
            $('#gi-test-connection').on('click', (e) => {
                e.preventDefault();
                this.testConnection();
            });

            // WP to Sheets 同期ボタン
            $('#gi-sync-wp-to-sheets').on('click', (e) => {
                e.preventDefault();
                this.syncData('wp_to_sheets');
            });

            // Sheets to WP 同期ボタン
            $('#gi-sync-sheets-to-wp').on('click', (e) => {
                e.preventDefault();
                this.syncData('sheets_to_wp');
            });
        },

        /**
         * 接続テスト
         */
        testConnection() {
            console.log('[GI Sheets Admin] Testing connection...');
            
            const $button = $('#gi-test-connection');
            const $result = $('#gi-test-result');
            
            // ボタンを無効化
            $button.prop('disabled', true);
            $button.html('<span class="gi-loading-spinner"></span> ' + giSheetsAdmin.strings.testing);
            
            // 結果エリアをクリア
            $result.removeClass('show gi-test-result-success gi-test-result-error').text('');
            
            // AJAX リクエスト
            $.ajax({
                url: giSheetsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gi_test_sheets_connection',
                    nonce: giSheetsAdmin.nonce
                },
                success: (response) => {
                    console.log('[GI Sheets Admin] Connection test response:', response);
                    
                    if (response.success) {
                        $result
                            .addClass('show gi-test-result-success')
                            .html('<strong>✓ ' + giSheetsAdmin.strings.success + '</strong><br>' + response.data.message);
                    } else {
                        $result
                            .addClass('show gi-test-result-error')
                            .html('<strong>✗ ' + giSheetsAdmin.strings.error + '</strong><br>' + response.data.message);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('[GI Sheets Admin] Connection test error:', error);
                    $result
                        .addClass('show gi-test-result-error')
                        .html('<strong>✗ ' + giSheetsAdmin.strings.error + '</strong><br>AJAX エラー: ' + error);
                },
                complete: () => {
                    // ボタンを再有効化
                    $button.prop('disabled', false);
                    $button.text('接続をテスト');
                }
            });
        },

        /**
         * データ同期
         */
        syncData(direction) {
            console.log('[GI Sheets Admin] Starting sync:', direction);
            
            // 確認ダイアログ
            if (!confirm(giSheetsAdmin.strings.confirm_sync)) {
                return;
            }
            
            const $button = direction === 'wp_to_sheets' 
                ? $('#gi-sync-wp-to-sheets') 
                : $('#gi-sync-sheets-to-wp');
            const $progressContainer = $('#gi-progress-container');
            const $progressBar = $('#gi-progress-fill');
            const $progressText = $('#gi-progress-text');
            const $logContainer = $('#gi-log-messages');
            
            // ボタンを無効化
            $button.prop('disabled', true);
            $button.html('<span class="gi-loading-spinner"></span> ' + giSheetsAdmin.strings.syncing);
            
            // プログレスバーを表示
            $progressContainer.show();
            $progressBar.css('width', '0%');
            $progressText.text('0%');
            
            // ログをクリア
            $logContainer.empty();
            
            // AJAX リクエスト
            $.ajax({
                url: giSheetsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gi_manual_sheets_sync',
                    direction: direction,
                    nonce: giSheetsAdmin.nonce
                },
                success: (response) => {
                    console.log('[GI Sheets Admin] Sync response:', response);
                    
                    if (response.success) {
                        // 成功
                        $progressBar.css('width', '100%');
                        $progressText.text('100%');
                        
                        this.addLogEntry('success', response.data.message);
                        
                        if (response.data.details) {
                            this.addLogEntry('info', '詳細: ' + JSON.stringify(response.data.details));
                        }
                        
                        // 3秒後にプログレスバーを非表示
                        setTimeout(() => {
                            $progressContainer.fadeOut();
                        }, 3000);
                    } else {
                        // エラー
                        $progressBar.css('width', '100%');
                        $progressText.text('エラー');
                        $progressBar.css('background', '#d63638');
                        
                        this.addLogEntry('error', response.data.message || '同期に失敗しました');
                        
                        if (response.data.details) {
                            this.addLogEntry('error', '詳細: ' + JSON.stringify(response.data.details));
                        }
                    }
                },
                error: (xhr, status, error) => {
                    console.error('[GI Sheets Admin] Sync error:', error);
                    
                    $progressBar.css('width', '100%');
                    $progressText.text('エラー');
                    $progressBar.css('background', '#d63638');
                    
                    this.addLogEntry('error', 'AJAX エラー: ' + error);
                    
                    if (xhr.responseText) {
                        this.addLogEntry('error', 'レスポンス: ' + xhr.responseText);
                    }
                },
                complete: () => {
                    // ボタンを再有効化
                    $button.prop('disabled', false);
                    
                    if (direction === 'wp_to_sheets') {
                        $button.html('<i class="dashicons dashicons-upload"></i> WP → Sheets 同期');
                    } else {
                        $button.html('<i class="dashicons dashicons-download"></i> Sheets → WP 同期');
                    }
                }
            });
        },

        /**
         * ログエントリーを追加
         */
        addLogEntry(type, message) {
            const $logContainer = $('#gi-log-messages');
            const timestamp = new Date().toLocaleTimeString('ja-JP');
            
            let typeClass = '';
            let typeIcon = '';
            
            switch(type) {
                case 'success':
                    typeClass = 'gi-log-success';
                    typeIcon = '✓';
                    break;
                case 'error':
                    typeClass = 'gi-log-error';
                    typeIcon = '✗';
                    break;
                case 'warning':
                    typeClass = 'gi-log-warning';
                    typeIcon = '⚠';
                    break;
                default:
                    typeClass = 'gi-log-message';
                    typeIcon = 'ℹ';
            }
            
            const $entry = $('<div class="gi-log-entry">')
                .html(
                    '<span class="gi-log-timestamp">[' + timestamp + ']</span>' +
                    '<span class="' + typeClass + '">' + typeIcon + ' ' + message + '</span>'
                );
            
            $logContainer.prepend($entry);
            
            // 最大50エントリーまで保持
            if ($logContainer.children().length > 50) {
                $logContainer.children().last().remove();
            }
        }
    };

    // ドキュメント読み込み完了時に初期化
    $(document).ready(() => {
        GISheetsAdmin.init();
    });

})(jQuery);
