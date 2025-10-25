<?php
/**
 * SEO Optimization Module v2.0 - Enhanced Edition
 * 
 * SEOスコア改善のためのメタタグと構造化データ
 * + タイトル・ディスクリプション自動生成機能
 * + 内部リンク自動挿入機能
 * 
 * 変更履歴:
 * - v2.0.0: タイトル自動生成機能追加（save_post_grant フック）
 * - v2.0.0: ディスクリプション自動生成機能追加
 * - v2.0.0: 内部リンク自動挿入機能追加（the_content フィルター）
 * - v2.0.0: 一括更新管理画面追加
 * - v1.0.0: 基本的なSEOタグ・構造化データ実装
 * 
 * @package Grant_Insight_Perfect
 * @version 2.0.0
 * @since 9.2.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class GI_SEO_Optimizer {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // メタタグ追加
        add_action('wp_head', [$this, 'add_seo_meta_tags'], 5);
        
        // Open Graphタグ追加
        add_action('wp_head', [$this, 'add_open_graph_tags'], 6);
        
        // 構造化データ追加
        add_action('wp_head', [$this, 'add_structured_data'], 10);
        
        // カノニカルURL追加
        add_action('wp_head', [$this, 'add_canonical_url'], 7);
        
        // ========== v2.0.0 新機能 ==========
        // SEO自動生成（投稿保存時）
        add_action('save_post_grant', [$this, 'auto_generate_seo_on_save'], 20, 3);
        
        // 内部リンク自動挿入
        add_filter('the_content', [$this, 'add_internal_links_to_content'], 20);
        
        // 管理画面：一括更新ページ
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // AJAX：一括更新処理
        add_action('wp_ajax_gi_bulk_update_seo', [$this, 'ajax_bulk_update_seo']);
    }
    
    /**
     * ========================================
     * SEOメタタグ
     * ========================================
     */
    
    /**
     * 基本的なSEOメタタグを追加
     */
    public function add_seo_meta_tags() {
        // 文字コード
        echo '<meta charset="' . get_bloginfo('charset') . '">' . "\n";
        
        // ビューポート（モバイル対応）
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">' . "\n";
        
        // テーマカラー
        echo '<meta name="theme-color" content="#000000">' . "\n";
        
        // 説明文
        $description = $this->get_page_description();
        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }
        
        // キーワード（個別投稿の場合はタグから生成）
        if (is_singular()) {
            $keywords = $this->get_page_keywords();
            if ($keywords) {
                echo '<meta name="keywords" content="' . esc_attr($keywords) . '">' . "\n";
            }
        }
        
        // robots（インデックス制御）
        if (is_search() || is_404()) {
            echo '<meta name="robots" content="noindex, follow">' . "\n";
        } else {
            echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
        }
    }
    
    /**
     * Open Graphタグを追加
     */
    public function add_open_graph_tags() {
        // サイト名
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        
        // タイトル
        $title = $this->get_page_title();
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        
        // 説明文
        $description = $this->get_page_description();
        if ($description) {
            echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        }
        
        // タイプ
        $og_type = is_singular() ? 'article' : 'website';
        echo '<meta property="og:type" content="' . $og_type . '">' . "\n";
        
        // URL
        $url = $this->get_current_url();
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        
        // 画像
        $image = $this->get_page_image();
        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
            echo '<meta property="og:image:width" content="1200">' . "\n";
            echo '<meta property="og:image:height" content="630">' . "\n";
        }
        
        // ロケール
        echo '<meta property="og:locale" content="ja_JP">' . "\n";
        
        // Twitter Card
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        if ($description) {
            echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        }
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }
    }
    
    /**
     * カノニカルURLを追加
     */
    public function add_canonical_url() {
        $canonical = $this->get_current_url();
        echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    }
    
    /**
     * ========================================
     * 構造化データ（JSON-LD）
     * ========================================
     */
    
    /**
     * 構造化データを追加
     */
    public function add_structured_data() {
        // 組織情報（全ページ共通）
        $this->output_organization_schema();
        
        // WebSiteスキーマ（ホームページ）
        if (is_front_page()) {
            $this->output_website_schema();
        }
        
        // 記事スキーマ（個別投稿）
        if (is_singular('grant')) {
            $this->output_article_schema();
        }
        
        // パンくずリストスキーマ
        if (!is_front_page()) {
            $this->output_breadcrumb_schema();
        }
    }
    
    /**
     * 組織情報スキーマ
     */
    private function output_organization_schema() {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => 'https://joseikin-insight.com/wp-content/uploads/2025/09/名称未設定のデザイン.png',
                'width' => 200,
                'height' => 60
            ],
            'description' => get_bloginfo('description'),
            'sameAs' => []
        ];
        
        $this->output_json_ld($schema);
    }
    
    /**
     * WebSiteスキーマ
     */
    private function output_website_schema() {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'description' => get_bloginfo('description'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            ]
        ];
        
        $this->output_json_ld($schema);
    }
    
    /**
     * 記事スキーマ
     */
    private function output_article_schema() {
        if (!is_singular()) {
            return;
        }
        
        $post_id = get_the_ID();
        $post = get_post($post_id);
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'description' => $this->get_page_description(),
            'image' => $this->get_page_image(),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'author' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name')
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://joseikin-insight.com/wp-content/uploads/2025/09/名称未設定のデザイン.png',
                    'width' => 200,
                    'height' => 60
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => get_permalink()
            ]
        ];
        
        $this->output_json_ld($schema);
    }
    
    /**
     * パンくずリストスキーマ
     */
    private function output_breadcrumb_schema() {
        $breadcrumbs = [
            [
                'name' => 'ホーム',
                'url' => home_url('/')
            ]
        ];
        
        // アーカイブページの場合
        if (is_archive()) {
            $breadcrumbs[] = [
                'name' => get_the_archive_title(),
                'url' => get_permalink()
            ];
        }
        
        // 個別投稿の場合
        if (is_singular()) {
            // カスタム投稿タイプのアーカイブ
            $post_type = get_post_type();
            $post_type_obj = get_post_type_object($post_type);
            
            if ($post_type_obj && $post_type !== 'post' && $post_type !== 'page') {
                $breadcrumbs[] = [
                    'name' => $post_type_obj->label,
                    'url' => get_post_type_archive_link($post_type)
                ];
            }
            
            // 現在のページ
            $breadcrumbs[] = [
                'name' => get_the_title(),
                'url' => get_permalink()
            ];
        }
        
        // スキーマ生成
        $items = [];
        foreach ($breadcrumbs as $index => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url']
            ];
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
        
        $this->output_json_ld($schema);
    }
    
    /**
     * JSON-LDを出力
     */
    private function output_json_ld($schema) {
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n" . '</script>' . "\n";
    }
    
    /**
     * ========================================
     * v2.0.0 新機能: SEO自動生成
     * ========================================
     */
    
    /**
     * 投稿保存時にSEOタイトル・ディスクリプションを自動生成
     * 
     * @param int $post_id 投稿ID
     * @param WP_Post $post 投稿オブジェクト
     * @param bool $update 更新かどうか
     */
    public function auto_generate_seo_on_save($post_id, $post, $update) {
        // 自動保存・リビジョンをスキップ
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        
        // 投稿ステータスが公開済みでない場合はスキップ
        if ($post->post_status !== 'publish') {
            return;
        }
        
        // デバッグログ
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("SEO自動生成開始: Post ID {$post_id}");
        }
        
        // タイトル生成
        $seo_title = $this->generate_optimized_title($post_id);
        if ($seo_title) {
            update_post_meta($post_id, '_gi_seo_title', $seo_title);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("SEOタイトル生成: {$seo_title}");
            }
        }
        
        // ディスクリプション生成
        $seo_description = $this->generate_optimized_description($post_id);
        if ($seo_description) {
            update_post_meta($post_id, '_gi_seo_description', $seo_description);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("SEOディスクリプション生成: {$seo_description}");
            }
        }
        
        // 生成日時を記録
        update_post_meta($post_id, '_gi_seo_generated_at', current_time('mysql'));
    }
    
    /**
     * SEO最適化されたタイトルを生成
     * 
     * @param int $post_id 投稿ID
     * @return string 最適化されたタイトル（40文字以内）
     */
    private function generate_optimized_title($post_id) {
        if (!function_exists('get_field')) {
            return get_the_title($post_id);
        }
        
        $original_title = get_the_title($post_id);
        
        // 自治体情報取得
        $municipalities = wp_get_post_terms($post_id, 'grant_municipality');
        $municipality_name = '';
        if (!empty($municipalities) && !is_wp_error($municipalities)) {
            $municipality_name = $municipalities[0]->name;
            // "◯◯市"や"◯◯都"などの接尾辞を除去
            $municipality_name = preg_replace('/(市|区|町|村|都|道|府|県)$/', '', $municipality_name);
        }
        
        // 金額情報取得
        $amount_numeric = intval(get_field('max_amount_numeric', $post_id));
        $amount_text = '';
        if ($amount_numeric > 0) {
            if ($amount_numeric >= 100000000) {
                $amount_text = number_format($amount_numeric / 100000000, 1) . '億円';
            } elseif ($amount_numeric >= 10000) {
                $amount_text = number_format($amount_numeric / 10000) . '万円';
            } else {
                $amount_text = number_format($amount_numeric) . '円';
            }
        }
        
        // 締切情報取得
        $deadline_date = get_field('deadline_date', $post_id);
        $deadline_text = '';
        if ($deadline_date) {
            $deadline_timestamp = strtotime($deadline_date);
            if ($deadline_timestamp) {
                $deadline_text = date('n/j', $deadline_timestamp);
            }
        }
        
        // 助成金種別を判定
        $grant_type = $this->detect_grant_type($original_title);
        
        // タイトルテンプレート生成
        $year = date('Y');
        
        // パターン1: 金額＋締切あり
        if ($municipality_name && $amount_text && $deadline_text) {
            $title = "【{$municipality_name}】{$grant_type}｜最大{$amount_text}｜締切{$deadline_text}";
        }
        // パターン2: 金額のみ
        elseif ($municipality_name && $amount_text) {
            $title = "【{$municipality_name}】{$grant_type}｜最大{$amount_text}【{$year}年】";
        }
        // パターン3: 自治体名のみ
        elseif ($municipality_name) {
            $title = "【{$municipality_name}】{$grant_type}｜申請ガイド【{$year}年】";
        }
        // パターン4: フォールバック
        else {
            $title = "{$grant_type}｜申請方法と条件【{$year}年最新】";
        }
        
        // 40文字制限
        if (mb_strlen($title) > 40) {
            $title = mb_substr($title, 0, 40);
        }
        
        return $title;
    }
    
    /**
     * SEO最適化されたメタディスクリプションを生成
     * 
     * @param int $post_id 投稿ID
     * @return string 最適化されたディスクリプション（120-160文字）
     */
    private function generate_optimized_description($post_id) {
        if (!function_exists('get_field')) {
            return '';
        }
        
        $original_title = get_the_title($post_id);
        
        // 自治体情報
        $municipalities = wp_get_post_terms($post_id, 'grant_municipality');
        $municipality_name = '';
        if (!empty($municipalities) && !is_wp_error($municipalities)) {
            $municipality_name = $municipalities[0]->name;
        }
        
        // ACFデータ取得
        $amount_numeric = intval(get_field('max_amount_numeric', $post_id));
        $amount_text = '';
        if ($amount_numeric > 0) {
            if ($amount_numeric >= 100000000) {
                $amount_text = number_format($amount_numeric / 100000000, 1) . '億円';
            } elseif ($amount_numeric >= 10000) {
                $amount_text = number_format($amount_numeric / 10000) . '万円';
            } else {
                $amount_text = number_format($amount_numeric) . '円';
            }
        }
        
        $subsidy_rate = get_field('subsidy_rate', $post_id);
        $grant_target = get_field('grant_target', $post_id);
        $deadline_date = get_field('deadline_date', $post_id);
        $adoption_rate = floatval(get_field('adoption_rate', $post_id));
        
        // 締切情報
        $deadline_text = '';
        if ($deadline_date) {
            $deadline_timestamp = strtotime($deadline_date);
            if ($deadline_timestamp) {
                $deadline_text = date('Y年n月j日', $deadline_timestamp);
            }
        }
        
        // 助成金種別
        $grant_type = $this->detect_grant_type($original_title);
        
        // ディスクリプション組み立て
        $year = date('Y');
        $parts = [];
        
        // 基本情報
        if ($municipality_name) {
            $parts[] = "{$municipality_name}の{$grant_type}";
        } else {
            $parts[] = $grant_type;
        }
        
        // 金額
        if ($amount_text) {
            $parts[] = "(最大{$amount_text})";
        }
        
        $description = implode('', $parts) . "の申請条件・必要書類を{$year}年最新版で解説。";
        
        // 追加情報
        $additional = [];
        
        if ($subsidy_rate) {
            $additional[] = "補助率{$subsidy_rate}";
        }
        
        if ($grant_target) {
            $target_short = mb_substr($grant_target, 0, 20);
            $additional[] = "{$target_short}が対象";
        }
        
        if ($deadline_text) {
            $additional[] = "締切{$deadline_text}";
        }
        
        if ($adoption_rate > 0) {
            $additional[] = "採択率{$adoption_rate}%";
        }
        
        if (!empty($additional)) {
            $description .= implode('、', $additional) . '。';
        }
        
        // 120-160文字に調整
        if (mb_strlen($description) < 120) {
            $description .= '申請手順と成功のコツを詳しく解説します。';
        }
        
        if (mb_strlen($description) > 160) {
            $description = mb_substr($description, 0, 157) . '...';
        }
        
        return $description;
    }
    
    /**
     * タイトルから助成金の種別を判定
     * 
     * @param string $title タイトル
     * @return string 助成金種別
     */
    private function detect_grant_type($title) {
        $patterns = [
            '/(太陽光|ソーラー|蓄電池|省エネ)/u' => '太陽光発電補助金',
            '/(採用|雇用|人材確保|就職)/u' => '採用補助金',
            '/(IT|デジタル|DX|システム)/u' => 'IT導入補助金',
            '/(創業|起業|開業|スタートアップ)/u' => '創業支援補助金',
            '/(事業再構築|転換|新分野)/u' => '事業再構築補助金',
            '/(ものづくり|製造|生産性)/u' => 'ものづくり補助金',
            '/(小規模事業者|持続化)/u' => '小規模事業者持続化補助金',
            '/(研究開発|R&D|技術開発)/u' => '研究開発補助金',
            '/(省エネ|エネルギー|環境)/u' => '省エネ補助金',
            '/(設備投資|機械導入|設備導入)/u' => '設備投資補助金',
            '/(輸出|海外展開|国際)/u' => '海外展開支援補助金',
            '/(テレワーク|リモート|在宅)/u' => 'テレワーク導入補助金',
            '/(観光|インバウンド|宿泊)/u' => '観光事業支援補助金',
            '/(農業|農産物|農林)/u' => '農業支援補助金',
            '/(子育て|育児|保育)/u' => '子育て支援補助金',
        ];
        
        foreach ($patterns as $pattern => $type) {
            if (preg_match($pattern, $title)) {
                return $type;
            }
        }
        
        // デフォルト
        return '助成金・補助金';
    }
    
    /**
     * ========================================
     * v2.0.0 新機能: 内部リンク自動挿入
     * ========================================
     */
    
    /**
     * コンテンツ末尾に関連助成金の内部リンクを追加
     * 
     * @param string $content 記事コンテンツ
     * @return string 内部リンク追加後のコンテンツ
     */
    public function add_internal_links_to_content($content) {
        // grant投稿タイプの個別ページのみ
        if (!is_singular('grant')) {
            return $content;
        }
        
        $post_id = get_the_ID();
        
        // 関連記事取得
        $related_grants = $this->get_related_grants($post_id, 5);
        
        if (empty($related_grants)) {
            return $content;
        }
        
        // HTML生成
        $html = '<div class="gi-related-grants" style="margin-top: 3rem; padding: 2rem; background: #f9fafb; border-radius: 8px;">';
        $html .= '<h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #059669;">関連する助成金・補助金</h3>';
        $html .= '<ul style="list-style: none; padding: 0; margin: 0;">';
        
        foreach ($related_grants as $grant) {
            $grant_title = get_the_title($grant->ID);
            $grant_url = get_permalink($grant->ID);
            
            // 自治体情報取得
            $municipalities = wp_get_post_terms($grant->ID, 'grant_municipality');
            $municipality_badge = '';
            if (!empty($municipalities) && !is_wp_error($municipalities)) {
                $municipality_name = $municipalities[0]->name;
                $municipality_badge = '<span style="display: inline-block; background: #059669; color: white; padding: 0.125rem 0.5rem; border-radius: 4px; font-size: 0.75rem; margin-right: 0.5rem;">' . esc_html($municipality_name) . '</span>';
            }
            
            $html .= '<li style="margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb;">';
            $html .= '<a href="' . esc_url($grant_url) . '" style="color: #047857; text-decoration: none; display: flex; align-items: center; transition: color 0.2s;">';
            $html .= $municipality_badge;
            $html .= '<span style="font-weight: 500;">' . esc_html($grant_title) . '</span>';
            $html .= '<span style="margin-left: auto; color: #059669;">→</span>';
            $html .= '</a>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        
        return $content . $html;
    }
    
    /**
     * 関連助成金記事を取得
     * 
     * @param int $post_id 現在の投稿ID
     * @param int $limit 取得件数
     * @return array WP_Postオブジェクトの配列
     */
    private function get_related_grants($post_id, $limit = 5) {
        // 現在の記事のタクソノミー情報取得
        $municipalities = wp_get_post_terms($post_id, 'grant_municipality', ['fields' => 'ids']);
        $categories = wp_get_post_terms($post_id, 'grant_category', ['fields' => 'ids']);
        
        $tax_query = ['relation' => 'OR'];
        
        // 同じ自治体
        if (!empty($municipalities) && !is_wp_error($municipalities)) {
            $tax_query[] = [
                'taxonomy' => 'grant_municipality',
                'field' => 'term_id',
                'terms' => $municipalities
            ];
        }
        
        // 同じカテゴリ
        if (!empty($categories) && !is_wp_error($categories)) {
            $tax_query[] = [
                'taxonomy' => 'grant_category',
                'field' => 'term_id',
                'terms' => $categories
            ];
        }
        
        // クエリ実行
        $args = [
            'post_type' => 'grant',
            'posts_per_page' => $limit,
            'post__not_in' => [$post_id],
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false
        ];
        
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
        
        $related_query = new WP_Query($args);
        
        return $related_query->posts;
    }
    
    /**
     * ========================================
     * v2.0.0 新機能: 一括更新管理画面
     * ========================================
     */
    
    /**
     * 管理画面にSEO一括更新ページを追加
     */
    public function add_seo_bulk_update_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=grant',
            'SEO一括更新',
            'SEO一括更新',
            'manage_options',
            'gi-seo-bulk-update',
            [$this, 'render_seo_bulk_update_page']
        );
    }
    
    /**
     * SEO一括更新ページのHTML出力
     */
    public function render_seo_bulk_update_page() {
        // 権限チェック
        if (!current_user_can('manage_options')) {
            wp_die('権限が不足しています');
        }
        
        // 助成金記事の総数取得
        $total_grants = wp_count_posts('grant');
        $total_published = $total_grants->publish;
        
        ?>
        <div class="wrap">
            <h1>SEO一括更新</h1>
            <p>全ての助成金記事のSEOタイトルとメタディスクリプションを一括で生成します。</p>
            
            <div class="card" style="max-width: 600px; margin-top: 20px;">
                <h2>更新対象</h2>
                <p><strong>公開済み助成金記事:</strong> <?php echo number_format($total_published); ?> 件</p>
                
                <div id="gi-bulk-progress" style="display: none; margin: 20px 0;">
                    <div style="background: #f0f0f0; height: 30px; border-radius: 4px; overflow: hidden;">
                        <div id="gi-progress-bar" style="background: #059669; height: 100%; width: 0%; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"></div>
                    </div>
                    <p id="gi-progress-text" style="margin-top: 10px;">準備中...</p>
                </div>
                
                <div id="gi-bulk-result" style="display: none; margin: 20px 0;"></div>
                
                <p>
                    <button id="gi-start-bulk-update" class="button button-primary button-large" onclick="giBulkUpdateSEO()">
                        一括更新を開始
                    </button>
                </p>
                
                <div class="notice notice-info inline">
                    <p><strong>注意:</strong></p>
                    <ul>
                        <li>処理には数分かかる場合があります</li>
                        <li>処理中はブラウザを閉じないでください</li>
                        <li>既存のSEOタイトル・ディスクリプションは上書きされます</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <script>
        function giBulkUpdateSEO() {
            const button = document.getElementById('gi-start-bulk-update');
            const progressDiv = document.getElementById('gi-bulk-progress');
            const progressBar = document.getElementById('gi-progress-bar');
            const progressText = document.getElementById('gi-progress-text');
            const resultDiv = document.getElementById('gi-bulk-result');
            
            button.disabled = true;
            button.textContent = '処理中...';
            progressDiv.style.display = 'block';
            resultDiv.style.display = 'none';
            
            const totalGrants = <?php echo intval($total_published); ?>;
            let processed = 0;
            let errors = 0;
            
            function processBatch(offset) {
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'gi_bulk_update_seo',
                        offset: offset,
                        _wpnonce: '<?php echo wp_create_nonce('gi_bulk_update_seo_nonce'); ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        processed += data.data.processed;
                        errors += data.data.errors || 0;
                        
                        const percent = Math.round((processed / totalGrants) * 100);
                        progressBar.style.width = percent + '%';
                        progressBar.textContent = percent + '%';
                        progressText.textContent = `処理済み: ${processed} / ${totalGrants} 件 (エラー: ${errors} 件)`;
                        
                        if (processed < totalGrants) {
                            processBatch(processed);
                        } else {
                            button.disabled = false;
                            button.textContent = '一括更新を開始';
                            resultDiv.style.display = 'block';
                            resultDiv.innerHTML = '<div class="notice notice-success inline"><p><strong>完了しました！</strong><br>処理件数: ' + processed + ' 件<br>エラー: ' + errors + ' 件</p></div>';
                        }
                    } else {
                        throw new Error(data.data.message || '処理中にエラーが発生しました');
                    }
                })
                .catch(error => {
                    button.disabled = false;
                    button.textContent = '一括更新を開始';
                    resultDiv.style.display = 'block';
                    resultDiv.innerHTML = '<div class="notice notice-error inline"><p><strong>エラー:</strong> ' + error.message + '</p></div>';
                });
            }
            
            processBatch(0);
        }
        </script>
        <?php
    }
    
    /**
     * AJAX: SEO一括更新処理
     */
    public function ajax_bulk_update_seo() {
        // 権限チェック
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => '権限が不足しています']);
            return;
        }
        
        // Nonce検証
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gi_bulk_update_seo')) {
            wp_send_json_error(['message' => 'セキュリティチェックに失敗しました']);
            return;
        }
        
        $offset = intval($_POST['offset'] ?? 0);
        $batch_size = 100;
        
        // 助成金記事を取得（100件ずつ）
        $grants = get_posts([
            'post_type' => 'grant',
            'posts_per_page' => $batch_size,
            'offset' => $offset,
            'post_status' => 'publish',
            'orderby' => 'ID',
            'order' => 'ASC',
            'no_found_rows' => true
        ]);
        
        $processed = 0;
        $success = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($grants as $grant) {
            try {
                // SEO タイトルと説明文を生成
                $seo_title = $this->generate_optimized_title($grant->ID);
                $seo_description = $this->generate_optimized_description($grant->ID);
                
                if ($seo_title && $seo_description) {
                    // カスタムフィールドに保存
                    update_post_meta($grant->ID, '_gi_seo_title', $seo_title);
                    update_post_meta($grant->ID, '_gi_seo_description', $seo_description);
                    update_post_meta($grant->ID, '_gi_seo_generated_at', current_time('mysql'));
                    $success++;
                } else {
                    $skipped++;
                }
                
                $processed++;
            } catch (Exception $e) {
                $errors++;
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("SEO一括更新エラー (Post ID {$grant->ID}): " . $e->getMessage());
                }
            }
        }
        
        wp_send_json_success([
            'processed' => $processed,
            'success' => $success,
            'skipped' => $skipped,
            'errors' => $errors,
            'offset' => $offset + $batch_size
        ]);
    }
    
    /**
     * ========================================
     * ヘルパー関数（既存 + v2.0.0拡張）
     * ========================================
     */
    
    /**
     * ページタイトルを取得
     * v2.0.0: カスタムフィールドから優先取得に変更
     */
    private function get_page_title() {
        // grant投稿タイプの場合、カスタムフィールドから取得を優先
        if (is_singular('grant')) {
            $custom_title = get_post_meta(get_the_ID(), '_gi_seo_title', true);
            if (!empty($custom_title)) {
                return $custom_title;
            }
        }
        
        // 既存のフォールバックロジック
        if (is_singular()) {
            return get_the_title();
        } elseif (is_archive()) {
            return get_the_archive_title();
        } elseif (is_search()) {
            return '検索結果: ' . get_search_query();
        } elseif (is_404()) {
            return 'ページが見つかりません';
        } else {
            return get_bloginfo('name');
        }
    }
    
    /**
     * ページ説明文を取得
     * v2.0.0: カスタムフィールドから優先取得に変更
     */
    private function get_page_description() {
        // grant投稿タイプの場合、カスタムフィールドから取得を優先
        if (is_singular('grant')) {
            $custom_desc = get_post_meta(get_the_ID(), '_gi_seo_description', true);
            if (!empty($custom_desc)) {
                return $custom_desc;
            }
        }
        
        // 既存のフォールバックロジック
        if (is_singular()) {
            $post_id = get_the_ID();
            
            // 抜粋があればそれを使用
            $excerpt = get_the_excerpt($post_id);
            if ($excerpt) {
                return wp_trim_words($excerpt, 30, '...');
            }
            
            // コンテンツから生成
            $content = get_the_content(null, false, $post_id);
            $content = wp_strip_all_tags($content);
            return wp_trim_words($content, 30, '...');
        } elseif (is_archive()) {
            $description = get_the_archive_description();
            return $description ? wp_trim_words($description, 30, '...') : get_bloginfo('description');
        } else {
            return get_bloginfo('description');
        }
    }
    
    /**
     * ページキーワードを取得
     */
    private function get_page_keywords() {
        if (!is_singular()) {
            return '';
        }
        
        $keywords = [];
        
        // タグから取得
        $tags = get_the_tags();
        if ($tags) {
            foreach ($tags as $tag) {
                $keywords[] = $tag->name;
            }
        }
        
        // カスタムタクソノミーから取得
        $taxonomies = get_object_taxonomies(get_post_type());
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy === 'post_tag') {
                continue; // 既に処理済み
            }
            
            $terms = get_the_terms(get_the_ID(), $taxonomy);
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $keywords[] = $term->name;
                }
            }
        }
        
        return implode(', ', array_unique($keywords));
    }
    
    /**
     * ページ画像を取得
     */
    private function get_page_image() {
        if (is_singular()) {
            $post_id = get_the_ID();
            
            // アイキャッチ画像
            if (has_post_thumbnail($post_id)) {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
                if ($image) {
                    return $image[0];
                }
            }
            
            // コンテンツ内の最初の画像
            $content = get_the_content(null, false, $post_id);
            preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);
            if (!empty($matches[1])) {
                return $matches[1];
            }
        }
        
        // デフォルト画像（ロゴ）
        return 'https://joseikin-insight.com/wp-content/uploads/2025/09/名称未設定のデザイン.png';
    }
    
    /**
     * 現在のURLを取得
     */
    private function get_current_url() {
        global $wp;
        return home_url(add_query_arg([], $wp->request));
    }
    
    // ============================================
    // バルクアップデート機能
    // ============================================
    
    /**
     * 管理画面メニューを追加
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=grant',
            'SEO 一括更新',
            'SEO 一括更新',
            'manage_options',
            'gi-seo-bulk-update',
            array($this, 'render_bulk_update_page')
        );
    }
    
    /**
     * バルクアップデートページをレンダリング
     */
    public function render_bulk_update_page() {
        // 投稿総数を取得
        $total_posts = wp_count_posts('grant');
        $total_count = $total_posts->publish;
        
        ?>
        <div class="wrap">
            <h1>SEO 一括更新</h1>
            <p>全ての助成金投稿に対して、SEO タイトルと説明文を自動生成します。</p>
            
            <div class="gi-bulk-update-info" style="background: #fff; border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
                <h2>実行前の確認事項</h2>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><strong>対象投稿数:</strong> <?php echo number_format($total_count); ?> 件</li>
                    <li><strong>バッチサイズ:</strong> 100 件/回</li>
                    <li><strong>推定処理時間:</strong> 約 <?php echo ceil($total_count / 100); ?> 分</li>
                    <li><strong>⚠️ 重要:</strong> 処理中はブラウザを閉じないでください</li>
                    <li><strong>⚠️ 推奨:</strong> 実行前にデータベースのバックアップを取得してください</li>
                </ul>
            </div>
            
            <div class="gi-bulk-update-controls">
                <button id="gi-start-bulk-update" class="button button-primary button-hero" style="margin-bottom: 20px;">
                    一括更新を開始
                </button>
                
                <button id="gi-stop-bulk-update" class="button button-secondary" style="display: none; margin-bottom: 20px; margin-left: 10px;">
                    処理を中止
                </button>
            </div>
            
            <div id="gi-bulk-progress" style="display: none;">
                <h3>処理中...</h3>
                <div style="background: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
                    <div id="gi-progress-bar" style="background: #0073aa; height: 30px; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p>
                    <strong>進行状況:</strong> 
                    <span id="gi-processed-count">0</span> / <?php echo number_format($total_count); ?> 件 
                    (<span id="gi-progress-percent">0</span>%)
                </p>
                <p>
                    <strong>現在のバッチ:</strong> <span id="gi-current-batch">0</span> / <span id="gi-total-batches">0</span>
                </p>
                <p>
                    <strong>推定残り時間:</strong> <span id="gi-estimated-time">計算中...</span>
                </p>
            </div>
            
            <div id="gi-bulk-results" style="display: none; background: #fff; border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
                <h3>処理結果</h3>
                <p><strong>処理件数:</strong> <span id="gi-result-total">0</span> 件</p>
                <p><strong>成功:</strong> <span id="gi-result-success">0</span> 件</p>
                <p><strong>スキップ:</strong> <span id="gi-result-skipped">0</span> 件</p>
                <p><strong>エラー:</strong> <span id="gi-result-errors">0</span> 件</p>
                <p><strong>処理時間:</strong> <span id="gi-result-time">0</span> 秒</p>
            </div>
            
            <div id="gi-bulk-log" style="background: #f9f9f9; border: 1px solid #ddd; padding: 10px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px; display: none;">
                <h4>処理ログ</h4>
                <div id="gi-log-content"></div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            let isRunning = false;
            let startTime = 0;
            let processedCount = 0;
            let totalCount = <?php echo $total_count; ?>;
            let batchSize = 100;
            let totalBatches = Math.ceil(totalCount / batchSize);
            let currentBatch = 0;
            let successCount = 0;
            let skippedCount = 0;
            let errorCount = 0;
            
            $('#gi-start-bulk-update').on('click', function() {
                if (!confirm('約' + totalCount.toLocaleString() + '件の助成金投稿に対してSEOタイトル・説明文を自動生成します。\nこの処理には約' + totalBatches + '分かかります。\n\n実行しますか？')) {
                    return;
                }
                
                startBulkUpdate();
            });
            
            $('#gi-stop-bulk-update').on('click', function() {
                if (confirm('処理を中止しますか？')) {
                    isRunning = false;
                    $(this).prop('disabled', true).text('中止中...');
                    addLog('⚠️ ユーザーによって処理が中止されました');
                }
            });
            
            function startBulkUpdate() {
                isRunning = true;
                startTime = Date.now();
                processedCount = 0;
                currentBatch = 0;
                successCount = 0;
                skippedCount = 0;
                errorCount = 0;
                
                $('#gi-start-bulk-update').prop('disabled', true);
                $('#gi-stop-bulk-update').show();
                $('#gi-bulk-progress').show();
                $('#gi-bulk-results').hide();
                $('#gi-bulk-log').show();
                $('#gi-log-content').empty();
                $('#gi-total-batches').text(totalBatches);
                
                addLog('✅ 一括更新を開始しました');
                addLog('📊 対象投稿数: ' + totalCount.toLocaleString() + ' 件');
                addLog('📦 バッチサイズ: ' + batchSize + ' 件/回');
                
                processBatch();
            }
            
            function processBatch() {
                if (!isRunning || currentBatch >= totalBatches) {
                    finishBulkUpdate();
                    return;
                }
                
                currentBatch++;
                let offset = (currentBatch - 1) * batchSize;
                
                $('#gi-current-batch').text(currentBatch);
                addLog('🔄 バッチ ' + currentBatch + '/' + totalBatches + ' を処理中... (Offset: ' + offset + ')');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'gi_bulk_update_seo',
                        offset: offset,
                        limit: batchSize,
                        nonce: '<?php echo wp_create_nonce('gi_bulk_update_seo'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            processedCount += response.data.processed;
                            successCount += (response.data.success || 0);
                            skippedCount += (response.data.skipped || 0);
                            errorCount += (response.data.errors || 0);
                            
                            updateProgress();
                            
                            addLog('✅ バッチ ' + currentBatch + ' 完了: ' + response.data.processed + ' 件処理');
                            
                            // 次のバッチを処理
                            setTimeout(processBatch, 100);
                        } else {
                            // エラーメッセージを適切に表示
                            let errorMsg = '不明なエラー';
                            if (response.data && response.data.message) {
                                errorMsg = response.data.message;
                            } else if (typeof response.data === 'string') {
                                errorMsg = response.data;
                            }
                            addLog('❌ エラー: ' + errorMsg);
                            errorCount++;
                            setTimeout(processBatch, 1000);
                        }
                    },
                    error: function(xhr, status, error) {
                        // XHR レスポンスからエラー詳細を取得
                        let errorMsg = error;
                        if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                            errorMsg = xhr.responseJSON.data.message;
                        } else if (xhr.responseText) {
                            errorMsg = xhr.responseText.substring(0, 100);
                        }
                        addLog('❌ AJAX エラー: ' + errorMsg);
                        errorCount++;
                        setTimeout(processBatch, 1000);
                    }
                });
            }
            
            function updateProgress() {
                let percent = Math.round((processedCount / totalCount) * 100);
                $('#gi-progress-bar').css('width', percent + '%');
                $('#gi-processed-count').text(processedCount.toLocaleString());
                $('#gi-progress-percent').text(percent);
                
                // 推定残り時間を計算
                let elapsedTime = (Date.now() - startTime) / 1000;
                let averageTimePerPost = elapsedTime / processedCount;
                let remainingPosts = totalCount - processedCount;
                let estimatedRemaining = Math.round(averageTimePerPost * remainingPosts);
                
                let minutes = Math.floor(estimatedRemaining / 60);
                let seconds = estimatedRemaining % 60;
                $('#gi-estimated-time').text(minutes + '分' + seconds + '秒');
            }
            
            function finishBulkUpdate() {
                isRunning = false;
                let totalTime = Math.round((Date.now() - startTime) / 1000);
                
                $('#gi-start-bulk-update').prop('disabled', false);
                $('#gi-stop-bulk-update').hide();
                $('#gi-bulk-progress').hide();
                $('#gi-bulk-results').show();
                
                $('#gi-result-total').text(processedCount.toLocaleString());
                $('#gi-result-success').text(successCount.toLocaleString());
                $('#gi-result-skipped').text(skippedCount.toLocaleString());
                $('#gi-result-errors').text(errorCount.toLocaleString());
                $('#gi-result-time').text(totalTime);
                
                addLog('');
                addLog('🎉 一括更新が完了しました！');
                addLog('📊 処理結果:');
                addLog('  - 処理件数: ' + processedCount.toLocaleString() + ' 件');
                addLog('  - 成功: ' + successCount.toLocaleString() + ' 件');
                addLog('  - スキップ: ' + skippedCount.toLocaleString() + ' 件');
                addLog('  - エラー: ' + errorCount.toLocaleString() + ' 件');
                addLog('  - 処理時間: ' + totalTime + ' 秒');
                
                alert('✅ 一括更新が完了しました！\n\n処理件数: ' + processedCount.toLocaleString() + ' 件\n成功: ' + successCount.toLocaleString() + ' 件\nスキップ: ' + skippedCount.toLocaleString() + ' 件\nエラー: ' + errorCount.toLocaleString() + ' 件\n処理時間: ' + totalTime + ' 秒');
            }
            
            function addLog(message) {
                let timestamp = new Date().toLocaleTimeString('ja-JP');
                $('#gi-log-content').append('<div>[' + timestamp + '] ' + message + '</div>');
                $('#gi-bulk-log').scrollTop($('#gi-bulk-log')[0].scrollHeight);
            }
        });
        </script>
        <?php
    }
}

// インスタンス化
GI_SEO_Optimizer::get_instance();
