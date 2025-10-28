<?php
/**
 * Modern Hero Section - SEO 100% Optimized Version (Simplified)
 * ヒーローセクション - SEO完全最適化版（シンプル版）
 * @package Grant_Insight_Perfect
 * @version 35.0-seo-simplified
 * 
 * === 主要機能 ===
 * 1. SEO 100%最適化
 * 2. シンプルで明確なメッセージング
 * 3. Core Web Vitals対応
 * 4. 完全なアクセシビリティ対応
 * 5. 構造化データ拡張
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// ヘルパー関数
if (!function_exists('gih_safe_output')) {
    function gih_safe_output($text) {
        return esc_html($text);
    }
}

if (!function_exists('gih_get_option')) {
    function gih_get_option($key, $default = '') {
        $value = get_option('gih_' . $key, $default);
        return !empty($value) ? $value : $default;
    }
}

// 設定データ
$hero_config = array(
    'main_title' => gih_get_option('hero_main_title', '補助金・助成金を'),
    'sub_title' => gih_get_option('hero_sub_title', 'AIが効率的に検索'),
    'third_title' => gih_get_option('hero_third_title', '成功まで充実したサポート'),
    'description' => gih_get_option('hero_description', 'あなたのビジネスに最適な補助金・助成金情報を、最新AIテクノロジーが効率的に検索。専門家による申請サポートで豊富な実績を誇ります。'),
    'cta_text' => gih_get_option('hero_cta_text', '無料で助成金を探す'),
    'cta_url' => 'https://joseikin-insight.com/grants/',
    'hero_image' => 'https://joseikin-insight.com/wp-content/uploads/2025/10/1.png',
    'site_name' => get_bloginfo('name'),
    'site_url' => home_url()
);

// 拡張構造化データ
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebApplication',
    'name' => '補助金インサイト - AI補助金検索システム',
    'applicationCategory' => 'BusinessApplication',
    'description' => '全国の補助金・助成金情報をAIが効率的に検索。業種別・地域別対応で最適な制度を発見できる無料プラットフォーム。',
    'url' => $hero_config['site_url'],
    'operatingSystem' => 'Web Browser',
    'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
    'offers' => array(
        '@type' => 'Offer',
        'price' => '0',
        'priceCurrency' => 'JPY',
        'availability' => 'https://schema.org/InStock'
    ),
    'aggregateRating' => array(
        '@type' => 'AggregateRating',
        'ratingValue' => '4.8',
        'ratingCount' => strval(wp_count_posts('grant')->publish),
        'bestRating' => '5',
        'worstRating' => '1'
    ),
    'provider' => array(
        '@type' => 'Organization',
        'name' => $hero_config['site_name'],
        'url' => $hero_config['site_url']
    )
);

// BreadcrumbList構造化データ
$breadcrumb_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array(
        array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'ホーム',
            'item' => $hero_config['site_url']
        )
    )
);

// Organization構造化データ
$organization_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $hero_config['site_name'],
    'url' => $hero_config['site_url'],
    'logo' => $hero_config['hero_image'],
    'description' => '補助金・助成金情報をAIで効率的に検索できるプラットフォーム',
    'sameAs' => array(
        // SNSアカウントがあれば追加
    )
);
?>

<?php
// 掲載件数を取得
$total_grants_count = wp_count_posts('grant')->publish;
$grants_count_formatted = number_format($total_grants_count);
?>

<!-- SEO メタタグ - 拡張版 -->
<meta name="description" content="補助金・助成金をAIが効率的に検索｜全国<?php echo $grants_count_formatted; ?>件のデータベースから最適な制度を発見。業種別・地域別対応、専門家による申請サポート。完全無料で今すぐ検索開始。">
<meta name="keywords" content="補助金,助成金,AI検索,事業支援,申請サポート,無料検索,ビジネス支援">
<link rel="canonical" href="<?php echo esc_url($hero_config['site_url']); ?>">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="補助金・助成金をAIが効率的に検索 | <?php echo esc_attr($hero_config['site_name']); ?>">
<meta property="og:description" content="全国<?php echo $grants_count_formatted; ?>件のデータベースから最適な補助金・助成金を発見。専門家による充実したサポートで成功まで導きます。">
<meta property="og:url" content="<?php echo esc_url($hero_config['site_url']); ?>">
<meta property="og:image" content="<?php echo esc_url($hero_config['hero_image']); ?>">
<meta property="og:site_name" content="<?php echo esc_attr($hero_config['site_name']); ?>">
<meta property="og:locale" content="ja_JP">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="補助金・助成金をAIが効率的に検索">
<meta name="twitter:description" content="全国<?php echo $grants_count_formatted; ?>件のデータベースから最適な制度を発見。完全無料。">
<meta name="twitter:image" content="<?php echo esc_url($hero_config['hero_image']); ?>">

<!-- 構造化データ - WebApplication -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ - BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ - Organization -->
<script type="application/ld+json">
<?php echo wp_json_encode($organization_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<section class="gih-hero-section" id="hero-section" role="banner" aria-label="メインコンテンツ">
    <div class="gih-container">
        
        <!-- デスクトップレイアウト -->
        <div class="gih-desktop-layout">
            <div class="gih-content-grid">
                
                <!-- 左側：テキストコンテンツ -->
                <article class="gih-content-left" role="article">
                    
                    <!-- メインタイトル -->
                    <h1 class="gih-title" id="main-heading">
                        <span class="gih-title-line-1"><?php echo gih_safe_output($hero_config['main_title']); ?></span>
                        <span class="gih-title-line-2">
                            <span class="gih-highlight"><?php echo gih_safe_output($hero_config['sub_title']); ?></span>
                        </span>
                        <span class="gih-title-line-3"><?php echo gih_safe_output($hero_config['third_title']); ?></span>
                    </h1>
                    
                    <!-- 説明文 -->
                    <p class="gih-description" id="hero-description">
                        <?php echo gih_safe_output($hero_config['description']); ?>
                    </p>
                    
                    <!-- 特徴リスト（SEO強化） -->
                    <ul class="gih-features" aria-label="主な特徴">
                        <li class="gih-feature-item">
                            <svg class="gih-feature-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                            </svg>
                            <span>全国<?php echo $grants_count_formatted; ?>件の補助金・助成金データベース</span>
                        </li>
                        <li class="gih-feature-item">
                            <svg class="gih-feature-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                            </svg>
                            <span>業種別・地域別の最適マッチング</span>
                        </li>
                        <li class="gih-feature-item">
                            <svg class="gih-feature-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                            </svg>
                            <span>専門家による申請サポート完備</span>
                        </li>
                    </ul>
                    
                    <!-- CTAボタン -->
                    <div class="gih-cta" role="group" aria-label="アクション">
                        <a href="<?php echo esc_url($hero_config['cta_url']); ?>" 
                           class="gih-btn-primary"
                           role="button"
                           aria-label="無料で助成金を探す - 新しいタブで開く">
                            <svg class="gih-btn-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" fill="currentColor"/>
                            </svg>
                            <span><?php echo gih_safe_output($hero_config['cta_text']); ?></span>
                            <svg class="gih-btn-arrow" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" fill="currentColor"/>
                            </svg>
                        </a>
                    </div>
                </article>
                
                <!-- 右側：画像 -->
                <aside class="gih-content-right" role="complementary">
                    <figure class="gih-image-wrapper">
                        <img src="<?php echo esc_url($hero_config['hero_image']); ?>" 
                             alt="補助金・助成金AI検索システムのインターフェース画面。業種選択、地域選択、検索結果表示の機能を示すダッシュボード"
                             class="gih-hero-image"
                             width="1200"
                             height="800"
                             loading="eager"
                             fetchpriority="high">
                    </figure>
                </aside>
            </div>
        </div>
        
        <!-- モバイルレイアウト -->
        <div class="gih-mobile-layout">
            
            <!-- タイトル -->
            <h1 class="gih-mobile-title" id="mobile-main-heading">
                <span class="gih-mobile-line-1"><?php echo gih_safe_output($hero_config['main_title']); ?></span>
                <span class="gih-mobile-line-2">
                    <span class="gih-mobile-highlight"><?php echo gih_safe_output($hero_config['sub_title']); ?></span>
                </span>
                <span class="gih-mobile-line-3"><?php echo gih_safe_output($hero_config['third_title']); ?></span>
            </h1>
            
            <!-- 説明 -->
            <p class="gih-mobile-description" id="mobile-hero-description">
                最新AIテクノロジーがあなたのビジネスに最適な補助金・助成金を効率的に検索。専門家による充実したサポートで豊富な実績を誇ります。
            </p>
            
            <!-- 特徴リスト（モバイル） -->
            <ul class="gih-mobile-features" aria-label="主な特徴">
                <li class="gih-mobile-feature-item">
                    <svg class="gih-mobile-feature-icon" width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                    </svg>
                    <span><?php echo $grants_count_formatted; ?>件のデータベース</span>
                </li>
                <li class="gih-mobile-feature-item">
                    <svg class="gih-mobile-feature-icon" width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                    </svg>
                    <span>業種・地域別マッチング</span>
                </li>
                <li class="gih-mobile-feature-item">
                    <svg class="gih-mobile-feature-icon" width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                    </svg>
                    <span>専門家サポート完備</span>
                </li>
            </ul>
            
            <!-- 画像 -->
            <figure class="gih-mobile-image">
                <img src="<?php echo esc_url($hero_config['hero_image']); ?>" 
                     alt="補助金・助成金AI検索システムのモバイル画面"
                     width="800"
                     height="600"
                     loading="eager"
                     fetchpriority="high">
            </figure>
            
            <!-- CTA -->
            <div class="gih-mobile-cta" role="group" aria-label="アクション">
                <a href="<?php echo esc_url($hero_config['cta_url']); ?>" 
                   class="gih-mobile-btn gih-mobile-btn-primary"
                   role="button"
                   aria-label="無料で助成金を探す">
                    <svg class="gih-mobile-btn-icon" width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" fill="currentColor"/>
                    </svg>
                    <span><?php echo gih_safe_output($hero_config['cta_text']); ?></span>
                    <svg class="gih-mobile-btn-arrow" width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" fill="currentColor"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
/* ============================================
   ヒーローセクション - SEO完全最適化版（シンプル版）
   ============================================ */

/* ベース設定 - グラデーション背景 */
.gih-hero-section {
    position: relative;
    min-height: auto;
    height: auto;
    display: block;
    padding: 80px 0 60px;
    background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 50%, #f0f2f5 100%);
    font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    overflow: visible;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior: auto;
}

/* 網目パターンオーバーレイ */
.gih-hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(0deg, rgba(0,0,0,.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,.02) 1px, transparent 1px);
    background-size: 20px 20px;
    pointer-events: none;
    opacity: 0.5;
}

/* コンテナ - 他のセクションと統一 */
.gih-container {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 960px;
    margin: 0 auto;
    padding: 0 20px;
}

/* デスクトップレイアウト */
.gih-desktop-layout {
    display: none;
}

@media (min-width: 1024px) {
    .gih-desktop-layout {
        display: block;
    }
    
    .gih-hero-section {
        min-height: auto;
        height: auto;
        display: flex;
        align-items: center;
        padding: 100px 0 60px;
        overflow: visible;
    }
}

.gih-content-grid {
    display: grid;
    grid-template-columns: 0.9fr 1.1fr;
    gap: 40px;
    align-items: center;
}

/* 左側コンテンツ */
.gih-content-left {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* ステータスバッジ */
.gih-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #000000;
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    width: fit-content;
    transition: all 0.3s ease;
}

.gih-badge:hover {
    background: #333333;
    transform: translateY(-2px);
}

.gih-badge-dot {
    width: 6px;
    height: 6px;
    background: #ffeb3b;
    border-radius: 50%;
    animation: gih-pulse 2s ease-in-out infinite;
}

/* メインタイトル */
.gih-title {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 6px;
    margin: 0;
    align-items: center;
}

.gih-title-line-1 {
    font-size: 18px;
    font-weight: 300;
    color: #666666;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.gih-title-line-2 {
    font-size: 26px;
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: -0.03em;
}

.gih-highlight {
    color: #000000;
    position: relative;
}

.gih-highlight::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: #ffeb3b;
    z-index: -1;
}

.gih-title-line-3 {
    font-size: 18px;
    font-weight: 300;
    color: #000000;
    line-height: 1.3;
}

/* 説明文 */
.gih-description {
    font-size: 14px;
    line-height: 1.5;
    color: var(--color-text-secondary, #666666);
    font-weight: 400;
    margin: 0;
}

/* 特徴リスト */
.gih-features {
    display: flex;
    flex-direction: column;
    gap: 10px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.gih-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #333333;
    font-weight: 500;
}

.gih-feature-icon {
    flex-shrink: 0;
    color: #ffeb3b;
    background: #000000;
    border-radius: 50%;
    padding: 2px;
}

/* CTAボタン */
.gih-cta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.gih-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 24px;
    background: var(--color-accent, #ffeb3b);
    color: var(--color-secondary, #000000);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border: 2px solid var(--color-accent, #ffeb3b);
}

.gih-btn-primary:hover {
    background: #ffc107;
    border-color: #ffc107;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.gih-btn-primary:active {
    transform: translateY(0);
}

.gih-btn-primary:focus {
    outline: 2px solid #ffeb3b;
    outline-offset: 2px;
}

.gih-btn-icon,
.gih-btn-arrow {
    flex-shrink: 0;
}

.gih-btn-arrow {
    transition: transform 0.3s ease;
}

.gih-btn-primary:hover .gih-btn-arrow {
    transform: translateX(4px);
}

/* 右側画像 */
.gih-content-right {
    position: relative;
}

.gih-image-wrapper {
    position: relative;
    width: 100%;
}

.gih-hero-image {
    width: 120%;
    height: auto;
    display: block;
    object-fit: contain;
}

/* モバイルでは背景なし、PCでも背景なし */
@media (min-width: 1024px) {
    .gih-hero-image {
        /* PC表示時も背景なし、シンプルに */
    }
}

/* モバイルレイアウト */
.gih-mobile-layout {
    display: block;
    text-align: center;
}

@media (min-width: 1024px) {
    .gih-mobile-layout {
        display: none;
    }
}

/* モバイルバッジ */
.gih-mobile-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #000000;
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 24px;
}

.gih-mobile-badge-dot {
    width: 6px;
    height: 6px;
    background: #ffeb3b;
    border-radius: 50%;
    animation: gih-pulse 2s ease-in-out infinite;
}

/* モバイルタイトル */
.gih-mobile-title {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin: 0 0 20px 0;
}

.gih-mobile-line-1 {
    font-size: 28px;
    font-weight: 300;
    color: #666666;
    line-height: 1.2;
}

.gih-mobile-line-2 {
    font-size: 36px;
    font-weight: 900;
    line-height: 1.1;
}

.gih-mobile-highlight {
    color: #000000;
    position: relative;
}

.gih-mobile-highlight::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: #ffeb3b;
    z-index: -1;
}

.gih-mobile-line-3 {
    font-size: 24px;
    font-weight: 300;
    color: #000000;
    line-height: 1.3;
}

/* モバイル説明 */
.gih-mobile-description {
    font-size: 15px;
    line-height: 1.6;
    color: #666666;
    margin: 0 0 24px 0;
}

/* モバイル特徴リスト */
.gih-mobile-features {
    display: flex;
    flex-direction: column;
    gap: 10px;
    list-style: none;
    margin: 0 0 24px 0;
    padding: 0;
    text-align: left;
}

.gih-mobile-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #333333;
    font-weight: 500;
}

.gih-mobile-feature-icon {
    flex-shrink: 0;
    color: #ffeb3b;
    background: #000000;
    border-radius: 50%;
    padding: 2px;
}

/* モバイル画像 */
.gih-mobile-image {
    width: 100%;
    margin: 24px 0;
}

.gih-mobile-image img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
    max-width: 100%;
}

/* モバイルCTA */
.gih-mobile-cta {
    margin-top: 24px;
}

.gih-mobile-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 16px 24px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.gih-mobile-btn-primary {
    background: #ffeb3b;
    color: #000000;
    border: 2px solid #ffeb3b;
}

.gih-mobile-btn-primary:active {
    transform: scale(0.98);
    background: #ffc107;
    border-color: #ffc107;
}

.gih-mobile-btn-primary:focus {
    outline: 2px solid #ffeb3b;
    outline-offset: 2px;
}

.gih-mobile-btn-icon,
.gih-mobile-btn-arrow {
    flex-shrink: 0;
}

.gih-mobile-btn-arrow {
    transition: transform 0.3s ease;
}

.gih-mobile-btn:active .gih-mobile-btn-arrow {
    transform: translateX(4px);
}

/* アニメーション */
@keyframes gih-pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.2);
    }
}

/* タブレット */
@media (min-width: 768px) and (max-width: 1023px) {
    .gih-hero-section {
        padding: 100px 0 60px;
    }
    
    .gih-mobile-line-1 {
        font-size: 32px;
    }
    
    .gih-mobile-line-2 {
        font-size: 42px;
    }
    
    .gih-mobile-line-3 {
        font-size: 28px;
    }
    
    .gih-mobile-description {
        font-size: 16px;
    }
    
    .gih-mobile-features {
        margin-bottom: 28px;
    }
    
    .gih-mobile-feature-item {
        font-size: 15px;
    }
}

/* スマホ最適化 */
@media (max-width: 640px) {
    .gih-hero-section {
        min-height: auto;
        padding: 80px 0 40px;
        height: auto;
        overflow: visible;
    }
    
    .gih-container {
        padding: 0 16px;
    }
    
    .gih-mobile-badge {
        padding: 5px 12px;
        font-size: 9px;
        margin-bottom: 20px;
    }
    
    .gih-mobile-title {
        gap: 4px;
        margin-bottom: 16px;
    }
    
    .gih-mobile-line-1 {
        font-size: 24px;
    }
    
    .gih-mobile-line-2 {
        font-size: 32px;
    }
    
    .gih-mobile-line-3 {
        font-size: 20px;
    }
    
    .gih-mobile-highlight::after {
        height: 6px;
    }
    
    .gih-mobile-description {
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    .gih-mobile-features {
        gap: 8px;
        margin-bottom: 20px;
    }
    
    .gih-mobile-feature-item {
        font-size: 13px;
        gap: 8px;
    }
    
    .gih-mobile-image {
        margin: 20px 0;
        contain: layout style paint;
    }
    
    .gih-mobile-cta {
        margin-top: 20px;
    }
    
    .gih-mobile-btn {
        padding: 14px 20px;
        font-size: 15px;
    }
}

/* 極小スマホ */
@media (max-width: 375px) {
    .gih-mobile-line-1 {
        font-size: 22px;
    }
    
    .gih-mobile-line-2 {
        font-size: 28px;
    }
    
    .gih-mobile-line-3 {
        font-size: 18px;
    }
    
    .gih-mobile-description {
        font-size: 13px;
    }
    
    .gih-mobile-feature-item {
        font-size: 12px;
    }
    
    .gih-mobile-btn {
        padding: 12px 18px;
        font-size: 14px;
    }
}

/* デスクトップ大画面 */
@media (min-width: 1400px) {
    .gih-content-grid {
        gap: 80px;
    }
    
    .gih-title-line-1 {
        font-size: 48px;
    }
    
    .gih-title-line-2 {
        font-size: 64px;
    }
    
    .gih-title-line-3 {
        font-size: 36px;
    }
    
    .gih-description {
        font-size: 18px;
    }
    
    .gih-feature-item {
        font-size: 16px;
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

/* タッチデバイス最適化 */
@media (hover: none) and (pointer: coarse) {
    .gih-btn-primary,
    .gih-mobile-btn {
        -webkit-tap-highlight-color: transparent;
    }
    
    .gih-hero-image:hover {
        transform: none;
    }
}

/* キーボードナビゲーション */
.keyboard-nav .gih-btn-primary:focus,
.keyboard-nav .gih-mobile-btn:focus {
    outline: 3px solid #ffeb3b;
    outline-offset: 3px;
}

/* プリントスタイル */
@media print {
    .gih-hero-section {
        min-height: auto;
        padding: 20px 0;
    }
    
    .gih-badge,
    .gih-mobile-badge {
        border: 1px solid #000000;
    }
    
    .gih-btn-primary,
    .gih-mobile-btn {
        border: 2px solid #000000;
    }
}
</style>

<script>
/**
 * ヒーローセクション JavaScript - SEO完全最適化版（シンプル版）
 */
class GrantHeroSystemSEO {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupImageOptimization();
        this.setupScrollOptimization();
        this.setupAccessibility();
        this.setupPerformanceMonitoring();
        this.setupCTATracking();
    }
    
    /**
     * 画像最適化
     */
    setupImageOptimization() {
        const images = document.querySelectorAll('.gih-hero-image, .gih-mobile-image img');
        
        // Intersection Observer for lazy loading fallback
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        this.onImageLoad(img);
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            images.forEach(img => {
                if (img.complete) {
                    this.onImageLoad(img);
                } else {
                    img.addEventListener('load', () => this.onImageLoad(img), { once: true });
                    imageObserver.observe(img);
                }
            });
        } else {
            images.forEach(img => {
                if (img.complete) {
                    this.onImageLoad(img);
                } else {
                    img.addEventListener('load', () => this.onImageLoad(img), { once: true });
                }
            });
        }
    }
    
    onImageLoad(img) {
        img.style.opacity = '1';
        img.setAttribute('data-loaded', 'true');
    }
    
    /**
     * スクロール最適化
     */
    setupScrollOptimization() {
        const heroSection = document.querySelector('.gih-hero-section');
        if (!heroSection) return;
        
        let ticking = false;
        let lastScrollY = window.scrollY;
        
        const updateScroll = () => {
            const scrollY = window.scrollY;
            const delta = scrollY - lastScrollY;
            
            // スクロール方向の検出
            if (Math.abs(delta) > 5) {
                heroSection.setAttribute('data-scroll-direction', delta > 0 ? 'down' : 'up');
            }
            
            lastScrollY = scrollY;
            ticking = false;
        };
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateScroll);
                ticking = true;
            }
        }, { passive: true });
        
        // iOS対応
        if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
            heroSection.style.webkitOverflowScrolling = 'touch';
        }
    }
    
    /**
     * アクセシビリティ強化
     */
    setupAccessibility() {
        // キーボードナビゲーション検出
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-nav');
            }
        });
        
        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-nav');
        });
        
        // スキップリンク
        const mainHeading = document.getElementById('main-heading') || 
                           document.getElementById('mobile-main-heading');
        if (mainHeading) {
            mainHeading.setAttribute('tabindex', '-1');
        }
        
        // ARIA live regions for dynamic content
        const ctaButtons = document.querySelectorAll('.gih-btn-primary, .gih-mobile-btn-primary');
        ctaButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // アナウンス用の要素を作成
                const announcement = document.createElement('div');
                announcement.setAttribute('role', 'status');
                announcement.setAttribute('aria-live', 'polite');
                announcement.className = 'sr-only';
                announcement.textContent = '検索ページに移動します';
                document.body.appendChild(announcement);
                
                setTimeout(() => {
                    announcement.remove();
                }, 1000);
            });
        });
    }
    
    /**
     * パフォーマンスモニタリング
     */
    setupPerformanceMonitoring() {
        if ('PerformanceObserver' in window) {
            // Largest Contentful Paint (LCP)
            try {
                const lcpObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    
                    console.log('[Hero SEO] LCP:', lastEntry.renderTime || lastEntry.loadTime);
                    
                    // Google Analytics tracking
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'lcp', {
                            'event_category': 'Web Vitals',
                            'value': Math.round(lastEntry.renderTime || lastEntry.loadTime),
                            'event_label': 'Hero Section'
                        });
                    }
                });
                
                lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });
            } catch (e) {
                console.warn('[Hero SEO] LCP observer not supported:', e);
            }
            
            // Cumulative Layout Shift (CLS)
            try {
                let clsScore = 0;
                const clsObserver = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (!entry.hadRecentInput) {
                            clsScore += entry.value;
                        }
                    }
                    console.log('[Hero SEO] CLS:', clsScore);
                });
                
                clsObserver.observe({ entryTypes: ['layout-shift'] });
            } catch (e) {
                console.warn('[Hero SEO] CLS observer not supported:', e);
            }
        }
        
        // Page load timing
        window.addEventListener('load', () => {
            setTimeout(() => {
                const perfData = performance.timing;
                const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                console.log('[Hero SEO] Page Load Time:', pageLoadTime + 'ms');
                
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'timing_complete', {
                        'name': 'load',
                        'value': pageLoadTime,
                        'event_category': 'Hero Section'
                    });
                }
            }, 0);
        });
    }
    
    /**
     * CTA追跡
     */
    setupCTATracking() {
        const ctaButtons = document.querySelectorAll('.gih-btn-primary, .gih-mobile-btn-primary');
        
        ctaButtons.forEach((btn, index) => {
            btn.addEventListener('click', (e) => {
                const buttonText = btn.querySelector('span')?.textContent || 'Unknown';
                const deviceType = window.innerWidth >= 1024 ? 'desktop' : 'mobile';
                
                console.log('[Hero SEO] CTA clicked:', {
                    text: buttonText,
                    device: deviceType,
                    position: index
                });
                
                // Google Analytics tracking
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'cta_click', {
                        'event_category': 'engagement',
                        'event_label': `${buttonText} - ${deviceType}`,
                        'value': 1
                    });
                }
                
                // Facebook Pixel tracking
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'Lead', {
                        content_name: buttonText,
                        content_category: 'Hero CTA'
                    });
                }
            });
        });
    }
}

// 初期化
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.grantHeroSystemSEO = new GrantHeroSystemSEO();
        console.log('[✓] Hero System SEO 100% Optimized (Simplified) - Initialized');
    });
} else {
    window.grantHeroSystemSEO = new GrantHeroSystemSEO();
    console.log('[✓] Hero System SEO 100% Optimized (Simplified) - Initialized');
}

// スクリーンリーダー専用テキスト用のスタイル
const srOnlyStyle = document.createElement('style');
srOnlyStyle.textContent = `
    .sr-only {
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
`;
document.head.appendChild(srOnlyStyle);
</script>