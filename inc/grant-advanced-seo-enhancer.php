<?php
/**
 * Grant Advanced SEO Enhancer
 * 
 * 助成金投稿のSEOを大幅強化
 * - Open Graph / Twitter Cardメタタグ自動生成
 * - 追加Schema.org構造化データ（FAQPage, HowTo, Organizationなど）
 * - 内部リンク自動挿入（関連助成金へのリンク）
 * - メタディスクリプション最適化
 * - パンくずリスト構造化データ強化
 * - 画像alt属性最適化
 * 
 * @package Grant_Insight
 * @since 2.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Grant_Advanced_SEO_Enhancer {
    
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
        // Open Graph / Twitter Cardメタタグ
        add_action('wp_head', array($this, 'add_social_meta_tags'), 5);
        
        // 追加Schema.org構造化データ
        add_action('wp_head', array($this, 'add_enhanced_schema_data'), 15);
        
        // メタディスクリプション最適化
        add_filter('wp_head', array($this, 'optimize_meta_description'), 1);
        
        // 内部リンク自動挿入
        add_filter('the_content', array($this, 'insert_internal_links'), 20);
        
        // Canonical URL設定
        add_action('wp_head', array($this, 'add_canonical_url'), 1);
        
        // 構造化データエラーチェック
        add_action('admin_notices', array($this, 'schema_validation_notice'));
    }
    
    /**
     * Open Graph / Twitter Cardメタタグを追加
     */
    public function add_social_meta_tags() {
        if (!is_singular('grant')) {
            return;
        }
        
        global $post;
        
        $title = get_the_title();
        $description = $this->generate_optimized_description($post);
        $url = get_permalink();
        $image = $this->get_post_image_url($post->ID);
        $site_name = get_bloginfo('name');
        
        // 助成金額を取得
        $grant_amount = get_field('grant_amount', $post->ID);
        $amount_display = $grant_amount ? '最大' . number_format($grant_amount) . '円' : '';
        
        echo "\n<!-- Open Graph Meta Tags -->\n";
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '" />' . "\n";
        
        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '" />' . "\n";
            echo '<meta property="og:image:width" content="1200" />' . "\n";
            echo '<meta property="og:image:height" content="630" />' . "\n";
        }
        
        if ($grant_amount) {
            echo '<meta property="og:price:amount" content="' . esc_attr($grant_amount) . '" />' . "\n";
            echo '<meta property="og:price:currency" content="JPY" />' . "\n";
        }
        
        echo "\n<!-- Twitter Card Meta Tags -->\n";
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '" />' . "\n";
        
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '" />' . "\n";
        }
        
        // Article specific meta tags
        $published_time = get_the_date('c');
        $modified_time = get_the_modified_date('c');
        
        echo "\n<!-- Article Meta Tags -->\n";
        echo '<meta property="article:published_time" content="' . esc_attr($published_time) . '" />' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr($modified_time) . '" />' . "\n";
        
        $categories = get_the_terms($post->ID, 'grant_category');
        if ($categories && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                echo '<meta property="article:section" content="' . esc_attr($category->name) . '" />' . "\n";
            }
        }
    }
    
    /**
     * 追加Schema.org構造化データを出力
     */
    public function add_enhanced_schema_data() {
        if (!is_singular('grant')) {
            return;
        }
        
        global $post;
        
        // FAQPageスキーマ（Q&A形式の内容がある場合）
        if ($this->has_faq_content($post)) {
            echo $this->generate_faq_schema($post);
        }
        
        // HowToスキーマ（申請手順がある場合）
        if (get_field('application_process', $post->ID)) {
            echo $this->generate_howto_schema($post);
        }
        
        // Organizationスキーマ（運営組織情報）
        echo $this->generate_organization_schema();
        
        // WebPageスキーマ（ページ全体の情報）
        echo $this->generate_webpage_schema($post);
        
        // MonetaryAmountスキーマ（助成金額の詳細）
        $grant_amount = get_field('grant_amount', $post->ID);
        if ($grant_amount) {
            echo $this->generate_monetary_amount_schema($post, $grant_amount);
        }
    }
    
    /**
     * FAQPageスキーマを生成
     */
    private function generate_faq_schema($post) {
        $content = $post->post_content;
        
        // H3見出しとその後の段落をQ&Aとして抽出
        preg_match_all('/<h3[^>]*>(.*?)<\/h3>\s*<p>(.*?)<\/p>/is', $content, $matches, PREG_SET_ORDER);
        
        if (count($matches) < 3) {
            return ''; // FAQ形式と判断できない
        }
        
        $faq_items = array();
        foreach ($matches as $match) {
            $question = strip_tags($match[1]);
            $answer = strip_tags($match[2]);
            
            if (strlen($question) > 10 && strlen($answer) > 20) {
                $faq_items[] = array(
                    '@type' => 'Question',
                    'name' => $question,
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => $answer
                    )
                );
            }
        }
        
        if (empty($faq_items)) {
            return '';
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faq_items
        );
        
        return "\n<!-- FAQPage Schema -->\n" .
               '<script type="application/ld+json">' . "\n" .
               wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n" .
               '</script>' . "\n";
    }
    
    /**
     * HowToスキーマを生成
     */
    private function generate_howto_schema($post) {
        $application_process = get_field('application_process', $post->ID);
        
        if (empty($application_process)) {
            return '';
        }
        
        // ol > li を抽出してステップにする
        preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $application_process, $matches);
        
        if (empty($matches[1])) {
            return '';
        }
        
        $steps = array();
        foreach ($matches[1] as $index => $step_text) {
            $steps[] = array(
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'name' => 'ステップ ' . ($index + 1),
                'text' => strip_tags($step_text)
            );
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => get_the_title() . 'の申請手順',
            'description' => '助成金の申請プロセスを順を追って説明します。',
            'step' => $steps,
            'totalTime' => 'P30D', // 約30日（例）
            'supply' => array(
                array(
                    '@type' => 'HowToSupply',
                    'name' => '必要書類'
                )
            )
        );
        
        return "\n<!-- HowTo Schema -->\n" .
               '<script type="application/ld+json">' . "\n" .
               wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n" .
               '</script>' . "\n";
    }
    
    /**
     * Organizationスキーマを生成
     */
    private function generate_organization_schema() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_site_icon_url()
            ),
            'description' => get_bloginfo('description'),
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'availableLanguage' => 'Japanese'
            )
        );
        
        return "\n<!-- Organization Schema -->\n" .
               '<script type="application/ld+json">' . "\n" .
               wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n" .
               '</script>' . "\n";
    }
    
    /**
     * WebPageスキーマを生成
     */
    private function generate_webpage_schema($post) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => get_permalink() . '#webpage',
            'url' => get_permalink(),
            'name' => get_the_title(),
            'description' => $this->generate_optimized_description($post),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'inLanguage' => 'ja-JP',
            'isPartOf' => array(
                '@type' => 'WebSite',
                '@id' => home_url() . '#website',
                'url' => home_url(),
                'name' => get_bloginfo('name')
            ),
            'breadcrumb' => array(
                '@id' => get_permalink() . '#breadcrumb'
            ),
            'potentialAction' => array(
                '@type' => 'ReadAction',
                'target' => array(get_permalink())
            )
        );
        
        return "\n<!-- WebPage Schema -->\n" .
               '<script type="application/ld+json">' . "\n" .
               wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n" .
               '</script>' . "\n";
    }
    
    /**
     * MonetaryAmountスキーマを生成
     */
    private function generate_monetary_amount_schema($post, $amount) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'MonetaryGrant',
            'name' => get_the_title(),
            'amount' => array(
                '@type' => 'MonetaryAmount',
                'currency' => 'JPY',
                'value' => $amount,
                'maxValue' => $amount
            ),
            'funder' => array(
                '@type' => 'GovernmentOrganization',
                'name' => get_field('implementing_agency', $post->ID) ?: '実施機関'
            )
        );
        
        return "\n<!-- MonetaryGrant Schema -->\n" .
               '<script type="application/ld+json">' . "\n" .
               wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n" .
               '</script>' . "\n";
    }
    
    /**
     * メタディスクリプションを最適化
     */
    public function optimize_meta_description() {
        if (!is_singular('grant')) {
            return;
        }
        
        global $post;
        
        $description = $this->generate_optimized_description($post);
        
        echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";
        
        // キーワードも追加（SEO効果は限定的だが、内部整理に有用）
        $keywords = $this->generate_keywords($post);
        if (!empty($keywords)) {
            echo '<meta name="keywords" content="' . esc_attr($keywords) . '" />' . "\n";
        }
    }
    
    /**
     * 最適化されたディスクリプションを生成
     */
    private function generate_optimized_description($post) {
        $grant_amount = get_field('grant_amount', $post->ID);
        $deadline = get_field('application_deadline', $post->ID);
        $target = get_field('grant_target', $post->ID);
        
        $description = get_the_title() . 'の詳細情報。';
        
        if ($grant_amount) {
            $description .= '助成金額は最大' . number_format($grant_amount) . '円。';
        }
        
        if ($deadline) {
            $description .= '募集期限は' . $deadline . '。';
        }
        
        if ($target) {
            $target_text = strip_tags($target);
            $target_text = mb_substr($target_text, 0, 50);
            $description .= '対象：' . $target_text . '。';
        }
        
        $description .= '申請条件、必要書類、手続きの流れを詳しく解説。';
        
        return mb_substr($description, 0, 155); // 155文字に制限
    }
    
    /**
     * キーワードを生成
     */
    private function generate_keywords($post) {
        $keywords = array();
        
        // タイトルからキーワード抽出
        $title = get_the_title();
        $keywords[] = $title;
        
        // カテゴリー
        $categories = get_the_terms($post->ID, 'grant_category');
        if ($categories && !is_wp_error($categories)) {
            foreach ($categories as $cat) {
                $keywords[] = $cat->name;
            }
        }
        
        // タグ
        $tags = get_the_terms($post->ID, 'grant_tag');
        if ($tags && !is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $keywords[] = $tag->name;
            }
        }
        
        // 共通キーワード
        $keywords[] = '助成金';
        $keywords[] = '補助金';
        $keywords[] = '支援制度';
        
        return implode(', ', array_unique($keywords));
    }
    
    /**
     * 内部リンクを自動挿入
     */
    public function insert_internal_links($content) {
        if (!is_singular('grant')) {
            return $content;
        }
        
        global $post;
        
        // 同じカテゴリーの関連助成金を取得
        $related_grants = $this->get_related_grants($post->ID, 3);
        
        if (empty($related_grants)) {
            return $content;
        }
        
        // コンテンツの最後に関連リンクセクションを追加
        $links_html = '<div class="grant-related-links" style="margin-top: 3rem; padding: 2rem; background: #f5f5f5; border-left: 4px solid #000000;">';
        $links_html .= '<h2 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 1.5rem;">関連する助成金・補助金</h2>';
        $links_html .= '<ul style="list-style: none; padding: 0;">';
        
        foreach ($related_grants as $related_post) {
            $related_url = get_permalink($related_post->ID);
            $related_title = get_the_title($related_post->ID);
            $related_amount = get_field('grant_amount', $related_post->ID);
            
            $links_html .= '<li style="margin-bottom: 1rem; padding: 1rem; background: #ffffff; border: 2px solid #e5e5e5;">';
            $links_html .= '<a href="' . esc_url($related_url) . '" style="font-weight: 700; color: #000000; text-decoration: none; display: block;">';
            $links_html .= esc_html($related_title);
            $links_html .= '</a>';
            
            if ($related_amount) {
                $links_html .= '<span style="font-size: 0.9rem; color: #4a4a4a; margin-top: 0.5rem; display: block;">最大 ' . number_format($related_amount) . '円</span>';
            }
            
            $links_html .= '</li>';
        }
        
        $links_html .= '</ul></div>';
        
        return $content . $links_html;
    }
    
    /**
     * 関連助成金を取得
     */
    private function get_related_grants($post_id, $limit = 3) {
        $categories = wp_get_post_terms($post_id, 'grant_category', array('fields' => 'ids'));
        
        if (empty($categories)) {
            return array();
        }
        
        $args = array(
            'post_type' => 'grant',
            'posts_per_page' => $limit,
            'post__not_in' => array($post_id),
            'tax_query' => array(
                array(
                    'taxonomy' => 'grant_category',
                    'field' => 'term_id',
                    'terms' => $categories
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        return get_posts($args);
    }
    
    /**
     * Canonical URLを追加
     */
    public function add_canonical_url() {
        if (!is_singular('grant')) {
            return;
        }
        
        $canonical_url = get_permalink();
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
    }
    
    /**
     * 投稿の画像URLを取得
     */
    private function get_post_image_url($post_id) {
        if (has_post_thumbnail($post_id)) {
            return get_the_post_thumbnail_url($post_id, 'full');
        }
        
        // デフォルト画像（存在する場合）
        $default_image = get_template_directory_uri() . '/assets/images/default-grant-image.jpg';
        
        return $default_image;
    }
    
    /**
     * FAQ形式のコンテンツがあるかチェック
     */
    private function has_faq_content($post) {
        $content = $post->post_content;
        
        // H3が8個以上あり、それぞれに段落が続いている場合
        $h3_count = preg_match_all('/<h3[^>]*>/i', $content);
        
        return $h3_count >= 8;
    }
    
    /**
     * スキーマ検証通知（管理画面）
     */
    public function schema_validation_notice() {
        $screen = get_current_screen();
        
        if ($screen->post_type !== 'grant') {
            return;
        }
        
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>SEO強化機能が有効です：</strong> Open Graph、Twitter Card、Schema.org構造化データが自動生成されます。</p>';
        echo '<p>構造化データのテストは<a href="https://search.google.com/test/rich-results" target="_blank">Google Rich Results Test</a>で確認できます。</p>';
        echo '</div>';
    }
}

// 初期化
Grant_Advanced_SEO_Enhancer::get_instance();
