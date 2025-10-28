<?php
/**
 * Grant Single Page - Mobile Optimized Design v13.3
 * 助成金詳細ページ - モバイル最適化デザイン
 * 
 * @package Grant_Insight_Perfect
 * @version 13.3.0-mobile-optimized
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!have_posts()) {
    wp_redirect(home_url('/404'), 302);
    exit;
}

get_header();
the_post();

$post_id = get_the_ID();

// ページ表示用タイトル（パンくずリストなどで使用）
$seo_title = get_the_title();

// 注意: SEO メタタグ（title, description, OGP など）は
// inc/seo-optimization.php から wp_head() 経由で自動出力されます

// ACFデータ取得
$grant_data = array(
    'organization' => function_exists('get_field') ? get_field('organization', $post_id) : '',
    'max_amount' => function_exists('get_field') ? get_field('max_amount', $post_id) : '',
    'max_amount_numeric' => function_exists('get_field') ? intval(get_field('max_amount_numeric', $post_id)) : 0,
    'subsidy_rate' => function_exists('get_field') ? get_field('subsidy_rate', $post_id) : '',
    'deadline' => function_exists('get_field') ? get_field('deadline', $post_id) : '',
    'deadline_date' => function_exists('get_field') ? get_field('deadline_date', $post_id) : '',
    'grant_target' => function_exists('get_field') ? get_field('grant_target', $post_id) : '',
    'contact_info' => function_exists('get_field') ? get_field('contact_info', $post_id) : '',
    'official_url' => function_exists('get_field') ? get_field('official_url', $post_id) : '',
    'application_status' => function_exists('get_field') ? get_field('application_status', $post_id) : 'open',
    'required_documents' => function_exists('get_field') ? get_field('required_documents', $post_id) : '',
    'adoption_rate' => function_exists('get_field') ? floatval(get_field('adoption_rate', $post_id)) : 0,
    'grant_difficulty' => function_exists('get_field') ? get_field('grant_difficulty', $post_id) : 'normal',
    'is_featured' => function_exists('get_field') ? get_field('is_featured', $post_id) : false,
    'views_count' => function_exists('get_field') ? intval(get_field('views_count', $post_id)) : 0,
    'ai_summary' => function_exists('get_field') ? get_field('ai_summary', $post_id) : '',
);

// タクソノミー取得
$taxonomies = array(
    'categories' => wp_get_post_terms($post_id, 'grant_category'),
    'prefectures' => wp_get_post_terms($post_id, 'grant_prefecture'),
    'tags' => wp_get_post_tags($post_id),
);

foreach ($taxonomies as $key => $terms) {
    if (is_wp_error($terms) || empty($terms)) {
        $taxonomies[$key] = array();
    }
}

// 金額フォーマット
$formatted_amount = '';
$max_amount_yen = intval($grant_data['max_amount_numeric']);

if ($max_amount_yen > 0) {
    if ($max_amount_yen >= 100000000) {
        $formatted_amount = number_format($max_amount_yen / 100000000, 1) . '億円';
    } elseif ($max_amount_yen >= 10000) {
        $formatted_amount = number_format($max_amount_yen / 10000) . '万円';
    } else {
        $formatted_amount = number_format($max_amount_yen) . '円';
    }
} elseif (!empty($grant_data['max_amount'])) {
    $formatted_amount = $grant_data['max_amount'];
}

// 締切日計算
$deadline_info = '';
$deadline_class = '';
$days_remaining = 0;

if (!empty($grant_data['deadline_date'])) {
    $deadline_timestamp = strtotime($grant_data['deadline_date']);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $deadline_info = date('Y/n/j', $deadline_timestamp);
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / 86400);
        
        if ($days_remaining <= 0) {
            $deadline_class = 'closed';
            $deadline_info .= ' (終了)';
        } elseif ($days_remaining <= 7) {
            $deadline_class = 'urgent';
            $deadline_info .= ' (残' . $days_remaining . '日)';
        } elseif ($days_remaining <= 30) {
            $deadline_class = 'warning';
        }
    }
} elseif (!empty($grant_data['deadline'])) {
    $deadline_info = $grant_data['deadline'];
}

// 難易度設定
$difficulty_configs = array(
    'easy' => array('label' => '易', 'dots' => 1),
    'normal' => array('label' => '中', 'dots' => 2),
    'hard' => array('label' => '難', 'dots' => 3),
);

$difficulty = !empty($grant_data['grant_difficulty']) ? $grant_data['grant_difficulty'] : 'normal';
$difficulty_data = isset($difficulty_configs[$difficulty]) ? $difficulty_configs[$difficulty] : $difficulty_configs['normal'];

// ステータス
$status_configs = array(
    'open' => array('label' => '募集中', 'class' => 'open'),
    'closed' => array('label' => '終了', 'class' => 'closed'),
);

$application_status = !empty($grant_data['application_status']) ? $grant_data['application_status'] : 'open';
$status_data = isset($status_configs[$application_status]) ? $status_configs[$application_status] : $status_configs['open'];

// 閲覧数更新
$current_views = intval($grant_data['views_count']);
$new_views = $current_views + 1;
if (function_exists('update_post_meta')) {
    update_post_meta($post_id, 'views_count', $new_views);
    $grant_data['views_count'] = $new_views;
}

// SEO: OGP画像取得
$og_image = '';
if (has_post_thumbnail($post_id)) {
    $og_image = get_the_post_thumbnail_url($post_id, 'large');
} else {
    $og_image = get_site_icon_url(512);
    if (empty($og_image)) {
        $og_image = home_url('/wp-content/uploads/default-og-image.jpg');
    }
}

// SEO: キーワード生成
$keywords = array();
if (!empty($taxonomies['categories'])) {
    foreach ($taxonomies['categories'] as $cat) {
        $keywords[] = $cat->name;
    }
}
if (!empty($taxonomies['prefectures'])) {
    foreach ($taxonomies['prefectures'] as $pref) {
        $keywords[] = $pref->name;
    }
}
if (!empty($taxonomies['tags'])) {
    foreach ($taxonomies['tags'] as $tag) {
        $keywords[] = $tag->name;
    }
}
$keywords[] = '助成金';
$keywords[] = '補助金';
$keywords_string = implode(', ', array_unique($keywords));

// SEO: 投稿日時
$published_time = get_the_date('c', $post_id);
$modified_time = get_the_modified_date('c', $post_id);

// SEO: サイト情報
$site_name = get_bloginfo('name');
$site_url = home_url('/');

// ============================================
// ✅ SEO タグは wp_head() フックから自動出力されます
// inc/seo-optimization.php の GI_SEO_Optimizer クラスが処理
// - Title タグ（自動生成された SEO タイトル）
// - Meta Description（自動生成された説明文）
// - OGP タグ（Facebook, LINE 対応）
// - Twitter Card タグ
// - JSON-LD 構造化データ（Article, MonetaryGrant, GovernmentService, Breadcrumb, FAQ）
// - Canonical URL
// ============================================
?>


<main class="gus-single">
    <!-- ヘッダー -->
    <header class="gus-header">
        <div class="gus-header-top">
            <div class="gus-status-badge <?php echo $status_data['class']; ?> <?php echo $deadline_class; ?>">
                <?php echo $status_data['label']; ?>
                <?php if ($days_remaining > 0 && $days_remaining <= 30): ?>
                    · <?php echo $days_remaining; ?>日
                <?php endif; ?>
            </div>
            
            <?php if ($grant_data['is_featured']): ?>
            <div class="gus-featured-badge">
                注目
            </div>
            <?php endif; ?>
        </div>
        
        <h1 class="gus-title"><?php the_title(); ?></h1>
    </header>
    
    <!-- レイアウト -->
    <div class="gus-layout">
        <!-- メインコンテンツ -->
        <div class="gus-main">
            <?php if ($grant_data['ai_summary']): ?>
            <section class="gus-section" style="border-left-color: var(--gus-yellow);">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">AI要約</h2>
                </header>
                <div class="gus-section-content">
                    <p><?php echo esc_html($grant_data['ai_summary']); ?></p>
                </div>
            </section>
            <?php endif; ?>
            
            <section class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">詳細情報</h2>
                </header>
                <div class="gus-section-content">
                    <?php the_content(); ?>
                </div>
            </section>
            
            <section class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">助成金詳細</h2>
                </header>
                <div class="gus-section-content">
                    <table class="gus-table">
                        <?php if ($grant_data['subsidy_rate']): ?>
                        <tr>
                            <th>補助率</th>
                            <td><?php echo esc_html($grant_data['subsidy_rate']); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <th>難易度</th>
                            <td>
                                <div class="gus-difficulty">
                                    <strong><?php echo $difficulty_data['label']; ?></strong>
                                    <div class="gus-difficulty-dots">
                                        <?php for ($i = 1; $i <= 3; $i++): ?>
                                            <div class="gus-difficulty-dot <?php echo $i <= $difficulty_data['dots'] ? 'filled' : ''; ?>"></div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>閲覧数</th>
                            <td><?php echo number_format($grant_data['views_count']); ?></td>
                        </tr>
                    </table>
                </div>
            </section>
            
            <?php if ($grant_data['grant_target']): ?>
            <section class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">対象者・対象事業</h2>
                </header>
                <div class="gus-section-content">
                    <?php echo wp_kses_post($grant_data['grant_target']); ?>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['required_documents']): ?>
            <section class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">必要書類</h2>
                </header>
                <div class="gus-section-content">
                    <?php echo wp_kses_post($grant_data['required_documents']); ?>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['contact_info']): ?>
            <section class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">お問い合わせ</h2>
                </header>
                <div class="gus-section-content">
                    <?php echo nl2br(esc_html($grant_data['contact_info'])); ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
        
        <!-- サイドバー -->
        <aside class="gus-sidebar">
            <div class="gus-sidebar-card">
                <h3 class="gus-sidebar-title">
                    <span class="gus-icon gus-icon-link"></span> アクション
                </h3>
                <div class="gus-actions">
                    <?php if ($grant_data['official_url']): ?>
                    <a href="<?php echo esc_url($grant_data['official_url']); ?>" class="gus-btn gus-btn-yellow" target="_blank" rel="noopener">
                        <span class="gus-icon gus-icon-link"></span> 公式サイト
                    </a>
                    <?php endif; ?>
                    
                    <button class="gus-btn gus-btn-secondary" onclick="window.print()">
                        印刷
                    </button>
                </div>
            </div>
            
            <div class="gus-sidebar-card">
                <h3 class="gus-sidebar-title">
                    <span class="gus-icon gus-icon-chart"></span> 統計
                </h3>
                <div class="gus-stats">
                    <div class="gus-stat">
                        <span class="gus-stat-number"><?php echo number_format($grant_data['views_count']); ?></span>
                        <span class="gus-stat-label">閲覧</span>
                    </div>
                    
                    <?php if ($days_remaining > 0): ?>
                    <div class="gus-stat">
                        <span class="gus-stat-number"><?php echo $days_remaining; ?></span>
                        <span class="gus-stat-label">残日数</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="gus-stat">
                        <span class="gus-stat-number"><?php echo $difficulty_data['dots']; ?>/3</span>
                        <span class="gus-stat-label">難易度</span>
                    </div>
                </div>
            </div>
            
            <?php if ($taxonomies['categories'] || $taxonomies['prefectures'] || $taxonomies['tags']): ?>
            <div class="gus-sidebar-card">
                <h3 class="gus-sidebar-title">
                    <span class="gus-icon gus-icon-tag"></span> タグ
                </h3>
                
                <?php if ($taxonomies['categories']): ?>
                <div class="gus-tags-section">
                    <div class="gus-tags-label">カテゴリー</div>
                    <div class="gus-tags">
                        <?php foreach ($taxonomies['categories'] as $cat): ?>
                        <a href="<?php echo get_term_link($cat); ?>" class="gus-tag">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($taxonomies['prefectures']): ?>
                <div class="gus-tags-section">
                    <div class="gus-tags-label">地域</div>
                    <div class="gus-tags">
                        <?php foreach ($taxonomies['prefectures'] as $pref): ?>
                        <a href="<?php echo get_term_link($pref); ?>" class="gus-tag">
                            <?php echo esc_html($pref->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($taxonomies['tags']): ?>
                <div class="gus-tags-section">
                    <div class="gus-tags-label">タグ</div>
                    <div class="gus-tags">
                        <?php foreach ($taxonomies['tags'] as $tag): ?>
                        <a href="<?php echo get_term_link($tag); ?>" class="gus-tag">
                            #<?php echo esc_html($tag->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </aside>
    </div>
    
    <!-- ============================================
         内部リンク戦略: 関連助成金セクション
         ============================================ -->
    <?php
    // 関連助成金取得（同じカテゴリー）
    $related_args = array(
        'post_type' => 'grant',
        'posts_per_page' => 4,
        'post__not_in' => array($post_id),
        'post_status' => 'publish',
        'orderby' => 'rand',
    );
    
    if (!empty($taxonomies['categories'])) {
        $related_args['tax_query'] = array(
            array(
                'taxonomy' => 'grant_category',
                'field' => 'term_id',
                'terms' => $taxonomies['categories'][0]->term_id,
            ),
        );
    }
    
    $related_query = new WP_Query($related_args);
    
    if ($related_query->have_posts()) :
    ?>
    <section class="gus-related-section" style="margin-top: 40px; padding-top: 40px; border-top: 2px solid var(--gus-gray-300);">
        <h2 class="gus-section-title" style="font-size: var(--gus-text-xl); font-weight: 700; margin-bottom: var(--gus-space-lg); display: flex; align-items: center; gap: var(--gus-space-sm);">
            <span class="gus-icon gus-icon-document"></span>
            関連する助成金
        </h2>
        <div class="gus-related-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--gus-space-lg);">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
            <article class="gus-related-card" style="background: var(--gus-gray-50); border: 1px solid var(--gus-gray-300); border-radius: var(--gus-radius); padding: var(--gus-space-md); transition: var(--gus-transition);">
                <h3 style="font-size: var(--gus-text-md); font-weight: 700; margin-bottom: var(--gus-space-sm); line-height: 1.4;">
                    <a href="<?php the_permalink(); ?>" style="color: var(--gus-black); text-decoration: none;" aria-label="<?php echo esc_attr(get_the_title() . 'の詳細を見る'); ?>">
                        <?php the_title(); ?>
                    </a>
                </h3>
                <?php
                $related_max_amount = function_exists('get_field') ? get_field('max_amount', get_the_ID()) : '';
                $related_deadline = function_exists('get_field') ? get_field('deadline', get_the_ID()) : '';
                ?>
                <?php if ($related_max_amount || $related_deadline): ?>
                <div style="font-size: var(--gus-text-sm); color: var(--gus-gray-600); margin-bottom: var(--gus-space-sm);">
                    <?php if ($related_max_amount): ?>
                    <div><strong>最大:</strong> <?php echo esc_html($related_max_amount); ?></div>
                    <?php endif; ?>
                    <?php if ($related_deadline): ?>
                    <div><strong>締切:</strong> <?php echo esc_html($related_deadline); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="gus-btn gus-btn-secondary" style="margin-top: auto; font-size: var(--gus-text-sm); padding: 8px 12px;" aria-label="<?php echo esc_attr(get_the_title() . 'の詳細ページへ'); ?>">
                    詳細を見る →
                </a>
            </article>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
    endif;
    wp_reset_postdata();
    ?>
    
    <!-- ============================================
         内部リンク戦略: カテゴリー・地域リンク
         ============================================ -->
    <section class="gus-taxonomy-links" style="margin-top: 40px; padding: var(--gus-space-lg); background: var(--gus-gray-50); border: 1px solid var(--gus-gray-300); border-radius: var(--gus-radius);">
        <h2 class="gus-section-title" style="font-size: var(--gus-text-lg); font-weight: 700; margin-bottom: var(--gus-space-md);">
            この助成金のカテゴリー・地域
        </h2>
        
        <div style="display: grid; gap: var(--gus-space-md);">
            <?php if (!empty($taxonomies['categories'])): ?>
            <div>
                <div class="gus-tags-label" style="font-size: var(--gus-text-xs); color: var(--gus-gray-600); font-weight: 600; margin-bottom: var(--gus-space-xs); text-transform: uppercase;">カテゴリー</div>
                <div style="display: flex; flex-wrap: wrap; gap: var(--gus-space-sm);">
                    <?php foreach ($taxonomies['categories'] as $cat): ?>
                    <a href="<?php echo get_term_link($cat); ?>" class="gus-tag" aria-label="<?php echo esc_attr($cat->name . 'カテゴリーの助成金一覧を見る'); ?>">
                        <?php echo esc_html($cat->name); ?> の助成金を見る →
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($taxonomies['prefectures'])): ?>
            <div>
                <div class="gus-tags-label" style="font-size: var(--gus-text-xs); color: var(--gus-gray-600); font-weight: 600; margin-bottom: var(--gus-space-xs); text-transform: uppercase;">地域</div>
                <div style="display: flex; flex-wrap: wrap; gap: var(--gus-space-sm);">
                    <?php foreach ($taxonomies['prefectures'] as $pref): ?>
                    <a href="<?php echo get_term_link($pref); ?>" class="gus-tag" aria-label="<?php echo esc_attr($pref->name . 'の助成金一覧を見る'); ?>">
                        <?php echo esc_html($pref->name); ?> の助成金を見る →
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- ============================================
         パンくずナビゲーション（視覚的）
         ============================================ -->
    <nav class="gus-breadcrumb" aria-label="パンくずナビゲーション" style="margin-top: 40px; padding: var(--gus-space-md); background: var(--gus-white); border: 1px solid var(--gus-gray-300); border-radius: var(--gus-radius);">
        <ol style="list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: var(--gus-space-sm); font-size: var(--gus-text-sm);">
            <li style="display: flex; align-items: center;">
                <a href="<?php echo home_url('/'); ?>" style="color: var(--gus-gray-700); text-decoration: none;" aria-label="ホームに戻る">ホーム</a>
                <span style="margin: 0 8px; color: var(--gus-gray-500);">›</span>
            </li>
            <li style="display: flex; align-items: center;">
                <a href="<?php echo home_url('/grant/'); ?>" style="color: var(--gus-gray-700); text-decoration: none;" aria-label="助成金一覧ページ">助成金一覧</a>
                <?php if (!empty($taxonomies['categories'])): ?>
                <span style="margin: 0 8px; color: var(--gus-gray-500);">›</span>
            </li>
            <li style="display: flex; align-items: center;">
                <a href="<?php echo get_term_link($taxonomies['categories'][0]); ?>" style="color: var(--gus-gray-700); text-decoration: none;" aria-label="<?php echo esc_attr($taxonomies['categories'][0]->name . 'カテゴリー'); ?>">
                    <?php echo esc_html($taxonomies['categories'][0]->name); ?>
                </a>
                <span style="margin: 0 8px; color: var(--gus-gray-500);">›</span>
            </li>
            <li style="color: var(--gus-gray-900); font-weight: 600;" aria-current="page">
                <?php echo esc_html(wp_trim_words($seo_title, 8, '...')); ?>
            </li>
                <?php else: ?>
                <span style="margin: 0 8px; color: var(--gus-gray-500);">›</span>
            </li>
            <li style="color: var(--gus-gray-900); font-weight: 600;" aria-current="page">
                <?php echo esc_html(wp_trim_words($seo_title, 8, '...')); ?>
            </li>
                <?php endif; ?>
        </ol>
    </nav>
</main>

<?php get_footer(); ?>