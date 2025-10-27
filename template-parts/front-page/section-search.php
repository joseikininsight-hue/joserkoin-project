<?php
/**
 * Job-Site Style Grant Search Section v3.0
 * 求人サイト風補助金検索セクション + レコメンド + 新着
 * 
 * @package Grant_Insight_Perfect
 * @version 3.0.0 - Job Site Style Interface
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// セッションID生成
$session_id = 'gi_session_' . wp_generate_uuid4();
$nonce = wp_create_nonce('gi_ai_search_nonce');

// カテゴリー（職種）を取得
$categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 20
));

// 都道府県（勤務地）を取得
$prefectures = gi_get_all_prefectures();

// レコメンド補助金を取得（注目度の高い6件）
$recommended_grants = get_posts(array(
    'post_type' => 'grant',
    'posts_per_page' => 4,
    'meta_key' => 'is_featured',
    'meta_value' => '1',
    'orderby' => 'rand', // ランダムでパーソナライズ感を演出
    'order' => 'DESC'
));

// 新着補助金を取得（最新8件）
$new_grants = get_posts(array(
    'post_type' => 'grant',
    'posts_per_page' => 8,
    'orderby' => 'date',
    'order' => 'DESC'
));
?>

<!-- Job-Site Style Search Interface -->
<section class="job-search-section">
    <div class="job-search-container">
        
        <!-- 求人サイト風検索バー -->
        <div class="job-search-bar-wrapper">
            <div class="job-search-bar">
                
                <!-- 職種（カテゴリー）ドロップダウン -->
                <div class="search-field search-category">
                    <label for="grant-category-select" class="search-label">
                        <i class="fas fa-briefcase"></i>
                        職種
                    </label>
                    <select id="grant-category-select" class="search-select">
                        <option value="">職種を選択してください</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo esc_attr($category->slug); ?>">
                                <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
                
                <!-- 勤務地（都道府県）ドロップダウン -->
                <div class="search-field search-location">
                    <label for="grant-prefecture-select" class="search-label">
                        <i class="fas fa-map-marker-alt"></i>
                        勤務地
                    </label>
                    <select id="grant-prefecture-select" class="search-select">
                        <option value="">勤務地を選択してください</option>
                        <?php foreach ($prefectures as $pref) : ?>
                            <option value="<?php echo esc_attr($pref['slug']); ?>">
                                <?php echo esc_html($pref['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
                
                <!-- キーワード検索 -->
                <div class="search-field search-keyword">
                    <label for="grant-keyword-input" class="search-label">
                        <i class="fas fa-search"></i>
                        スキルや条件など
                    </label>
                    <input 
                        type="text" 
                        id="grant-keyword-input" 
                        class="search-input" 
                        placeholder="キーワードを入力"
                        autocomplete="off">
                </div>
                
                <!-- 検索ボタン -->
                <button type="button" id="job-search-btn" class="job-search-button">
                    <i class="fas fa-search"></i>
                    <span class="btn-text">検索</span>
                </button>
                
            </div>
            
            <!-- 検索条件絞り込みリンク -->
            <div class="search-options">
                <button type="button" class="search-option-btn" id="detailed-search-toggle">
                    <i class="fas fa-sliders-h"></i>
                    詳しい条件から探す
                </button>
                <button type="button" class="search-option-btn" id="saved-search-btn">
                    <i class="fas fa-star"></i>
                    新着のみ
                </button>
            </div>
        </div>
        
    </div>
</section>

<!-- レコメンドシステムセクション -->
<section class="recommendation-section">
    <div class="recommendation-container">
        
        <!-- セクションヘッダー -->
        <div class="section-header">
            <div class="header-left">
                <h2 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    あなたへのおすすめ補助金
                </h2>
                <p class="section-subtitle">閲覧履歴や希望条件に基づいたおすすめ</p>
            </div>
            <a href="<?php echo home_url('/grants/'); ?>" class="view-all-link">
                一覧へ
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        
        <!-- レコメンド補助金カード -->
        <div class="grant-cards-grid">
            <?php foreach ($recommended_grants as $grant) : 
                $deadline = get_post_meta($grant->ID, 'deadline', true);
                $max_amount = get_post_meta($grant->ID, 'max_amount', true);
                $organization = get_post_meta($grant->ID, 'organization', true);
                $is_featured = get_post_meta($grant->ID, 'is_featured', true);
                $permalink = get_permalink($grant->ID);
                
                // カテゴリー取得
                $grant_categories = get_the_terms($grant->ID, 'grant_category');
                $category_name = $grant_categories && !is_wp_error($grant_categories) ? $grant_categories[0]->name : '';
                
                // 都道府県取得
                $grant_prefectures = get_the_terms($grant->ID, 'grant_prefecture');
                $prefecture_name = $grant_prefectures && !is_wp_error($grant_prefectures) ? $grant_prefectures[0]->name : '';
            ?>
            <article class="grant-card">
                <?php if ($is_featured) : ?>
                <span class="grant-badge grant-badge-featured">注目</span>
                <?php endif; ?>
                
                <a href="<?php echo esc_url($permalink); ?>" class="grant-card-link">
                    <div class="grant-card-header">
                        <?php if ($organization) : ?>
                        <span class="grant-company">
                            <i class="fas fa-building"></i>
                            <?php echo esc_html($organization); ?>
                        </span>
                        <?php endif; ?>
                        <button class="grant-bookmark" aria-label="ブックマーク">
                            <i class="far fa-bookmark"></i>
                        </button>
                    </div>
                    
                    <h3 class="grant-card-title"><?php echo esc_html($grant->post_title); ?></h3>
                    
                    <div class="grant-card-meta">
                        <?php if ($category_name) : ?>
                        <span class="meta-item">
                            <i class="fas fa-tag"></i>
                            <?php echo esc_html($category_name); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($prefecture_name) : ?>
                        <span class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo esc_html($prefecture_name); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grant-card-footer">
                        <?php if ($max_amount) : ?>
                        <span class="grant-amount">
                            <i class="fas fa-yen-sign"></i>
                            最大<?php echo esc_html($max_amount); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($deadline) : ?>
                        <span class="grant-deadline">
                            <i class="fas fa-clock"></i>
                            <?php echo esc_html(date('Y/m/d', strtotime($deadline))); ?>まで
                        </span>
                        <?php endif; ?>
                    </div>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
        
    </div>
</section>

<!-- 新着補助金セクション -->
<section class="new-grants-section">
    <div class="new-grants-container">
        
        <!-- セクションヘッダー -->
        <div class="section-header">
            <div class="header-left">
                <h2 class="section-title">
                    <i class="fas fa-clock"></i>
                    新着補助金
                    <span class="count-badge"><?php echo number_format(count($new_grants)); ?>件</span>
                </h2>
                <p class="section-subtitle"><?php echo date('Y/m/d'); ?> 更新　毎月・木曜更新</p>
            </div>
            <a href="<?php echo home_url('/grants/?orderby=date'); ?>" class="view-all-link">
                一覧へ
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        
        <!-- 新着補助金カードグリッド -->
        <div class="grant-cards-grid grant-cards-grid-large">
            <?php foreach ($new_grants as $grant) : 
                $deadline = get_post_meta($grant->ID, 'deadline', true);
                $max_amount = get_post_meta($grant->ID, 'max_amount', true);
                $organization = get_post_meta($grant->ID, 'organization', true);
                $is_new = (strtotime($grant->post_date) > strtotime('-7 days'));
                $permalink = get_permalink($grant->ID);
                
                // カテゴリー取得
                $grant_categories = get_the_terms($grant->ID, 'grant_category');
                $category_name = $grant_categories && !is_wp_error($grant_categories) ? $grant_categories[0]->name : '';
                
                // 都道府県取得
                $grant_prefectures = get_the_terms($grant->ID, 'grant_prefecture');
                $prefecture_name = $grant_prefectures && !is_wp_error($grant_prefectures) ? $grant_prefectures[0]->name : '';
            ?>
            <article class="grant-card">
                <?php if ($is_new) : ?>
                <span class="grant-badge grant-badge-new">NEW</span>
                <?php endif; ?>
                
                <a href="<?php echo esc_url($permalink); ?>" class="grant-card-link">
                    <div class="grant-card-header">
                        <?php if ($organization) : ?>
                        <span class="grant-company">
                            <i class="fas fa-building"></i>
                            <?php echo esc_html($organization); ?>
                        </span>
                        <?php endif; ?>
                        <button class="grant-bookmark" aria-label="ブックマーク">
                            <i class="far fa-bookmark"></i>
                        </button>
                    </div>
                    
                    <h3 class="grant-card-title"><?php echo esc_html($grant->post_title); ?></h3>
                    
                    <div class="grant-card-meta">
                        <?php if ($category_name) : ?>
                        <span class="meta-item">
                            <i class="fas fa-tag"></i>
                            <?php echo esc_html($category_name); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($prefecture_name) : ?>
                        <span class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo esc_html($prefecture_name); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grant-card-footer">
                        <?php if ($max_amount) : ?>
                        <span class="grant-amount">
                            <i class="fas fa-yen-sign"></i>
                            最大<?php echo esc_html($max_amount); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($deadline) : ?>
                        <span class="grant-deadline">
                            <i class="fas fa-clock"></i>
                            <?php echo esc_html(date('Y/m/d', strtotime($deadline))); ?>まで
                        </span>
                        <?php endif; ?>
                    </div>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
        
    </div>
</section>

<style>
/* ============================================
   Job-Site Style Search Interface v3.0
   求人サイト風検索インターフェース
   ============================================ */

/* 検索セクション */
.job-search-section {
    padding: 60px 0 40px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 1px solid #e5e5e5;
}

.job-search-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* 検索バーラッパー */
.job-search-bar-wrapper {
    background: #ffffff;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 2px solid #000000;
}

/* 検索バー */
.job-search-bar {
    display: grid;
    grid-template-columns: 2fr 2fr 3fr auto;
    gap: 16px;
    align-items: end;
}

/* 検索フィールド */
.search-field {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.search-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    color: #000000;
}

.search-label i {
    font-size: 14px;
}

/* セレクトボックス */
.search-select {
    width: 100%;
    padding: 14px 40px 14px 16px;
    font-size: 15px;
    color: #000000;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 10px;
    appearance: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
}

.search-select:hover {
    border-color: #333333;
}

.search-select:focus {
    outline: none;
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.3);
}

.select-arrow {
    position: absolute;
    right: 16px;
    bottom: 16px;
    font-size: 12px;
    color: #666666;
    pointer-events: none;
}

/* キーワード入力 */
.search-input {
    width: 100%;
    padding: 14px 16px;
    font-size: 15px;
    color: #000000;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 10px;
    transition: all 0.2s ease;
    font-weight: 500;
}

.search-input:hover {
    border-color: #333333;
}

.search-input:focus {
    outline: none;
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.3);
}

.search-input::placeholder {
    color: #999999;
}

/* 検索ボタン */
.job-search-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 32px;
    background: #000000;
    color: #ffffff;
    border: 2px solid #000000;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    height: 52px;
}

.job-search-button:hover {
    background: #ffeb3b;
    color: #000000;
    border-color: #ffeb3b;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.job-search-button i {
    font-size: 16px;
}

/* 検索オプション */
.search-options {
    display: flex;
    gap: 16px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e5e5;
}

.search-option-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: transparent;
    color: #000000;
    border: 1px solid #000000;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.search-option-btn:hover {
    background: #000000;
    color: #ffffff;
}

.search-option-btn i {
    font-size: 12px;
}

/* ============================================
   レコメンドセクション
   ============================================ */

.recommendation-section,
.new-grants-section {
    padding: 60px 0;
    background: #ffffff;
}

.recommendation-container,
.new-grants-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* セクションヘッダー */
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

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 28px;
    font-weight: 900;
    color: #000000;
    margin: 0 0 8px;
}

.section-title i {
    font-size: 32px;
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

.section-subtitle {
    font-size: 13px;
    color: #666666;
    font-weight: 500;
    margin: 0;
}

.view-all-link {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    background: #000000;
    color: #ffffff;
    text-decoration: none;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.3s ease;
}

.view-all-link:hover {
    background: #ffeb3b;
    color: #000000;
    transform: translateY(-2px);
}

.view-all-link i {
    font-size: 12px;
}

/* ============================================
   補助金カードグリッド
   ============================================ */

.grant-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.grant-cards-grid-large {
    grid-template-columns: repeat(4, 1fr);
}

/* 補助金カード */
.grant-card {
    position: relative;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.grant-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: #ffeb3b;
}

.grant-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    z-index: 2;
    color: #ffffff;
}

.grant-badge-featured {
    background: #ff4444;
}

.grant-badge-new {
    background: #ffeb3b;
    color: #000000;
}

.grant-card-link {
    display: block;
    padding: 16px;
    text-decoration: none;
    color: inherit;
}

/* カードヘッダー */
.grant-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.grant-company {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #666666;
    font-weight: 600;
}

.grant-company i {
    font-size: 11px;
}

.grant-bookmark {
    padding: 6px;
    background: transparent;
    border: none;
    color: #666666;
    cursor: pointer;
    transition: all 0.2s ease;
}

.grant-bookmark:hover {
    color: #ffeb3b;
    transform: scale(1.1);
}

/* カードタイトル */
.grant-card-title {
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

/* カードメタ */
.grant-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #666666;
    font-weight: 600;
}

.meta-item i {
    font-size: 10px;
}

/* カードフッター */
.grant-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid #e5e5e5;
    font-size: 12px;
    font-weight: 600;
}

.grant-amount {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #000000;
}

.grant-amount i {
    font-size: 11px;
}

.grant-deadline {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #ff4444;
}

.grant-deadline i {
    font-size: 11px;
}

/* ============================================
   レスポンシブデザイン
   ============================================ */

@media (max-width: 1024px) {
    .job-search-bar {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    
    .job-search-button {
        grid-column: 1 / -1;
    }
    
    .grant-cards-grid,
    .grant-cards-grid-large {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .job-search-section {
        padding: 40px 0 30px;
    }
    
    .job-search-bar-wrapper {
        padding: 24px 20px;
    }
    
    .job-search-bar {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .job-search-button {
        width: 100%;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .section-title {
        font-size: 24px;
    }
    
    .section-title i {
        font-size: 28px;
    }
    
    .grant-cards-grid,
    .grant-cards-grid-large {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
}

@media (max-width: 640px) {
    .job-search-section {
        padding: 30px 0 20px;
    }
    
    .section-title {
        font-size: 20px;
    }
    
    .grant-cards-grid,
    .grant-cards-grid-large {
        grid-template-columns: 1fr;
    }
    
    .search-options {
        flex-direction: column;
        gap: 12px;
    }
    
    .search-option-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 求人サイト風検索機能
    const searchBtn = document.getElementById('job-search-btn');
    const categorySelect = document.getElementById('grant-category-select');
    const prefectureSelect = document.getElementById('grant-prefecture-select');
    const keywordInput = document.getElementById('grant-keyword-input');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const category = categorySelect.value;
            const prefecture = prefectureSelect.value;
            const keyword = keywordInput.value;
            
            // 検索URLの構築
            let searchUrl = '<?php echo home_url('/grants/'); ?>?';
            const params = [];
            
            if (category) params.push('category=' + encodeURIComponent(category));
            if (prefecture) params.push('prefecture=' + encodeURIComponent(prefecture));
            if (keyword) params.push('s=' + encodeURIComponent(keyword));
            
            searchUrl += params.join('&');
            
            // 検索結果ページへ遷移
            window.location.href = searchUrl;
        });
    }
    
    // Enterキーで検索
    if (keywordInput) {
        keywordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchBtn.click();
            }
        });
    }
    
    // ブックマーク機能
    document.querySelectorAll('.grant-bookmark').forEach(btn => {
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
    
    console.log('[OK] Job-Site Style Search Interface v3.0 loaded');
});
</script>

<!-- AI Grant Search Section - Full Responsive Monochrome Professional Edition -->
<section id="ai-search-section" class="monochrome-ai-search" data-session-id="<?php echo esc_attr($session_id); ?>">
    <!-- 背景エフェクト -->
    <div class="background-effects">
        <div class="grid-pattern"></div>
        <div class="gradient-overlay"></div>
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>

    <div class="section-container">
        
        <!-- Section Header -->
        <header class="browse-header" data-aos="fade-up">
            <div class="browse-badge">
                <div class="badge-pulse"></div>
                <span>AI POWERED SEARCH</span>
            </div>
            
            <h2 class="browse-title">
                <span class="title-main">補助金AI検索</span>
                <span class="title-sub">最適な補助金を瞬時に発見<br>💡 AI質問ボタンを押して、なんでも質問してみてください！</span>
            </h2>
        </header>

        <!-- Main Search Interface -->
        <div class="ai-search-interface">
            
            <!-- Search Bar -->
            <div class="ai-search-bar">
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2"/>
                        <path d="M12.5 12.5L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input 
                        type="text" 
                        id="ai-search-input" 
                        class="search-input"
                        placeholder="業種、地域、目的などを入力..."
                        autocomplete="off"
                        aria-label="補助金検索">
                    <div class="search-actions">
                        <button class="voice-btn" aria-label="音声入力" title="音声で検索">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <rect x="8" y="2" width="4" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M5 10c0 2.761 2.239 5 5 5s5-2.239 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M10 15v3M7 18h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <button id="ai-search-btn" class="search-btn" aria-label="検索実行">
                            <span class="btn-text">検索</span>
                            <svg class="btn-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M7 10l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="search-suggestions" id="search-suggestions" role="listbox"></div>
            </div>

            <!-- Quick Filters -->
            <div class="quick-filters" role="tablist">
                <button class="filter-chip active" data-filter="all" role="tab" aria-selected="true">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <rect x="2" y="2" width="12" height="12" rx="2" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>すべて</span>
                </button>
                <button class="filter-chip" data-filter="it" role="tab" aria-selected="false">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <rect x="2" y="3" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/>
                        <path d="M2 6h12" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>IT導入</span>
                </button>
                <button class="filter-chip" data-filter="manufacturing" role="tab" aria-selected="false">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M3 8l3-3 3 3 3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="2" y="10" width="12" height="4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>ものづくり</span>
                </button>
                <button class="filter-chip" data-filter="startup" role="tab" aria-selected="false">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M8 2v12M2 8h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>創業支援</span>
                </button>
                <button class="filter-chip" data-filter="sustainability" role="tab" aria-selected="false">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2"/>
                        <path d="M8 5v6M5 8h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>持続化</span>
                </button>
                <button class="filter-chip" data-filter="innovation" role="tab" aria-selected="false">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M8 2l2 4 4 1-3 3 1 4-4-2-4 2 1-4-3-3 4-1z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>事業再構築</span>
                </button>
                <button class="filter-chip" data-filter="employment" role="tab" aria-selected="false">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <circle cx="8" cy="5" r="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M4 14c0-2.21 1.79-4 4-4s4 1.79 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>雇用関連</span>
                </button>
            </div>



            <!-- AI Chat & Results -->
            <div class="ai-main-content">
                
                <!-- Search Results Only (AI Assistant removed) -->
                <div class="search-results-panel">
                    <div class="results-header">
                        <h3 class="results-title">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <rect x="2" y="2" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M6 6h8M6 10h8M6 14h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span id="results-count">0</span>件の補助金
                        </h3>
                        <div class="view-controls">
                            <button class="view-btn active" data-view="grid" aria-label="グリッド表示">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <rect x="2" y="2" width="5" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                    <rect x="11" y="2" width="5" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                    <rect x="2" y="11" width="5" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                    <rect x="11" y="11" width="5" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                            <button class="view-btn" data-view="list" aria-label="リスト表示">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <path d="M2 4h14M2 9h14M2 14h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="results-container" id="results-container">
                        <!-- Initial Featured Grants -->
                        <div class="featured-grants">
                            <?php
                            // 注目の補助金を表示
                            $featured_grants = get_posts([
                                'post_type' => 'grant',
                                'posts_per_page' => 6,
                                'meta_key' => 'is_featured',
                                'meta_value' => '1',
                                'orderby' => 'date',
                                'order' => 'DESC'
                            ]);
                            
                            foreach ($featured_grants as $grant):
                                $amount = get_post_meta($grant->ID, 'max_amount', true);
                                $deadline = get_post_meta($grant->ID, 'deadline', true);
                                $organization = get_post_meta($grant->ID, 'organization', true);
                                $success_rate = get_field('adoption_rate', $grant->ID);
                                $permalink = get_permalink($grant->ID);
                            ?>
                            <div class="grant-card" data-id="<?php echo $grant->ID; ?>">
                                <div class="card-badge">注目</div>
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo esc_html($grant->post_title); ?></h4>
                                    <button class="card-bookmark" aria-label="ブックマーク">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                            <path d="M3 2h12v14l-6-3-6 3V2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="card-meta">
                                    <span class="meta-item">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                            <circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="2"/>
                                            <path d="M7 4v3h3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span class="meta-label">最大</span>
                                        <span class="meta-value"><?php echo esc_html($amount ?: '未定'); ?></span>
                                    </span>
                                    <span class="meta-item">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                            <rect x="2" y="3" width="10" height="9" rx="1" stroke="currentColor" stroke-width="2"/>
                                            <path d="M4 1v2M10 1v2M2 6h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span class="meta-label">締切</span>
                                        <span class="meta-value"><?php echo esc_html($deadline ?: '随時'); ?></span>
                                    </span>
                                </div>
                                <p class="card-org">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <rect x="1" y="2" width="10" height="8" rx="1" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M3 5h6M3 7h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    <?php echo esc_html($organization); ?>
                                </p>
                                <?php if ($success_rate): ?>
                                <div class="card-rate">
                                    <div class="rate-bar">
                                        <div class="rate-fill" style="width: <?php echo $success_rate; ?>%"></div>
                                    </div>
                                    <span class="rate-text">
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                            <path d="M1 8l2.5-2.5L5 7l4-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        採択率 <?php echo $success_rate; ?>%
                                    </span>
                                </div>
                                <?php endif; ?>
                                <div class="card-actions">
                                    <button class="ai-assist-btn" 
                                            data-grant-id="<?php echo $grant->ID; ?>" 
                                            data-post-id="<?php echo $grant->ID; ?>"
                                            data-grant-title="<?php echo esc_attr($grant->post_title); ?>"
                                            data-grant-permalink="<?php echo esc_url($permalink); ?>"
                                            aria-label="AIに質問">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <rect x="2" y="4" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="2"/>
                                            <path d="M5 7h6M5 10h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <circle cx="8" cy="2" r="1" fill="currentColor"/>
                                        </svg>
                                        <span>AI質問</span>
                                    </button>
                                    <a href="<?php echo esc_url($permalink); ?>" class="card-link" aria-label="詳細を見る">
                                        <span>詳細を見る</span>
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                            <path d="M5 3l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="results-loading" id="results-loading">
                        <div class="loading-spinner"></div>
                        <span>検索中...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button (Mobile) -->
    <button class="fab-mobile" id="fab-mobile" aria-label="AIチャットを開く">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="6" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M8 11h8M8 15h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <circle cx="12" cy="3" r="1.5" fill="currentColor"/>
        </svg>
    </button>


</section>

<style>
/* ============================================
   ⚠️ CRITICAL: セクション完全隔離
   他のセクション（ヒーローなど）に影響を与えません
   ============================================ */

/* このセクションのみに適用されるCSS変数 */
.monochrome-ai-search {
    /* セクション隔離 */
    isolation: isolate;
    contain: layout style;
    
    /* Colors - このセクション専用 */
    --gi-color-primary: #000000;
    --gi-color-secondary: #333333;
    --gi-color-tertiary: #666666;
    --gi-color-accent: #FFEB3B;
    --gi-color-background: #FFFFFF;
    --gi-color-surface: #FAFAFA;
    --gi-color-border: #000000;
    --gi-color-text: #000000;
    --gi-color-text-muted: #666666;
    --gi-color-text-light: #999999;
    
    /* Spacing */
    --gi-spacing-xs: 3px;
    --gi-spacing-sm: 6px;
    --gi-spacing-md: 12px;
    --gi-spacing-lg: 18px;
    --gi-spacing-xl: 24px;
    --gi-spacing-2xl: 36px;
    --gi-spacing-3xl: 48px;
    --gi-spacing-section: 90px;
    
    /* Typography */
    --gi-font-size-xs: 9px;
    --gi-font-size-sm: 10px;
    --gi-font-size-base: 12px;
    --gi-font-size-md: 14px;
    --gi-font-size-lg: 16px;
    --gi-font-size-xl: 20px;
    --gi-font-size-2xl: 26px;
    --gi-font-size-3xl: 32px;
    --gi-font-size-4xl: 44px;
    
    /* Line Heights */
    --gi-line-height-tight: 1.2;
    --gi-line-height-normal: 1.5;
    --gi-line-height-relaxed: 1.8;
    
    /* Border Radius */
    --gi-radius-sm: 4px;
    --gi-radius-md: 8px;
    --gi-radius-lg: 12px;
    --gi-radius-xl: 16px;
    --gi-radius-2xl: 20px;
    --gi-radius-full: 9999px;
    
    /* Shadows */
    --gi-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
    --gi-shadow-md: 0 4px 8px rgba(0, 0, 0, 0.08);
    --gi-shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.1);
    --gi-shadow-xl: 0 12px 24px rgba(0, 0, 0, 0.12);
    --gi-shadow-2xl: 0 20px 40px rgba(0, 0, 0, 0.15);
    
    /* Transitions */
    --gi-transition-fast: 0.15s ease;
    --gi-transition-base: 0.3s ease;
    --gi-transition-slow: 0.5s ease;
    
    /* Z-index */
    --gi-z-base: 1;
    --gi-z-dropdown: 10;
    --gi-z-sticky: 100;
    --gi-z-fixed: 1000;
    --gi-z-modal: 10000;
    --gi-z-tooltip: 100000;
}

/* このセクション内の要素のみにbox-sizingを適用 */
.monochrome-ai-search *,
.monochrome-ai-search *::before,
.monochrome-ai-search *::after {
    box-sizing: border-box;
}

/* ============================================
   Tablet Responsive Variables (768px - 1023px)
   ============================================ */
@media (min-width: 768px) and (max-width: 1023px) {
    .monochrome-ai-search {
        --gi-spacing-section: 60px;
        --gi-spacing-3xl: 36px;
        --gi-spacing-2xl: 30px;
        --gi-spacing-xl: 21px;
        
        --gi-font-size-4xl: 32px;
        --gi-font-size-3xl: 26px;
        --gi-font-size-2xl: 22px;
        --gi-font-size-xl: 18px;
        --gi-font-size-lg: 14px;
        --gi-font-size-md: 13px;
        --gi-font-size-base: 12px;
    }
}

/* ============================================
   Mobile Responsive Variables (< 768px)
   ============================================ */
@media (max-width: 767px) {
    .monochrome-ai-search {
        --gi-spacing-section: 45px;
        --gi-spacing-3xl: 30px;
        --gi-spacing-2xl: 24px;
        --gi-spacing-xl: 18px;
        --gi-spacing-lg: 15px;
        --gi-spacing-md: 9px;
        
        --gi-font-size-4xl: 22px;
        --gi-font-size-3xl: 19px;
        --gi-font-size-2xl: 16px;
        --gi-font-size-xl: 14px;
        --gi-font-size-lg: 13px;
        --gi-font-size-md: 12px;
        --gi-font-size-base: 11px;
        --gi-font-size-sm: 10px;
        --gi-font-size-xs: 9px;
    }
}

/* ============================================
   Base Section Styles
   ============================================ */
.monochrome-ai-search {
    position: relative;
    padding: var(--gi-spacing-section) 0;
    background: linear-gradient(135deg, var(--gi-color-background) 0%, var(--gi-color-surface) 100%);
    font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    overflow: hidden;
    min-height: 100vh;
}

/* Background Effects */
.monochrome-ai-search .background-effects {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
}

.monochrome-ai-search .grid-pattern {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(0, 0, 0, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.03) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: grid-move 20s linear infinite;
}

@keyframes grid-move {
    0% { transform: translate(0, 0); }
    100% { transform: translate(50px, 50px); }
}

.monochrome-ai-search .gradient-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 30% 50%, rgba(0, 0, 0, 0.02) 0%, transparent 70%);
}

.monochrome-ai-search .floating-shapes {
    position: absolute;
    inset: 0;
}

.monochrome-ai-search .shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(40px);
    opacity: 0.05;
}

.monochrome-ai-search .shape-1 {
    width: 400px;
    height: 400px;
    background: var(--gi-color-primary);
    top: -200px;
    left: -200px;
    animation: float-1 20s ease-in-out infinite;
}

.monochrome-ai-search .shape-2 {
    width: 300px;
    height: 300px;
    background: var(--gi-color-secondary);
    bottom: -150px;
    right: -150px;
    animation: float-2 25s ease-in-out infinite;
}

.monochrome-ai-search .shape-3 {
    width: 250px;
    height: 250px;
    background: var(--gi-color-tertiary);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: float-3 30s ease-in-out infinite;
}

@keyframes float-1 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(100px, 50px) scale(1.1); }
}

@keyframes float-2 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(-50px, -100px) rotate(180deg); }
}

@keyframes float-3 {
    0%, 100% { transform: translate(-50%, -50%) scale(1); }
    50% { transform: translate(-50%, -50%) scale(1.2); }
}

.monochrome-ai-search .section-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 var(--gi-spacing-3xl);
    position: relative;
    z-index: var(--gi-z-base);
}

/* ============================================
   Section Header
   ============================================ */
.monochrome-ai-search .section-header {
    text-align: center;
    margin-bottom: var(--gi-spacing-3xl);
    position: relative;
}

.monochrome-ai-search .header-accent {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, transparent, var(--gi-color-primary), transparent);
    margin: 0 auto var(--gi-spacing-2xl);
    animation: accent-pulse 3s ease-in-out infinite;
}

@keyframes accent-pulse {
    0%, 100% { opacity: 0.3; transform: scaleX(1); }
    50% { opacity: 1; transform: scaleX(1.5); }
}

.monochrome-ai-search .section-title {
    margin: 0 0 var(--gi-spacing-lg);
}

.monochrome-ai-search .title-en {
    display: block;
    font-size: var(--gi-font-size-4xl);
    font-weight: 900;
    letter-spacing: -0.02em;
    line-height: var(--gi-line-height-tight);
    margin-bottom: var(--gi-spacing-md);
    background: linear-gradient(135deg, var(--gi-color-primary) 0%, var(--gi-color-secondary) 50%, var(--gi-color-primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: gradient-shift 5s ease infinite;
}

@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.monochrome-ai-search .title-ja {
    display: block;
    font-size: var(--gi-font-size-md);
    font-weight: 600;
    letter-spacing: 0.15em;
    color: var(--gi-color-tertiary);
    margin-bottom: var(--gi-spacing-lg);
}

.monochrome-ai-search .section-description {
    font-size: var(--gi-font-size-lg);
    color: var(--gi-color-text-muted);
    letter-spacing: 0.05em;
    margin: 0 auto var(--gi-spacing-2xl);
    max-width: 600px;
    line-height: var(--gi-line-height-relaxed);
}

.monochrome-ai-search .ai-help-text {
    display: inline-block;
    margin-top: 12px;
    font-size: 0.95em;
    color: #555;
    font-weight: 500;
    padding: 8px 16px;
    background: rgba(255, 235, 59, 0.1);
    border-radius: 20px;
    border: 1px solid rgba(255, 235, 59, 0.3);
}

.monochrome-ai-search .yellow-marker {
    width: 60px;
    height: 4px;
    background: var(--gi-color-accent);
    margin: var(--gi-spacing-sm) auto 0;
    border-radius: var(--gi-radius-sm);
    position: relative;
    box-shadow: 0 2px 8px rgba(255, 235, 59, 0.3);
}

.monochrome-ai-search .yellow-marker::after {
    content: '';
    position: absolute;
    top: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 8px;
    height: 8px;
    background: var(--gi-color-accent);
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(255, 235, 59, 0.4);
}

/* ============================================
   Search Bar - Responsive
   ============================================ */
.monochrome-ai-search .ai-search-bar {
    position: relative;
    max-width: 800px;
    margin: 0 auto var(--gi-spacing-2xl);
}

.monochrome-ai-search .search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: var(--gi-color-background);
    border: 2px solid var(--gi-color-primary);
    border-radius: 0;
    transition: all var(--gi-transition-base);
    overflow: hidden;
}

.monochrome-ai-search .search-icon {
    position: absolute;
    left: var(--gi-spacing-lg);
    color: var(--gi-color-text-muted);
    pointer-events: none;
    z-index: 1;
}

.monochrome-ai-search .search-input-wrapper::before {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 0;
    height: 2px;
    background: var(--gi-color-accent);
    transition: width var(--gi-transition-base);
}

.monochrome-ai-search .search-input-wrapper:focus-within::before {
    width: 100%;
}

.monochrome-ai-search .search-input-wrapper:focus-within {
    transform: translateY(-2px);
    box-shadow: var(--gi-shadow-xl);
}

.monochrome-ai-search .search-input {
    flex: 1;
    padding: var(--gi-spacing-lg) var(--gi-spacing-lg) var(--gi-spacing-lg) 52px;
    background: none;
    border: none;
    font-size: var(--gi-font-size-md);
    font-weight: 500;
    outline: none;
    letter-spacing: 0.02em;
    color: var(--gi-color-text);
}

.monochrome-ai-search .search-input::placeholder {
    color: var(--gi-color-text-light);
    font-weight: 400;
}

.monochrome-ai-search .search-actions {
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-sm);
    padding-right: var(--gi-spacing-sm);
}

.monochrome-ai-search .voice-btn {
    width: 44px;
    height: 44px;
    border: none;
    background: transparent;
    color: var(--gi-color-tertiary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--gi-transition-fast);
    position: relative;
    border-radius: var(--gi-radius-sm);
}

.monochrome-ai-search .voice-btn::after {
    content: '';
    position: absolute;
    inset: 8px;
    border: 2px solid transparent;
    transition: all var(--gi-transition-fast);
    border-radius: var(--gi-radius-sm);
}

.monochrome-ai-search .voice-btn:hover {
    color: var(--gi-color-primary);
    background: var(--gi-color-surface);
}

.monochrome-ai-search .voice-btn:hover::after {
    border-color: var(--gi-color-primary);
}

.monochrome-ai-search .voice-btn.recording {
    background: #dc2626;
    color: var(--gi-color-background);
    animation: recordPulse 1.5s infinite;
}

@keyframes recordPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.monochrome-ai-search .search-btn {
    height: 56px;
    padding: 0 var(--gi-spacing-2xl);
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    border: none;
    font-size: var(--gi-font-size-base);
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-sm);
    transition: all var(--gi-transition-base);
    position: relative;
    overflow: hidden;
}

.monochrome-ai-search .search-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.monochrome-ai-search .search-btn:hover::before {
    transform: translateX(100%);
}

.monochrome-ai-search .search-btn:hover {
    transform: scale(1.02);
    box-shadow: var(--gi-shadow-xl);
}

.monochrome-ai-search .search-btn:active {
    transform: scale(0.98);
}

.monochrome-ai-search .btn-icon {
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
}

/* Search Suggestions */
.monochrome-ai-search .search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    margin-top: var(--gi-spacing-sm);
    background: var(--gi-color-background);
    border-radius: var(--gi-radius-xl);
    box-shadow: var(--gi-shadow-xl);
    display: none;
    z-index: var(--gi-z-dropdown);
    max-height: 400px;
    overflow-y: auto;
}

.monochrome-ai-search .search-suggestions.active {
    display: block;
}

.monochrome-ai-search .suggestion-item {
    padding: var(--gi-spacing-md) var(--gi-spacing-lg);
    cursor: pointer;
    transition: background var(--gi-transition-fast);
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-md);
    border-bottom: 1px solid var(--gi-color-border);
}

.monochrome-ai-search .suggestion-item:last-child {
    border-bottom: none;
}

.monochrome-ai-search .suggestion-item:hover {
    background: var(--gi-color-surface);
}

.monochrome-ai-search .suggestion-icon {
    width: 32px;
    height: 32px;
    background: var(--gi-color-surface);
    border-radius: var(--gi-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--gi-font-size-md);
    flex-shrink: 0;
}

/* ============================================
   Quick Filters - Responsive
   ============================================ */
.monochrome-ai-search .quick-filters {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: var(--gi-spacing-md);
    margin-bottom: var(--gi-spacing-3xl);
}

.monochrome-ai-search .filter-chip {
    padding: var(--gi-spacing-md) var(--gi-spacing-lg);
    background: transparent;
    border: 2px solid var(--gi-color-primary);
    font-size: var(--gi-font-size-sm);
    font-weight: 600;
    letter-spacing: 0.05em;
    color: var(--gi-color-primary);
    cursor: pointer;
    transition: all var(--gi-transition-base);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-sm);
    white-space: nowrap;
}

.monochrome-ai-search .filter-chip svg {
    flex-shrink: 0;
}

.monochrome-ai-search .filter-chip::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--gi-color-primary);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform var(--gi-transition-base);
    z-index: -1;
}

.monochrome-ai-search .filter-chip:hover::before {
    transform: scaleX(1);
}

.monochrome-ai-search .filter-chip:hover {
    color: var(--gi-color-background);
    transform: translateY(-2px);
    box-shadow: var(--gi-shadow-lg);
}

.monochrome-ai-search .filter-chip.active {
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
}

.monochrome-ai-search .filter-chip.active::before {
    transform: scaleX(1);
}

/* ============================================
   AI Consult Link
   ============================================ */
.monochrome-ai-search .ai-consult-link-wrapper {
    margin-top: var(--gi-spacing-xl);
    margin-bottom: var(--gi-spacing-2xl);
    text-align: center;
}

.monochrome-ai-search .ai-consult-link {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 16px 28px;
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    border: 2px solid var(--gi-color-primary);
    font-size: var(--gi-font-size-md);
    font-weight: 700;
    text-decoration: none;
    transition: all var(--gi-transition-base);
    border-radius: 0;
    box-shadow: var(--gi-shadow-md);
}

.monochrome-ai-search .ai-consult-link:hover {
    background: var(--gi-color-background);
    color: var(--gi-color-primary);
    transform: translateY(-2px);
    box-shadow: var(--gi-shadow-xl);
}

.monochrome-ai-search .ai-consult-link svg:last-child {
    transition: transform var(--gi-transition-base);
}

.monochrome-ai-search .ai-consult-link:hover svg:last-child {
    transform: translateX(4px);
}

/* ============================================
   Main Content - Responsive Grid (AI Assistant Removed)
   ============================================ */
.monochrome-ai-search .ai-main-content {
    display: block;
    margin-bottom: var(--gi-spacing-2xl);
}

/* ============================================
   AI Assistant Panel (REMOVED)
   ============================================ */
/* AI Assistant section removed as requested */

.monochrome-ai-search .assistant-header {
    padding: var(--gi-spacing-lg);
    border-bottom: 2px solid #000000;
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-md);
    position: relative;
}

.monochrome-ai-search .assistant-avatar {
    position: relative;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
}

.monochrome-ai-search .avatar-ring {
    position: absolute;
    inset: 0;
    border: 2px solid var(--gi-color-primary);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.05); }
}

.monochrome-ai-search .avatar-icon {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    border-radius: 50%;
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
}

.monochrome-ai-search .assistant-info {
    flex: 1;
    min-width: 0;
}

.monochrome-ai-search .assistant-name {
    font-size: var(--gi-font-size-sm);
    font-weight: 600;
    margin: 0 0 var(--gi-spacing-xs);
    color: var(--gi-color-text);
}

.monochrome-ai-search .assistant-status {
    font-size: var(--gi-font-size-xs);
    color: #10b981;
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-xs);
}

.monochrome-ai-search .status-dot {
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

/* AI History Button */
.monochrome-ai-search .ai-history-btn {
    margin-left: auto;
    background: var(--gi-color-background);
    border: 2px solid var(--gi-color-primary);
    padding: var(--gi-spacing-sm) var(--gi-spacing-md);
    border-radius: var(--gi-radius-sm);
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-sm);
    cursor: pointer;
    transition: all var(--gi-transition-base);
    font-weight: 600;
    font-size: var(--gi-font-size-xs);
    color: var(--gi-color-text);
}

.monochrome-ai-search .ai-history-btn:hover {
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
}

.monochrome-ai-search .history-count {
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    padding: 2px var(--gi-spacing-sm);
    border-radius: var(--gi-radius-sm);
    font-size: var(--gi-font-size-xs);
    font-weight: 700;
    min-width: 20px;
    text-align: center;
}

.monochrome-ai-search .ai-history-btn:hover .history-count {
    background: var(--gi-color-background);
    color: var(--gi-color-primary);
}

/* AI History Panel */
.monochrome-ai-search .ai-history-panel {
    position: absolute;
    top: 100%;
    right: 0;
    width: 100%;
    max-height: 300px;
    background: var(--gi-color-background);
    border: 2px solid var(--gi-color-primary);
    border-radius: var(--gi-radius-lg);
    box-shadow: var(--gi-shadow-2xl);
    z-index: var(--gi-z-dropdown);
    overflow: hidden;
    animation: slideDown var(--gi-transition-base);
    margin-top: var(--gi-spacing-sm);
}

@keyframes slideDown {
    from { 
        opacity: 0; 
        transform: translateY(-10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.monochrome-ai-search .ai-history-header {
    padding: var(--gi-spacing-md);
    border-bottom: 2px solid var(--gi-color-primary);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--gi-color-surface);
}

.monochrome-ai-search .ai-history-header h4 {
    margin: 0;
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-sm);
    color: var(--gi-color-text);
}

.monochrome-ai-search .ai-history-clear {
    background: var(--gi-color-background);
    border: 2px solid var(--gi-color-primary);
    padding: 6px var(--gi-spacing-md);
    border-radius: var(--gi-radius-sm);
    font-size: var(--gi-font-size-xs);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--gi-transition-base);
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--gi-color-text);
}

.monochrome-ai-search .ai-history-clear:hover {
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
}

.monochrome-ai-search .ai-history-list {
    padding: var(--gi-spacing-md);
    max-height: 220px;
    overflow-y: auto;
}

.monochrome-ai-search .ai-history-empty {
    text-align: center;
    color: var(--gi-color-text-light);
    font-size: var(--gi-font-size-sm);
    padding: var(--gi-spacing-xl) var(--gi-spacing-md);
    margin: 0;
}

.monochrome-ai-search .ai-history-item {
    padding: var(--gi-spacing-md);
    border: 2px solid var(--gi-color-border);
    border-radius: var(--gi-radius-sm);
    margin-bottom: var(--gi-spacing-sm);
    cursor: pointer;
    transition: all var(--gi-transition-base);
}

.monochrome-ai-search .ai-history-item:hover {
    border-color: var(--gi-color-primary);
    background: var(--gi-color-surface);
    transform: translateX(4px);
}

.monochrome-ai-search .ai-history-item:last-child {
    margin-bottom: 0;
}

.monochrome-ai-search .history-date {
    font-size: var(--gi-font-size-xs);
    color: var(--gi-color-text-light);
    margin-bottom: var(--gi-spacing-xs);
    font-weight: 600;
}

.monochrome-ai-search .history-question {
    font-size: var(--gi-font-size-sm);
    color: var(--gi-color-secondary);
    font-weight: 500;
    line-height: var(--gi-line-height-normal);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Chat Messages */
.monochrome-ai-search .chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: var(--gi-spacing-lg);
    display: flex;
    flex-direction: column;
    gap: var(--gi-spacing-md);
}

.monochrome-ai-search .message {
    display: flex;
    gap: var(--gi-spacing-md);
    animation: messageIn var(--gi-transition-base) ease-out;
}

@keyframes messageIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.monochrome-ai-search .message-user {
    flex-direction: row-reverse;
}

.monochrome-ai-search .message-avatar {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gi-color-surface);
    border-radius: 50%;
    border: 2px solid var(--gi-color-border);
}

.monochrome-ai-search .message-bubble {
    max-width: 85%;
    padding: var(--gi-spacing-md) var(--gi-spacing-lg);
    background: var(--gi-color-background);
    border-radius: var(--gi-radius-md);
    font-size: var(--gi-font-size-base);
    line-height: var(--gi-line-height-relaxed);
    box-shadow: var(--gi-shadow-sm);
    border: 1px solid var(--gi-color-border);
    word-wrap: break-word;
}

.monochrome-ai-search .message-user .message-bubble {
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    border-color: var(--gi-color-primary);
}

/* Chat Input */
.monochrome-ai-search .chat-input-area {
    padding: var(--gi-spacing-md);
    border-top: 1px solid var(--gi-color-border);
    position: relative;
    display: flex;
    gap: var(--gi-spacing-sm);
    align-items: flex-end;
}

.monochrome-ai-search .typing-indicator {
    position: absolute;
    top: calc(-24px - var(--gi-spacing-sm));
    left: var(--gi-spacing-lg);
    display: none;
    gap: var(--gi-spacing-xs);
}

.monochrome-ai-search .typing-indicator.active {
    display: flex;
}

.monochrome-ai-search .typing-indicator span {
    width: 8px;
    height: 8px;
    background: var(--gi-color-text-light);
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.monochrome-ai-search .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.monochrome-ai-search .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}

.monochrome-ai-search .chat-input {
    flex: 1;
    padding: var(--gi-spacing-md);
    background: var(--gi-color-background);
    border: 2px solid var(--gi-color-primary);
    border-radius: var(--gi-radius-sm);
    font-size: var(--gi-font-size-base);
    resize: none;
    outline: none;
    transition: all var(--gi-transition-fast);
    min-height: 44px;
    max-height: 120px;
    font-family: inherit;
}

.monochrome-ai-search .chat-input:focus {
    border-color: var(--gi-color-accent);
    box-shadow: 0 0 0 2px rgba(255, 235, 59, 0.2);
}

.monochrome-ai-search .chat-send-btn {
    height: 44px;
    padding: 0 var(--gi-spacing-lg);
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    border: 2px solid var(--gi-color-primary);
    border-radius: var(--gi-radius-sm);
    cursor: pointer;
    transition: all var(--gi-transition-fast);
    font-weight: 600;
    font-size: var(--gi-font-size-base);
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-sm);
    flex-shrink: 0;
}

.monochrome-ai-search .chat-send-btn:hover {
    background: var(--gi-color-background);
    color: var(--gi-color-primary);
}

.monochrome-ai-search .chat-send-btn:active {
    transform: scale(0.98);
}

/* Quick Questions */
.monochrome-ai-search .quick-questions {
    padding: var(--gi-spacing-md);
    display: flex;
    flex-wrap: wrap;
    gap: var(--gi-spacing-sm);
    border-top: 1px solid var(--gi-color-border);
}

.monochrome-ai-search .quick-q {
    padding: var(--gi-spacing-sm) var(--gi-spacing-md);
    background: var(--gi-color-background);
    border: 1px solid var(--gi-color-border);
    border-radius: var(--gi-radius-full);
    font-size: var(--gi-font-size-xs);
    font-weight: 500;
    color: var(--gi-color-tertiary);
    cursor: pointer;
    transition: all var(--gi-transition-fast);
    display: flex;
    align-items: center;
    gap: 6px;
}

.monochrome-ai-search .quick-q:hover {
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    border-color: var(--gi-color-primary);
}

/* ============================================
   Search Results Panel
   ============================================ */
.monochrome-ai-search .search-results-panel {
    background: var(--gi-color-surface);
    border-radius: var(--gi-radius-2xl);
    padding: var(--gi-spacing-lg);
    min-height: 600px;
}

.monochrome-ai-search .results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--gi-spacing-2xl);
    padding-bottom: var(--gi-spacing-lg);
    border-bottom: 5px solid #000000;
}

.monochrome-ai-search .results-title {
    font-size: var(--gi-font-size-lg);
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-md);
    color: var(--gi-color-text);
    border: 4px solid var(--gi-color-primary);
    padding: var(--gi-spacing-md) var(--gi-spacing-lg);
    border-radius: var(--gi-radius-md);
    background: var(--gi-color-surface);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    width: fit-content;
}

.monochrome-ai-search #results-count {
    font-size: var(--gi-font-size-2xl);
    font-weight: 900;
    color: var(--gi-color-primary);
}

.monochrome-ai-search .view-controls {
    display: flex;
    gap: var(--gi-spacing-xs);
    padding: var(--gi-spacing-xs);
    background: var(--gi-color-background);
    border-radius: var(--gi-radius-md);
}

.monochrome-ai-search .view-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: none;
    color: var(--gi-color-text-light);
    cursor: pointer;
    border-radius: var(--gi-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--gi-transition-fast);
}

.monochrome-ai-search .view-btn:hover {
    background: var(--gi-color-surface);
}

.monochrome-ai-search .view-btn.active {
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
}

/* ============================================
   Grant Cards - Grid View (Default)
   ============================================ */
.monochrome-ai-search .featured-grants {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--gi-spacing-2xl);
    max-width: 1200px;
    margin: 0 auto;
}

.monochrome-ai-search .grant-card {
    position: relative;
    background: var(--gi-color-background);
    padding: var(--gi-spacing-2xl);
    border: 5px solid #000000 !important;
    transition: all var(--gi-transition-base);
    cursor: pointer;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
}

.monochrome-ai-search .grant-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--gi-color-accent);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform var(--gi-transition-base);
}

.monochrome-ai-search .grant-card:hover::before {
    transform: scaleX(1);
}

.monochrome-ai-search .grant-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(0,0,0,0.02) 100%);
    opacity: 0;
    transition: opacity var(--gi-transition-base);
}

.monochrome-ai-search .grant-card:hover::after {
    opacity: 1;
}

.monochrome-ai-search .grant-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
    border-color: #333333 !important;
}

.monochrome-ai-search .card-badge {
    position: absolute;
    top: 0;
    right: 0;
    padding: var(--gi-spacing-sm) var(--gi-spacing-md);
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    font-size: var(--gi-font-size-xs);
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.monochrome-ai-search .card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--gi-spacing-sm);
    margin-bottom: var(--gi-spacing-md);
}

.monochrome-ai-search .card-title {
    font-size: var(--gi-font-size-lg);
    font-weight: 700;
    margin: 0;
    line-height: var(--gi-line-height-normal);
    flex: 1;
    color: var(--gi-color-text);
}

.monochrome-ai-search .card-bookmark {
    width: 32px;
    height: 32px;
    border: 2px solid var(--gi-color-border);
    background: var(--gi-color-background);
    border-radius: var(--gi-radius-sm);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--gi-transition-fast);
    flex-shrink: 0;
}

.monochrome-ai-search .card-bookmark:hover {
    border-color: var(--gi-color-accent);
    background: var(--gi-color-accent);
    color: var(--gi-color-primary);
}

.monochrome-ai-search .card-meta {
    display: flex;
    gap: var(--gi-spacing-md);
    margin-bottom: var(--gi-spacing-md);
}

.monochrome-ai-search .meta-item {
    display: flex;
    flex-direction: column;
    gap: var(--gi-spacing-xs);
}

.monochrome-ai-search .meta-item svg {
    margin-bottom: var(--gi-spacing-xs);
}

.monochrome-ai-search .meta-label {
    font-size: var(--gi-font-size-xs);
    color: var(--gi-color-text-light);
}

.monochrome-ai-search .meta-value {
    font-size: var(--gi-font-size-base);
    font-weight: 800;
    color: var(--gi-color-primary);
}

.monochrome-ai-search .card-org {
    font-size: var(--gi-font-size-xs);
    color: var(--gi-color-tertiary);
    margin: 0 0 var(--gi-spacing-md);
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-xs);
}

.monochrome-ai-search .card-rate {
    margin-bottom: var(--gi-spacing-md);
}

.monochrome-ai-search .rate-bar {
    height: 4px;
    background: var(--gi-color-border);
    border-radius: var(--gi-radius-sm);
    overflow: hidden;
    margin-bottom: var(--gi-spacing-xs);
}

.monochrome-ai-search .rate-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #34d399);
    transition: width 1s ease-out;
}

.monochrome-ai-search .rate-text {
    font-size: var(--gi-font-size-xs);
    color: var(--gi-color-tertiary);
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-xs);
}

/* Card Actions */
.monochrome-ai-search .card-actions {
    display: flex;
    align-items: center;
    gap: var(--gi-spacing-md);
    margin-top: auto;
    pointer-events: auto !important;
    position: relative;
    z-index: 10;
}

.monochrome-ai-search .ai-assist-btn {
    padding: var(--gi-spacing-md) var(--gi-spacing-lg);
    background: transparent;
    border: 3px solid var(--gi-color-primary);
    color: var(--gi-color-primary);
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    border-radius: var(--gi-radius-full);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all var(--gi-transition-base);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    pointer-events: auto !important;
    position: relative;
    z-index: 20;
}

.monochrome-ai-search .ai-assist-btn:hover {
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    transform: translateY(-1px);
    box-shadow: var(--gi-shadow-md);
}

.monochrome-ai-search .card-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: var(--gi-font-size-base);
    font-weight: 700;
    color: var(--gi-color-primary);
    text-decoration: none;
    transition: all var(--gi-transition-fast);
    pointer-events: auto !important;
    position: relative;
    z-index: 20;
}

.monochrome-ai-search .card-link:hover {
    gap: 10px;
    color: var(--gi-color-accent);
}

/* ============================================
   List View - Fixed Layout
   ============================================ */
.monochrome-ai-search .results-list {
    display: flex;
    flex-direction: column;
    gap: var(--gi-spacing-md);
}

.monochrome-ai-search .results-list .grant-card {
    display: grid;
    grid-template-columns: 1fr auto;
    grid-template-rows: auto auto auto;
    gap: var(--gi-spacing-md);
    align-items: start;
    padding: var(--gi-spacing-lg);
}

.monochrome-ai-search .results-list .card-badge {
    grid-column: 2;
    grid-row: 1;
    position: static;
}

.monochrome-ai-search .results-list .card-header {
    grid-column: 1;
    grid-row: 1;
    margin-bottom: 0;
}

.monochrome-ai-search .results-list .card-title {
    font-size: var(--gi-font-size-md);
}

.monochrome-ai-search .results-list .card-meta {
    grid-column: 1;
    grid-row: 2;
    display: flex;
    gap: var(--gi-spacing-xl);
    margin-bottom: 0;
}

.monochrome-ai-search .results-list .meta-item {
    flex-direction: row;
    align-items: center;
    gap: var(--gi-spacing-sm);
}

.monochrome-ai-search .results-list .meta-item svg {
    margin-bottom: 0;
}

.monochrome-ai-search .results-list .card-org {
    grid-column: 1;
    grid-row: 3;
    margin-bottom: 0;
}

.monochrome-ai-search .results-list .card-rate {
    grid-column: 2;
    grid-row: 2 / 4;
    margin-bottom: 0;
    min-width: 200px;
}

.monochrome-ai-search .results-list .card-actions {
    grid-column: 1 / 3;
    grid-row: 4;
    justify-content: flex-end;
    margin-top: var(--gi-spacing-md);
}

/* Loading State */
.monochrome-ai-search .results-loading {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--gi-spacing-3xl);
    color: var(--gi-color-tertiary);
    font-size: var(--gi-font-size-sm);
}

.monochrome-ai-search .results-loading.active {
    display: flex;
}

.monochrome-ai-search .loading-spinner {
    width: 32px;
    height: 32px;
    border: 3px solid var(--gi-color-surface);
    border-top-color: var(--gi-color-primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-bottom: var(--gi-spacing-md);
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ============================================
   Floating Action Button (Mobile Only)
   ============================================ */
.monochrome-ai-search .fab-mobile {
    display: none;
    position: fixed;
    bottom: var(--gi-spacing-lg);
    right: var(--gi-spacing-lg);
    width: 56px;
    height: 56px;
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    border: none;
    border-radius: 50%;
    box-shadow: var(--gi-shadow-2xl);
    cursor: pointer;
    z-index: var(--gi-z-fixed);
    transition: all var(--gi-transition-base);
}

.monochrome-ai-search .fab-mobile:hover {
    transform: scale(1.1);
}

.monochrome-ai-search .fab-mobile:active {
    transform: scale(0.95);
}

/* ============================================
   Keyboard Shortcuts Hint (PC Only)
   ============================================ */
.monochrome-ai-search .keyboard-shortcuts-hint {
    display: none;
    position: fixed;
    bottom: var(--gi-spacing-lg);
    left: var(--gi-spacing-lg);
    padding: var(--gi-spacing-sm) var(--gi-spacing-md);
    background: var(--gi-color-primary);
    color: var(--gi-color-background);
    border-radius: var(--gi-radius-md);
    font-size: var(--gi-font-size-xs);
    font-weight: 600;
    z-index: var(--gi-z-fixed);
    animation: fadeInUp var(--gi-transition-base);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.monochrome-ai-search .keyboard-shortcuts-hint kbd {
    display: inline-block;
    padding: 2px 6px;
    background: var(--gi-color-background);
    color: var(--gi-color-primary);
    border-radius: var(--gi-radius-sm);
    font-family: monospace;
    font-size: var(--gi-font-size-xs);
    margin: 0 2px;
}

/* ============================================
   RESPONSIVE BREAKPOINTS
   ============================================ */

/* ============================================
   Tablet (768px - 1023px)
   ============================================ */
@media (min-width: 768px) and (max-width: 1023px) {
    /* AI Assistant removed - no specific tablet overrides needed */
    
    .monochrome-ai-search .section-container {
        padding: 0 var(--gi-spacing-lg);
    }
    
    .monochrome-ai-search .featured-grants,
    .monochrome-ai-search .results-list {
        grid-template-columns: 1fr;
        max-width: 100%;
    }
    
    .monochrome-ai-search .search-results-panel {
        max-width: 100%;
        border-width: 3px;
    }
    
    .monochrome-ai-search .keyboard-shortcuts-hint {
        display: block;
    }
}

/* ============================================
   Mobile (< 768px)
   ============================================ */
@media (max-width: 767px) {
    .monochrome-ai-search {
        padding: var(--gi-spacing-3xl) 0;
    }
    
    .monochrome-ai-search .section-header {
        margin-bottom: var(--gi-spacing-2xl);
    }
    
    /* AI Assistant removed - mobile layout simplified */
    
    .monochrome-ai-search .quick-filters {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--gi-spacing-sm);
        margin-bottom: var(--gi-spacing-xl);
    }
    
    .monochrome-ai-search .filter-chip {
        padding: var(--gi-spacing-sm) var(--gi-spacing-md);
        font-size: var(--gi-font-size-xs);
        justify-content: center;
    }
    
    .monochrome-ai-search .filter-chip svg {
        display: none;
    }
    
    .monochrome-ai-search .featured-grants,
    .monochrome-ai-search .results-list {
        grid-template-columns: 1fr;
        gap: var(--gi-spacing-md);
    }
    
    .monochrome-ai-search .fab-mobile {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .monochrome-ai-search .section-container {
        padding: 0 var(--gi-spacing-md);
    }
    
    .monochrome-ai-search .ai-search-bar {
        margin-bottom: var(--gi-spacing-lg);
    }
    
    .monochrome-ai-search .search-input-wrapper {
        padding: var(--gi-spacing-md);
    }
    
    .monochrome-ai-search .search-input {
        font-size: var(--gi-font-size-sm);
        padding-left: 40px;
    }
    
    .monochrome-ai-search .search-icon {
        left: var(--gi-spacing-md);
        width: 16px;
        height: 16px;
    }
    
    .monochrome-ai-search .search-btn {
        padding: var(--gi-spacing-sm) var(--gi-spacing-md);
        height: 40px;
    }
    
    .monochrome-ai-search .btn-text {
        display: none;
    }
    
    .monochrome-ai-search .voice-btn {
        width: 36px;
        height: 36px;
    }
    
    .monochrome-ai-search .card-actions {
        flex-direction: column;
        gap: var(--gi-spacing-sm);
        align-items: stretch;
    }
    
    .monochrome-ai-search .ai-assist-btn {
        justify-content: center;
        padding: var(--gi-spacing-sm) var(--gi-spacing-md);
    }
    
    .monochrome-ai-search .card-link {
        text-align: center;
        justify-content: center;
    }
    
    .monochrome-ai-search .ai-history-btn .history-text {
        display: none;
    }
    
    .monochrome-ai-search .chat-input-area {
        padding: var(--gi-spacing-sm);
    }
    
    .monochrome-ai-search .chat-send-btn {
        height: 40px;
        padding: 0 var(--gi-spacing-md);
    }
    
    .monochrome-ai-search .btn-text-desktop {
        display: none;
    }
    
    /* List View Mobile Adjustments */
    .monochrome-ai-search .results-list .grant-card {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }
    
    .monochrome-ai-search .results-list .card-badge {
        grid-column: 1;
        grid-row: 1;
        justify-self: start;
    }
    
    .monochrome-ai-search .results-list .card-header {
        grid-column: 1;
        grid-row: 2;
    }
    
    .monochrome-ai-search .results-list .card-meta {
        grid-column: 1;
        grid-row: 3;
    }
    
    .monochrome-ai-search .results-list .card-org {
        grid-column: 1;
        grid-row: 4;
    }
    
    .monochrome-ai-search .results-list .card-rate {
        grid-column: 1;
        grid-row: 5;
        min-width: auto;
    }
    
    .monochrome-ai-search .results-list .card-actions {
        grid-column: 1;
        grid-row: 6;
    }
}

/* ============================================
   Mobile Small (< 375px)
   ============================================ */
@media (max-width: 374px) {
    .monochrome-ai-search {
        padding: var(--gi-spacing-xl) 0;
    }
    
    .monochrome-ai-search .section-header {
        margin-bottom: var(--gi-spacing-lg);
    }
    
    .monochrome-ai-search .section-description {
        font-size: var(--gi-font-size-sm);
    }
    
    .monochrome-ai-search .quick-filters {
        gap: 6px;
        margin-bottom: var(--gi-spacing-md);
    }
    
    .monochrome-ai-search .filter-chip {
        padding: 6px var(--gi-spacing-sm);
        font-size: var(--gi-font-size-xs);
    }
    
    .monochrome-ai-search .search-input-wrapper {
        padding: var(--gi-spacing-sm);
    }
    
    .monochrome-ai-search .search-input {
        font-size: var(--gi-font-size-sm);
        padding: var(--gi-spacing-sm) var(--gi-spacing-sm) var(--gi-spacing-sm) 36px;
    }
    
    .monochrome-ai-search .search-btn {
        padding: var(--gi-spacing-sm);
        height: 36px;
    }
    
    .monochrome-ai-search .voice-btn {
        width: 32px;
        height: 32px;
    }
    
    /* AI Assistant removed */
    
    .monochrome-ai-search .section-container {
        padding: 0 var(--gi-spacing-sm);
    }
    
    .monochrome-ai-search .grant-card {
        padding: var(--gi-spacing-md);
    }
    
    .monochrome-ai-search .card-title {
        font-size: var(--gi-font-size-sm);
    }
    
    .monochrome-ai-search .fab-mobile {
        width: 48px;
        height: 48px;
        bottom: var(--gi-spacing-md);
        right: var(--gi-spacing-md);
    }
}

/* ============================================
   Desktop Large (1440px+)
   ============================================ */
@media (min-width: 1440px) {
    .monochrome-ai-search .section-container {
        max-width: 1600px;
        padding: 0 var(--gi-spacing-3xl);
    }
    
    /* AI Assistant removed */
    
    .monochrome-ai-search .featured-grants {
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: var(--gi-spacing-xl);
    }
    
    .monochrome-ai-search .keyboard-shortcuts-hint {
        display: block;
    }
}

/* ============================================
   4K Display (1920px+)
   ============================================ */
@media (min-width: 1920px) {
    .monochrome-ai-search .section-container {
        max-width: 1800px;
    }
    
    /* AI Assistant removed */
    
    .monochrome-ai-search .featured-grants {
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    }
}

/* ============================================
   Print Styles
   ============================================ */
@media print {
    .monochrome-ai-search .background-effects,
    .monochrome-ai-search .fab-mobile,
    .monochrome-ai-search .keyboard-shortcuts-hint,
    .monochrome-ai-search .voice-btn,
    .monochrome-ai-search .ai-history-btn,
    .monochrome-ai-search .quick-questions,
    .monochrome-ai-search .view-controls {
        display: none !important;
    }
    
    .monochrome-ai-search {
        background: white;
    }
    
    .monochrome-ai-search .grant-card {
        page-break-inside: avoid;
    }
}

/* ============================================
   Accessibility - High Contrast Mode
   ============================================ */
@media (prefers-contrast: high) {
    .monochrome-ai-search {
        --gi-color-primary: #000000;
        --gi-color-background: #FFFFFF;
        --gi-color-border: #000000;
    }
    
    .monochrome-ai-search .grant-card,
    .monochrome-ai-search .search-input-wrapper,
    .monochrome-ai-search .filter-chip,
    /* AI Assistant removed */
    }
}

/* ============================================
   Accessibility - Reduced Motion
   ============================================ */
@media (prefers-reduced-motion: reduce) {
    .monochrome-ai-search *,
    .monochrome-ai-search *::before,
    .monochrome-ai-search *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* ============================================
   Utility Classes
   ============================================ */
.monochrome-ai-search .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

.monochrome-ai-search .no-scroll {
    overflow: hidden;
}

.monochrome-ai-search .text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.monochrome-ai-search .text-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.monochrome-ai-search .text-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ============================================
   CRITICAL: Force Border Visibility for Search Results
   These styles have maximum specificity to ensure borders are visible
   ============================================ */
.monochrome-ai-search .grant-card,
.monochrome-ai-search .featured-grants .grant-card,
.monochrome-ai-search .results-list .grant-card,
.monochrome-ai-search .results-container .grant-card {
    border: 4px solid #000000 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
}

.monochrome-ai-search .grant-card:hover,
.monochrome-ai-search .featured-grants .grant-card:hover,
.monochrome-ai-search .results-list .grant-card:hover,
.monochrome-ai-search .results-container .grant-card:hover {
    border: 4px solid #333333 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
}

.monochrome-ai-search .results-header {
    border-bottom: 4px solid #000000 !important;
}

/* AI Assistant styles removed */

.monochrome-ai-search .suggestion-item {
    border-bottom: 1px solid #000000 !important;
}

.monochrome-ai-search .quick-questions {
    border-top: 1px solid #000000 !important;
}

.monochrome-ai-search .chat-input-area {
    border-top: 1px solid #000000 !important;
}
</style>

<script>
(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        API_URL: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
        NONCE: '<?php echo esc_js($nonce); ?>',
        SESSION_ID: '<?php echo esc_js($session_id); ?>',
        TYPING_DELAY: 30,
        DEBOUNCE_DELAY: 300,
    };
    
    // Device Detection
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const isTablet = /iPad|Android/i.test(navigator.userAgent) && window.innerWidth >= 768 && window.innerWidth <= 1023;
    const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    console.log('Device Info:', { isMobile, isTablet, isTouch });

    // AI Search Controller
    class AISearchController {
        constructor() {
            this.state = {
                isSearching: false,
                isTyping: false,
                currentFilter: 'all',
                currentView: 'grid',
                results: [],
                chatHistory: [],
            };
            
            this.elements = {};
            this.init();
        }

        init() {
            this.cacheElements();
            this.bindEvents();
            this.initAnimations();
            this.testConnection();
            this.debugButtonStates();
            
            // Device-specific initialization
            if (!isMobile) {
                this.initKeyboardShortcuts();
                this.initHoverEffects();
            }
            
            if (isTouch) {
                this.initTouchGestures();
                this.initPullToRefresh();
            }
        }

        debugButtonStates() {
            console.log('=== Button Debug Information ===');
            
            const aiButtons = document.querySelectorAll('.ai-assist-btn');
            console.log(`AI Assistant buttons found: ${aiButtons.length}`);
            aiButtons.forEach((btn, index) => {
                console.log(`AI Button ${index}:`, {
                    grantId: btn.dataset.grantId,
                    postId: btn.dataset.postId, 
                    grantTitle: btn.dataset.grantTitle,
                    grantPermalink: btn.dataset.grantPermalink,
                    clickable: window.getComputedStyle(btn).pointerEvents !== 'none'
                });
            });
            
            const detailLinks = document.querySelectorAll('.card-link');
            console.log(`Detail links found: ${detailLinks.length}`);
            detailLinks.forEach((link, index) => {
                console.log(`Detail Link ${index}:`, {
                    href: link.href,
                    clickable: window.getComputedStyle(link).pointerEvents !== 'none'
                });
            });
        }

        async testConnection() {
            try {
                const formData = new FormData();
                formData.append('action', 'gi_test_connection');
                
                const response = await fetch(CONFIG.API_URL, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                console.log('Test connection result:', data);
            } catch (error) {
                console.error('Test connection failed:', error);
            }
        }

        cacheElements() {
            this.elements = {
                searchInput: document.getElementById('ai-search-input'),
                searchBtn: document.getElementById('ai-search-btn'),
                suggestions: document.getElementById('search-suggestions'),
                filterChips: document.querySelectorAll('.filter-chip'),
                chatMessages: document.getElementById('chat-messages'),
                chatInput: document.getElementById('chat-input'),
                chatSend: document.getElementById('chat-send'),
                typingIndicator: document.getElementById('typing-indicator'),
                resultsContainer: document.getElementById('results-container'),
                resultsLoading: document.getElementById('results-loading'),
                resultsCount: document.getElementById('results-count'),
                viewBtns: document.querySelectorAll('.view-btn'),
                quickQuestions: document.querySelectorAll('.quick-q'),
                voiceBtn: document.querySelector('.voice-btn'),
                fabMobile: document.getElementById('fab-mobile'),
            };
        }

        bindEvents() {
            // Search events
            this.elements.searchInput?.addEventListener('input', this.debounce(this.handleSearchInput.bind(this), CONFIG.DEBOUNCE_DELAY));
            this.elements.searchInput?.addEventListener('focus', this.showSuggestions.bind(this));
            this.elements.searchBtn?.addEventListener('click', this.performSearch.bind(this));
            
            // Enter key for search
            this.elements.searchInput?.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.performSearch();
                }
            });

            // Filter chips
            this.elements.filterChips.forEach(chip => {
                chip.addEventListener('click', this.handleFilterClick.bind(this));
            });

            // Chat events
            this.elements.chatInput?.addEventListener('input', this.autoResizeTextarea.bind(this));
            this.elements.chatSend?.addEventListener('click', this.sendChatMessage.bind(this));
            
            // Enter key for chat
            this.elements.chatInput?.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendChatMessage();
                }
            });

            // Quick questions
            this.elements.quickQuestions.forEach(btn => {
                btn.addEventListener('click', this.handleQuickQuestion.bind(this));
            });

            // View controls
            this.elements.viewBtns.forEach(btn => {
                btn.addEventListener('click', this.handleViewChange.bind(this));
            });

            // Voice input
            this.elements.voiceBtn?.addEventListener('click', this.startVoiceInput.bind(this));

            // FAB Mobile
            this.elements.fabMobile?.addEventListener('click', () => {
                const chatPanel = document.querySelector('.ai-assistant-panel');
                if (chatPanel) {
                    chatPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    this.elements.chatInput?.focus();
                }
            });

            // Click outside to close suggestions
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.ai-search-bar')) {
                    this.hideSuggestions();
                }
            });
        }

        // Keyboard Shortcuts (PC Only)
        initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + K: Focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    this.elements.searchInput?.focus();
                }
                
                // Escape: Close modals
                if (e.key === 'Escape') {
                    this.hideSuggestions();
                    const modal = document.querySelector('.grant-assistant-modal.active');
                    if (modal) {
                        modal.classList.remove('active');
                        setTimeout(() => modal.remove(), 300);
                    }
                }
            });
        }

        // Hover Effects (PC Only)
        initHoverEffects() {
            const cards = document.querySelectorAll('.grant-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.zIndex = '10';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.zIndex = '1';
                });
            });
        }

        // Touch Gestures (Mobile/Tablet)
        initTouchGestures() {
            let touchStartX = 0;
            let touchEndX = 0;
            
            const filterContainer = document.querySelector('.quick-filters');
            if (!filterContainer) return;
            
            filterContainer.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            
            filterContainer.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe();
            }, { passive: true });
            
            const handleSwipe = () => {
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > swipeThreshold) {
                    const activeChip = document.querySelector('.filter-chip.active');
                    const allChips = Array.from(document.querySelectorAll('.filter-chip'));
                    const currentIndex = allChips.indexOf(activeChip);
                    
                    let nextIndex;
                    if (diff > 0) {
                        // Swipe left - next
                        nextIndex = (currentIndex + 1) % allChips.length;
                    } else {
                        // Swipe right - previous
                        nextIndex = (currentIndex - 1 + allChips.length) % allChips.length;
                    }
                    
                    allChips[nextIndex].click();
                }
            };
            
            this.handleSwipe = handleSwipe;
        }

        // Pull to Refresh (Mobile)
        initPullToRefresh() {
            let startY = 0;
            let isPulling = false;
            
            const container = document.querySelector('.search-results-panel');
            if (!container) return;
            
            container.addEventListener('touchstart', (e) => {
                if (container.scrollTop === 0) {
                    startY = e.touches[0].pageY;
                    isPulling = true;
                }
            }, { passive: true });
            
            container.addEventListener('touchmove', (e) => {
                if (!isPulling) return;
                
                const currentY = e.touches[0].pageY;
                const pullDistance = currentY - startY;
                
                if (pullDistance > 100) {
                    console.log('Pull to refresh triggered');
                }
            }, { passive: true });
            
            container.addEventListener('touchend', () => {
                if (isPulling) {
                    isPulling = false;
                    if (this.elements.searchInput?.value) {
                        this.performSearch();
                    }
                }
            }, { passive: true });
        }

        // Search Methods
        async handleSearchInput(e) {
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                this.hideSuggestions();
                return;
            }

            const suggestions = await this.fetchSuggestions(query);
            this.displaySuggestions(suggestions);
        }

        async fetchSuggestions(query) {
            try {
                const formData = new FormData();
                formData.append('action', 'gi_search_suggestions');
                formData.append('nonce', CONFIG.NONCE);
                formData.append('query', query);

                const response = await fetch(CONFIG.API_URL, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const data = await response.json();
                
                if (data.success && data.data && data.data.suggestions && Array.isArray(data.data.suggestions)) {
                    return data.data.suggestions.filter(s => s && typeof s.text === 'string' && s.text.trim() !== '');
                }
            } catch (error) {
                console.error('Suggestions error:', error);
            }

            const fallbackSuggestions = [
                { icon: '🏭', text: 'ものづくり補助金', type: 'grant' },
                { icon: '💻', text: 'IT導入補助金', type: 'grant' },
                { icon: '🏪', text: '小規模事業者持続化補助金', type: 'grant' },
                { icon: '🔄', text: '事業再構築補助金', type: 'grant' }
            ];

            return fallbackSuggestions.filter(s => s.text && s.text.toLowerCase().includes(query.toLowerCase()));
        }

        displaySuggestions(suggestions) {
            const container = this.elements.suggestions;
            if (!container) return;

            if (!suggestions || suggestions.length === 0) {
                this.hideSuggestions();
                return;
            }

            container.innerHTML = suggestions.map(s => {
                const text = s?.text || '';
                const icon = s?.icon || '🔍';
                
                if (!text) return '';
                
                return `
                    <div class="suggestion-item" data-text="${text}">
                        <span class="suggestion-icon">${icon}</span>
                        <span>${text}</span>
                    </div>
                `;
            }).filter(html => html !== '').join('');

            container.classList.add('active');

            container.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', () => {
                    this.elements.searchInput.value = item.dataset.text;
                    this.hideSuggestions();
                    this.performSearch();
                });
            });
        }

        showSuggestions() {
            if (this.elements.searchInput.value.length >= 2) {
                this.elements.suggestions?.classList.add('active');
            }
        }

        hideSuggestions() {
            this.elements.suggestions?.classList.remove('active');
        }

        async performSearch() {
            const query = this.elements.searchInput.value.trim();
            if (!query || this.state.isSearching) return;

            this.state.isSearching = true;
            this.state.currentQuery = query;
            this.showLoading();

            try {
                const formData = new FormData();
                formData.append('action', 'gi_ai_search');
                formData.append('nonce', CONFIG.NONCE);
                formData.append('query', query);
                formData.append('filter', this.state.currentFilter);
                formData.append('session_id', CONFIG.SESSION_ID);

                console.log('Sending search request:', {
                    url: CONFIG.API_URL,
                    action: 'gi_ai_search',
                    nonce: CONFIG.NONCE,
                    query: query,
                    filter: this.state.currentFilter,
                    session_id: CONFIG.SESSION_ID
                });
                
                console.log('🧪 Debug: All FormData entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(`  ${key}: ${value}`);
                }

                const response = await fetch(CONFIG.API_URL, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const text = await response.text();
                console.log('🔍 Full Response text:', text);
                console.log('🔍 Response status:', response.status);
                let data;
                
                try {
                    data = JSON.parse(text);
                    console.log('🔍 Parsed response data:', data);
                } catch (e) {
                    console.error('❌ Invalid JSON response:', text);
                    console.error('❌ Parse error:', e);
                    this.showError('サーバーからの応答が不正です: ' + text.substring(0, 100));
                    return;
                }

                if (data.success) {
                    this.displayResults(data.data.grants);
                    this.updateResultsCount(data.data.count);
                    
                    if (data.data.ai_response) {
                        this.addChatMessage(data.data.ai_response, 'ai');
                    }
                } else {
                    const errorMsg = data.data?.message || data.data || '検索エラーが発生しました';
                    console.error('Search failed:', errorMsg);
                    this.showError(errorMsg);
                }
            } catch (error) {
                console.error('Search error:', error);
                this.showError('通信エラーが発生しました: ' + error.message);
            } finally {
                this.state.isSearching = false;
                this.hideLoading();
            }
        }

        displayResults(grants) {
            const container = this.elements.resultsContainer;
            if (!container || !grants) return;

            if (grants.length === 0) {
                this.showSmartNoResultsSuggestions(this.state.currentQuery);
                return;
            }

            const viewClass = this.state.currentView === 'list' ? 'results-list' : 'featured-grants';
            container.innerHTML = `<div class="${viewClass}">` + 
                grants.map(grant => this.createGrantCard(grant)).join('') +
                '</div>';
            this.animateCards();
            this.bindGrantCardEvents();
        }

        createGrantCard(grant) {
            return `
                <div class="grant-card" data-id="${grant.id}" style="animation-delay: ${Math.random() * 0.2}s">
                    ${grant.featured ? '<div class="card-badge">注目</div>' : ''}
                    <div class="card-header">
                        <h4 class="card-title">${grant.title}</h4>
                        <button class="card-bookmark" aria-label="ブックマーク">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M3 2h12v14l-6-3-6 3V2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <div class="card-meta">
                        <span class="meta-item">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="2"/>
                                <path d="M7 4v3h3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span class="meta-label">最大</span>
                            <span class="meta-value">${grant.amount || '未定'}</span>
                        </span>
                        <span class="meta-item">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <rect x="2" y="3" width="10" height="9" rx="1" stroke="currentColor" stroke-width="2"/>
                                <path d="M4 1v2M10 1v2M2 6h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span class="meta-label">締切</span>
                            <span class="meta-value">${grant.deadline || '随時'}</span>
                        </span>
                    </div>
                    <p class="card-org">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <rect x="1" y="2" width="10" height="8" rx="1" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M3 5h6M3 7h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        ${grant.organization || ''}
                    </p>
                    ${grant.success_rate ? `
                        <div class="card-rate">
                            <div class="rate-bar">
                                <div class="rate-fill" style="width: ${grant.success_rate}%"></div>
                            </div>
                            <span class="rate-text">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M1 8l2.5-2.5L5 7l4-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                採択率 ${grant.success_rate}%
                            </span>
                        </div>
                    ` : ''}
                    <div class="card-actions">
                        <button class="ai-assist-btn" 
                                data-grant-id="${grant.id}" 
                                data-post-id="${grant.id}"
                                data-grant-title="${grant.title}"
                                data-grant-permalink="${grant.permalink}"
                                aria-label="AIに質問">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <rect x="2" y="4" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="2"/>
                                <path d="M5 7h6M5 10h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="8" cy="2" r="1" fill="currentColor"/>
                            </svg>
                            <span>AI質問</span>
                        </button>
                        <a href="${grant.permalink}" class="card-link" aria-label="詳細を見る">
                            <span>詳細を見る</span>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M5 3l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            `;
        }

        updateResultsCount(count) {
            if (this.elements.resultsCount) {
                // NaN問題を防ぐための事前チェック
                const validCount = (count === null || count === undefined || isNaN(count)) ? 0 : count;
                this.animateNumber(this.elements.resultsCount, validCount);
            }
        }

        // Filter Methods
        handleFilterClick(e) {
            const filter = e.currentTarget.dataset.filter;
            
            this.elements.filterChips.forEach(chip => {
                chip.classList.toggle('active', chip.dataset.filter === filter);
            });

            this.state.currentFilter = filter;
            
            if (this.elements.searchInput.value) {
                this.performSearch();
            }
        }

        // Chat Methods
        async sendChatMessage() {
            const message = this.elements.chatInput.value.trim();
            if (!message || this.state.isTyping) return;

            this.elements.chatInput.value = '';
            this.autoResizeTextarea();

            this.addChatMessage(message, 'user');

            this.showTyping();

            try {
                const formData = new FormData();
                formData.append('action', 'gi_ai_chat');
                formData.append('nonce', CONFIG.NONCE);
                formData.append('message', message);
                formData.append('session_id', CONFIG.SESSION_ID);

                const response = await fetch(CONFIG.API_URL, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const text = await response.text();
                let data;
                
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    this.addChatMessage('サーバーエラー: 不正な応答形式です。', 'ai');
                    return;
                }

                if (data.success) {
                    this.typeMessage(data.data.response);
                    
                    if (typeof window.saveChatHistory === 'function') {
                        window.saveChatHistory(message, data.data.response);
                    }
                    
                    if (data.data.related_grants) {
                        this.displayResults(data.data.related_grants);
                    }
                } else {
                    const errorMsg = data.data?.message || data.data || '申し訳ございません。エラーが発生しました。';
                    console.error('Chat failed:', errorMsg);
                    this.addChatMessage(errorMsg, 'ai');
                }
            } catch (error) {
                console.error('Chat error:', error);
                this.addChatMessage('通信エラーが発生しました: ' + error.message, 'ai');
            } finally {
                this.hideTyping();
            }
        }

        addChatMessage(text, type) {
            // Null check to prevent appendChild error when element doesn't exist
            if (!this.elements.chatMessages) {
                console.warn('Chat messages container not found - skipping addChatMessage');
                return;
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message message-${type}`;
            
            const avatarSvg = type === 'ai' ? `
                <div class="message-avatar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="4" y="6" width="16" height="12" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 10h6M9 14h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="3" r="1" fill="currentColor"/>
                    </svg>
                </div>
            ` : '';
            
            messageDiv.innerHTML = `
                ${avatarSvg}
                <div class="message-bubble">${text}</div>
            `;
            
            this.elements.chatMessages.appendChild(messageDiv);
            this.scrollChatToBottom();
        }

        typeMessage(text) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message message-ai';
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="4" y="6" width="16" height="12" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 10h6M9 14h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="3" r="1" fill="currentColor"/>
                    </svg>
                </div>
                <div class="message-bubble"></div>
            `;
            
            const bubble = messageDiv.querySelector('.message-bubble');
            this.elements.chatMessages.appendChild(messageDiv);
            
            let index = 0;
            const typeChar = () => {
                if (index < text.length) {
                    bubble.textContent += text[index];
                    index++;
                    this.scrollChatToBottom();
                    setTimeout(typeChar, CONFIG.TYPING_DELAY);
                }
            };
            
            typeChar();
        }

        handleQuickQuestion(e) {
            const question = e.currentTarget.dataset.q;
            this.elements.chatInput.value = question;
            this.autoResizeTextarea();
            this.sendChatMessage();
        }

        autoResizeTextarea() {
            const textarea = this.elements.chatInput;
            if (!textarea) return;
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }

        scrollChatToBottom() {
            if (this.elements.chatMessages) {
                this.elements.chatMessages.scrollTop = this.elements.chatMessages.scrollHeight;
            }
        }

        showTyping() {
            this.state.isTyping = true;
            this.elements.typingIndicator?.classList.add('active');
        }

        hideTyping() {
            this.state.isTyping = false;
            this.elements.typingIndicator?.classList.remove('active');
        }

        // View Methods
        handleViewChange(e) {
            const view = e.currentTarget.dataset.view;
            
            this.elements.viewBtns.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === view);
            });

            this.state.currentView = view;
            
            const container = this.elements.resultsContainer;
            if (container) {
                const grantsContainer = container.querySelector('.featured-grants, .results-list');
                if (grantsContainer) {
                    grantsContainer.className = view === 'list' ? 'results-list' : 'featured-grants';
                }
            }
        }

        // Voice Input
        startVoiceInput() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            
            if (!SpeechRecognition) {
                this.showNotification('音声入力はこのブラウザではサポートされていません', 'error');
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.lang = 'ja-JP';
            recognition.interimResults = true;
            recognition.maxAlternatives = 1;
            recognition.continuous = false;

            this.elements.voiceBtn?.classList.add('recording');
            this.showNotification('音声入力中...話してください', 'info');

            recognition.onstart = () => {
                console.log('Voice recognition started');
            };

            recognition.onresult = async (event) => {
                const transcript = Array.from(event.results)
                    .map(result => result[0])
                    .map(result => result.transcript)
                    .join('');
                
                this.elements.searchInput.value = transcript;
                
                if (event.results[event.results.length - 1].isFinal) {
                    this.hideNotification();
                    this.performSearch();
                    
                    if (transcript) {
                        this.saveVoiceHistory(transcript, event.results[0][0].confidence);
                    }
                }
            };

            recognition.onerror = (event) => {
                console.error('Voice recognition error:', event.error);
                let errorMessage = '音声認識エラーが発生しました';
                
                switch(event.error) {
                    case 'no-speech':
                        errorMessage = '音声が検出されませんでした';
                        break;
                    case 'audio-capture':
                        errorMessage = 'マイクが使用できません';
                        break;
                    case 'not-allowed':
                        errorMessage = 'マイクのアクセスが拒否されました';
                        break;
                }
                
                this.showNotification(errorMessage, 'error');
            };

            recognition.onend = () => {
                this.elements.voiceBtn?.classList.remove('recording');
                this.hideNotification();
            };

            recognition.start();
        }

        async saveVoiceHistory(text, confidence) {
            try {
                const formData = new FormData();
                formData.append('action', 'gi_voice_history');
                formData.append('nonce', CONFIG.NONCE);
                formData.append('session_id', CONFIG.SESSION_ID);
                formData.append('text', text);
                formData.append('confidence', confidence);

                await fetch(CONFIG.API_URL, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
            } catch (error) {
                console.error('Voice history save error:', error);
            }
        }

        // Notification system
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `ai-notification ${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                bottom: ${isMobile ? '80px' : '20px'};
                left: 50%;
                transform: translateX(-50%);
                padding: 12px 24px;
                background: ${type === 'error' ? '#dc2626' : type === 'success' ? '#10b981' : '#2563eb'};
                color: white;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                z-index: 10001;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
                animation: slideUp 0.3s ease;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideDown 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        hideNotification() {
            const notification = document.querySelector('.ai-notification');
            if (notification) {
                notification.classList.remove('visible');
                setTimeout(() => notification.remove(), 300);
            }
        }

        // Loading States
        showLoading() {
            this.elements.resultsLoading?.classList.add('active');
            this.elements.resultsContainer?.classList.add('loading');
        }

        hideLoading() {
            this.elements.resultsLoading?.classList.remove('active');
            this.elements.resultsContainer?.classList.remove('loading');
        }

        showError(message) {
            const container = this.elements.resultsContainer;
            if (container) {
                container.innerHTML = `
                    <div class="error-message" style="
                        padding: 40px;
                        text-align: center;
                        color: #dc2626;
                        background: #fee;
                        border-radius: 12px;
                        font-weight: 600;
                    ">
                        ${message}
                    </div>
                `;
            }
        }

        // Smart No Results Suggestions
        async showSmartNoResultsSuggestions(query) {
            const container = this.elements.resultsContainer;
            if (!container) return;

            container.innerHTML = '<div class="no-results-loading">より良い結果を探しています...</div>';

            try {
                const formData = new FormData();
                formData.append('action', 'gi_no_results_suggestions');
                formData.append('query', query);

                const response = await fetch(CONFIG.API_URL, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    this.renderNoResultsSuggestions(query, data.data);
                } else {
                    container.innerHTML = this.getBasicNoResults(query);
                }
            } catch (error) {
                console.error('Suggestions error:', error);
                container.innerHTML = this.getBasicNoResults(query);
            }
        }

        renderNoResultsSuggestions(query, suggestions) {
            const container = this.elements.resultsContainer;
            container.innerHTML = `
                <div class="smart-no-results" style="padding: 40px 20px; text-align: center;">
                    <div class="no-results-header">
                        <div class="icon-circle" style="
                            width: 80px;
                            height: 80px;
                            margin: 0 auto 20px;
                            background: #f5f5f5;
                            border: 3px solid #000;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 2rem;
                            font-weight: 600;
                        ">×</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 10px;">「${this.escapeHtml(query)}」の検索結果が見つかりませんでした</h3>
                        <p style="color: #666;">以下の方法をお試しください</p>
                    </div>
                </div>
            `;
        }

        getBasicNoResults(query) {
            return `
                <div class="basic-no-results" style="padding: 60px 20px; text-align: center;">
                    <div class="icon" style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 10px;">該当する補助金が見つかりませんでした</h3>
                    <p style="color: #666; margin-bottom: 30px;">「${this.escapeHtml(query)}」の検索結果が見つかりませんでした</p>
                    <div class="basic-tips" style="text-align: left; max-width: 400px; margin: 0 auto 30px; color: #666;">
                        <p>• キーワードを変更してみてください</p>
                        <p>• 業種や地域を追加してみてください</p>
                        <p>• カテゴリから探してみてください</p>
                    </div>
                    <button class="retry-button" onclick="document.querySelector('#ai-search-input')?.focus()" style="
                        padding: 12px 32px;
                        background: #000;
                        color: #fff;
                        border: 2px solid #000;
                        border-radius: 50px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s;
                    ">
                        再検索する
                    </button>
                </div>
            `;
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Animation Methods
        initAnimations() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.grant-card').forEach(card => {
                observer.observe(card);
            });
        }

        animateCards() {
            const cards = document.querySelectorAll('.grant-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 50);
            });
        }

        animateNumber(element, target) {
            // NaN問題を防ぐための簡単な検証
            if (target === null || target === undefined || isNaN(target) || !isFinite(target)) {
                element.textContent = '0';
                return;
            }
            
            const validTarget = Math.max(0, Math.floor(Number(target)));
            const duration = 1500;
            const step = validTarget / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= validTarget) {
                    current = validTarget;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, 16);
        }

        bindGrantCardEvents() {
            document.querySelectorAll('.ai-assist-btn').forEach(btn => {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                newBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const grantId = newBtn.dataset.postId || newBtn.dataset.grantId;
                    const grantTitle = newBtn.dataset.grantTitle;
                    const grantPermalink = newBtn.dataset.grantPermalink;
                    
                    console.log('AI Assistant clicked:', { grantId, grantTitle, grantPermalink });
                    
                    if (grantId && grantTitle && grantPermalink) {
                        this.showGrantAssistant(grantId, grantTitle, grantPermalink);
                    } else {
                        console.error('Missing grant data:', { grantId, grantTitle, grantPermalink });
                    }
                });
            });
            
            document.querySelectorAll('.card-link').forEach(link => {
                link.style.pointerEvents = 'auto';
                link.style.cursor = 'pointer';
                
                console.log('Detail link found:', link.href);
            });
        }

        // Grant-specific AI Assistant Interface
        async showGrantAssistant(grantId, grantTitle, grantPermalink) {
            const modal = this.createAssistantModal(grantId, grantTitle, grantPermalink);
            document.body.appendChild(modal);
            
            this.showInitialGrantSuggestions(grantId, grantPermalink);
            
            setTimeout(() => {
                modal.classList.add('active');
            }, 10);
        }

        createAssistantModal(grantId, grantTitle, grantPermalink) {
            const modal = document.createElement('div');
            modal.className = 'grant-assistant-modal';
            
            const isMobileView = window.innerWidth < 768;
            
            modal.style.cssText = `
                position: fixed;
                inset: 0;
                z-index: 10000;
                display: flex;
                align-items: ${isMobileView ? 'stretch' : 'center'};
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            `;
            
            modal.innerHTML = `
                <div class="modal-overlay" style="position: absolute; inset: 0; background: rgba(0, 0, 0, ${isMobileView ? '0.95' : '0.5'}); backdrop-filter: blur(4px);"></div>
                <div class="modal-content" style="
                    position: relative;
                    width: ${isMobileView ? '100vw' : '90vw'};
                    max-width: ${isMobileView ? '100vw' : '600px'};
                    height: ${isMobileView ? '100vh' : 'auto'};
                    max-height: ${isMobileView ? '100vh' : '85vh'};
                    background: #fff;
                    border-radius: ${isMobileView ? '0' : '20px'};
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    display: flex;
                    flex-direction: column;
                    transform: ${isMobileView ? 'translateY(100%)' : 'scale(0.9)'};
                    transition: transform 0.3s ease;
                ">
                    <div class="modal-header" style="
                        padding: ${isMobileView ? '16px' : '20px'};
                        border-bottom: 2px solid #000;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        background: #fafafa;
                        flex-shrink: 0;
                    ">
                        <div class="assistant-info" style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                            <div class="assistant-avatar" style="
                                position: relative;
                                width: ${isMobileView ? '40px' : '48px'};
                                height: ${isMobileView ? '40px' : '48px'};
                                background: #000;
                                color: #fff;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-weight: 700;
                                font-size: ${isMobileView ? '12px' : '14px'};
                                flex-shrink: 0;
                            ">AI</div>
                            <div class="assistant-details" style="flex: 1; min-width: 0;">
                                <h3 style="font-size: ${isMobileView ? '13px' : '14px'}; font-weight: 600; margin: 0 0 4px 0;">補助金AIアシスタント</h3>
                                <p class="grant-title" style="
                                    font-size: ${isMobileView ? '11px' : '12px'};
                                    color: #666;
                                    margin: 0;
                                    white-space: nowrap;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                ">${grantTitle}</p>
                            </div>
                        </div>
                        <button class="modal-close" style="
                            width: ${isMobileView ? '36px' : '40px'};
                            height: ${isMobileView ? '36px' : '40px'};
                            border: 2px solid #000;
                            background: #fff;
                            border-radius: 50%;
                            font-size: ${isMobileView ? '20px' : '24px'};
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            transition: all 0.2s;
                            flex-shrink: 0;
                            font-weight: 700;
                            line-height: 1;
                        ">×</button>
                    </div>
                    
                    <div class="modal-body" style="
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        min-height: 0;
                        overflow: hidden;
                    ">
                        <div class="assistant-chat" id="assistant-chat-${grantId}" style="
                            flex: 1;
                            padding: ${isMobileView ? '16px' : '20px'};
                            overflow-y: auto;
                            font-size: ${isMobileView ? '13px' : '14px'};
                            line-height: 1.6;
                        ">
                            <div class="initial-message">
                                <div class="message-bubble" style="
                                    display: inline-block;
                                    max-width: 85%;
                                    padding: ${isMobileView ? '12px 14px' : '14px 16px'};
                                    border-radius: 16px;
                                    font-size: ${isMobileView ? '13px' : '14px'};
                                    line-height: 1.6;
                                    background: #f8f9fa;
                                    color: #333;
                                    word-wrap: break-word;
                                ">
                                    <p style="margin: 0 0 12px 0;">こんにちは！「<strong>${grantTitle}</strong>」について、どのようなことをお聞きしたいですか？</p>
                                    <div class="grant-intro-actions" style="
                                        margin-top: 12px;
                                        padding-top: 12px;
                                        border-top: 1px solid rgba(0, 0, 0, 0.1);
                                    ">
                                        <a href="${grantPermalink}" class="detail-link" target="_blank" style="
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 6px;
                                            padding: ${isMobileView ? '10px 16px' : '8px 16px'};
                                            background: #000;
                                            color: #fff;
                                            text-decoration: none;
                                            font-size: ${isMobileView ? '13px' : '12px'};
                                            font-weight: 600;
                                            border-radius: 20px;
                                            transition: all 0.3s ease;
                                            border: 2px solid #000;
                                        ">詳細ページはこちら →</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="suggestion-buttons" id="suggestions-${grantId}" style="
                            padding: 0 ${isMobileView ? '16px' : '20px'} ${isMobileView ? '12px' : '16px'};
                            display: flex;
                            flex-wrap: wrap;
                            gap: ${isMobileView ? '6px' : '8px'};
                            flex-shrink: 0;
                        ">
                        </div>
                        
                        <div class="chat-input-area" style="
                            padding: ${isMobileView ? '12px 16px' : '16px 20px'};
                            border-top: 2px solid #e0e0e0;
                            display: flex;
                            gap: ${isMobileView ? '8px' : '12px'};
                            align-items: flex-end;
                            background: #fafafa;
                            flex-shrink: 0;
                        ">
                            <textarea 
                                id="grant-chat-input-${grantId}" 
                                class="grant-chat-input"
                                placeholder="質問を入力してください..."
                                rows="1"
                                style="
                                    flex: 1;
                                    padding: ${isMobileView ? '10px 14px' : '12px 16px'};
                                    border: 2px solid #000;
                                    border-radius: ${isMobileView ? '12px' : '20px'};
                                    font-size: ${isMobileView ? '14px' : '13px'};
                                    resize: none;
                                    outline: none;
                                    transition: border-color 0.2s;
                                    max-height: ${isMobileView ? '80px' : '100px'};
                                    font-family: inherit;
                                    line-height: 1.5;
                                "></textarea>
                            <button class="send-btn" data-grant-id="${grantId}" style="
                                width: ${isMobileView ? '44px' : '40px'};
                                height: ${isMobileView ? '44px' : '40px'};
                                background: #000;
                                color: #fff;
                                border: none;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                cursor: pointer;
                                transition: all 0.2s;
                                flex-shrink: 0;
                            ">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M18 2L9 11M18 2l-6 16-3-7-7-3 16-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            this.bindModalEvents(modal, grantId, grantPermalink);
            
            return modal;
        }

        bindModalEvents(modal, grantId, grantPermalink) {
            const closeBtn = modal.querySelector('.modal-close');
            const overlay = modal.querySelector('.modal-overlay');
            const sendBtn = modal.querySelector('.send-btn');
            const chatInput = modal.querySelector(`#grant-chat-input-${grantId}`);
            
            const closeModal = () => {
                modal.classList.remove('active');
                modal.style.opacity = '0';
                modal.style.visibility = 'hidden';
                const content = modal.querySelector('.modal-content');
                if (content) {
                    const isMobileView = window.innerWidth < 768;
                    content.style.transform = isMobileView ? 'translateY(100%)' : 'scale(0.9)';
                }
                setTimeout(() => {
                    modal.remove();
                }, 300);
            };
            
            [closeBtn, overlay].forEach(el => {
                el.addEventListener('click', closeModal);
            });
            
            // Auto-resize textarea
            chatInput.addEventListener('input', () => {
                chatInput.style.height = 'auto';
                const maxHeight = window.innerWidth < 768 ? 80 : 100;
                chatInput.style.height = Math.min(chatInput.scrollHeight, maxHeight) + 'px';
            });
            
            sendBtn.addEventListener('click', () => {
                this.sendGrantQuestion(grantId, chatInput.value.trim(), 'custom', grantPermalink);
                chatInput.value = '';
                chatInput.style.height = 'auto';
            });
            
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendGrantQuestion(grantId, chatInput.value.trim(), 'custom', grantPermalink);
                    chatInput.value = '';
                    chatInput.style.height = 'auto';
                }
            });
            
            // Swipe to close on mobile
            if (window.innerWidth < 768) {
                let startY = 0;
                let currentY = 0;
                const content = modal.querySelector('.modal-content');
                
                content.addEventListener('touchstart', (e) => {
                    startY = e.touches[0].clientY;
                }, { passive: true });
                
                content.addEventListener('touchmove', (e) => {
                    currentY = e.touches[0].clientY;
                    const diff = currentY - startY;
                    if (diff > 0) {
                        content.style.transform = `translateY(${diff}px)`;
                    }
                }, { passive: true });
                
                content.addEventListener('touchend', () => {
                    const diff = currentY - startY;
                    if (diff > 100) {
                        closeModal();
                    } else {
                        content.style.transform = 'translateY(0)';
                    }
                }, { passive: true });
            }
            
            // Show modal animation
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.style.visibility = 'visible';
                const content = modal.querySelector('.modal-content');
                if (content) {
                    const isMobileView = window.innerWidth < 768;
                    content.style.transform = isMobileView ? 'translateY(0)' : 'scale(1)';
                }
            }, 10);
        }

        async showInitialGrantSuggestions(grantId, grantPermalink) {
            const suggestionsContainer = document.getElementById(`suggestions-${grantId}`);
            if (!suggestionsContainer) return;
            
            const isMobileView = window.innerWidth < 768;
            
            const initialSuggestions = [
                { text: 'この補助金の概要を教えて', type: 'overview' },
                { text: '申請要件について', type: 'requirements' },
                { text: '申請手順を知りたい', type: 'process' },
                { text: '採択のコツは？', type: 'tips' }
            ];
            
            suggestionsContainer.innerHTML = initialSuggestions.map(suggestion => `
                <button class="suggestion-btn" data-grant-id="${grantId}" data-type="${suggestion.type}" style="
                    padding: ${isMobileView ? '8px 14px' : '8px 16px'};
                    background: #fff;
                    border: 2px solid #e0e0e0;
                    border-radius: 16px;
                    font-size: ${isMobileView ? '12px' : '12px'};
                    color: #666;
                    cursor: pointer;
                    transition: all 0.2s;
                    white-space: nowrap;
                    font-weight: 500;
                ">
                    ${suggestion.text}
                </button>
            `).join('');
            
            suggestionsContainer.querySelectorAll('.suggestion-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const questionType = btn.dataset.type;
                    const questionText = btn.textContent;
                    this.sendGrantQuestion(grantId, questionText, questionType, grantPermalink);
                });
                
                btn.addEventListener('mouseenter', (e) => {
                    e.target.style.background = '#000';
                    e.target.style.color = '#fff';
                    e.target.style.borderColor = '#000';
                });
                
                btn.addEventListener('mouseleave', (e) => {
                    e.target.style.background = '#fff';
                    e.target.style.color = '#666';
                    e.target.style.borderColor = '#e0e0e0';
                });
            });
        }

        async sendGrantQuestion(grantId, question, questionType, grantPermalink) {
            if (!question.trim()) return;
            
            const chatContainer = document.getElementById(`assistant-chat-${grantId}`);
            const suggestionsContainer = document.getElementById(`suggestions-${grantId}`);
            
            this.addAssistantMessage(chatContainer, question, 'user');
            
            const typingIndicator = this.addTypingIndicator(chatContainer);
            
            try {
                const formData = new FormData();
                formData.append('action', 'handle_grant_ai_question');
                formData.append('nonce', CONFIG.NONCE);
                formData.append('post_id', grantId);
                formData.append('question', question);
                formData.append('session_id', CONFIG.SESSION_ID);

                const response = await fetch(CONFIG.API_URL, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const data = await response.json();
                
                if (typingIndicator) {
                    typingIndicator.remove();
                }

                if (data.success) {
                    this.addAssistantMessage(chatContainer, data.data.response, 'ai', grantPermalink);
                    
                    if (data.data.suggestions && suggestionsContainer) {
                        const isMobileView = window.innerWidth < 768;
                        suggestionsContainer.innerHTML = data.data.suggestions.map(suggestion => `
                            <button class="suggestion-btn small" data-grant-id="${grantId}" data-type="custom" style="
                                padding: ${isMobileView ? '6px 12px' : '6px 12px'};
                                background: #fff;
                                border: 2px solid #e0e0e0;
                                border-radius: 16px;
                                font-size: ${isMobileView ? '11px' : '11px'};
                                color: #666;
                                cursor: pointer;
                                transition: all 0.2s;
                                white-space: nowrap;
                                font-weight: 500;
                            ">
                                ${suggestion}
                            </button>
                        `).join('');
                        
                        suggestionsContainer.querySelectorAll('.suggestion-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const questionText = btn.textContent;
                                this.sendGrantQuestion(grantId, questionText, 'custom', grantPermalink);
                            });
                            
                            btn.addEventListener('mouseenter', (e) => {
                                e.target.style.background = '#000';
                                e.target.style.color = '#fff';
                                e.target.style.borderColor = '#000';
                            });
                            
                            btn.addEventListener('mouseleave', (e) => {
                                e.target.style.background = '#fff';
                                e.target.style.color = '#666';
                                e.target.style.borderColor = '#e0e0e0';
                            });
                        });
                    }
                } else {
                    this.addAssistantMessage(chatContainer, '申し訳ございません。エラーが発生しました。', 'ai');
                }
                
            } catch (error) {
                console.error('Grant assistant error:', error);
                if (typingIndicator) {
                    typingIndicator.remove();
                }
                this.addAssistantMessage(chatContainer, '通信エラーが発生しました。', 'ai');
            }
        }

        addAssistantMessage(container, text, type, grantPermalink = null) {
            const isMobileView = window.innerWidth < 768;
            const messageDiv = document.createElement('div');
            messageDiv.className = `assistant-message ${type}`;
            messageDiv.style.cssText = `
                margin-bottom: ${isMobileView ? '12px' : '16px'};
                ${type === 'user' ? 'text-align: right;' : ''}
            `;
            
            let messageContent = text.replace(/\n/g, '<br>');
            if (type === 'ai' && grantPermalink) {
                messageContent += `
                    <div class="message-action-links" style="
                        margin-top: 12px;
                        padding-top: 12px;
                        border-top: 1px solid rgba(0, 0, 0, 0.1);
                    ">
                        <a href="${grantPermalink}" class="message-detail-link" target="_blank" style="
                            display: inline-flex;
                            align-items: center;
                            gap: 6px;
                            padding: ${isMobileView ? '10px 16px' : '8px 16px'};
                            background: #000;
                            color: #fff;
                            text-decoration: none;
                            font-size: ${isMobileView ? '13px' : '12px'};
                            font-weight: 600;
                            border-radius: 20px;
                            transition: all 0.3s ease;
                            border: 2px solid #000;
                        ">詳細ページで確認する →</a>
                    </div>
                `;
            }
            
            messageDiv.innerHTML = `
                <div class="message-bubble ${type}" style="
                    display: inline-block;
                    max-width: 85%;
                    padding: ${isMobileView ? '12px 14px' : '12px 16px'};
                    border-radius: 16px;
                    font-size: ${isMobileView ? '13px' : '13px'};
                    line-height: 1.6;
                    word-wrap: break-word;
                    ${type === 'user' ? 'background: #000; color: #fff;' : 'background: #f8f9fa; color: #333;'}
                ">
                    ${messageContent}
                </div>
            `;
            
            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
            
            return messageDiv;
        }

        addTypingIndicator(container) {
            const isMobileView = window.innerWidth < 768;
            const indicator = document.createElement('div');
            indicator.className = 'assistant-message ai typing';
            indicator.style.cssText = `margin-bottom: ${isMobileView ? '12px' : '16px'};`;
            indicator.innerHTML = `
                <div class="message-bubble ai" style="
                    display: inline-block;
                    max-width: 85%;
                    padding: ${isMobileView ? '12px 14px' : '12px 16px'};
                    border-radius: 16px;
                    background: #f8f9fa;
                ">
                    <div class="typing-dots" style="display: flex; gap: 4px;">
                        <span style="
                            width: 6px;
                            height: 6px;
                            background: #999;
                            border-radius: 50%;
                            animation: typingBounce 1.4s infinite;
                        "></span>
                        <span style="
                            width: 6px;
                            height: 6px;
                            background: #999;
                            border-radius: 50%;
                            animation: typingBounce 1.4s infinite;
                            animation-delay: 0.2s;
                        "></span>
                        <span style="
                            width: 6px;
                            height: 6px;
                            background: #999;
                            border-radius: 50%;
                            animation: typingBounce 1.4s infinite;
                            animation-delay: 0.4s;
                        "></span>
                    </div>
                </div>
            `;
            
            if (!document.getElementById('typing-animation-style')) {
                const style = document.createElement('style');
                style.id = 'typing-animation-style';
                style.textContent = `
                    @keyframes typingBounce {
                        0%, 60%, 100% {
                            transform: translateY(0);
                        }
                        30% {
                            transform: translateY(-8px);
                        }
                    }
                `;
                document.head.appendChild(style);
            }
            
            container.appendChild(indicator);
            container.scrollTop = container.scrollHeight;
            
            return indicator;
        }

        // Utility Methods
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }
    // Ensure global AI chat function is available
    if (typeof window.openGrantAIChat === 'undefined') {
        window.openGrantAIChat = function(button) {
            const postId = button.getAttribute('data-post-id') || button.dataset.postId;
            const grantTitle = button.getAttribute('data-grant-title') || button.dataset.grantTitle;
            const grantPermalink = button.getAttribute('data-grant-permalink') || button.dataset.grantPermalink;
            
            if (!postId || !grantPermalink) {
                console.error('Post ID or Permalink not found');
                return;
            }
            
            const searchSection = document.getElementById('ai-search-section');
            if (searchSection && searchSection._aiController) {
                searchSection._aiController.showGrantAssistant(postId, grantTitle, grantPermalink);
            } else {
                console.error('AI Controller not found');
            }
        };
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        const controller = new AISearchController();
        
        const searchSection = document.getElementById('ai-search-section');
        if (searchSection) {
            searchSection._aiController = controller;
        }
        
        controller.bindGrantCardEvents();
        
        // Emergency fix - ensure buttons work
        setTimeout(() => {
            document.querySelectorAll('.ai-assist-btn').forEach(btn => {
                btn.style.pointerEvents = 'auto';
                btn.style.cursor = 'pointer';
                
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                newBtn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const grantId = this.dataset.postId || this.dataset.grantId;
                    const grantTitle = this.dataset.grantTitle;
                    const grantPermalink = this.dataset.grantPermalink;
                    
                    console.log('Emergency AI click handler:', { grantId, grantTitle, grantPermalink });
                    
                    if (grantId && grantTitle && grantPermalink) {
                        if (controller) {
                            controller.showGrantAssistant(grantId, grantTitle, grantPermalink);
                        } else {
                            alert(`AI質問機能：${grantTitle}`);
                        }
                    } else {
                        alert('AI質問機能 - データが不正です');
                    }
                };
            });
            
            document.querySelectorAll('.card-link').forEach(link => {
                link.style.pointerEvents = 'auto';
                link.style.cursor = 'pointer';
                
                console.log('Detail link enabled:', link.href);
            });
            
            console.log('Emergency fix applied - buttons should work now');
        }, 500);
    });

    // ============================================
    // AI Chat History Management
    // ============================================
    
    window.toggleChatHistory = function() {
        const panel = document.getElementById('ai-history-panel');
        if (!panel) return;
        
        if (panel.style.display === 'none' || !panel.style.display) {
            loadChatHistory();
            panel.style.display = 'block';
        } else {
            panel.style.display = 'none';
        }
    };
    
    window.saveChatHistory = function(question, answer) {
        try {
            let history = JSON.parse(localStorage.getItem('gi_chat_history') || '[]');
            
            history.unshift({
                id: Date.now(),
                question: question,
                answer: answer,
                timestamp: new Date().toISOString(),
                date: new Date().toLocaleDateString('ja-JP', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                })
            });
            
            history = history.slice(0, 20);
            localStorage.setItem('gi_chat_history', JSON.stringify(history));
            
            updateHistoryCount();
            
            console.log('Chat history saved:', history.length);
        } catch (error) {
            console.error('Error saving chat history:', error);
        }
    };
    
    window.loadChatHistory = function() {
        try {
            const history = JSON.parse(localStorage.getItem('gi_chat_history') || '[]');
            const listContainer = document.getElementById('ai-history-list');
            
            if (!listContainer) return;
            
            if (history.length === 0) {
                listContainer.innerHTML = '<p class="ai-history-empty">履歴がありません</p>';
                return;
            }
            
            listContainer.innerHTML = history.map((item, index) => `
                <div class="ai-history-item" onclick="restoreConversation(${item.id})" data-index="${index}">
                    <div class="history-date">${item.date}</div>
                    <div class="history-question">${escapeHtml(item.question.substring(0, 80))}${item.question.length > 80 ? '...' : ''}</div>
                </div>
            `).join('');
            
            console.log('Chat history loaded:', history.length);
        } catch (error) {
            console.error('Error loading chat history:', error);
        }
    };
    
    window.clearChatHistory = function() {
        if (confirm('会話履歴を削除しますか？この操作は取り消せません。')) {
            try {
                localStorage.removeItem('gi_chat_history');
                updateHistoryCount();
                
                const listContainer = document.getElementById('ai-history-list');
                if (listContainer) {
                    listContainer.innerHTML = '<p class="ai-history-empty">履歴がありません</p>';
                }
                
                console.log('Chat history cleared');
            } catch (error) {
                console.error('Error clearing chat history:', error);
            }
        }
    };
    
    window.restoreConversation = function(id) {
        try {
            const history = JSON.parse(localStorage.getItem('gi_chat_history') || '[]');
            const conversation = history.find(item => item.id == id);
            
            if (!conversation) {
                console.error('Conversation not found:', id);
                return;
            }
            
            const chatMessages = document.getElementById('chat-messages');
            if (!chatMessages) return;
            
            chatMessages.innerHTML = `
                <div class="message message-user" style="animation: messageIn 0.3s ease-out;">
                    <div class="message-bubble">${escapeHtml(conversation.question)}</div>
                </div>
                <div class="message message-ai" style="animation: messageIn 0.3s ease-out;">
                    <div class="message-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="6" width="16" height="12" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 10h6M9 14h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="12" cy="3" r="1" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="message-bubble">${escapeHtml(conversation.answer)}</div>
                </div>
            `;
            
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            const panel = document.getElementById('ai-history-panel');
            if (panel) {
                panel.style.display = 'none';
            }
            
            console.log('Conversation restored:', id);
        } catch (error) {
            console.error('Error restoring conversation:', error);
        }
    };
    
    function updateHistoryCount() {
        try {
            const history = JSON.parse(localStorage.getItem('gi_chat_history') || '[]');
            const countBadge = document.querySelector('.history-count');
            
            if (countBadge) {
                countBadge.textContent = history.length;
                countBadge.style.display = history.length > 0 ? 'inline-block' : 'none';
            }
        } catch (error) {
            console.error('Error updating history count:', error);
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initialize history count on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateHistoryCount();
        console.log('Chat history initialized');
    });

})();
</script>

<?php
/**
 * =====================================================
 * Browse Section Integration (用途から探す統合)
 * =====================================================
 * 検索セクションの下に「用途から探す」を統合表示
 */

// やりたいこと別カテゴリー（ユーザー視点の自然な言葉で表現）
// データ分析により最も需要の高い8つの用途を厳選
$purpose_categories = array(
    // メイン8カード（カテゴリーセクションと同じ表示数）
    array(
        'title' => '設備を導入したい',
        'icon' => 'fas fa-industry',
        'slug' => 'equipment',
        'url' => home_url('/purpose/equipment/'),
        'description' => '製造設備・機械・IT機器などの導入支援',
        'keywords' => 'ものづくり補助金, IT導入補助金, 設備投資',
        'categories' => array('設備投資', 'ものづくり・新商品開発', 'IT導入・DX', '生産性向上・業務効率化', '防犯・防災・BCP', '省エネ・再エネ', '医療・福祉', '観光・インバウンド', '農業・林業・漁業')
    ),
    array(
        'title' => '経営を改善したい',
        'icon' => 'fas fa-chart-line',
        'slug' => 'management',
        'url' => home_url('/purpose/management/'),
        'description' => '経営強化・生産性向上・業務効率化の支援',
        'keywords' => '経営改善, 生産性向上, 業務効率化, 経営強化',
        'categories' => array('経営改善・経営強化', '生産性向上・業務効率化', '事業承継', '事業再建', '経営コンサル', '専門家派遣', 'DX推進', 'IT・デジタル化')
    ),
    array(
        'title' => '人材を育成したい',
        'icon' => 'fas fa-user-graduate',
        'slug' => 'training',
        'url' => home_url('/purpose/training/'),
        'description' => '従業員研修・資格取得・スキルアップ・雇用支援',
        'keywords' => '人材開発, 教育訓練, キャリアアップ, 雇用促進',
        'categories' => array('人材育成・雇用', '人材確保・育成', '雇用維持・促進', '働き方改革', '職場環境改善', '福利厚生', '資格取得', '研修・教育訓練')
    ),
    array(
        'title' => '販路を拡大したい',
        'icon' => 'fas fa-rocket',
        'slug' => 'sales',
        'url' => home_url('/purpose/sales/'),
        'description' => '販路開拓・マーケティング・広告宣伝・海外展開',
        'keywords' => '販路拡大, 展示会出展, EC構築, 海外進出',
        'categories' => array('販路開拓・販路拡大', '海外展開', '販売促進', 'マーケティング', '広告・宣伝', 'EC・IT活用', 'インバウンド', '輸出支援')
    ),
    array(
        'title' => '事業を始めたい',
        'icon' => 'fas fa-lightbulb',
        'slug' => 'startup',
        'url' => home_url('/purpose/startup/'),
        'description' => '創業・起業・新規事業・店舗開業の支援',
        'keywords' => '創業支援, スタートアップ, 新規開業, 起業',
        'categories' => array('起業・創業・ベンチャー', '創業支援', '新規事業・第二創業', '店舗改装', '家賃補助', '開業支援', '事業承継', '移住・起業')
    ),
    array(
        'title' => 'IT化・DXを進めたい',
        'icon' => 'fas fa-laptop-code',
        'slug' => 'digital',
        'url' => home_url('/purpose/digital/'),
        'description' => 'デジタル化・DX推進・システム導入・IT活用',
        'keywords' => 'DX, IT導入, デジタル化, デジタルトランスフォーメーション',
        'categories' => array('IT導入・DX', 'DX推進', 'IT・DX化', 'デジタル', 'デジタル化', 'IT化・デジタル化', '業務効率化・IT', 'システム導入')
    ),
    array(
        'title' => '環境対策したい',
        'icon' => 'fas fa-leaf',
        'slug' => 'environment',
        'url' => home_url('/purpose/environment/'),
        'description' => '省エネ・脱炭素・再エネ・環境配慮型事業',
        'keywords' => 'カーボンニュートラル, 省エネ, 再生可能エネルギー, GX',
        'categories' => array('省エネ・再エネ', '省エネ・脱炭素', '環境・エネルギー', '再エネ・畜エネ', 'カーボンニュートラル', 'GX', '環境保全', '省エネルギー')
    ),
    array(
        'title' => '地域を活性化したい',
        'icon' => 'fas fa-city',
        'slug' => 'regional',
        'url' => home_url('/purpose/regional/'),
        'description' => '地域資源活用・観光振興・まちづくり・地域貢献',
        'keywords' => '地域振興, 観光, まちづくり, 地方創生',
        'categories' => array('地域活性・まちづくり', '地域活性化', '観光・インバウンド', '観光振興', 'まちづくり', '地域振興', '地方創生', '移住・定住支援')
    ),
);

// その他の用途（「もっと見る」で表示）
$other_purposes = array(
    array(
        'title' => '事業を引き継ぎたい',
        'icon' => 'fas fa-handshake',
        'slug' => 'succession',
        'url' => home_url('/purpose/succession/'),
        'description' => '事業承継・M&A・後継者育成の支援',
        'categories' => array('事業承継', '事業承継・M&A', '後継者支援', '事業引継ぎ')
    ),
    array(
        'title' => '研究開発したい',
        'icon' => 'fas fa-flask',
        'slug' => 'rnd',
        'url' => home_url('/purpose/rnd/'),
        'description' => '新技術開発・製品開発・研究活動・イノベーション',
        'categories' => array('研究開発', '研究・実証実験・産学連携', 'イノベーション', '技術開発', '新製品開発', '特許・知的財産')
    ),
    array(
        'title' => '住宅関連の支援',
        'icon' => 'fas fa-home',
        'slug' => 'housing',
        'url' => home_url('/purpose/housing/'),
        'description' => '住宅改修・リフォーム・省エネ住宅・バリアフリー',
        'categories' => array('住宅・リフォーム', '住宅改修', '住宅支援', '空き家利用', '空き家対策', 'バリアフリー', '防災・減災')
    ),
    array(
        'title' => '農林水産業を支援',
        'icon' => 'fas fa-tractor',
        'slug' => 'agriculture',
        'url' => home_url('/purpose/agriculture/'),
        'description' => '農業・林業・水産業・六次産業化の支援',
        'categories' => array('農業・林業・漁業', '農林水産業', '農業支援', '六次産業化', '新規就農', '農業法人')
    ),
    array(
        'title' => '個人で使いたい',
        'icon' => 'fas fa-user',
        'slug' => 'individual',
        'url' => home_url('/purpose/individual/'),
        'description' => '個人事業主・フリーランス・資格取得・生活支援',
        'categories' => array('個人事業主支援', 'フリーランス', '資格取得', '就職・転職支援', '生活支援', '子育て支援', '移住・定住')
    ),
);
?>

<!-- フォント・アイコン読み込み -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="browse-section-integrated" itemscope itemtype="https://schema.org/WebPageElement" aria-label="補助金検索ナビゲーション">
    <meta itemprop="name" content="補助金検索">
    <meta itemprop="description" content="やりたいこと・診断・一覧から補助金を検索">
    
    <div class="browse-container">
        
        <!-- セクションヘッダー -->
        <header class="browse-header">
            <div class="browse-badge">
                <div class="badge-pulse"></div>
                <span>BROWSE GRANTS</span>
            </div>
            
            <h2 class="browse-title" itemprop="headline">
                <span class="title-main">用途から探す</span>
                <span class="title-sub">あなたの目的に合った補助金・助成金を見つけよう</span>
            </h2>
        </header>

        <!-- 写真のようなスタイリッシュな2カラムレイアウト -->
        <div class="browse-photo-style-layout">
            
            <!-- 左カラム：主要用途の大きなビジュアルカード（3つ） -->
            <div class="browse-hero-column">
                <?php 
                // 最も需要の高い3つの用途を大きく表示
                $hero_purposes = array_slice($purpose_categories, 0, 3);
                foreach ($hero_purposes as $index => $purpose) : 
                ?>
                <a href="<?php echo esc_url($purpose['url']); ?>" class="hero-purpose-card hero-purpose-<?php echo $index + 1; ?>">
                    <!-- 写真風の背景グラデーション -->
                    <div class="hero-card-background"></div>
                    
                    <!-- カード内容 -->
                    <div class="hero-card-content">
                        <div class="hero-icon-wrapper">
                            <i class="<?php echo esc_attr($purpose['icon']); ?>"></i>
                        </div>
                        <h3 class="hero-purpose-title"><?php echo esc_html($purpose['title']); ?></h3>
                        <p class="hero-purpose-description"><?php echo esc_html($purpose['description']); ?></p>
                        <div class="hero-card-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                    
                    <!-- ホバー時のオーバーレイ -->
                    <div class="hero-card-overlay"></div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- 右カラム：用途から探す（小グリッド） -->
            <div class="browse-grid-column">
                <div class="grid-column-header">
                    <h3 class="grid-column-title">用途から探す</h3>
                    <p class="grid-column-subtitle">その他のニーズに合った補助金を見つける</p>
                </div>

                <!-- コンパクトな用途グリッド（残りの用途 + その他） -->
                <div class="purpose-compact-grid" itemscope itemtype="https://schema.org/ItemList">
                    <meta itemprop="name" content="補助金検索 - やりたいこと別">
                    <meta itemprop="description" content="事業の目的別に補助金・助成金を検索できます">
                    
                    <?php 
                    // 4番目以降の用途を小カードで表示
                    $grid_purposes = array_slice($purpose_categories, 3);
                    $position = 4;
                    foreach ($grid_purposes as $purpose) : 
                    ?>
                    <a href="<?php echo esc_url($purpose['url']); ?>" class="purpose-compact-card" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <meta itemprop="position" content="<?php echo $position++; ?>">
                        <link itemprop="url" href="<?php echo esc_url($purpose['url']); ?>">
                        <div class="compact-card-icon">
                            <i class="<?php echo esc_attr($purpose['icon']); ?>"></i>
                        </div>
                        <div class="compact-card-content">
                            <h4 class="compact-card-title" itemprop="name"><?php echo esc_html($purpose['title']); ?></h4>
                        </div>
                        <div class="compact-card-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    
                    <?php 
                    // その他の用途も小カードで追加
                    foreach ($other_purposes as $purpose) : 
                    ?>
                    <a href="<?php echo esc_url($purpose['url']); ?>" class="purpose-compact-card" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <meta itemprop="position" content="<?php echo $position++; ?>">
                        <link itemprop="url" href="<?php echo esc_url($purpose['url']); ?>">
                        <div class="compact-card-icon">
                            <i class="<?php echo esc_attr($purpose['icon']); ?>"></i>
                        </div>
                        <div class="compact-card-content">
                            <h4 class="compact-card-title" itemprop="name"><?php echo esc_html($purpose['title']); ?></h4>
                        </div>
                        <div class="compact-card-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* ============================================
   Browse Section Integration - 統合版スタイル
   ============================================ */

.browse-section-integrated {
    padding: 80px 0 100px;
    background: #ffffff;
    border-top: 1px solid #e5e5e5;
    font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
}

.browse-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ヘッダー */
.browse-header {
    text-align: center;
    margin-bottom: 50px;
}

.browse-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #000000;
    color: #ffffff;
    padding: 8px 20px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.12em;
    margin-bottom: 20px;
}

.badge-pulse {
    width: 7px;
    height: 7px;
    background: #ffffff;
    border-radius: 50%;
    animation: browse-pulse 2s ease-in-out infinite;
}

@keyframes browse-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.browse-title {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 30px;
}

.title-main {
    font-size: 42px;
    font-weight: 900;
    color: #000000;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.title-sub {
    font-size: 17px;
    font-weight: 500;
    color: #666666;
    line-height: 1.5;
}

/* ============================================
   写真風スタイリッシュ2カラムレイアウト
   ============================================ */

.browse-photo-style-layout {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 30px;
    margin-top: 50px;
}

/* ============================================
   左カラム：ヒーローカード（大きなビジュアルカード）
   ============================================ */

.browse-hero-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.hero-purpose-card {
    position: relative;
    height: 220px;
    background: #000000;
    border-radius: 20px;
    overflow: hidden;
    text-decoration: none;
    display: block;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 4px solid #000000;
}

.hero-purpose-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.35);
    border-color: #ffeb3b;
}

/* ヒーローカード背景（写真風グラデーション） */
.hero-card-background {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
    z-index: 1;
}

/* 各カードごとに異なるグラデーション */
.hero-purpose-1 .hero-card-background {
    background: 
        radial-gradient(circle at 20% 80%, rgba(255, 235, 59, 0.15) 0%, transparent 50%),
        linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
}

.hero-purpose-2 .hero-card-background {
    background: 
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
        linear-gradient(45deg, #0a0a0a 0%, #1f1f1f 50%, #0a0a0a 100%);
}

.hero-purpose-3 .hero-card-background {
    background: 
        radial-gradient(circle at 50% 50%, rgba(255, 235, 59, 0.1) 0%, transparent 60%),
        linear-gradient(225deg, #000000 0%, #262626 50%, #000000 100%);
}

/* 動的パターンオーバーレイ */
.hero-card-background::before {
    content: '';
    position: absolute;
    inset: 0;
    background: 
        repeating-linear-gradient(
            45deg,
            transparent,
            transparent 10px,
            rgba(255, 255, 255, 0.02) 10px,
            rgba(255, 255, 255, 0.02) 20px
        );
    animation: pattern-slide 20s linear infinite;
}

@keyframes pattern-slide {
    0% { transform: translateX(0); }
    100% { transform: translateX(28.28px); }
}

/* ヒーローカードコンテンツ */
.hero-card-content {
    position: relative;
    z-index: 2;
    padding: 30px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    color: #ffffff;
}

.hero-icon-wrapper {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #ffffff;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.hero-purpose-card:hover .hero-icon-wrapper {
    background: #ffeb3b;
    border-color: #ffeb3b;
    color: #000000;
    transform: scale(1.05);
}

.hero-purpose-title {
    font-size: 24px;
    font-weight: 900;
    color: #ffffff;
    margin: 0 0 10px 0;
    line-height: 1.2;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.hero-purpose-description {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.5;
    margin: 0;
}

.hero-card-arrow {
    position: absolute;
    bottom: 30px;
    right: 30px;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 16px;
    transition: all 0.3s ease;
}

.hero-purpose-card:hover .hero-card-arrow {
    background: #ffeb3b;
    border-color: #ffeb3b;
    color: #000000;
    transform: translateX(4px);
}

/* ホバー時のオーバーレイエフェクト */
.hero-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 235, 59, 0.15) 0%, rgba(0, 0, 0, 0.3) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 1;
}

.hero-purpose-card:hover .hero-card-overlay {
    opacity: 1;
}

/* ============================================
   右カラム：用途グリッド（小カード）
   ============================================ */

.browse-grid-column {
    display: flex;
    flex-direction: column;
}

.grid-column-header {
    margin-bottom: 24px;
}

.grid-column-title {
    font-size: 24px;
    font-weight: 900;
    color: #000000;
    margin: 0 0 8px 0;
    line-height: 1.2;
}

.grid-column-subtitle {
    font-size: 14px;
    color: #666666;
    margin: 0;
    line-height: 1.5;
}

/* コンパクトグリッド（3x4レイアウト） */
.purpose-compact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.purpose-compact-card {
    position: relative;
    background: #ffffff;
    border: 3px solid #000000;
    border-radius: 12px;
    padding: 16px 12px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 100px;
}

.purpose-compact-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    border-color: #ffeb3b;
    background: #fffef5;
}

.compact-card-icon {
    width: 44px;
    height: 44px;
    background: #000000;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #ffffff;
    transition: all 0.3s ease;
}

.purpose-compact-card:hover .compact-card-icon {
    background: #ffeb3b;
    color: #000000;
    transform: scale(1.1);
}

.compact-card-content {
    flex: 1;
    text-align: center;
}

.compact-card-title {
    font-size: 13px;
    font-weight: 700;
    color: #000000;
    margin: 0;
    line-height: 1.3;
}

.compact-card-arrow {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 20px;
    height: 20px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #666666;
    opacity: 0;
    transition: all 0.3s ease;
}

.purpose-compact-card:hover .compact-card-arrow {
    opacity: 1;
    background: #ffeb3b;
    color: #000000;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

/* ============================================
   レスポンシブデザイン
   ============================================ */

/* タブレット（1024px以下） */
@media (max-width: 1024px) {
    .browse-photo-style-layout {
        grid-template-columns: 1fr 360px;
        gap: 24px;
    }
    
    .hero-purpose-card {
        height: 200px;
    }
    
    .hero-purpose-title {
        font-size: 22px;
    }
    
    .purpose-compact-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
}

/* タブレット（768px以下） */
@media (max-width: 768px) {
    .browse-photo-style-layout {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .hero-purpose-card {
        height: 180px;
    }
    
    .hero-card-content {
        padding: 24px;
    }
    
    .hero-icon-wrapper {
        width: 56px;
        height: 56px;
        font-size: 28px;
    }
    
    .hero-purpose-title {
        font-size: 20px;
    }
    
    .hero-purpose-description {
        font-size: 13px;
    }
    
    .grid-column-title {
        font-size: 22px;
    }
    
    .purpose-compact-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* モバイル（640px以下） */
@media (max-width: 640px) {
    .browse-section-integrated {
        padding: 60px 0 80px;
    }
    
    .browse-header {
        margin-bottom: 40px;
    }
    
    .title-main {
        font-size: 32px;
    }
    
    .title-sub {
        font-size: 15px;
    }
    
    .hero-purpose-card {
        height: 160px;
    }
    
    .hero-card-content {
        padding: 20px;
    }
    
    .hero-icon-wrapper {
        width: 48px;
        height: 48px;
        font-size: 24px;
        margin-bottom: 16px;
    }
    
    .hero-purpose-title {
        font-size: 18px;
    }
    
    .hero-purpose-description {
        font-size: 12px;
    }
    
    .hero-card-arrow {
        width: 36px;
        height: 36px;
        font-size: 14px;
        bottom: 20px;
        right: 20px;
    }
    
    .purpose-compact-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .purpose-compact-card {
        padding: 14px 10px;
        min-height: 90px;
    }
    
    .compact-card-icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    
    .compact-card-title {
        font-size: 12px;
    }
}

/* 小型モバイル（480px以下） */
@media (max-width: 480px) {
    .title-main {
        font-size: 28px;
    }
    
    .hero-purpose-card {
        height: 140px;
        gap: 16px;
    }
    
    .browse-hero-column {
        gap: 16px;
    }
    
    .purpose-compact-grid {
        grid-template-columns: 1fr;
    }
    
    .purpose-compact-card {
        flex-direction: row;
        padding: 12px;
        min-height: auto;
    }
    
    .compact-card-content {
        text-align: left;
    }
}



.purpose-more-categories {

</style>

<script>
(function() {
    'use strict';
    
    console.log('[Browse Section] Script loaded');
    
    // DOMContentLoaded と load の両方で初期化を試みる（カスタマイザー対策）
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[Browse Section] DOMContentLoaded fired');
        initBrowseTabsIntegrated();
    });
    
    // カスタマイザーなどで DOMContentLoaded が既に発火している場合の対策
    if (document.readyState === 'loading') {
        console.log('[Browse Section] Document is still loading, waiting for DOMContentLoaded');
    } else {
        console.log('[Browse Section] Document already loaded, initializing immediately');
        setTimeout(function() {
            initBrowseTabsIntegrated();
        }, 100);
    }
    
    function initBrowseTabsIntegrated() {
        console.log('[Browse Init] Starting Browse Section Integrated initialization...');
        
        const tabButtons = document.querySelectorAll('.browse-section-integrated .tab-button');
        const tabContents = document.querySelectorAll('.browse-section-integrated .tab-content');
        
        console.log('[Browse Init] Found', tabButtons.length, 'tab buttons');
        console.log('[Browse Init] Found', tabContents.length, 'tab contents');
        
        if (tabButtons.length === 0) {
            console.error('[Browse Init] ERROR: No tab buttons found!');
            return;
        }
        
        if (tabContents.length === 0) {
            console.error('[Browse Init] ERROR: No tab contents found!');
            return;
        }
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // 全てのタブをリセット
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                tabContents.forEach(content => content.classList.remove('active'));
                
                // 選択されたタブをアクティブ化
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                console.log('[Browse Tab] Switched to tab:', targetTab);
                
                // Browse section内のタブコンテンツのみを対象とする
                const browseSectionContents = document.querySelectorAll('.browse-section-integrated .tab-content');
                browseSectionContents.forEach(content => {
                    if (content.getAttribute('data-content') === targetTab) {
                        content.classList.add('active');
                        console.log('[Browse Tab] Activated content:', targetTab);
                    }
                });
                
                // トラッキング（Google Analyticsが有効な場合）
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'browse_tab_click_integrated', {
                        event_category: 'navigation',
                        event_label: targetTab
                    });
                }
            });
        });
        
        console.log('[✓] Browse Section Integrated initialized successfully');
        console.log('[Browse Init] Active tab button:', document.querySelector('.browse-section-integrated .tab-button.active'));
        console.log('[Browse Init] Active tab content:', document.querySelector('.browse-section-integrated .tab-content.active'));
    }
    
    // セクションの存在確認
    const browseSection = document.querySelector('.browse-section-integrated');
    if (browseSection) {
        console.log('[✓] Browse Section Integrated DOM element found');
    } else {
        console.error('[✗] Browse Section Integrated DOM element NOT FOUND!');
    }
    
    // 写真風レイアウトでは全ての用途を表示（トグル不要）
    console.log('[✓] Photo-style purpose layout initialized (no toggle needed)');
})();
</script>