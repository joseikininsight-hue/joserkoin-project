<?php
/**
 * Grant Content SEO Optimizer
 * 
 * 助成金・補助金投稿のコンテンツを完璧なSEO構造に自動変換するクラス
 * - セマンティックHTML5構造の適用
 * - Schema.org構造化データの追加
 * - 見出し階層の最適化
 * - 統一されたCSS設計
 * 
 * @package Grant_Insight
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Grant_Content_SEO_Optimizer {
    
    /**
     * シングルトンインスタンス
     */
    private static $instance = null;
    
    /**
     * シングルトンパターン
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * コンストラクタ
     */
    private function __construct() {
        // 投稿表示時にコンテンツを最適化
        add_filter('the_content', array($this, 'optimize_grant_content'), 10);
        
        // 抜粋にもフィルターを適用（検索結果用）
        add_filter('the_excerpt', array($this, 'optimize_grant_excerpt'), 10);
        
        // headにSchema.orgデータを追加
        add_action('wp_head', array($this, 'add_schema_org_data'), 5);
        
        // SEO最適化用のCSSを読み込み
        // CSS読み込みは削除（unified-frontend.css に統合済み）
        // add_action('wp_enqueue_scripts', array($this, 'enqueue_seo_styles'));
    }
    
    /**
     * SEO最適化用CSSを読み込み - 削除済み（unified-frontend.css に統合）
     */
    // public function enqueue_seo_styles() {
    //     if (is_singular('grant')) {
    //         wp_enqueue_style(
    //             'grant-seo-styles',
    //             get_template_directory_uri() . '/assets/css/grant-seo.css',
    //             array(),
    //             '1.0.0'
    //         );
    //     }
    // }
    
    /**
     * 助成金コンテンツを完璧なSEO構造に最適化
     * 
     * @param string $content 元のコンテンツ
     * @return string 最適化されたコンテンツ
     */
    public function optimize_grant_content($content) {
        // grant投稿タイプのみ処理
        if (!is_singular('grant')) {
            return $content;
        }
        
        global $post;
        
        // セマンティックHTML5構造でラップ
        $optimized_content = '<article class="grant-article" itemscope itemtype="https://schema.org/GovernmentService">';
        
        // ヘッダーセクション
        $optimized_content .= $this->build_header_section($post);
        
        // メインコンテンツ
        $optimized_content .= '<div class="grant-article__content">';
        
        // 既存コンテンツを見出し構造に変換
        $optimized_content .= $this->transform_heading_structure($content);
        
        // ACFフィールドからセクションを構築
        $optimized_content .= $this->build_acf_sections($post->ID);
        
        $optimized_content .= '</div>'; // .grant-article__content
        
        // フッターセクション（更新日、タグなど）
        $optimized_content .= $this->build_footer_section($post);
        
        $optimized_content .= '</article>';
        
        return $optimized_content;
    }
    
    /**
     * ヘッダーセクションを構築
     * 
     * @param WP_Post $post 投稿オブジェクト
     * @return string HTMLコード
     */
    private function build_header_section($post) {
        $header = '<header class="grant-article__header">';
        
        // パンくずリスト（構造化データ付き）
        $header .= $this->build_breadcrumbs($post);
        
        // タイトル（H1）
        $header .= '<h1 class="grant-article__title" itemprop="name">' . esc_html(get_the_title($post)) . '</h1>';
        
        // メタ情報
        $header .= '<div class="grant-article__meta">';
        
        // 助成金額
        $amount = get_field('grant_amount', $post->ID);
        if ($amount) {
            $header .= '<div class="grant-meta__item grant-meta__amount" itemprop="offers" itemscope itemtype="https://schema.org/Offer">';
            $header .= '<svg class="grant-meta__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>';
            $header .= '<span class="grant-meta__label">助成金額：</span>';
            $header .= '<span class="grant-meta__value" itemprop="price">' . esc_html($amount) . '</span>';
            $header .= '</div>';
        }
        
        // 申請期限
        $deadline = get_field('application_deadline', $post->ID);
        if ($deadline) {
            $header .= '<div class="grant-meta__item grant-meta__deadline">';
            $header .= '<svg class="grant-meta__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
            $header .= '<span class="grant-meta__label">申請期限：</span>';
            $header .= '<span class="grant-meta__value" itemprop="availabilityEnds">' . esc_html($deadline) . '</span>';
            $header .= '</div>';
        }
        
        // 地域
        $prefectures = wp_get_post_terms($post->ID, 'prefecture');
        if (!empty($prefectures) && !is_wp_error($prefectures)) {
            $header .= '<div class="grant-meta__item grant-meta__location" itemprop="areaServed" itemscope itemtype="https://schema.org/Place">';
            $header .= '<svg class="grant-meta__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
            $header .= '<span class="grant-meta__label">対象地域：</span>';
            $header .= '<span class="grant-meta__value" itemprop="name">' . esc_html($prefectures[0]->name) . '</span>';
            $header .= '</div>';
        }
        
        // 更新日
        $modified_date = get_the_modified_date('Y年n月j日', $post);
        $modified_iso = get_the_modified_date('c', $post);
        $header .= '<div class="grant-meta__item grant-meta__updated">';
        $header .= '<svg class="grant-meta__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>';
        $header .= '<span class="grant-meta__label">最終更新：</span>';
        $header .= '<time class="grant-meta__value" datetime="' . esc_attr($modified_iso) . '" itemprop="dateModified">' . esc_html($modified_date) . '</time>';
        $header .= '</div>';
        
        $header .= '</div>'; // .grant-article__meta
        
        // 抜粋（概要）
        $excerpt = get_field('grant_excerpt', $post->ID);
        if (!$excerpt) {
            $excerpt = get_the_excerpt($post);
        }
        if ($excerpt) {
            $header .= '<div class="grant-article__excerpt" itemprop="description">';
            $header .= '<p>' . esc_html($excerpt) . '</p>';
            $header .= '</div>';
        }
        
        $header .= '</header>';
        
        return $header;
    }
    
    /**
     * パンくずリストを構築
     * 
     * @param WP_Post $post 投稿オブジェクト
     * @return string HTMLコード
     */
    private function build_breadcrumbs($post) {
        $breadcrumbs = '<nav class="grant-breadcrumbs" aria-label="パンくずリスト" itemscope itemtype="https://schema.org/BreadcrumbList">';
        
        $items = array();
        
        // ホーム
        $items[] = array(
            'name' => 'ホーム',
            'url' => home_url('/'),
            'position' => 1
        );
        
        // 助成金一覧
        $items[] = array(
            'name' => '助成金・補助金',
            'url' => get_post_type_archive_link('grant'),
            'position' => 2
        );
        
        // カテゴリー
        $categories = wp_get_post_terms($post->ID, 'grant_category');
        if (!empty($categories) && !is_wp_error($categories)) {
            $items[] = array(
                'name' => $categories[0]->name,
                'url' => get_term_link($categories[0]),
                'position' => 3
            );
        }
        
        // 現在のページ
        $items[] = array(
            'name' => get_the_title($post),
            'url' => get_permalink($post),
            'position' => count($items) + 1,
            'current' => true
        );
        
        foreach ($items as $item) {
            $breadcrumbs .= '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            
            if (!empty($item['current'])) {
                $breadcrumbs .= '<span class="grant-breadcrumbs__current" itemprop="name">' . esc_html($item['name']) . '</span>';
            } else {
                $breadcrumbs .= '<a href="' . esc_url($item['url']) . '" class="grant-breadcrumbs__link" itemprop="item">';
                $breadcrumbs .= '<span itemprop="name">' . esc_html($item['name']) . '</span>';
                $breadcrumbs .= '</a>';
                $breadcrumbs .= '<span class="grant-breadcrumbs__separator" aria-hidden="true">›</span>';
            }
            
            $breadcrumbs .= '<meta itemprop="position" content="' . esc_attr($item['position']) . '">';
            $breadcrumbs .= '</span>';
        }
        
        $breadcrumbs .= '</nav>';
        
        return $breadcrumbs;
    }
    
    /**
     * 見出し構造を最適化（H3→H2に変換）+ 表・改行のSEO最適化
     * 
     * @param string $content 元のコンテンツ
     * @return string 変換後のコンテンツ
     */
    private function transform_heading_structure($content) {
        // ===== 見出し階層の最適化 =====
        
        // H3をH2に変換（既存のH3がメインセクション見出しとして使われているため）
        $content = preg_replace('/<h3([^>]*)>/i', '<h2$1 class="grant-section__heading">', $content);
        $content = preg_replace('/<\/h3>/i', '</h2>', $content);
        
        // H4があればH3に
        $content = preg_replace('/<h4([^>]*)>/i', '<h3$1 class="grant-subsection__heading">', $content);
        $content = preg_replace('/<\/h4>/i', '</h3>', $content);
        
        // H5があればH4に
        $content = preg_replace('/<h5([^>]*)>/i', '<h4$1 class="grant-subsubsection__heading">', $content);
        $content = preg_replace('/<\/h5>/i', '</h4>', $content);
        
        // ===== テーブルのSEO最適化 =====
        
        // テーブルを見つけて最適化
        $content = preg_replace_callback(
            '/<table([^>]*)>(.*?)<\/table>/is',
            array($this, 'optimize_table_structure'),
            $content
        );
        
        // ===== 改行のSEO最適化 =====
        
        // 連続するbrタグを段落に変換（SEO: 段落構造の明確化）
        $content = preg_replace('/<br\s*\/?>\s*<br\s*\/?>/i', '</p><p>', $content);
        
        // 段落タグがない場合、自動的に追加
        if (strpos($content, '<p>') === false && !empty(trim(strip_tags($content)))) {
            // ブロック要素で囲まれていないテキストを段落で囲む
            $content = '<p>' . $content . '</p>';
            $content = preg_replace('/<p>\s*<(div|section|article|h[1-6]|table|ul|ol|blockquote)/i', '<$1', $content);
            $content = preg_replace('/<\/(div|section|article|h[1-6]|table|ul|ol|blockquote)>\s*<\/p>/i', '</$1>', $content);
        }
        
        // ===== リストのSEO最適化 =====
        
        // リスト項目の改行を最適化
        $content = preg_replace('/<li([^>]*)>\s*<br\s*\/?>/i', '<li$1>', $content);
        $content = preg_replace('/<br\s*\/?>\s*<\/li>/i', '</li>', $content);
        
        // ===== 空要素の除去 =====
        
        // 空の段落タグを削除（SEO: 無駄なマークアップ除去）
        $content = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $content);
        
        // 空の見出しタグを削除
        $content = preg_replace('/<h[1-6][^>]*>\s*<\/h[1-6]>/i', '', $content);
        
        return $content;
    }
    
    /**
     * テーブル構造をSEO最適化
     * 
     * @param array $matches 正規表現マッチ結果
     * @return string 最適化されたテーブルHTML
     */
    private function optimize_table_structure($matches) {
        $table_attrs = $matches[1];
        $table_content = $matches[2];
        
        // テーブルにクラスを追加（既存のクラスを保持）
        if (strpos($table_attrs, 'class=') !== false) {
            $table_attrs = preg_replace('/class=["\']([^"\']*)["\']/', 'class="$1 grant-optimized-table"', $table_attrs);
        } else {
            $table_attrs .= ' class="grant-optimized-table"';
        }
        
        // summary属性を追加（アクセシビリティ向上）
        if (strpos($table_attrs, 'summary=') === false) {
            $table_attrs .= ' summary="助成金情報の詳細表"';
        }
        
        // role属性を追加（アクセシビリティ）
        if (strpos($table_attrs, 'role=') === false) {
            $table_attrs .= ' role="table"';
        }
        
        // theadがない場合、最初のtrをtheadに変換
        if (strpos($table_content, '<thead') === false && preg_match('/<tr([^>]*)>(.*?)<\/tr>/is', $table_content, $first_row)) {
            // 最初の行にthタグが含まれているかチェック
            if (strpos($first_row[2], '<th') !== false) {
                $thead = '<thead>' . $first_row[0] . '</thead>';
                $table_content = preg_replace('/<tr([^>]*)>' . preg_quote($first_row[2], '/') . '<\/tr>/is', '', $table_content, 1);
                $table_content = $thead . '<tbody>' . $table_content . '</tbody>';
            } else {
                // thタグがない場合、tdをthに変換
                $header_cells = preg_replace('/<td([^>]*)>/i', '<th$1 scope="col">', $first_row[2]);
                $header_cells = preg_replace('/<\/td>/i', '</th>', $header_cells);
                $thead = '<thead><tr>' . $header_cells . '</tr></thead>';
                $table_content = preg_replace('/<tr([^>]*)>' . preg_quote($first_row[2], '/') . '<\/tr>/is', '', $table_content, 1);
                $table_content = $thead . '<tbody>' . $table_content . '</tbody>';
            }
        } elseif (strpos($table_content, '<tbody') === false) {
            // theadはあるがtbodyがない場合
            $table_content = preg_replace('/(<\/thead>)/i', '$1<tbody>', $table_content, 1);
            $table_content .= '</tbody>';
        }
        
        // thタグにscope属性を追加（SEO: テーブル構造の明確化）
        $table_content = preg_replace('/<th(?![^>]*scope=)([^>]*)>/i', '<th scope="col"$1>', $table_content);
        
        // tdタグにheaders属性を追加する準備（複雑なテーブル用）
        // ここでは基本的な最適化のみ実施
        
        // レスポンシブテーブルラッパーで囲む
        return '<div class="table-responsive"><table' . $table_attrs . '>' . $table_content . '</table></div>';
    }
    
    /**
     * ACFフィールドからセクションを構築
     * 
     * @param int $post_id 投稿ID
     * @return string HTMLコード
     */
    private function build_acf_sections($post_id) {
        $sections = '';
        
        // 各ACFフィールドをセマンティックなセクションとして構築
        $field_map = array(
            'grant_target' => array(
                'title' => '対象者・対象事業',
                'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                'schema_prop' => 'eligibleRegion'
            ),
            'required_documents' => array(
                'title' => '必要書類',
                'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
                'schema_prop' => 'termsOfService'
            ),
            'eligible_expenses' => array(
                'title' => '対象経費',
                'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
                'schema_prop' => 'availableChannel'
            ),
            'application_process' => array(
                'title' => '申請方法・手続き',
                'icon' => '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>',
                'schema_prop' => 'serviceType'
            ),
            'grant_notes' => array(
                'title' => '注意事項・備考',
                'icon' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
                'schema_prop' => 'disclaimer'
            ),
            'contact_info' => array(
                'title' => 'お問い合わせ先',
                'icon' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
                'schema_prop' => 'provider'
            ),
            'official_url' => array(
                'title' => '公式ページ',
                'icon' => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
                'schema_prop' => 'url'
            )
        );
        
        foreach ($field_map as $field_name => $field_config) {
            $field_value = get_field($field_name, $post_id, false); // HTMLをそのまま取得
            
            if (empty($field_value)) {
                continue;
            }
            
            // 特別処理: official_url
            if ($field_name === 'official_url') {
                $sections .= '<section class="grant-section grant-section--official-link" itemprop="' . esc_attr($field_config['schema_prop']) . '">';
                $sections .= '<h2 class="grant-section__heading">';
                $sections .= '<svg class="grant-section__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' . $field_config['icon'] . '</svg>';
                $sections .= esc_html($field_config['title']);
                $sections .= '</h2>';
                $sections .= '<div class="grant-section__content">';
                $sections .= '<a href="' . esc_url($field_value) . '" class="grant-official-link" target="_blank" rel="noopener noreferrer">';
                $sections .= '<span class="grant-official-link__text">公式サイトで詳細を確認する</span>';
                $sections .= '<svg class="grant-official-link__arrow" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
                $sections .= '</a>';
                $sections .= '</div>';
                $sections .= '</section>';
                continue;
            }
            
            // 通常のセクション
            $sections .= '<section class="grant-section grant-section--' . esc_attr($field_name) . '" itemprop="' . esc_attr($field_config['schema_prop']) . '">';
            
            // セクション見出し
            $sections .= '<h2 class="grant-section__heading">';
            $sections .= '<svg class="grant-section__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' . $field_config['icon'] . '</svg>';
            $sections .= esc_html($field_config['title']);
            $sections .= '</h2>';
            
            // セクションコンテンツ（SEO最適化を適用）
            $sections .= '<div class="grant-section__content">';
            $sections .= wp_kses_post($this->optimize_field_content($field_value));
            $sections .= '</div>';
            
            $sections .= '</section>';
        }
        
        return $sections;
    }
    
    /**
     * ACFフィールドのコンテンツをSEO最適化
     * 
     * @param string $content フィールドの内容
     * @return string 最適化されたコンテンツ
     */
    private function optimize_field_content($content) {
        if (empty($content)) {
            return $content;
        }
        
        // テーブルのSEO最適化
        $content = preg_replace_callback(
            '/<table([^>]*)>(.*?)<\/table>/is',
            array($this, 'optimize_table_structure'),
            $content
        );
        
        // 改行の最適化
        $content = preg_replace('/<br\s*\/?>\s*<br\s*\/?>/i', '</p><p>', $content);
        
        // 空の段落タグを削除
        $content = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $content);
        
        // リスト項目の改行を最適化
        $content = preg_replace('/<li([^>]*)>\s*<br\s*\/?>/i', '<li$1>', $content);
        $content = preg_replace('/<br\s*\/?>\s*<\/li>/i', '</li>', $content);
        
        return $content;
    }
    
    /**
     * フッターセクションを構築
     * 
     * @param WP_Post $post 投稿オブジェクト
     * @return string HTMLコード
     */
    private function build_footer_section($post) {
        $footer = '<footer class="grant-article__footer">';
        
        // タグ
        $tags = wp_get_post_terms($post->ID, 'grant_tag');
        if (!empty($tags) && !is_wp_error($tags)) {
            $footer .= '<div class="grant-tags">';
            $footer .= '<h3 class="grant-tags__heading">関連タグ</h3>';
            $footer .= '<ul class="grant-tags__list">';
            foreach ($tags as $tag) {
                $footer .= '<li class="grant-tags__item">';
                $footer .= '<a href="' . esc_url(get_term_link($tag)) . '" class="grant-tags__link" rel="tag">';
                $footer .= '<span class="grant-tags__hash">#</span>' . esc_html($tag->name);
                $footer .= '</a>';
                $footer .= '</li>';
            }
            $footer .= '</ul>';
            $footer .= '</div>';
        }
        
        // シェアボタン（今後追加可能）
        $footer .= '<!-- シェアボタンエリア（今後実装） -->';
        
        $footer .= '</footer>';
        
        return $footer;
    }
    
    /**
     * 抜粋を最適化
     * 
     * @param string $excerpt 元の抜粋
     * @return string 最適化された抜粋
     */
    public function optimize_grant_excerpt($excerpt) {
        if (!is_post_type_archive('grant') && !is_tax(array('grant_category', 'prefecture', 'grant_tag'))) {
            return $excerpt;
        }
        
        // HTMLタグを除去してプレーンテキストに
        $excerpt = wp_strip_all_tags($excerpt);
        
        // 文字数制限（120文字）
        if (mb_strlen($excerpt) > 120) {
            $excerpt = mb_substr($excerpt, 0, 120) . '...';
        }
        
        return $excerpt;
    }
    
    /**
     * Schema.org構造化データをheadに追加
     */
    public function add_schema_org_data() {
        if (!is_singular('grant')) {
            return;
        }
        
        global $post;
        
        // Schema.orgデータをJSON-LD形式で出力
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'GovernmentService',
            'name' => get_the_title($post),
            'description' => wp_strip_all_tags(get_the_excerpt($post)),
            'url' => get_permalink($post),
            'dateModified' => get_the_modified_date('c', $post),
            'datePublished' => get_the_date('c', $post)
        );
        
        // 助成金額
        $amount = get_field('grant_amount', $post->ID);
        if ($amount) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $amount,
                'priceCurrency' => 'JPY'
            );
        }
        
        // 申請期限
        $deadline = get_field('application_deadline', $post->ID);
        if ($deadline) {
            $schema['availabilityEnds'] = $deadline;
        }
        
        // 地域
        $prefectures = wp_get_post_terms($post->ID, 'prefecture');
        if (!empty($prefectures) && !is_wp_error($prefectures)) {
            $schema['areaServed'] = array(
                '@type' => 'Place',
                'name' => $prefectures[0]->name
            );
        }
        
        // カテゴリー
        $categories = wp_get_post_terms($post->ID, 'grant_category');
        if (!empty($categories) && !is_wp_error($categories)) {
            $schema['category'] = $categories[0]->name;
        }
        
        // 提供者（実施機関）
        $provider = get_field('implementing_agency', $post->ID);
        if ($provider) {
            $schema['provider'] = array(
                '@type' => 'GovernmentOrganization',
                'name' => $provider
            );
        }
        
        // 公式URL
        $official_url = get_field('official_url', $post->ID);
        if ($official_url) {
            $schema['sameAs'] = $official_url;
        }
        
        // JSON-LD出力
        echo '<script type="application/ld+json">';
        echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        echo '</script>' . "\n";
    }
}

// インスタンス化
Grant_Content_SEO_Optimizer::get_instance();
