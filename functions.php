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

// Purpose page rewrite rules
add_action('init', 'gi_register_purpose_rewrite_rules');
function gi_register_purpose_rewrite_rules() {
    add_rewrite_rule(
        '^purpose/([^/]+)/?$',
        'index.php?gi_purpose=$matches[1]',
        'top'
    );
}

// AUTO-FLUSH: Rewrite rules for purpose pages (remove after first load)
add_action('init', function() {
    if (get_option('gi_purpose_rewrite_flushed') !== 'yes') {
        flush_rewrite_rules(false);
        update_option('gi_purpose_rewrite_flushed', 'yes');
    }
}, 99);

// Register purpose query var
add_filter('query_vars', 'gi_register_purpose_query_var');
function gi_register_purpose_query_var($vars) {
    $vars[] = 'gi_purpose';
    return $vars;
}

// Template redirect for purpose pages
add_action('template_redirect', 'gi_purpose_template_redirect');
function gi_purpose_template_redirect() {
    $purpose_slug = get_query_var('gi_purpose');
    if ($purpose_slug) {
        $template = locate_template('page-purpose.php');
        if ($template) {
            include $template;
            exit;
        }
    }
}

/**
 * Get purpose-to-category mapping
 * Maps purpose slugs to actual grant_category taxonomy term slugs from database
 * 
 * @return array Associative array of purpose_slug => array of category_slugs
 */
function gi_get_purpose_category_mapping() {
    // Static cache to avoid repeated queries
    static $mapping = null;
    
    if ($mapping !== null) {
        return $mapping;
    }
    
    // Define mapping between purpose slugs and category term slugs
    // These category slugs should match actual terms in the grant_category taxonomy
    $mapping = array(
        'equipment' => array('monozukuri', 'it-subsidy', 'equipment-investment'),
        'training' => array('human-resources', 'training', 'career-up'),
        'sales' => array('sales-expansion', 'small-business', 'ec-it'),
        'startup' => array('startup', 'new-business', 'business-restructuring'),
        'digital' => array('it-subsidy', 'dx', 'digitalization'),
        'funding' => array('financing', 'funding', 'subsidy-grant'),
        'environment' => array('environment-energy', 'energy-saving', 'carbon-neutral'),
        'succession' => array('business-succession', 'successor-support', 'business-transfer'),
        'global' => array('global-expansion', 'export-support', 'inbound'),
        'rnd' => array('research-development', 'innovation', 'technology-development'),
        'workstyle' => array('work-style-reform', 'telework', 'workplace-improvement'),
        'regional' => array('regional-revitalization', 'tourism', 'community-development'),
        'individual' => array('individual-business', 'freelance', 'personal-subsidy')
    );
    
    return $mapping;
}

/**
 * Get grant categories for a specific purpose
 * 
 * @param string $purpose_slug The purpose slug
 * @return array Array of WP_Term objects, or empty array if not found
 */
function gi_get_categories_for_purpose($purpose_slug) {
    $mapping = gi_get_purpose_category_mapping();
    
    if (!isset($mapping[$purpose_slug])) {
        return array();
    }
    
    $category_slugs = $mapping[$purpose_slug];
    
    // Query actual terms from database
    $terms = get_terms(array(
        'taxonomy' => 'grant_category',
        'slug' => $category_slugs,
        'hide_empty' => false
    ));
    
    if (is_wp_error($terms)) {
        return array();
    }
    
    return $terms;
}

/**
 * Get category slugs for a specific purpose
 * 
 * @param string $purpose_slug The purpose slug
 * @return array Array of category slugs
 */
function gi_get_category_slugs_for_purpose($purpose_slug) {
    $terms = gi_get_categories_for_purpose($purpose_slug);
    $slugs = array();
    
    if (empty($terms)) {
        error_log('[Purpose Debug] No categories found for purpose: ' . $purpose_slug);
        return $slugs; // Return empty array
    }
    
    foreach ($terms as $term) {
        $slugs[] = $term->slug;
    }
    
    error_log('[Purpose Debug] Found ' . count($slugs) . ' category slugs for purpose: ' . $purpose_slug);
    error_log('[Purpose Debug] Category slugs: ' . implode(', ', $slugs));
    
    return $slugs;
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
    'safe-sync-manager.php'          // 安全同期管理システム
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

