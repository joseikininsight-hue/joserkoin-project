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
    
    // Define mapping between purpose slugs and category term names (Japanese)
    // v2.1: Updated to match new 8 main + 5 additional purpose structure
    // Categories are stored as Japanese names to match the actual WordPress taxonomy terms
    $mapping = array(
        // ===== 8 Main Purposes =====
        'equipment' => array(
            '設備投資', 'ものづくり・新商品開発', 'IT導入・DX', 
            '生産性向上・業務効率化', '防犯・防災・BCP', 
            '省エネ・再エネ', '医療・福祉', '観光・インバウンド', 
            '農業・林業・漁業'
        ),
        'training' => array(
            '人材育成・人材確保', '雇用維持・促進', 
            '働き方改革・待遇改善', '女性活躍・多様性', 
            '若者・学生支援', 'シニア・障害者支援', 
            'IT導入・DX', '生産性向上・業務効率化'
        ),
        'sales' => array(
            '販路拡大', '事業拡大', '新規事業・第二創業', 
            'ものづくり・新商品開発', '広告・マーケティング', 
            'EC・オンライン販売', '展示会・商談会', 
            '海外展開', '観光・インバウンド'
        ),
        'startup' => array(
            '創業・スタートアップ', '新規事業・第二創業', 
            '事業拡大', '販路拡大', '資金調達', 
            'IT導入・DX', '人材育成・人材確保', 
            '起業・独立'
        ),
        'digital' => array(
            'IT導入・DX', '生産性向上・業務効率化', 
            'EC・オンライン販売', '働き方改革・待遇改善', 
            'クラウド・SaaS', 'セキュリティ', 
            'AI・IoT・先端技術', '設備投資'
        ),
        'funding' => array(
            '資金調達', '運転資金', '設備投資', 
            '事業拡大', '創業・スタートアップ', 
            '事業再構築・転換', '新規事業・第二創業'
        ),
        'environment' => array(
            '省エネ・再エネ', '環境保護・脱炭素', 
            '設備投資', '生産性向上・業務効率化', 
            'SDGs', '循環型経済', '農業・林業・漁業'
        ),
        'global' => array(
            '海外展開', '輸出促進', '観光・インバウンド', 
            '販路拡大', 'クールジャパン・コンテンツ', 
            '国際交流', '展示会・商談会'
        ),
        
        // ===== 5 Additional Purposes =====
        'succession' => array(
            '事業承継', 'M&A', '経営改善', 
            '事業再構築・転換', '後継者育成', 
            '人材育成・人材確保'
        ),
        'rnd' => array(
            '研究開発', 'AI・IoT・先端技術', 
            'ものづくり・新商品開発', '設備投資', 
            '産学連携', 'イノベーション', 
            '特許・知的財産'
        ),
        'housing' => array(
            '住宅支援', 'リフォーム・改修', 
            '省エネ・再エネ', '防犯・防災・BCP', 
            '空き家対策', '子育て支援', 
            '移住・定住'
        ),
        'agriculture' => array(
            '農業・林業・漁業', '6次産業化', 
            '設備投資', '販路拡大', 
            '省エネ・再エネ', '人材育成・人材確保', 
            '地域活性化'
        ),
        'individual' => array(
            '起業・独立', 'フリーランス', 
            '資格取得・スキルアップ', '若者・学生支援', 
            '創業・スタートアップ', 'テレワーク・在宅ワーク', 
            '副業・兼業'
        )
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
        error_log('[Purpose Debug] No mapping found for purpose: ' . $purpose_slug);
        return array();
    }
    
    $category_names = $mapping[$purpose_slug];
    
    // Query actual terms from database using Japanese names
    $terms = get_terms(array(
        'taxonomy' => 'grant_category',
        'name' => $category_names,
        'hide_empty' => false
    ));
    
    if (is_wp_error($terms)) {
        error_log('[Purpose Debug] Error querying categories: ' . $terms->get_error_message());
        return array();
    }
    
    error_log('[Purpose Debug] Found ' . count($terms) . ' category terms for purpose: ' . $purpose_slug);
    
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
    
    // Google Sheets integration (consolidated into one file)
    'google-sheets-integration.php', // Google Sheets統合（全機能統合版）
    'safe-sync-manager.php',         // 安全同期管理システム
    
    // Grant Content SEO Optimizer (v9.3.0+)
    'grant-content-seo-optimizer.php',  // 助成金コンテンツSEO最適化
    
    // Dynamic CSS Generator (v9.3.1+)
    'grant-dynamic-css-generator.php',  // 投稿内容に応じた動的CSS生成
    
    // Advanced SEO Enhancer (v9.3.2+)
    'grant-advanced-seo-enhancer.php'   // SEO大幅強化（OGP、Schema.org拡張、内部リンク）
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

