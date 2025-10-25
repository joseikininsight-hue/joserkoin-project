<?php
/**
 * Grant Insight Perfect - Functions File (Consolidated & Clean Edition)
 * 
 * Simplified structure with consolidated files in single /inc/ directory
 * - Removed unused code and duplicate functionality
 * - Merged related files for better organization
 * - Eliminated folder over-organization
 * 
 * @package Grant_Insight_Perfect
 * @version 9.0.0 (Consolidated Edition)
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// テーマバージョン定数
if (!defined('GI_THEME_VERSION')) {
    define('GI_THEME_VERSION', '9.1.0'); // Municipality slug standardization update
}
if (!defined('GI_THEME_PREFIX')) {
    define('GI_THEME_PREFIX', 'gi_');
}

// EMERGENCY: File editing temporarily disabled to prevent memory exhaustion
// All theme editor functionality removed until memory issue is resolved

// 🔧 MEMORY OPTIMIZATION
// Increase memory limit for admin area only
if (is_admin() && !wp_doing_ajax()) {
    @ini_set('memory_limit', '256M');
    
    // Limit WordPress features that consume memory
    add_action('init', function() {
        // Disable post revisions temporarily
        if (!defined('WP_POST_REVISIONS')) {
            define('WP_POST_REVISIONS', 3);
        }
        
        // Reduce autosave interval
        if (!defined('AUTOSAVE_INTERVAL')) {
            define('AUTOSAVE_INTERVAL', 300); // 5 minutes
        }
    }, 1);
}

// 統合されたファイルの読み込み（シンプルな配列）
$inc_dir = get_template_directory() . '/inc/';

$required_files = array(
    // Core files
    'theme-foundation.php',        // テーマ設定、投稿タイプ、タクソノミー
    'data-processing.php',         // データ処理・ヘルパー関数
    
    // Admin & UI
    'admin-functions.php',         // 管理画面カスタマイズ + メタボックス (統合済み)
    'acf-fields.php',              // ACF設定とフィールド定義
    
    // Core functionality
    'card-display.php',            // カードレンダリング・表示機能
    'ajax-functions.php',          // AJAX処理
    'ai-functions.php',            // AI機能・検索履歴 (統合済み)
    
    // Performance optimization
    'performance-optimization.php', // パフォーマンス最適化（v9.2.0+）
    'seo-optimization.php',         // SEO最適化（v9.2.1+）
    
    // Google Sheets integration (consolidated into one file)
    'google-sheets-integration.php', // Google Sheets統合（全機能統合版）
    'safe-sync-manager.php',         // 安全同期管理システム
    'disable-auto-sync.php'          // 自動同期無効化
);

// ファイルを安全に読み込み
foreach ($required_files as $file) {
    $file_path = $inc_dir . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        // デバッグモードの場合のみエラーログに記録
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Grant Insight: Missing required file: ' . $file);
        }
    }
}

