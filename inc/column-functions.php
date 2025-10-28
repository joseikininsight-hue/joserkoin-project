<?php
/**
 * Column Helper Functions
 * コラム用のヘルパー関数群
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

/**
 * コラムの読了時間を取得
 * 
 * @param int $post_id コラムのID
 * @return int 読了時間（分）
 */
function gi_get_column_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $reading_time = get_post_meta($post_id, '_column_reading_time', true);
    
    if (!$reading_time) {
        // 本文から自動計算
        $content = get_post_field('post_content', $post_id);
        $word_count = mb_strlen(strip_tags($content));
        $reading_time = max(1, ceil($word_count / 600));
    }
    
    return intval($reading_time);
}

/**
 * コラムの著者情報を取得
 * 
 * @param int $post_id コラムのID
 * @return array 著者情報の配列
 */
function gi_get_column_author_info($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return array(
        'name' => get_post_meta($post_id, '_column_author_name', true) ?: get_the_author_meta('display_name', get_post_field('post_author', $post_id)),
        'title' => get_post_meta($post_id, '_column_author_title', true),
    );
}

/**
 * 関連する助成金を取得
 * 
 * @param int $post_id コラムのID
 * @param int $limit 取得する件数
 * @return array 助成金の配列
 */
function gi_get_column_related_grants($post_id = null, $limit = 3) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $related_grant_ids = get_post_meta($post_id, '_column_related_grants', true);
    
    if (!$related_grant_ids) {
        return array();
    }
    
    $grant_ids = array_map('intval', explode(',', $related_grant_ids));
    $grant_ids = array_filter($grant_ids);
    $grant_ids = array_slice($grant_ids, 0, $limit);
    
    if (empty($grant_ids)) {
        return array();
    }
    
    $args = array(
        'post_type' => 'grant',
        'post__in' => $grant_ids,
        'posts_per_page' => $limit,
        'orderby' => 'post__in',
        'post_status' => 'publish',
    );
    
    return get_posts($args);
}

/**
 * 関連コラムを取得（カテゴリーまたはタグから）
 * 
 * @param int $post_id コラムのID
 * @param int $limit 取得する件数
 * @return array コラムの配列
 */
function gi_get_related_columns($post_id = null, $limit = 3) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // カテゴリーを取得
    $categories = get_the_terms($post_id, 'column_category');
    $category_ids = array();
    if ($categories && !is_wp_error($categories)) {
        $category_ids = wp_list_pluck($categories, 'term_id');
    }
    
    // タグを取得
    $tags = get_the_terms($post_id, 'column_tag');
    $tag_ids = array();
    if ($tags && !is_wp_error($tags)) {
        $tag_ids = wp_list_pluck($tags, 'term_id');
    }
    
    $args = array(
        'post_type' => 'column',
        'post__not_in' => array($post_id),
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'rand',
    );
    
    // カテゴリーまたはタグで絞り込み
    if (!empty($category_ids) || !empty($tag_ids)) {
        $args['tax_query'] = array(
            'relation' => 'OR',
        );
        
        if (!empty($category_ids)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'column_category',
                'field' => 'term_id',
                'terms' => $category_ids,
            );
        }
        
        if (!empty($tag_ids)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'column_tag',
                'field' => 'term_id',
                'terms' => $tag_ids,
            );
        }
    }
    
    return get_posts($args);
}

/**
 * 人気のコラムを取得（閲覧数ベース）
 * 
 * @param int $limit 取得する件数
 * @param int $days 過去何日間
 * @return array コラムの配列
 */
function gi_get_popular_columns($limit = 5, $days = 30) {
    $args = array(
        'post_type' => 'column',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'meta_key' => '_column_view_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'after' => $days . ' days ago',
            ),
        ),
    );
    
    return get_posts($args);
}

/**
 * 最新のコラムを取得
 * 
 * @param int $limit 取得する件数
 * @param string $category_slug カテゴリースラッグ（オプション）
 * @return array コラムの配列
 */
function gi_get_recent_columns($limit = 6, $category_slug = '') {
    $args = array(
        'post_type' => 'column',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    if (!empty($category_slug)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'column_category',
                'field' => 'slug',
                'terms' => $category_slug,
            ),
        );
    }
    
    return get_posts($args);
}

/**
 * 注目コラムを取得
 * 
 * @param int $limit 取得する件数
 * @return array コラムの配列
 */
function gi_get_featured_columns($limit = 3) {
    $args = array(
        'post_type' => 'column',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_column_is_featured',
                'value' => '1',
                'compare' => '=',
            ),
        ),
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    return get_posts($args);
}

/**
 * コラムの閲覧数をカウント
 * 
 * @param int $post_id コラムのID
 */
function gi_count_column_view($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    if (get_post_type($post_id) !== 'column') {
        return;
    }
    
    // ボットや管理者の閲覧は除外
    if (is_admin() || current_user_can('manage_options')) {
        return;
    }
    
    $view_count = get_post_meta($post_id, '_column_view_count', true);
    $view_count = $view_count ? intval($view_count) + 1 : 1;
    
    update_post_meta($post_id, '_column_view_count', $view_count);
}
add_action('wp', function() {
    if (is_singular('column')) {
        gi_count_column_view();
    }
});

/**
 * コラムの閲覧数を取得
 * 
 * @param int $post_id コラムのID
 * @return int 閲覧数
 */
function gi_get_column_view_count($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $view_count = get_post_meta($post_id, '_column_view_count', true);
    return $view_count ? intval($view_count) : 0;
}

/**
 * コラムのカテゴリーリンクを取得
 * 
 * @param int $post_id コラムのID
 * @param string $separator 区切り文字
 * @return string カテゴリーリンクのHTML
 */
function gi_get_column_category_links($post_id = null, $separator = ', ') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $categories = get_the_terms($post_id, 'column_category');
    
    if (!$categories || is_wp_error($categories)) {
        return '';
    }
    
    $links = array();
    foreach ($categories as $category) {
        $links[] = '<a href="' . esc_url(get_term_link($category)) . '" class="column-category-link">' . esc_html($category->name) . '</a>';
    }
    
    return implode($separator, $links);
}

/**
 * コラムのタグリンクを取得
 * 
 * @param int $post_id コラムのID
 * @param string $separator 区切り文字
 * @return string タグリンクのHTML
 */
function gi_get_column_tag_links($post_id = null, $separator = ' ') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $tags = get_the_terms($post_id, 'column_tag');
    
    if (!$tags || is_wp_error($tags)) {
        return '';
    }
    
    $links = array();
    foreach ($tags as $tag) {
        $links[] = '<a href="' . esc_url(get_term_link($tag)) . '" class="column-tag-link">' . esc_html($tag->name) . '</a>';
    }
    
    return implode($separator, $links);
}

/**
 * コラムのアイキャッチ画像URLを取得
 * 
 * @param int $post_id コラムのID
 * @param string $size 画像サイズ
 * @return string 画像URL
 */
function gi_get_column_thumbnail_url($post_id = null, $size = 'large') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $thumbnail_id = get_post_thumbnail_id($post_id);
    
    if ($thumbnail_id) {
        $image_url = wp_get_attachment_image_url($thumbnail_id, $size);
        if ($image_url) {
            return $image_url;
        }
    }
    
    // デフォルト画像（存在する場合）
    return get_template_directory_uri() . '/assets/images/default-column-thumbnail.jpg';
}

/**
 * コラムの抜粋を取得（カスタム長さ）
 * 
 * @param int $post_id コラムのID
 * @param int $length 文字数
 * @return string 抜粋
 */
function gi_get_column_excerpt($post_id = null, $length = 120) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $excerpt = get_the_excerpt($post_id);
    
    if (!$excerpt) {
        $content = get_post_field('post_content', $post_id);
        $excerpt = wp_strip_all_tags($content);
    }
    
    if (mb_strlen($excerpt) > $length) {
        $excerpt = mb_substr($excerpt, 0, $length) . '...';
    }
    
    return $excerpt;
}

/**
 * コラムのパンくずリストを生成
 * 
 * @param int $post_id コラムのID
 * @return array パンくずリストの配列
 */
function gi_get_column_breadcrumb($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $breadcrumb = array(
        array(
            'title' => 'ホーム',
            'url' => home_url('/'),
        ),
        array(
            'title' => 'コラム',
            'url' => get_post_type_archive_link('column'),
        ),
    );
    
    // カテゴリーを追加
    $categories = get_the_terms($post_id, 'column_category');
    if ($categories && !is_wp_error($categories)) {
        $category = array_shift($categories);
        $breadcrumb[] = array(
            'title' => $category->name,
            'url' => get_term_link($category),
        );
    }
    
    // 現在のページ
    $breadcrumb[] = array(
        'title' => get_the_title($post_id),
        'url' => get_permalink($post_id),
    );
    
    return $breadcrumb;
}

/**
 * コラムの統計情報を取得
 * 
 * @return array 統計情報の配列
 */
function gi_get_column_stats() {
    $stats = wp_cache_get('column_stats', 'gi_column');
    
    if (false === $stats) {
        $column_count = wp_count_posts('column');
        $category_count = wp_count_terms(array('taxonomy' => 'column_category', 'hide_empty' => false));
        
        $stats = array(
            'total_columns' => $column_count->publish,
            'total_categories' => is_wp_error($category_count) ? 0 : $category_count,
        );
        
        wp_cache_set('column_stats', $stats, 'gi_column', 3600); // 1時間キャッシュ
    }
    
    return $stats;
}
