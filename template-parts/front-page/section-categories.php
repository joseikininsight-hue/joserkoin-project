<?php
/**
 * Modern Categories Section - Photo Style v4.0
 * カテゴリー別・地域別助成金検索セクション - 写真風スタイリッシュデザイン
 *
 * @package Grant_Insight_Perfect
 * @version 24.4-photo-style
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// データベースから実際のカテゴリと件数を取得
$main_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 8
));

$all_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
));

// カテゴリアイコン設定
$category_icons = array(
    0 => 'fas fa-laptop-code',
    1 => 'fas fa-industry',
    2 => 'fas fa-rocket',
    3 => 'fas fa-store',
    4 => 'fas fa-leaf',
    5 => 'fas fa-users',
    6 => 'fas fa-chart-line',
    7 => 'fas fa-handshake'
);

$archive_base_url = get_post_type_archive_link('grant');

// 統計情報を取得
if (function_exists('gi_get_cached_stats')) {
    $stats = gi_get_cached_stats();
} else {
    $stats = array(
        'total_grants' => wp_count_posts('grant')->publish ?? 0,
        'active_grants' => 0
    );
}

// 地域別に都道府県を整理
$prefectures = gi_get_all_prefectures();
$regions = array(
    'hokkaido_tohoku' => array(
        'name' => '北海道・東北',
        'icon' => 'fas fa-mountain',
        'prefectures' => array()
    ),
    'kanto' => array(
        'name' => '関東',
        'icon' => 'fas fa-building',
        'prefectures' => array()
    ),
    'chubu' => array(
        'name' => '中部',
        'icon' => 'fas fa-torii-gate',
        'prefectures' => array()
    ),
    'kinki' => array(
        'name' => '近畿',
        'icon' => 'fas fa-city',
        'prefectures' => array()
    ),
    'chugoku' => array(
        'name' => '中国',
        'icon' => 'fas fa-water',
        'prefectures' => array()
    ),
    'shikoku' => array(
        'name' => '四国',
        'icon' => 'fas fa-bridge',
        'prefectures' => array()
    ),
    'kyushu' => array(
        'name' => '九州・沖縄',
        'icon' => 'fas fa-island-tropical',
        'prefectures' => array()
    )
);

// 都道府県を地域別に振り分け
foreach ($prefectures as $pref) {
    $region_key = match($pref['region']) {
        'hokkaido' => 'hokkaido_tohoku',
        'tohoku' => 'hokkaido_tohoku',
        'kanto' => 'kanto',
        'chubu' => 'chubu',
        'kinki' => 'kinki',
        'chugoku' => 'chugoku',
        'shikoku' => 'shikoku',
        'kyushu' => 'kyushu',
        default => 'kyushu'
    };
    
    $prefecture_term = get_term_by('slug', $pref['slug'], 'grant_prefecture');
    $pref['count'] = $prefecture_term ? $prefecture_term->count : 0;
    
    $regions[$region_key]['prefectures'][] = $pref;
}
?>

<!-- フォント・アイコン読み込み -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+JP:wght@400;500;700;900&display=swap" rel="stylesheet">

<!-- カテゴリーセクション - 写真風スタイリッシュデザイン -->
<section class="giac-categories-section" id="grant-categories">
    <div class="giac-container">
        <!-- セクションヘッダー -->
        <header class="browse-header">
            <div class="browse-badge">
                <div class="badge-pulse"></div>
                <span>CATEGORY SEARCH</span>
            </div>
            
            <h2 class="browse-title">
                <span class="title-main">カテゴリー別検索</span>
                <span class="title-sub">最適な補助金を業種・目的別に発見</span>
            </h2>
        </header>

        <!-- 写真風スタイリッシュ2カラムレイアウト -->
        <div class="browse-photo-style-layout">
            
            <!-- 左カラム：主要カテゴリーの大きなビジュアルカード（3つ） -->
            <div class="browse-hero-column">
                <?php 
                $hero_categories = array_slice($main_categories, 0, 3);
                foreach ($hero_categories as $index => $category) : 
                    $icon = $category_icons[$index] ?? 'fas fa-folder';
                    $category_url = get_term_link($category);
                ?>
                <a href="<?php echo esc_url($category_url); ?>" class="hero-category-card hero-category-<?php echo $index + 1; ?>">
                    <!-- 写真風の背景グラデーション -->
                    <div class="hero-card-background"></div>
                    
                    <!-- カード内容 -->
                    <div class="hero-card-content">
                        <div class="hero-icon-wrapper">
                            <i class="<?php echo esc_attr($icon); ?>"></i>
                        </div>
                        <h3 class="hero-category-title"><?php echo esc_html($category->name); ?></h3>
                        <p class="hero-category-count"><?php echo number_format($category->count); ?>件の補助金</p>
                        <div class="hero-card-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                    
                    <!-- ホバー時のオーバーレイ -->
                    <div class="hero-card-overlay"></div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- 右カラム：カテゴリーから探す（小グリッド） -->
            <div class="browse-grid-column">
                <div class="grid-column-header">
                    <h3 class="grid-column-title">カテゴリーから探す</h3>
                    <p class="grid-column-subtitle">その他の業種・目的別補助金</p>
                </div>

                <!-- コンパクトなカテゴリーグリッド -->
                <div class="category-compact-grid">
                    <?php 
                    $grid_categories = array_slice($main_categories, 3);
                    foreach ($grid_categories as $index => $category) : 
                        $icon = $category_icons[$index + 3] ?? 'fas fa-folder';
                        $category_url = get_term_link($category);
                    ?>
                    <a href="<?php echo esc_url($category_url); ?>" class="category-compact-card">
                        <div class="compact-card-icon">
                            <i class="<?php echo esc_attr($icon); ?>"></i>
                        </div>
                        <div class="compact-card-content">
                            <h4 class="compact-card-title"><?php echo esc_html($category->name); ?></h4>
                            <span class="compact-card-count"><?php echo $category->count; ?>件</span>
                        </div>
                        <div class="compact-card-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- その他のカテゴリー表示ボタン -->
                <?php if (count($all_categories) > 8) : ?>
                <button type="button" class="show-more-categories-btn" id="show-more-categories">
                    <span class="btn-text">すべてのカテゴリー</span>
                    <i class="fas fa-chevron-down btn-icon"></i>
                </button>
                <?php endif; ?>
            </div>

        </div>

        <!-- その他のカテゴリー（展開エリア） -->
        <?php if (count($all_categories) > 8) : 
            $other_categories = array_slice($all_categories, 8);
        ?>
        <div class="more-categories-panel" id="more-categories-panel">
            <div class="more-categories-grid">
                <?php foreach ($other_categories as $category) :
                    $category_url = get_term_link($category);
                ?>
                <a href="<?php echo esc_url($category_url); ?>" class="mini-category-card">
                    <i class="fas fa-folder"></i>
                    <span class="mini-title"><?php echo esc_html($category->name); ?></span>
                    <span class="mini-count"><?php echo $category->count; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 地域選択セクション - 写真風デザイン -->
        <div class="giac-location-section">
            <!-- セクションヘッダー -->
            <header class="browse-header">
                <div class="browse-badge">
                    <div class="badge-pulse"></div>
                    <span>LOCATION SEARCH</span>
                </div>
                
                <h2 class="browse-title">
                    <span class="title-main">地域から探す</span>
                    <span class="title-sub">都道府県・市町村別の助成金・補助金を検索</span>
                </h2>
            </header>

            <!-- 地域別写真風レイアウト -->
            <div class="regions-photo-layout">
                <?php foreach ($regions as $region_key => $region) : 
                    if (empty($region['prefectures'])) continue;
                ?>
                <div class="region-section" data-region="<?php echo esc_attr($region_key); ?>">
                    <!-- 地域ヒーローカード -->
                    <div class="region-hero-card region-<?php echo esc_attr($region_key); ?>">
                        <div class="region-hero-background"></div>
                        <div class="region-hero-content">
                            <div class="region-icon-wrapper">
                                <i class="<?php echo esc_attr($region['icon']); ?>"></i>
                            </div>
                            <h3 class="region-title"><?php echo esc_html($region['name']); ?></h3>
                            <p class="region-pref-count"><?php echo count($region['prefectures']); ?>都道府県</p>
                            <button type="button" class="region-toggle-btn" data-region-target="<?php echo esc_attr($region_key); ?>">
                                <span class="toggle-text">詳細を見る</span>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </button>
                        </div>
                        <div class="region-hero-overlay"></div>
                    </div>

                    <!-- 都道府県グリッド（モバイルでは閉じた状態） -->
                    <div class="region-prefectures-panel" id="region-panel-<?php echo esc_attr($region_key); ?>">
                        <div class="prefectures-grid">
                            <?php foreach ($region['prefectures'] as $pref) : ?>
                            <a href="<?php echo esc_url(get_term_link($pref['slug'], 'grant_prefecture')); ?>" 
                               class="prefecture-mini-card">
                                <div class="prefecture-mini-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="prefecture-mini-content">
                                    <h5 class="prefecture-mini-name"><?php echo esc_html($pref['name']); ?></h5>
                                    <span class="prefecture-mini-count"><?php echo number_format($pref['count']); ?>件</span>
                                </div>
                                <div class="prefecture-mini-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- 市町村検索 -->
            <div class="municipality-search-section">
                <h3 class="search-section-title">
                    <i class="fas fa-search"></i>
                    市町村で検索
                </h3>
                <div class="search-wrapper">
                    <div class="search-container">
                        <input type="text" 
                               id="municipality-search-input" 
                               class="search-input" 
                               placeholder="市町村名を入力（例：横浜市、大阪市）"
                               autocomplete="off">
                        <button type="button" class="search-button" id="municipality-search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <!-- 検索結果 -->
                    <div class="search-results-panel" id="municipality-search-results">
                        <!-- 検索結果がここに表示されます -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ============================================
   Photo Style Category & Prefecture Section
   写真風スタイリッシュデザイン v4.0
   ============================================ */

/* ベース設定 */
.giac-categories-section {
    position: relative;
    padding: 80px 0 100px;
    background: #ffffff;
    border-top: 1px solid #e5e5e5;
    font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
}

.giac-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ヘッダースタイル */
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
    animation: badge-pulse-animation 2s ease-in-out infinite;
}

@keyframes badge-pulse-animation {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.browse-title {
    display: flex;
    flex-direction: column;
    gap: 10px;
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
   写真風2カラムレイアウト
   ============================================ */

.browse-photo-style-layout {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 30px;
    margin-top: 50px;
    margin-bottom: 60px;
}

/* ============================================
   左カラム：ヒーローカード
   ============================================ */

.browse-hero-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.hero-category-card {
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

.hero-category-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.35);
    border-color: #ffeb3b;
}

/* ヒーローカード背景 */
.hero-card-background {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
    z-index: 1;
}

/* 各カード固有のグラデーション */
.hero-category-1 .hero-card-background {
    background: 
        radial-gradient(circle at 20% 80%, rgba(255, 235, 59, 0.15) 0%, transparent 50%),
        linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
}

.hero-category-2 .hero-card-background {
    background: 
        radial-gradient(circle at 80% 20%, rgba(255, 235, 59, 0.12) 0%, transparent 50%),
        linear-gradient(135deg, #1a1a1a 0%, #000000 50%, #1a1a1a 100%);
}

.hero-category-3 .hero-card-background {
    background: 
        radial-gradient(circle at 50% 50%, rgba(255, 235, 59, 0.1) 0%, transparent 60%),
        linear-gradient(135deg, #000000 0%, #0d0d0d 50%, #000000 100%);
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

.hero-category-card:hover .hero-icon-wrapper {
    background: #ffeb3b;
    border-color: #ffeb3b;
    color: #000000;
    transform: scale(1.05);
}

.hero-category-title {
    font-size: 24px;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.2;
    margin: 0 0 8px;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.hero-category-count {
    font-size: 14px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
}

.hero-card-arrow {
    position: absolute;
    bottom: 30px;
    right: 30px;
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 18px;
    transition: all 0.3s ease;
}

.hero-category-card:hover .hero-card-arrow {
    background: #ffeb3b;
    border-color: #ffeb3b;
    color: #000000;
    transform: translateX(4px);
}

.hero-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.4) 100%);
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.hero-category-card:hover .hero-card-overlay {
    opacity: 1;
}

/* ============================================
   右カラム：コンパクトグリッド
   ============================================ */

.browse-grid-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.grid-column-header {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
    border-radius: 16px;
    border: 2px solid #000000;
}

.grid-column-title {
    font-size: 20px;
    font-weight: 900;
    color: #000000;
    margin: 0 0 8px;
}

.grid-column-subtitle {
    font-size: 13px;
    font-weight: 500;
    color: #666666;
    margin: 0;
}

.category-compact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.category-compact-card {
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

.category-compact-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    border-color: #ffeb3b;
    background: #fffef5;
}

.compact-card-icon {
    width: 42px;
    height: 42px;
    background: #000000;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 20px;
    transition: all 0.3s ease;
}

.category-compact-card:hover .compact-card-icon {
    background: #ffeb3b;
    color: #000000;
    transform: rotate(-5deg);
}

.compact-card-content {
    text-align: center;
    flex: 1;
}

.compact-card-title {
    font-size: 12px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 4px;
    line-height: 1.3;
}

.compact-card-count {
    font-size: 10px;
    font-weight: 600;
    color: #666666;
}

.compact-card-arrow {
    width: 24px;
    height: 24px;
    background: #f5f5f5;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666666;
    font-size: 10px;
    transition: all 0.3s ease;
}

.category-compact-card:hover .compact-card-arrow {
    background: #ffeb3b;
    color: #000000;
}

/* すべて表示ボタン */
.show-more-categories-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px;
    background: #ffffff;
    border: 3px solid #000000;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    color: #000000;
    cursor: pointer;
    transition: all 0.3s ease;
}

.show-more-categories-btn:hover {
    background: #000000;
    color: #ffffff;
}

.show-more-categories-btn.active {
    background: #000000;
    color: #ffffff;
}

.show-more-categories-btn .btn-icon {
    transition: transform 0.3s ease;
}

.show-more-categories-btn.active .btn-icon {
    transform: rotate(180deg);
}

/* その他のカテゴリーパネル */
.more-categories-panel {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease;
    margin-top: 30px;
}

.more-categories-panel.show {
    max-height: 2000px;
}

.more-categories-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    padding: 30px;
    background: #fafafa;
    border-radius: 16px;
    border: 2px solid #e5e5e5;
}

.mini-category-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.mini-category-card:hover {
    border-color: #000000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.mini-category-card i {
    font-size: 16px;
    color: #666666;
}

.mini-title {
    flex: 1;
    font-size: 12px;
    font-weight: 600;
    color: #000000;
}

.mini-count {
    padding: 3px 8px;
    background: #f5f5f5;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    color: #666666;
}

/* ============================================
   地域選択セクション - 写真風デザイン
   ============================================ */

.giac-location-section {
    margin-top: 80px;
    padding-top: 80px;
    border-top: 3px solid #000000;
}

/* 地域別写真風レイアウト */
.regions-photo-layout {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
    margin-top: 50px;
    margin-bottom: 60px;
}

.region-section {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* 地域ヒーローカード */
.region-hero-card {
    position: relative;
    height: 200px;
    background: #000000;
    border-radius: 16px;
    overflow: hidden;
    border: 4px solid #000000;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.region-hero-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.3);
    border-color: #ffeb3b;
}

/* 地域別背景グラデーション */
.region-hero-background {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #1a1a1a 0%, #000000 50%, #1a1a1a 100%);
    z-index: 1;
}

.region-hokkaido_tohoku .region-hero-background {
    background: 
        radial-gradient(circle at 30% 70%, rgba(100, 181, 246, 0.15) 0%, transparent 50%),
        linear-gradient(135deg, #1a1a1a 0%, #000000 50%, #1a1a1a 100%);
}

.region-kanto .region-hero-background {
    background: 
        radial-gradient(circle at 70% 30%, rgba(255, 235, 59, 0.15) 0%, transparent 50%),
        linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
}

.region-chubu .region-hero-background {
    background: 
        radial-gradient(circle at 50% 80%, rgba(139, 195, 74, 0.15) 0%, transparent 50%),
        linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 50%, #1a1a1a 100%);
}

.region-kinki .region-hero-background {
    background: 
        radial-gradient(circle at 80% 20%, rgba(255, 152, 0, 0.15) 0%, transparent 50%),
        linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
}

.region-chugoku .region-hero-background {
    background: 
        radial-gradient(circle at 20% 30%, rgba(156, 39, 176, 0.12) 0%, transparent 50%),
        linear-gradient(135deg, #1a1a1a 0%, #000000 50%, #1a1a1a 100%);
}

.region-shikoku .region-hero-background {
    background: 
        radial-gradient(circle at 60% 60%, rgba(0, 150, 136, 0.15) 0%, transparent 50%),
        linear-gradient(135deg, #000000 0%, #0d0d0d 50%, #000000 100%);
}

.region-kyushu .region-hero-background {
    background: 
        radial-gradient(circle at 40% 40%, rgba(244, 67, 54, 0.15) 0%, transparent 50%),
        linear-gradient(135deg, #1a1a1a 0%, #000000 50%, #1a1a1a 100%);
}

/* 地域ヒーローコンテンツ */
.region-hero-content {
    position: relative;
    z-index: 2;
    padding: 24px;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    text-align: center;
}

.region-icon-wrapper {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #ffffff;
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.region-hero-card:hover .region-icon-wrapper {
    background: #ffeb3b;
    border-color: #ffeb3b;
    color: #000000;
    transform: scale(1.05);
}

.region-title {
    font-size: 20px;
    font-weight: 900;
    color: #ffffff;
    margin: 0 0 6px;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.region-pref-count {
    font-size: 12px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.8);
    margin: 0 0 16px;
}

.region-toggle-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 999px;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.region-toggle-btn:hover {
    background: #ffeb3b;
    border-color: #ffeb3b;
    color: #000000;
}

.region-toggle-btn.active .toggle-icon {
    transform: rotate(180deg);
}

.region-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.region-hero-card:hover .region-hero-overlay {
    opacity: 1;
}

/* 都道府県パネル */
.region-prefectures-panel {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease;
}

.region-prefectures-panel.show {
    max-height: 1000px;
}

.prefectures-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    padding: 16px;
    background: #fafafa;
    border-radius: 12px;
    border: 2px solid #e5e5e5;
}

.prefecture-mini-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #ffffff;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.prefecture-mini-card:hover {
    border-color: #000000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.prefecture-mini-icon {
    width: 32px;
    height: 32px;
    background: #000000;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 14px;
    flex-shrink: 0;
}

.prefecture-mini-card:hover .prefecture-mini-icon {
    background: #ffeb3b;
    color: #000000;
}

.prefecture-mini-content {
    flex: 1;
}

.prefecture-mini-name {
    font-size: 12px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 2px;
}

.prefecture-mini-count {
    font-size: 10px;
    font-weight: 600;
    color: #666666;
}

.prefecture-mini-arrow {
    width: 24px;
    height: 24px;
    background: #f5f5f5;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666666;
    font-size: 10px;
    transition: all 0.3s ease;
}

.prefecture-mini-card:hover .prefecture-mini-arrow {
    background: #ffeb3b;
    color: #000000;
    transform: translateX(2px);
}

/* ============================================
   市町村検索セクション
   ============================================ */

.municipality-search-section {
    margin-top: 60px;
    padding: 40px;
    background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
    border-radius: 20px;
    border: 3px solid #000000;
}

.search-section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 20px;
    font-weight: 900;
    color: #000000;
    margin: 0 0 24px;
}

.search-section-title i {
    font-size: 24px;
}

.search-wrapper {
    max-width: 600px;
    margin: 0 auto;
}

.search-container {
    display: flex;
    margin-bottom: 20px;
}

.search-input {
    flex: 1;
    padding: 16px 20px;
    font-size: 16px;
    color: #000000;
    background: #ffffff;
    border: 2px solid #000000;
    border-right: none;
    border-radius: 12px 0 0 12px;
    transition: all 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.3);
}

.search-input::placeholder {
    color: #999999;
}

.search-button {
    padding: 16px 24px;
    background: #000000;
    color: #ffffff;
    border: 2px solid #000000;
    border-radius: 0 12px 12px 0;
    cursor: pointer;
    transition: all 0.2s ease;
}

.search-button:hover {
    background: #333333;
}

.search-button i {
    font-size: 18px;
}

.search-results-panel {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    background: #ffffff;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
}

.search-results-panel.show {
    max-height: 400px;
    overflow-y: auto;
}

/* ============================================
   レスポンシブデザイン
   ============================================ */

/* タブレット */
@media (max-width: 1024px) {
    .browse-photo-style-layout {
        grid-template-columns: 1fr 360px;
        gap: 24px;
    }
    
    .category-compact-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .more-categories-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* タブレット・スマホ */
@media (max-width: 768px) {
    .giac-categories-section {
        padding: 60px 0 80px;
    }
    
    .title-main {
        font-size: 32px;
    }
    
    .title-sub {
        font-size: 15px;
    }
    
    .browse-photo-style-layout {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .hero-category-card {
        height: 200px;
    }
    
    .regions-photo-layout {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .region-hero-card {
        height: 180px;
    }
    
    .more-categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .municipality-search-section {
        padding: 30px 20px;
    }
    
    /* モバイルアコーディオン：デフォルトで閉じた状態 */
    .region-prefectures-panel {
        max-height: 0;
    }
    
    .region-prefectures-panel.show {
        max-height: 1000px;
    }
}

/* スマホ最適化 */
@media (max-width: 640px) {
    .giac-container {
        padding: 0 16px;
    }
    
    .title-main {
        font-size: 28px;
    }
    
    .title-sub {
        font-size: 14px;
    }
    
    .category-compact-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .category-compact-card {
        padding: 12px 10px;
        min-height: 90px;
    }
    
    .compact-card-icon {
        width: 36px;
        height: 36px;
        font-size: 18px;
    }
    
    .compact-card-title {
        font-size: 11px;
    }
    
    .regions-photo-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .prefectures-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .more-categories-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .search-container {
        flex-direction: column;
    }
    
    .search-input {
        border-radius: 12px 12px 0 0;
        border-right: 2px solid #000000;
    }
    
    .search-button {
        border-radius: 0 0 12px 12px;
    }
}

/* 極小スマホ */
@media (max-width: 480px) {
    .hero-category-card {
        height: 180px;
    }
    
    .hero-icon-wrapper {
        width: 56px;
        height: 56px;
        font-size: 28px;
    }
    
    .hero-category-title {
        font-size: 20px;
    }
    
    .category-compact-grid {
        grid-template-columns: 1fr;
    }
    
    .category-compact-card {
        flex-direction: row;
        justify-content: flex-start;
        padding: 12px;
        min-height: auto;
    }
    
    .compact-card-content {
        text-align: left;
    }
    
    .region-hero-card {
        height: 160px;
    }
}

/* パフォーマンス最適化 */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // すべてのカテゴリー展開
    const moreCategoriesBtn = document.getElementById('show-more-categories');
    const moreCategoriesPanel = document.getElementById('more-categories-panel');
    
    if (moreCategoriesBtn && moreCategoriesPanel) {
        moreCategoriesBtn.addEventListener('click', function() {
            const isOpen = moreCategoriesPanel.classList.contains('show');
            
            if (isOpen) {
                moreCategoriesPanel.classList.remove('show');
                this.classList.remove('active');
                this.querySelector('.btn-text').textContent = 'すべてのカテゴリー';
            } else {
                moreCategoriesPanel.classList.add('show');
                this.classList.add('active');
                this.querySelector('.btn-text').textContent = '閉じる';
            }
        });
    }
    
    // 地域別都道府県パネルのトグル
    document.querySelectorAll('.region-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const regionKey = this.getAttribute('data-region-target');
            const panel = document.getElementById('region-panel-' + regionKey);
            
            if (panel) {
                const isOpen = panel.classList.contains('show');
                
                if (isOpen) {
                    panel.classList.remove('show');
                    this.classList.remove('active');
                    this.querySelector('.toggle-text').textContent = '詳細を見る';
                } else {
                    panel.classList.add('show');
                    this.classList.add('active');
                    this.querySelector('.toggle-text').textContent = '閉じる';
                }
            }
        });
    });
    
    // モバイル判定：768px以下でアコーディオンを閉じた状態に
    function handleMobileAccordions() {
        const isMobile = window.innerWidth <= 768;
        const prefecturePanels = document.querySelectorAll('.region-prefectures-panel');
        const regionToggleBtns = document.querySelectorAll('.region-toggle-btn');
        
        if (isMobile) {
            prefecturePanels.forEach(panel => {
                panel.classList.remove('show');
            });
            regionToggleBtns.forEach(btn => {
                btn.classList.remove('active');
                btn.querySelector('.toggle-text').textContent = '詳細を見る';
            });
        }
    }
    
    // 初期実行とリサイズ時の処理
    handleMobileAccordions();
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(handleMobileAccordions, 250);
    });
    
    // 市町村検索機能
    const searchInput = document.getElementById('municipality-search-input');
    const searchButton = document.getElementById('municipality-search-btn');
    const searchResults = document.getElementById('municipality-search-results');
    
    if (searchInput && searchButton && searchResults) {
        let searchTimeout;
        
        function performMunicipalitySearch() {
            const query = searchInput.value.trim();
            
            if (query.length < 2) {
                searchResults.classList.remove('show');
                return;
            }
            
            searchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">検索中...</div>';
            searchResults.classList.add('show');
            
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'search_municipalities',
                    query: query,
                    nonce: '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.results) {
                    displaySearchResults(data.data.results);
                } else {
                    searchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">該当する市町村が見つかりませんでした。</div>';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #e74c3c;">検索エラーが発生しました。</div>';
            });
        }
        
        function displaySearchResults(results) {
            if (results.length === 0) {
                searchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">該当する市町村が見つかりませんでした。</div>';
                return;
            }
            
            const resultsHtml = results.map(result => `
                <a href="${result.url}" class="search-result-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; text-decoration: none; border-bottom: 1px solid #e5e5e5; transition: background-color 0.2s ease;">
                    <span style="font-size: 14px; font-weight: 600; color: #000000;">${result.name}</span>
                    <span style="font-size: 12px; color: #666666;">${result.count}件</span>
                </a>
            `).join('');
            
            searchResults.innerHTML = resultsHtml;
            
            // ホバー効果を追加
            searchResults.querySelectorAll('.search-result-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#fafafa';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'transparent';
                });
            });
        }
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performMunicipalitySearch, 300);
        });
        
        searchButton.addEventListener('click', performMunicipalitySearch);
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performMunicipalitySearch();
            }
        });
        
        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                searchResults.classList.remove('show');
            }, 200);
        });
        
        searchInput.addEventListener('focus', function() {
            if (searchResults.innerHTML.trim() && this.value.trim().length >= 2) {
                searchResults.classList.add('show');
            }
        });
    }
    
    // タップフィードバック
    document.querySelectorAll('.hero-category-card, .category-compact-card, .prefecture-mini-card').forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.opacity = '0.8';
        });
        
        card.addEventListener('touchend', function() {
            this.style.opacity = '1';
        });
    });
    
    console.log('Photo Style Category & Prefecture Section v4.0 loaded');
});
</script>

<?php
// デバッグ情報（開発環境のみ）
if (defined('WP_DEBUG') && WP_DEBUG) {
    echo '<!-- Categories Section v4.0 - Photo Style Design -->';
    echo '<!-- Total Categories: ' . count($all_categories) . ' -->';
    echo '<!-- Main Categories: ' . count($main_categories) . ' -->';
}
?>
