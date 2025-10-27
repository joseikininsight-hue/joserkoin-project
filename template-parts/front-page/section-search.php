<?php
/**
 * Mobile-Optimized Subsidy Search Section v5.0
 * モバイル最適化補助金検索セクション
 * 
 * Features:
 * - シンプルな検索フォーム（カテゴリ1列、都道府県）
 * - 用途（カテゴリー）から探す
 * - 都道府県から探す（8地域別・白黒アイコン）
 * - おすすめ補助金（正確な締切日表示）
 * - 新着補助金（正確な締切日表示）
 * - 100%幅モバイルレスポンシブ
 * 
 * @package Grant_Insight_Perfect
 * @version 5.0.0 - Mobile Optimized Clean Version
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// カテゴリーを取得
$all_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC'
));

// 都道府県を取得
$prefectures = gi_get_all_prefectures();

// 市町村を取得（全件）
$all_municipalities = get_terms(array(
    'taxonomy' => 'grant_municipality',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
));

// レコメンド補助金を取得（注目度の高い4件）
$recommended_grants = get_posts(array(
    'post_type' => 'grant',
    'posts_per_page' => 4,
    'meta_key' => 'is_featured',
    'meta_value' => '1',
    'orderby' => 'rand',
    'order' => 'DESC'
));

// 新着補助金を取得（最新8件）
$new_grants = get_posts(array(
    'post_type' => 'grant',
    'posts_per_page' => 8,
    'orderby' => 'date',
    'order' => 'DESC'
));

// カテゴリーをグループ化（用途から探す用）
$category_groups = array(
    array(
        'name' => '補助金の種類',
        'icon' => 'fa-briefcase',
        'categories' => array_slice($all_categories, 0, 8)
    ),
    array(
        'name' => '対象分野',
        'icon' => 'fa-industry',
        'categories' => array_slice($all_categories, 8, 8)
    ),
    array(
        'name' => '支援内容',
        'icon' => 'fa-hands-helping',
        'categories' => array_slice($all_categories, 16, 8)
    )
);

// 地域別都道府県データ（白黒アイコン）
$regions_data = array(
    array(
        'name' => '北海道・東北',
        'class' => 'hokkaido-tohoku',
        'icon' => 'fa-map',
        'prefectures' => array('北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県')
    ),
    array(
        'name' => '北陸・甲信越',
        'class' => 'hokuriku',
        'icon' => 'fa-mountain',
        'prefectures' => array('新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県')
    ),
    array(
        'name' => '関東',
        'class' => 'kanto',
        'icon' => 'fa-city',
        'prefectures' => array('東京都', '埼玉県', '千葉県', '神奈川県', '茨城県', '栃木県', '群馬県')
    ),
    array(
        'name' => '東海',
        'class' => 'tokai',
        'icon' => 'fa-building',
        'prefectures' => array('愛知県', '岐阜県', '三重県', '静岡県')
    ),
    array(
        'name' => '関西',
        'class' => 'kansai',
        'icon' => 'fa-landmark',
        'prefectures' => array('大阪府', '兵庫県', '京都府', '滋賀県', '奈良県', '和歌山県')
    ),
    array(
        'name' => '中国',
        'class' => 'chugoku',
        'icon' => 'fa-water',
        'prefectures' => array('鳥取県', '島根県', '岡山県', '広島県', '山口県')
    ),
    array(
        'name' => '四国',
        'class' => 'shikoku',
        'icon' => 'fa-tree',
        'prefectures' => array('徳島県', '香川県', '愛媛県', '高知県')
    ),
    array(
        'name' => '九州・沖縄',
        'class' => 'kyushu',
        'icon' => 'fa-sun',
        'prefectures' => array('福岡県', '佐賀県', '熊本県', '大分県', '宮崎県', '鹿児島県', '長崎県', '沖縄県')
    )
);
?>

<!-- 公開求人数バナー -->
<section class="stats-banner">
    <div class="stats-wrapper">
        <div class="stat-item">
            <span class="stat-number">
                <?php 
                $total_grants = wp_count_posts('grant')->publish;
                echo number_format($total_grants);
                ?>件
            </span>
            <span class="stat-label">掲載</span>
        </div>
        <div class="stat-update">
            <?php echo date('Y/m/d'); ?> (<?php echo array('日', '月', '火', '水', '木', '金', '土')[date('w')]; ?>) 更新 / 毎週月・木曜更新
        </div>
    </div>
</section>

<!-- 検索セクション -->
<section class="search-section">
    <div class="search-wrapper">
        <h2 class="section-title">
            <i class="fas fa-search"></i>
            補助金から探す
        </h2>

        <form class="search-form" id="grant-search-form">
            <!-- 用途（カテゴリ） -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-briefcase"></i>
                    用途
                </label>
                <select id="category-select" class="form-select">
                    <option value="">カテゴリーを選択</option>
                    <?php foreach ($all_categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat->slug); ?>">
                            <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 都道府県 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-map-marker-alt"></i>
                    都道府県
                </label>
                <select id="prefecture-select" class="form-select">
                    <option value="">都道府県を選択</option>
                    <?php foreach ($prefectures as $pref) : ?>
                        <option value="<?php echo esc_attr($pref['slug']); ?>">
                            <?php echo esc_html($pref['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 市町村 -->
            <div class="form-group" id="municipality-group" style="display: none;">
                <label class="form-label">
                    <i class="fas fa-building"></i>
                    市町村
                </label>
                <select id="municipality-select" class="form-select">
                    <option value="">市町村を選択</option>
                </select>
            </div>

            <!-- フリーワード検索 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-search"></i>
                    フリーワード
                </label>
                <input type="text" 
                       id="keyword-input" 
                       class="form-input" 
                       placeholder="例：IT導入補助金、設備投資、創業支援など">
            </div>

            <!-- ボタングループ -->
            <div class="button-group">
                <button type="button" class="btn btn-reset" id="reset-btn">
                    <i class="fas fa-undo"></i>
                    条件クリア
                </button>
                <button type="submit" class="btn btn-search" id="search-btn">
                    <i class="fas fa-search"></i>
                    この条件で検索する
                </button>
            </div>
        </form>

        <!-- 補助リンク -->
        <div class="sub-links">
            <a href="<?php echo home_url('/grants/'); ?>" class="sub-link">
                <i class="fas fa-list"></i>
                詳しい条件で検索する
            </a>
            <a href="#" class="sub-link">
                <i class="fas fa-bookmark"></i>
                保存した検索条件
            </a>
            <a href="#" class="sub-link">
                <i class="fas fa-history"></i>
                閲覧した求人
            </a>
        </div>
    </div>
</section>

<!-- 用途から探すセクション -->
<section class="category-browse-section">
    <div class="browse-wrapper">
        <h2 class="section-title">
            <i class="fas fa-th-large"></i>
            用途から探す
        </h2>

        <div class="category-grid">
            <?php foreach ($category_groups as $group) : ?>
                <?php if (!empty($group['categories'])) : ?>
                <div class="category-group-card">
                    <h3 class="group-title">
                        <i class="fas <?php echo esc_attr($group['icon']); ?>"></i>
                        <?php echo esc_html($group['name']); ?>
                    </h3>
                    <div class="category-links">
                        <?php foreach ($group['categories'] as $category) : ?>
                            <?php 
                            $cat_url = get_term_link($category->slug, 'grant_category');
                            if (!is_wp_error($cat_url)) :
                            ?>
                            <a href="<?php echo esc_url($cat_url); ?>" class="category-link">
                                <?php echo esc_html($category->name); ?>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 都道府県から探すセクション -->
<section class="prefecture-section">
    <div class="prefecture-wrapper">
        <h2 class="section-title">
            <i class="fas fa-map-marked-alt"></i>
            都道府県から探す
        </h2>

        <div class="prefecture-grid">
            <?php foreach ($regions_data as $region) : ?>
            <div class="region-card">
                <h3 class="region-title">
                    <i class="fas <?php echo esc_attr($region['icon']); ?>"></i>
                    <?php echo esc_html($region['name']); ?>
                </h3>
                <div class="prefecture-links">
                    <?php 
                    foreach ($region['prefectures'] as $pref_name) : 
                        $pref_slug = '';
                        foreach ($prefectures as $pref) {
                            if ($pref['name'] === $pref_name) {
                                $pref_slug = $pref['slug'];
                                break;
                            }
                        }
                        if ($pref_slug) :
                            $pref_url = get_term_link($pref_slug, 'grant_prefecture');
                            if (!is_wp_error($pref_url)) :
                    ?>
                        <a href="<?php echo esc_url($pref_url); ?>" class="prefecture-link">
                            <?php echo esc_html($pref_name); ?>
                        </a>
                    <?php 
                            endif;
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 市町村から探すセクション -->
<section class="municipality-section">
    <div class="municipality-wrapper">
        <h2 class="section-title">
            <i class="fas fa-building"></i>
            市町村から探す
        </h2>

        <div class="municipality-search-container">
            <!-- 都道府県選択 -->
            <div class="municipality-filter">
                <label for="municipality-prefecture-filter" class="filter-label">
                    <i class="fas fa-map-marker-alt"></i>
                    都道府県で絞り込み
                </label>
                <select id="municipality-prefecture-filter" class="filter-select">
                    <option value="">すべての都道府県</option>
                    <?php foreach ($prefectures as $pref) : ?>
                        <option value="<?php echo esc_attr($pref['slug']); ?>">
                            <?php echo esc_html($pref['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 市町村リスト -->
            <div class="municipality-grid" id="municipality-list">
                <?php if (!empty($all_municipalities) && !is_wp_error($all_municipalities)) : ?>
                    <?php foreach ($all_municipalities as $municipality) : ?>
                        <?php 
                        $muni_url = get_term_link($municipality, 'grant_municipality');
                        if (is_wp_error($muni_url)) continue;
                        
                        // カスタムフィールドから都道府県を取得（軽量化のため投稿検索は削除）
                        $related_pref_slug = get_term_meta($municipality->term_id, 'prefecture_slug', true);
                        ?>
                        <a href="<?php echo esc_url($muni_url); ?>" 
                           class="municipality-link" 
                           data-prefecture="<?php echo esc_attr($related_pref_slug); ?>">
                            <?php echo esc_html($municipality->name); ?>
                            <span class="municipality-count">(<?php echo $municipality->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="no-municipalities">市町村データがありません</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- あなたへのおすすめ -->
<section class="recommend-section">
    <div class="recommend-wrapper">
        <div class="section-header">
            <div class="header-left">
                <h2 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    あなたの関覧履歴からおすすめ
                </h2>
                <p class="section-desc">希望条件を設定しておくと、あなたに合った補助金が見つかります</p>
            </div>
            <a href="<?php echo home_url('/grants/'); ?>" class="view-all">
                一覧へ <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div class="grants-grid">
            <?php 
            if (!empty($recommended_grants)) :
                foreach ($recommended_grants as $grant) : 
                    // メタデータ取得
                    $deadline = get_post_meta($grant->ID, 'deadline', true);
                    $max_amount = get_post_meta($grant->ID, 'max_amount', true);
                    $organization = get_post_meta($grant->ID, 'organization', true);
                    $is_featured = get_post_meta($grant->ID, 'is_featured', true);
                    
                    // 締切日のフォーマット処理
                    $deadline_display = '';
                    if ($deadline) {
                        // 複数の日付フォーマットに対応
                        $timestamp = false;
                        if (is_numeric($deadline)) {
                            // UNIXタイムスタンプの場合
                            $timestamp = intval($deadline);
                        } else {
                            // 文字列の日付の場合
                            $timestamp = strtotime($deadline);
                        }
                        
                        if ($timestamp && $timestamp > 0) {
                            $deadline_display = date('Y/m/d', $timestamp);
                        } else {
                            $deadline_display = $deadline; // そのまま表示
                        }
                    }
                    
                    $permalink = get_permalink($grant->ID);
                    
                    // カテゴリー
                    $grant_categories = get_the_terms($grant->ID, 'grant_category');
                    $category_names = array();
                    if ($grant_categories && !is_wp_error($grant_categories)) {
                        foreach (array_slice($grant_categories, 0, 2) as $cat) {
                            $category_names[] = $cat->name;
                        }
                    }
                    
                    // 都道府県
                    $grant_prefectures = get_the_terms($grant->ID, 'grant_prefecture');
                    $prefecture_name = $grant_prefectures && !is_wp_error($grant_prefectures) ? $grant_prefectures[0]->name : '';
            ?>
                <article class="grant-card">
                    <?php if ($is_featured) : ?>
                    <span class="badge badge-featured">注目</span>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url($permalink); ?>" class="card-link">
                        <div class="card-header">
                            <div class="card-org">
                                <i class="fas fa-building"></i>
                                <?php echo esc_html($organization ?: '公的機関'); ?>
                            </div>
                            <button class="btn-bookmark" onclick="event.preventDefault();">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        
                        <h3 class="card-title"><?php echo esc_html($grant->post_title); ?></h3>
                        
                        <div class="card-tags">
                            <?php foreach ($category_names as $cat_name) : ?>
                            <span class="tag">
                                <i class="fas fa-tag"></i>
                                <?php echo esc_html($cat_name); ?>
                            </span>
                            <?php endforeach; ?>
                            
                            <?php if ($prefecture_name) : ?>
                            <span class="tag tag-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo esc_html($prefecture_name); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer">
                            <?php if ($max_amount) : ?>
                            <div class="footer-item">
                                <i class="fas fa-yen-sign"></i>
                                <span>最大: <?php echo esc_html($max_amount); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($deadline_display) : ?>
                            <div class="footer-item deadline">
                                <i class="fas fa-clock"></i>
                                <span>締切: <?php echo esc_html($deadline_display); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php 
                endforeach;
            else : 
            ?>
                <p class="no-data">現在、おすすめの補助金はありません。</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 新着補助金 -->
<section class="new-grants-section">
    <div class="new-grants-wrapper">
        <div class="section-header">
            <div class="header-left">
                <h2 class="section-title">
                    <i class="fas fa-clock"></i>
                    新着補助金
                    <span class="count-badge"><?php echo count($new_grants); ?></span>
                </h2>
                <p class="section-desc"><?php echo date('Y/m/d'); ?> (<?php echo array('日', '月', '火', '水', '木', '金', '土')[date('w')]; ?>) 更新 / 毎週月・木曜更新</p>
            </div>
            <a href="<?php echo home_url('/grants/?orderby=date'); ?>" class="view-all">
                一覧へ <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div class="grants-grid">
            <?php 
            if (!empty($new_grants)) :
                foreach ($new_grants as $grant) : 
                    // メタデータ取得
                    $deadline = get_post_meta($grant->ID, 'deadline', true);
                    $max_amount = get_post_meta($grant->ID, 'max_amount', true);
                    $organization = get_post_meta($grant->ID, 'organization', true);
                    $is_new = (strtotime($grant->post_date) > strtotime('-7 days'));
                    
                    // 締切日のフォーマット処理
                    $deadline_display = '';
                    if ($deadline) {
                        $timestamp = false;
                        if (is_numeric($deadline)) {
                            $timestamp = intval($deadline);
                        } else {
                            $timestamp = strtotime($deadline);
                        }
                        
                        if ($timestamp && $timestamp > 0) {
                            $deadline_display = date('Y/m/d', $timestamp);
                        } else {
                            $deadline_display = $deadline;
                        }
                    }
                    
                    $permalink = get_permalink($grant->ID);
                    
                    // カテゴリー
                    $grant_categories = get_the_terms($grant->ID, 'grant_category');
                    $category_names = array();
                    if ($grant_categories && !is_wp_error($grant_categories)) {
                        foreach (array_slice($grant_categories, 0, 2) as $cat) {
                            $category_names[] = $cat->name;
                        }
                    }
                    
                    // 都道府県
                    $grant_prefectures = get_the_terms($grant->ID, 'grant_prefecture');
                    $prefecture_name = $grant_prefectures && !is_wp_error($grant_prefectures) ? $grant_prefectures[0]->name : '';
            ?>
                <article class="grant-card">
                    <?php if ($is_new) : ?>
                    <span class="badge badge-new">NEW</span>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url($permalink); ?>" class="card-link">
                        <div class="card-header">
                            <div class="card-org">
                                <i class="fas fa-building"></i>
                                <?php echo esc_html($organization ?: '公的機関'); ?>
                            </div>
                            <button class="btn-bookmark" onclick="event.preventDefault();">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        
                        <h3 class="card-title"><?php echo esc_html($grant->post_title); ?></h3>
                        
                        <div class="card-tags">
                            <?php foreach ($category_names as $cat_name) : ?>
                            <span class="tag">
                                <i class="fas fa-tag"></i>
                                <?php echo esc_html($cat_name); ?>
                            </span>
                            <?php endforeach; ?>
                            
                            <?php if ($prefecture_name) : ?>
                            <span class="tag tag-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo esc_html($prefecture_name); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer">
                            <?php if ($max_amount) : ?>
                            <div class="footer-item">
                                <i class="fas fa-yen-sign"></i>
                                <span>最大: <?php echo esc_html($max_amount); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($deadline_display) : ?>
                            <div class="footer-item deadline">
                                <i class="fas fa-clock"></i>
                                <span>締切: <?php echo esc_html($deadline_display); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php 
                endforeach;
            else : 
            ?>
                <p class="no-data">現在、新着補助金はありません。</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* ============================================
   Mobile-Optimized Search Interface v5.0
   モバイル最適化補助金検索
   ============================================ */

/* ===== 基本設定 ===== */
* {
    box-sizing: border-box;
}

/* ===== 統計バナー ===== */
.stats-banner {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 3px solid #000000;
    padding: 16px 0;
}

.stats-wrapper {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-number {
    font-size: 28px;
    font-weight: 900;
    color: #000000;
}

.stat-label {
    font-size: 14px;
    font-weight: 600;
    color: #666666;
}

.stat-update {
    font-size: 12px;
    color: #666666;
    text-align: center;
}

/* ===== 検索セクション ===== */
.search-section {
    background: #ffffff;
    padding: 32px 0;
}

.search-wrapper {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 16px;
}

.section-title {
    font-size: 22px;
    font-weight: 900;
    color: #000000;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    font-size: 24px;
}

.search-form {
    background: #ffffff;
    border: 2px solid #000000;
    padding: 20px 16px;
    margin-bottom: 16px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group:last-of-type {
    margin-bottom: 0;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 700;
    color: #000000;
    margin-bottom: 8px;
}

.form-select,
.form-input {
    width: 100%;
    padding: 12px 14px;
    font-size: 15px;
    font-weight: 500;
    color: #000000;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 0;
}

.form-select:focus,
.form-input:focus {
    outline: none;
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.3);
}

.form-input::placeholder {
    color: #666666;
    font-weight: 400;
}

.button-group {
    display: flex;
    gap: 12px;
    margin-top: 20px;
}

.btn {
    flex: 1;
    padding: 14px 16px;
    font-size: 15px;
    font-weight: 700;
    border: 2px solid #000000;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-reset {
    background: #ffffff;
    color: #000000;
}

.btn-search {
    background: #000000;
    color: #ffffff;
}

.btn-search:active {
    background: #ffeb3b;
    color: #000000;
}

.sub-links {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid #e5e5e5;
}

.sub-link {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    font-weight: 600;
    color: #000000;
    text-decoration: none;
}

.sub-link i {
    font-size: 12px;
}

/* ===== 用途セクション ===== */
.category-browse-section {
    background: #f8f9fa;
    padding: 32px 0;
}

.browse-wrapper {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 16px;
}

.category-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}

.category-group-card {
    background: #ffffff;
    border: 2px solid #000000;
    padding: 16px;
}

.group-title {
    font-size: 16px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e5e5;
}

.category-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.category-link {
    display: inline-block;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #000000;
    background: #ffffff;
    border: 1px solid #000000;
    text-decoration: none;
}

/* ===== 都道府県セクション ===== */
.prefecture-section {
    background: #ffffff;
    padding: 32px 0;
}

.prefecture-wrapper {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 16px;
}

.prefecture-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}

.region-card {
    background: #ffffff;
    border: 2px solid #000000;
    padding: 16px;
}

.region-title {
    font-size: 16px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e5e5;
}

.prefecture-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.prefecture-link {
    display: inline-block;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #000000;
    background: #ffffff;
    border: 1px solid #000000;
    text-decoration: none;
}

/* ===== おすすめ・新着セクション ===== */
.recommend-section,
.new-grants-section {
    padding: 32px 0;
    background: #f8f9fa;
}

.recommend-wrapper,
.new-grants-wrapper {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 16px;
}

.section-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 3px solid #000000;
}

.header-left {
    margin-bottom: 12px;
}

.section-desc {
    font-size: 12px;
    color: #666666;
    margin: 8px 0 0;
}

.count-badge {
    display: inline-flex;
    padding: 2px 10px;
    background: #ffeb3b;
    color: #000000;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    margin-left: 6px;
}

.view-all {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 16px;
    background: #000000;
    color: #ffffff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    border: 2px solid #000000;
}

.grants-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}

.grant-card {
    position: relative;
    background: #ffffff;
    border: 2px solid #000000;
}

.badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    z-index: 2;
}

.badge-featured {
    background: #ff4444;
    color: #ffffff;
}

.badge-new {
    background: #ffeb3b;
    color: #000000;
    border: 1px solid #000000;
}

.card-link {
    display: block;
    padding: 14px;
    text-decoration: none;
    color: inherit;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.card-org {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #666666;
    font-weight: 600;
}

.btn-bookmark {
    padding: 4px;
    background: transparent;
    border: none;
    color: #666666;
    cursor: pointer;
    font-size: 16px;
}

.card-title {
    font-size: 14px;
    font-weight: 700;
    color: #000000;
    line-height: 1.4;
    margin: 0 0 10px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 10px;
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    color: #000000;
    background: #f5f5f5;
    border: 1px solid #dddddd;
}

.tag i {
    font-size: 10px;
}

.tag-location {
    background: #fff9e6;
    border-color: #ffeb3b;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid #e5e5e5;
    font-size: 12px;
    font-weight: 600;
}

.footer-item {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #000000;
}

.footer-item i {
    font-size: 11px;
}

.footer-item.deadline {
    color: #ff4444;
}

.no-data {
    text-align: center;
    padding: 32px;
    color: #666666;
    font-size: 14px;
}

/* ===== タブレット (768px+) ===== */
@media (min-width: 768px) {
    .stats-wrapper {
        flex-direction: row;
        justify-content: center;
    }
    
    .search-wrapper,
    .browse-wrapper,
    .prefecture-wrapper,
    .recommend-wrapper,
    .new-grants-wrapper {
        max-width: 700px;
    }
    
    .category-grid,
    .prefecture-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .grants-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* ===== デスクトップ (1024px+) ===== */
@media (min-width: 1024px) {
    .stats-wrapper,
    .search-wrapper,
    .browse-wrapper,
    .prefecture-wrapper,
    .recommend-wrapper,
    .new-grants-wrapper {
        max-width: 960px;
    }
    
    .category-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .prefecture-grid {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .grants-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* ===============================================
   市町村から探すセクション
   =============================================== */

.municipality-section {
    padding: 40px 0;
    background: #ffffff;
}

.municipality-wrapper {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 20px;
}

.municipality-search-container {
    margin-top: 24px;
}

.municipality-filter {
    margin-bottom: 24px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.filter-label i {
    color: #666;
}

.filter-select {
    width: 100%;
    padding: 12px 16px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-select:hover {
    border-color: #999;
}

.filter-select:focus {
    outline: none;
    border-color: #000;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
}

.municipality-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.municipality-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.municipality-link:hover {
    background: #f8f9fa;
    border-color: #000;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.municipality-count {
    font-size: 12px;
    color: #666;
    font-weight: 400;
}

.no-municipalities {
    text-align: center;
    padding: 32px;
    color: #999;
    font-size: 14px;
}

@media (min-width: 768px) {
    .municipality-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (min-width: 1024px) {
    .municipality-grid {
        grid-template-columns: repeat(6, 1fr);
    }
    
    .municipality-wrapper {
        max-width: 960px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    console.log('[OK] Mobile-Optimized Search Interface v5.0 Initialized');
    
    // フォーム要素
    const form = document.getElementById('grant-search-form');
    const categorySelect = document.getElementById('category-select');
    const prefectureSelect = document.getElementById('prefecture-select');
    const municipalityGroup = document.getElementById('municipality-group');
    const municipalitySelect = document.getElementById('municipality-select');
    const keywordInput = document.getElementById('keyword-input');
    const searchBtn = document.getElementById('search-btn');
    const resetBtn = document.getElementById('reset-btn');
    
    // 都道府県変更時に市町村を読み込む
    if (prefectureSelect && municipalityGroup && municipalitySelect) {
        prefectureSelect.addEventListener('change', function() {
            const prefectureSlug = this.value;
            
            if (!prefectureSlug) {
                municipalityGroup.style.display = 'none';
                municipalitySelect.innerHTML = '<option value="">市町村を選択</option>';
                return;
            }
            
            // 市町村を取得
            const formData = new FormData();
            formData.append('action', 'gi_get_municipalities_for_prefecture');
            formData.append('prefecture_slug', prefectureSlug);
            formData.append('nonce', '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>');
            
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                municipalitySelect.innerHTML = '<option value="">市町村を選択</option>';
                
                if (data.success && data.data && data.data.data && Array.isArray(data.data.data.municipalities)) {
                    const municipalities = data.data.data.municipalities;
                    if (municipalities.length > 0) {
                        municipalities.forEach(muni => {
                            const option = document.createElement('option');
                            option.value = muni.slug;
                            option.textContent = muni.name;
                            municipalitySelect.appendChild(option);
                        });
                        municipalityGroup.style.display = 'block';
                    } else {
                        municipalityGroup.style.display = 'none';
                    }
                } else {
                    municipalityGroup.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('[Municipality] Load error:', error);
                municipalityGroup.style.display = 'none';
            });
        });
    }
    
    // 検索実行
    if (form && searchBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const category = categorySelect.value;
            const prefecture = prefectureSelect.value;
            const municipality = municipalitySelect ? municipalitySelect.value : '';
            const keyword = keywordInput ? keywordInput.value.trim() : '';
            
            let searchUrl = '<?php echo home_url('/grants/'); ?>?';
            const params = [];
            
            if (category) params.push('category=' + encodeURIComponent(category));
            if (prefecture) params.push('prefecture=' + encodeURIComponent(prefecture));
            if (municipality) params.push('municipality=' + encodeURIComponent(municipality));
            if (keyword) params.push('search=' + encodeURIComponent(keyword));
            
            searchUrl += params.join('&');
            
            console.log('[Search] Navigate:', searchUrl);
            window.location.href = searchUrl;
        });
    }
    
    // リセット
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (categorySelect) categorySelect.value = '';
            if (prefectureSelect) prefectureSelect.value = '';
            if (municipalitySelect) {
                municipalitySelect.value = '';
                municipalitySelect.innerHTML = '<option value="">市町村を選択</option>';
            }
            if (municipalityGroup) municipalityGroup.style.display = 'none';
            if (keywordInput) keywordInput.value = '';
            console.log('[Reset] Form cleared');
        });
    }
    
    // ブックマーク
    document.querySelectorAll('.btn-bookmark').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                this.style.color = '#ffeb3b';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.style.color = '#666666';
            }
        });
    });
    
    // 市町村フィルター
    const municipalityPrefFilter = document.getElementById('municipality-prefecture-filter');
    const municipalityLinks = document.querySelectorAll('.municipality-link');
    
    if (municipalityPrefFilter && municipalityLinks.length > 0) {
        municipalityPrefFilter.addEventListener('change', function() {
            const selectedPref = this.value;
            
            municipalityLinks.forEach(link => {
                const linkPref = link.getAttribute('data-prefecture');
                
                if (!selectedPref || linkPref === selectedPref) {
                    link.style.display = 'flex';
                } else {
                    link.style.display = 'none';
                }
            });
            
            // 表示されている市町村の数をカウント
            const visibleCount = Array.from(municipalityLinks).filter(link => 
                link.style.display !== 'none'
            ).length;
            
            console.log('[Municipality Filter] Showing', visibleCount, 'municipalities');
        });
    }
});
</script>
