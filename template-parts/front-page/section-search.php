<?php
/**
 * DODA-Style Subsidy Search Section v4.0
 * 求人サイト風補助金検索セクション - 完全統合版
 * 
 * Features:
 * - カテゴリと都道府県の2列表示検索フォーム
 * - AI検索統合
 * - 都道府県から探す（8地域別）
 * - おすすめ補助金（レコメンド機能）
 * - 新着補助金
 * - 完全レスポンシブデザイン
 * 
 * @package Grant_Insight_Perfect
 * @version 4.0.0 - DODA Style Complete Integration
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// セッションID生成
$session_id = 'gi_session_' . wp_generate_uuid4();
$nonce = wp_create_nonce('gi_ai_search_nonce');

// カテゴリーを取得（2列表示用に分割）
$all_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 30
));

// 都道府県を取得（関数から）
$prefectures = gi_get_all_prefectures();

// カテゴリーを2つのグループに分割
$categories_col1 = array_slice($all_categories, 0, ceil(count($all_categories) / 2));
$categories_col2 = array_slice($all_categories, ceil(count($all_categories) / 2));

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

// 地域別都道府県データ（画像に基づく）
$regions_data = array(
    array(
        'name' => '北海道・東北',
        'class' => 'hokkaido-tohoku',
        'icon' => '🗾',
        'prefectures' => array('北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県')
    ),
    array(
        'name' => '北陸・甲信越',
        'class' => 'hokuriku',
        'icon' => '⛰️',
        'prefectures' => array('新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県')
    ),
    array(
        'name' => '関東',
        'class' => 'kanto',
        'icon' => '🏙️',
        'prefectures' => array('東京都', '埼玉県', '千葉県', '神奈川県', '茨城県', '栃木県', '群馬県')
    ),
    array(
        'name' => '東海',
        'class' => 'tokai',
        'icon' => '🏭',
        'prefectures' => array('愛知県', '岐阜県', '三重県', '静岡県')
    ),
    array(
        'name' => '関西',
        'class' => 'kansai',
        'icon' => '🏯',
        'prefectures' => array('大阪府', '兵庫県', '京都府', '滋賀県', '奈良県', '和歌山県')
    ),
    array(
        'name' => '中国',
        'class' => 'chugoku',
        'icon' => '🌊',
        'prefectures' => array('鳥取県', '島根県', '岡山県', '広島県', '山口県')
    ),
    array(
        'name' => '四国',
        'class' => 'shikoku',
        'icon' => '🌴',
        'prefectures' => array('徳島県', '香川県', '愛媛県', '高知県')
    ),
    array(
        'name' => '九州・沖縄',
        'class' => 'kyushu',
        'icon' => '🌺',
        'prefectures' => array('福岡県', '佐賀県', '熊本県', '大分県', '宮崎県', '鹿児島県', '長崎県', '沖縄県')
    )
);
?>

<!-- 公開求人数表示（DODAスタイル） -->
<section class="job-stats-banner">
    <div class="stats-container">
        <div class="stat-item">
            <span class="stat-label">公開求人</span>
            <span class="stat-number">
                <?php 
                $total_grants = wp_count_posts('grant')->publish;
                echo number_format($total_grants);
                ?>件
            </span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-label">毎週月・木曜更新</span>
            <span class="stat-date"><?php echo date('Y/m/d'); ?> (月)更新</span>
        </div>
    </div>
</section>

<!-- メイン検索セクション -->
<section class="main-search-section">
    <div class="search-container">
        <h2 class="search-title">
            <i class="fas fa-search"></i>
            補助金から探す
        </h2>

        <!-- 検索フォーム -->
        <div class="search-form-wrapper">
            <form class="grant-search-form" id="grant-search-form">
                
                <!-- 用途（カテゴリ）検索 - 2列表示 -->
                <div class="search-row">
                    <div class="search-field">
                        <label class="field-label">
                            <i class="fas fa-briefcase"></i>
                            用途
                        </label>
                        <div class="dual-select-wrapper">
                            <select id="category-select-1" class="category-select">
                                <option value="">カテゴリーを選択</option>
                                <?php foreach ($categories_col1 as $cat) : ?>
                                    <option value="<?php echo esc_attr($cat->slug); ?>">
                                        <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select id="category-select-2" class="category-select">
                                <option value="">カテゴリーを選択</option>
                                <?php foreach ($categories_col2 as $cat) : ?>
                                    <option value="<?php echo esc_attr($cat->slug); ?>">
                                        <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 勤務地（都道府県）検索 -->
                <div class="search-row">
                    <div class="search-field">
                        <label class="field-label">
                            <i class="fas fa-map-marker-alt"></i>
                            都道府県
                        </label>
                        <select id="prefecture-select" class="prefecture-select">
                            <option value="">都道府県を選択</option>
                            <?php foreach ($prefectures as $pref) : ?>
                                <option value="<?php echo esc_attr($pref['slug']); ?>">
                                    <?php echo esc_html($pref['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- スキル・キーワード（AI検索統合） -->
                <div class="search-row">
                    <div class="search-field search-field-ai">
                        <label class="field-label">
                            <i class="fas fa-brain"></i>
                            スキル・キーワード (AI検索)
                        </label>
                        <div class="ai-search-input-wrapper">
                            <input 
                                type="text" 
                                id="ai-keyword-input" 
                                class="keyword-input"
                                placeholder="例：IT導入補助金、設備投資、創業支援など"
                                autocomplete="off"
                                data-session-id="<?php echo esc_attr($session_id); ?>"
                            >
                            <button type="button" class="ai-assist-btn" id="ai-assist-btn" title="AI質問モード">
                                <i class="fas fa-robot"></i>
                                AI質問
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 検索ボタン -->
                <div class="search-actions">
                    <button type="button" class="btn-reset" id="search-reset-btn">
                        <i class="fas fa-undo"></i>
                        条件クリア
                    </button>
                    <button type="submit" class="btn-search" id="main-search-btn">
                        <i class="fas fa-search"></i>
                        この条件で検索する
                    </button>
                </div>

            </form>

            <!-- 詳しい条件で探すリンク -->
            <div class="advanced-search-links">
                <a href="<?php echo home_url('/grants/'); ?>" class="link-item">
                    <i class="fas fa-list"></i>
                    詳しい条件で検索する
                </a>
                <a href="#" class="link-item" id="saved-conditions-link">
                    <i class="fas fa-bookmark"></i>
                    保存した検索条件
                </a>
                <a href="#" class="link-item" id="browse-history-link">
                    <i class="fas fa-history"></i>
                    閲覧した求人
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 都道府県から探すセクション -->
<section class="prefecture-browse-section">
    <div class="browse-container">
        <h2 class="section-heading">
            <i class="fas fa-map-marked-alt"></i>
            都道府県から探す
        </h2>

        <div class="prefecture-regions-grid">
            <?php foreach ($regions_data as $region) : ?>
            <div class="region-card <?php echo esc_attr($region['class']); ?>">
                <h3 class="region-title">
                    <span class="region-icon"><?php echo $region['icon']; ?></span>
                    <?php echo esc_html($region['name']); ?>
                </h3>
                <div class="prefecture-links">
                    <?php 
                    foreach ($region['prefectures'] as $pref_name) : 
                        // 都道府県slugを取得
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

<!-- あなたにおすすめの補助金セクション -->
<section class="recommended-section">
    <div class="recommend-container">
        <div class="section-header">
            <div class="header-left">
                <h2 class="section-heading">
                    <i class="fas fa-user-circle"></i>
                    あなたの関覧履歴からおすすめ
                </h2>
                <p class="section-subtitle">希望条件を設定しておくと、あなたに合った補助金が見つかります</p>
            </div>
            <div class="header-right">
                <a href="<?php echo home_url('/grants/'); ?>" class="view-all-btn">
                    一覧へ
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        <div class="grants-grid">
            <?php 
            if (!empty($recommended_grants)) :
                foreach ($recommended_grants as $grant) : 
                    $deadline = get_post_meta($grant->ID, 'deadline', true);
                    $max_amount = get_post_meta($grant->ID, 'max_amount', true);
                    $organization = get_post_meta($grant->ID, 'organization', true);
                    $is_featured = get_post_meta($grant->ID, 'is_featured', true);
                    $permalink = get_permalink($grant->ID);
                    
                    // カテゴリー取得
                    $grant_categories = get_the_terms($grant->ID, 'grant_category');
                    $category_names = array();
                    if ($grant_categories && !is_wp_error($grant_categories)) {
                        foreach (array_slice($grant_categories, 0, 2) as $cat) {
                            $category_names[] = $cat->name;
                        }
                    }
                    
                    // 都道府県取得
                    $grant_prefectures = get_the_terms($grant->ID, 'grant_prefecture');
                    $prefecture_name = $grant_prefectures && !is_wp_error($grant_prefectures) ? $grant_prefectures[0]->name : '';
            ?>
                <article class="grant-card">
                    <?php if ($is_featured) : ?>
                    <span class="badge badge-featured">注目</span>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url($permalink); ?>" class="card-link">
                        <div class="card-header">
                            <div class="card-company">
                                <i class="fas fa-building"></i>
                                <?php echo esc_html($organization ?: '公的機関'); ?>
                            </div>
                            <button class="btn-bookmark" aria-label="ブックマーク" onclick="event.preventDefault();">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        
                        <h3 class="card-title"><?php echo esc_html($grant->post_title); ?></h3>
                        
                        <div class="card-tags">
                            <?php if (!empty($category_names)) : ?>
                                <?php foreach ($category_names as $cat_name) : ?>
                                <span class="tag">
                                    <i class="fas fa-tag"></i>
                                    <?php echo esc_html($cat_name); ?>
                                </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
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
                                <span class="label">最大:</span>
                                <span class="value"><?php echo esc_html($max_amount); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($deadline) : ?>
                            <div class="footer-item deadline">
                                <i class="fas fa-clock"></i>
                                <span class="label">締切:</span>
                                <span class="value"><?php echo esc_html(date('Y/m/d', strtotime($deadline))); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php 
                endforeach;
            else : 
            ?>
                <p class="no-grants-message">現在、おすすめの補助金はありません。</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 新着補助金セクション -->
<section class="new-grants-section">
    <div class="new-grants-container">
        <div class="section-header">
            <div class="header-left">
                <h2 class="section-heading">
                    <i class="fas fa-clock"></i>
                    新着補助金
                    <span class="count-badge"><?php echo number_format(count($new_grants)); ?></span>
                </h2>
                <p class="section-subtitle"><?php echo date('Y/m/d'); ?> 更新　毎週月・木曜更新</p>
            </div>
            <div class="header-right">
                <a href="<?php echo home_url('/grants/?orderby=date'); ?>" class="view-all-btn">
                    一覧へ
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        <div class="grants-grid grants-grid-large">
            <?php 
            if (!empty($new_grants)) :
                foreach ($new_grants as $grant) : 
                    $deadline = get_post_meta($grant->ID, 'deadline', true);
                    $max_amount = get_post_meta($grant->ID, 'max_amount', true);
                    $organization = get_post_meta($grant->ID, 'organization', true);
                    $is_new = (strtotime($grant->post_date) > strtotime('-7 days'));
                    $permalink = get_permalink($grant->ID);
                    
                    // カテゴリー取得
                    $grant_categories = get_the_terms($grant->ID, 'grant_category');
                    $category_names = array();
                    if ($grant_categories && !is_wp_error($grant_categories)) {
                        foreach (array_slice($grant_categories, 0, 2) as $cat) {
                            $category_names[] = $cat->name;
                        }
                    }
                    
                    // 都道府県取得
                    $grant_prefectures = get_the_terms($grant->ID, 'grant_prefecture');
                    $prefecture_name = $grant_prefectures && !is_wp_error($grant_prefectures) ? $grant_prefectures[0]->name : '';
            ?>
                <article class="grant-card">
                    <?php if ($is_new) : ?>
                    <span class="badge badge-new">NEW</span>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url($permalink); ?>" class="card-link">
                        <div class="card-header">
                            <div class="card-company">
                                <i class="fas fa-building"></i>
                                <?php echo esc_html($organization ?: '公的機関'); ?>
                            </div>
                            <button class="btn-bookmark" aria-label="ブックマーク" onclick="event.preventDefault();">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        
                        <h3 class="card-title"><?php echo esc_html($grant->post_title); ?></h3>
                        
                        <div class="card-tags">
                            <?php if (!empty($category_names)) : ?>
                                <?php foreach ($category_names as $cat_name) : ?>
                                <span class="tag">
                                    <i class="fas fa-tag"></i>
                                    <?php echo esc_html($cat_name); ?>
                                </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
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
                                <span class="label">最大:</span>
                                <span class="value"><?php echo esc_html($max_amount); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($deadline) : ?>
                            <div class="footer-item deadline">
                                <i class="fas fa-clock"></i>
                                <span class="label">締切:</span>
                                <span class="value"><?php echo esc_html(date('Y/m/d', strtotime($deadline))); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php 
                endforeach;
            else : 
            ?>
                <p class="no-grants-message">現在、新着補助金はありません。</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* ============================================
   DODA-Style Subsidy Search Interface v4.0
   求人サイト風補助金検索 - 完全統合版
   ============================================ */

/* ===== Base Styles ===== */
* {
    box-sizing: border-box;
}

/* ===== 公開求人数バナー ===== */
.job-stats-banner {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 3px solid #000000;
    padding: 20px 0;
}

.stats-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 40px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.stat-label {
    font-size: 14px;
    font-weight: 600;
    color: #666666;
}

.stat-number {
    font-size: 32px;
    font-weight: 900;
    color: #000000;
    letter-spacing: -0.5px;
}

.stat-date {
    font-size: 16px;
    font-weight: 700;
    color: #000000;
}

.stat-divider {
    width: 2px;
    height: 40px;
    background: #dddddd;
}

/* ===== メイン検索セクション ===== */
.main-search-section {
    background: #ffffff;
    padding: 60px 0;
    border-bottom: 1px solid #e5e5e5;
}

.search-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.search-title {
    font-size: 28px;
    font-weight: 900;
    color: #000000;
    margin: 0 0 32px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.search-title i {
    font-size: 32px;
}

/* 検索フォームラッパー */
.search-form-wrapper {
    background: #ffffff;
    border: 3px solid #000000;
    border-radius: 0;
    padding: 32px;
    box-shadow: 8px 8px 0 rgba(0, 0, 0, 0.1);
}

.grant-search-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.search-row {
    display: flex;
    gap: 16px;
}

.search-field {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.field-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #000000;
    margin-bottom: 4px;
}

.field-label i {
    font-size: 16px;
}

/* デュアルセレクトラッパー（カテゴリ2列） */
.dual-select-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.category-select,
.prefecture-select,
.keyword-input {
    width: 100%;
    padding: 14px 16px;
    font-size: 15px;
    font-weight: 500;
    color: #000000;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 0;
    appearance: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.category-select:hover,
.prefecture-select:hover,
.keyword-input:hover {
    border-color: #333333;
    box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.05);
}

.category-select:focus,
.prefecture-select:focus,
.keyword-input:focus {
    outline: none;
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.3);
}

/* AI検索フィールド */
.search-field-ai {
    flex: 1;
}

.ai-search-input-wrapper {
    display: flex;
    gap: 12px;
    align-items: stretch;
}

.keyword-input {
    flex: 1;
    cursor: text;
}

.keyword-input::placeholder {
    color: #999999;
}

.ai-assist-btn {
    padding: 14px 24px;
    background: #ffeb3b;
    color: #000000;
    border: 2px solid #000000;
    border-radius: 0;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.ai-assist-btn:hover {
    background: #000000;
    color: #ffeb3b;
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 rgba(255, 235, 59, 0.3);
}

.ai-assist-btn i {
    font-size: 16px;
}

/* 検索アクション */
.search-actions {
    display: flex;
    gap: 16px;
    margin-top: 8px;
}

.btn-reset,
.btn-search {
    padding: 16px 32px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: 2px solid #000000;
}

.btn-reset {
    flex: 0 0 auto;
    background: #ffffff;
    color: #000000;
}

.btn-reset:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.1);
}

.btn-search {
    flex: 1;
    background: #000000;
    color: #ffffff;
}

.btn-search:hover {
    background: #ffeb3b;
    color: #000000;
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 rgba(255, 235, 59, 0.3);
}

/* 詳細検索リンク */
.advanced-search-links {
    display: flex;
    gap: 24px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 2px solid #e5e5e5;
    flex-wrap: wrap;
}

.link-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #000000;
    text-decoration: none;
    transition: all 0.2s ease;
}

.link-item:hover {
    color: #ffeb3b;
    text-decoration: underline;
}

.link-item i {
    font-size: 14px;
}

/* ===== 都道府県から探すセクション ===== */
.prefecture-browse-section {
    background: #f8f9fa;
    padding: 60px 0;
    border-bottom: 1px solid #e5e5e5;
}

.browse-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.section-heading {
    font-size: 28px;
    font-weight: 900;
    color: #000000;
    margin: 0 0 32px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-heading i {
    font-size: 32px;
}

.prefecture-regions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.region-card {
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 0;
    padding: 20px;
    transition: all 0.3s ease;
}

.region-card:hover {
    transform: translateY(-4px);
    box-shadow: 6px 6px 0 rgba(0, 0, 0, 0.1);
}

.region-title {
    font-size: 16px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e5e5;
}

.region-icon {
    font-size: 20px;
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
    transition: all 0.2s ease;
}

.prefecture-link:hover {
    background: #000000;
    color: #ffffff;
}

/* ===== おすすめ・新着補助金セクション ===== */
.recommended-section,
.new-grants-section {
    padding: 60px 0;
    background: #ffffff;
}

.recommend-container,
.new-grants-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 32px;
    padding-bottom: 16px;
    border-bottom: 3px solid #000000;
}

.header-left {
    flex: 1;
}

.section-subtitle {
    font-size: 13px;
    color: #666666;
    font-weight: 500;
    margin: 8px 0 0;
}

.count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 12px;
    background: #ffeb3b;
    color: #000000;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 700;
    margin-left: 8px;
}

.header-right {
    display: flex;
    align-items: center;
}

.view-all-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    background: #000000;
    color: #ffffff;
    text-decoration: none;
    border-radius: 0;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.3s ease;
    border: 2px solid #000000;
}

.view-all-btn:hover {
    background: #ffeb3b;
    color: #000000;
    border-color: #ffeb3b;
    transform: translateY(-2px);
}

/* ===== 補助金カードグリッド ===== */
.grants-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.grants-grid-large {
    grid-template-columns: repeat(4, 1fr);
}

.grant-card {
    position: relative;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.grant-card:hover {
    transform: translateY(-4px);
    box-shadow: 8px 8px 0 rgba(0, 0, 0, 0.1);
}

.badge {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 4px 12px;
    border-radius: 0;
    font-size: 11px;
    font-weight: 700;
    z-index: 2;
    color: #ffffff;
}

.badge-featured {
    background: #ff4444;
}

.badge-new {
    background: #ffeb3b;
    color: #000000;
    border: 1px solid #000000;
}

.card-link {
    display: block;
    padding: 16px;
    text-decoration: none;
    color: inherit;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.card-company {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #666666;
    font-weight: 600;
}

.card-company i {
    font-size: 11px;
}

.btn-bookmark {
    padding: 6px;
    background: transparent;
    border: none;
    color: #666666;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-bookmark:hover {
    color: #ffeb3b;
    transform: scale(1.1);
}

.card-title {
    font-size: 14px;
    font-weight: 700;
    color: #000000;
    line-height: 1.4;
    margin: 0 0 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 12px;
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
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
    padding-top: 12px;
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

.footer-item .label {
    color: #666666;
}

.footer-item .value {
    font-weight: 700;
}

.footer-item.deadline {
    color: #ff4444;
}

.footer-item.deadline .value {
    font-weight: 700;
    color: #ff4444;
}

.no-grants-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    font-size: 16px;
    color: #666666;
}

/* ===== レスポンシブデザイン ===== */
@media (max-width: 1024px) {
    .stats-container {
        gap: 24px;
    }
    
    .stat-number {
        font-size: 28px;
    }
    
    .dual-select-wrapper {
        grid-template-columns: 1fr;
    }
    
    .prefecture-regions-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .grants-grid,
    .grants-grid-large {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .job-stats-banner {
        padding: 16px 0;
    }
    
    .stats-container {
        flex-direction: column;
        gap: 16px;
    }
    
    .stat-divider {
        display: none;
    }
    
    .main-search-section,
    .prefecture-browse-section,
    .recommended-section,
    .new-grants-section {
        padding: 40px 0;
    }
    
    .search-title,
    .section-heading {
        font-size: 24px;
    }
    
    .search-form-wrapper {
        padding: 24px 20px;
    }
    
    .search-actions {
        flex-direction: column;
    }
    
    .btn-reset,
    .btn-search {
        width: 100%;
    }
    
    .advanced-search-links {
        flex-direction: column;
        gap: 16px;
    }
    
    .prefecture-regions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .grants-grid,
    .grants-grid-large {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .ai-search-input-wrapper {
        flex-direction: column;
    }
}

@media (max-width: 640px) {
    .search-title,
    .section-heading {
        font-size: 20px;
    }
    
    .search-form-wrapper {
        border-width: 2px;
        padding: 20px 16px;
        box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.1);
    }
    
    .prefecture-regions-grid {
        grid-template-columns: 1fr;
    }
    
    .grants-grid,
    .grants-grid-large {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    console.log('[OK] DODA-Style Search Interface v4.0 Initialized');
    
    // 検索フォーム要素
    const searchForm = document.getElementById('grant-search-form');
    const categorySelect1 = document.getElementById('category-select-1');
    const categorySelect2 = document.getElementById('category-select-2');
    const prefectureSelect = document.getElementById('prefecture-select');
    const keywordInput = document.getElementById('ai-keyword-input');
    const searchBtn = document.getElementById('main-search-btn');
    const resetBtn = document.getElementById('search-reset-btn');
    const aiAssistBtn = document.getElementById('ai-assist-btn');
    
    // 検索実行
    if (searchForm && searchBtn) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const category1 = categorySelect1.value;
            const category2 = categorySelect2.value;
            const prefecture = prefectureSelect.value;
            const keyword = keywordInput.value.trim();
            
            // URLパラメータの構築
            let searchUrl = '<?php echo home_url('/grants/'); ?>?';
            const params = [];
            
            if (category1) params.push('category=' + encodeURIComponent(category1));
            if (category2 && category2 !== category1) params.push('category2=' + encodeURIComponent(category2));
            if (prefecture) params.push('prefecture=' + encodeURIComponent(prefecture));
            if (keyword) params.push('s=' + encodeURIComponent(keyword));
            
            searchUrl += params.join('&');
            
            console.log('[Search] Navigating to:', searchUrl);
            window.location.href = searchUrl;
        });
    }
    
    // リセットボタン
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (categorySelect1) categorySelect1.value = '';
            if (categorySelect2) categorySelect2.value = '';
            if (prefectureSelect) prefectureSelect.value = '';
            if (keywordInput) keywordInput.value = '';
            
            console.log('[Search] Form reset');
        });
    }
    
    // AI質問ボタン
    if (aiAssistBtn) {
        aiAssistBtn.addEventListener('click', function() {
            const keyword = keywordInput.value.trim();
            
            if (keyword) {
                // AI質問モードに切り替え（既存のAI機能との統合）
                console.log('[AI] Question mode activated:', keyword);
                alert('AI質問モード: 「' + keyword + '」について検索します。');
                // ここで既存のAI検索機能を呼び出す
            } else {
                alert('質問内容を入力してください。');
                keywordInput.focus();
            }
        });
    }
    
    // Enterキーで検索
    if (keywordInput) {
        keywordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (searchBtn) searchBtn.click();
            }
        });
    }
    
    // ブックマークボタン
    document.querySelectorAll('.btn-bookmark').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                this.style.color = '#ffeb3b';
                console.log('[Bookmark] Added');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.style.color = '#666666';
                console.log('[Bookmark] Removed');
            }
        });
    });
    
    console.log('[Debug] Search elements initialized');
    console.log('[Debug] Category Select 1:', !!categorySelect1);
    console.log('[Debug] Category Select 2:', !!categorySelect2);
    console.log('[Debug] Prefecture Select:', !!prefectureSelect);
    console.log('[Debug] Keyword Input:', !!keywordInput);
});
</script>
