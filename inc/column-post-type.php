<?php
/**
 * Column Custom Post Type Registration
 * コラム用カスタム投稿タイプとタクソノミーの登録
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

/**
 * コラムカスタム投稿タイプの登録
 */
function gi_register_column_post_type() {
    $labels = array(
        'name'                  => 'コラム',
        'singular_name'         => 'コラム',
        'menu_name'             => 'コラム',
        'name_admin_bar'        => 'コラム',
        'add_new'               => '新規追加',
        'add_new_item'          => '新規コラムを追加',
        'new_item'              => '新規コラム',
        'edit_item'             => 'コラムを編集',
        'view_item'             => 'コラムを表示',
        'all_items'             => 'すべてのコラム',
        'search_items'          => 'コラムを検索',
        'parent_item_colon'     => '親コラム:',
        'not_found'             => 'コラムが見つかりませんでした。',
        'not_found_in_trash'    => 'ゴミ箱にコラムはありません。',
        'featured_image'        => 'アイキャッチ画像',
        'set_featured_image'    => 'アイキャッチ画像を設定',
        'remove_featured_image' => 'アイキャッチ画像を削除',
        'use_featured_image'    => 'アイキャッチ画像として使用',
        'archives'              => 'コラムアーカイブ',
        'insert_into_item'      => 'コラムに挿入',
        'uploaded_to_this_item' => 'このコラムにアップロード',
        'filter_items_list'     => 'コラムリストをフィルター',
        'items_list_navigation' => 'コラムリストナビゲーション',
        'items_list'            => 'コラムリスト',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array(
            'slug'       => 'column',
            'with_front' => false,
        ),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-edit-large',
        'supports'            => array(
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'comments',
            'revisions',
            'custom-fields',
        ),
        'show_in_rest'        => true, // Gutenbergエディター対応
        'rest_base'           => 'columns',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    );

    register_post_type('column', $args);
}
add_action('init', 'gi_register_column_post_type');

/**
 * コラムカテゴリータクソノミーの登録
 */
function gi_register_column_taxonomies() {
    // コラムカテゴリー
    $category_labels = array(
        'name'              => 'コラムカテゴリー',
        'singular_name'     => 'コラムカテゴリー',
        'search_items'      => 'カテゴリーを検索',
        'all_items'         => 'すべてのカテゴリー',
        'parent_item'       => '親カテゴリー',
        'parent_item_colon' => '親カテゴリー:',
        'edit_item'         => 'カテゴリーを編集',
        'update_item'       => 'カテゴリーを更新',
        'add_new_item'      => '新規カテゴリーを追加',
        'new_item_name'     => '新規カテゴリー名',
        'menu_name'         => 'カテゴリー',
    );

    $category_args = array(
        'hierarchical'      => true, // カテゴリー形式（階層構造あり）
        'labels'            => $category_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array(
            'slug'       => 'column-category',
            'with_front' => false,
        ),
        'show_in_rest'      => true,
    );

    register_taxonomy('column_category', array('column'), $category_args);

    // コラムタグ
    $tag_labels = array(
        'name'              => 'コラムタグ',
        'singular_name'     => 'コラムタグ',
        'search_items'      => 'タグを検索',
        'all_items'         => 'すべてのタグ',
        'edit_item'         => 'タグを編集',
        'update_item'       => 'タグを更新',
        'add_new_item'      => '新規タグを追加',
        'new_item_name'     => '新規タグ名',
        'menu_name'         => 'タグ',
    );

    $tag_args = array(
        'hierarchical'      => false, // タグ形式（階層構造なし）
        'labels'            => $tag_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array(
            'slug'       => 'column-tag',
            'with_front' => false,
        ),
        'show_in_rest'      => true,
    );

    register_taxonomy('column_tag', array('column'), $tag_args);
}
add_action('init', 'gi_register_column_taxonomies');

/**
 * コラムのカスタムフィールド（メタボックス）
 */
function gi_add_column_meta_boxes() {
    add_meta_box(
        'column_details',
        'コラム詳細情報',
        'gi_render_column_meta_box',
        'column',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'gi_add_column_meta_boxes');

/**
 * コラムメタボックスのレンダリング
 */
function gi_render_column_meta_box($post) {
    // Nonceフィールドを追加
    wp_nonce_field('gi_save_column_meta', 'gi_column_meta_nonce');

    // 既存の値を取得
    $reading_time = get_post_meta($post->ID, '_column_reading_time', true);
    $author_name = get_post_meta($post->ID, '_column_author_name', true);
    $author_title = get_post_meta($post->ID, '_column_author_title', true);
    $related_grant_ids = get_post_meta($post->ID, '_column_related_grants', true);
    $is_featured = get_post_meta($post->ID, '_column_is_featured', true);
    $external_link = get_post_meta($post->ID, '_column_external_link', true);

    ?>
    <div style="padding: 10px;">
        <p>
            <label for="column_reading_time" style="display: inline-block; width: 150px; font-weight: bold;">
                読了時間（分）:
            </label>
            <input type="number" id="column_reading_time" name="column_reading_time" 
                   value="<?php echo esc_attr($reading_time); ?>" 
                   min="1" max="60" style="width: 100px;">
            <span style="color: #666; margin-left: 10px;">※自動計算されますが、手動で変更も可能です</span>
        </p>

        <p>
            <label for="column_author_name" style="display: inline-block; width: 150px; font-weight: bold;">
                著者名:
            </label>
            <input type="text" id="column_author_name" name="column_author_name" 
                   value="<?php echo esc_attr($author_name); ?>" 
                   style="width: 300px;" placeholder="例: 山田太郎">
        </p>

        <p>
            <label for="column_author_title" style="display: inline-block; width: 150px; font-weight: bold;">
                著者の肩書き:
            </label>
            <input type="text" id="column_author_title" name="column_author_title" 
                   value="<?php echo esc_attr($author_title); ?>" 
                   style="width: 300px;" placeholder="例: 補助金コンサルタント">
        </p>

        <p>
            <label for="column_is_featured" style="display: inline-block; width: 150px; font-weight: bold;">
                注目記事:
            </label>
            <input type="checkbox" id="column_is_featured" name="column_is_featured" 
                   value="1" <?php checked($is_featured, '1'); ?>>
            <span style="color: #666; margin-left: 10px;">チェックすると、注目記事として表示されます</span>
        </p>

        <p>
            <label for="column_external_link" style="display: inline-block; width: 150px; font-weight: bold;">
                外部リンク:
            </label>
            <input type="url" id="column_external_link" name="column_external_link" 
                   value="<?php echo esc_attr($external_link); ?>" 
                   style="width: 500px;" placeholder="https://example.com">
            <span style="color: #666; margin-left: 10px;">※設定すると、クリック時に外部サイトへ遷移します</span>
        </p>

        <p>
            <label for="column_related_grants" style="display: inline-block; width: 150px; font-weight: bold; vertical-align: top;">
                関連助成金:
            </label>
            <textarea id="column_related_grants" name="column_related_grants" 
                      rows="3" style="width: 500px;"><?php echo esc_textarea($related_grant_ids); ?></textarea>
            <br>
            <span style="color: #666; margin-left: 160px;">※助成金のIDをカンマ区切りで入力（例: 123,456,789）</span>
        </p>
    </div>
    <?php
}

/**
 * コラムメタデータの保存
 */
function gi_save_column_meta($post_id) {
    // Nonce検証
    if (!isset($_POST['gi_column_meta_nonce']) || 
        !wp_verify_nonce($_POST['gi_column_meta_nonce'], 'gi_save_column_meta')) {
        return;
    }

    // 自動保存の場合は処理しない
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 読了時間
    if (isset($_POST['column_reading_time'])) {
        $reading_time = absint($_POST['column_reading_time']);
        update_post_meta($post_id, '_column_reading_time', $reading_time);
    } else {
        // 自動計算（本文の文字数から）
        $content = get_post_field('post_content', $post_id);
        $word_count = mb_strlen(strip_tags($content));
        $reading_time = max(1, ceil($word_count / 600)); // 1分あたり600文字として計算
        update_post_meta($post_id, '_column_reading_time', $reading_time);
    }

    // 著者名
    if (isset($_POST['column_author_name'])) {
        update_post_meta($post_id, '_column_author_name', sanitize_text_field($_POST['column_author_name']));
    }

    // 著者の肩書き
    if (isset($_POST['column_author_title'])) {
        update_post_meta($post_id, '_column_author_title', sanitize_text_field($_POST['column_author_title']));
    }

    // 注目記事
    $is_featured = isset($_POST['column_is_featured']) ? '1' : '0';
    update_post_meta($post_id, '_column_is_featured', $is_featured);

    // 外部リンク
    if (isset($_POST['column_external_link'])) {
        update_post_meta($post_id, '_column_external_link', esc_url_raw($_POST['column_external_link']));
    }

    // 関連助成金
    if (isset($_POST['column_related_grants'])) {
        $related_grants = sanitize_text_field($_POST['column_related_grants']);
        update_post_meta($post_id, '_column_related_grants', $related_grants);
    }
}
add_action('save_post_column', 'gi_save_column_meta');

/**
 * コラムのパーマリンク構造を最適化
 */
function gi_column_post_type_link($post_link, $post) {
    if ($post->post_type !== 'column') {
        return $post_link;
    }
    
    // カテゴリーをURLに含める
    $terms = get_the_terms($post->ID, 'column_category');
    if ($terms && !is_wp_error($terms)) {
        $term = array_shift($terms);
        $post_link = str_replace('%column_category%', $term->slug, $post_link);
    } else {
        $post_link = str_replace('%column_category%', 'uncategorized', $post_link);
    }
    
    return $post_link;
}
add_filter('post_type_link', 'gi_column_post_type_link', 10, 2);

/**
 * リライトルールをフラッシュ（初回のみ）
 */
function gi_column_flush_rewrite_rules() {
    if (get_option('gi_column_rewrite_flushed') !== 'yes') {
        gi_register_column_post_type();
        gi_register_column_taxonomies();
        flush_rewrite_rules();
        update_option('gi_column_rewrite_flushed', 'yes');
    }
}
add_action('init', 'gi_column_flush_rewrite_rules', 999);
