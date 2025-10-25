<?php
/**
 * How To Use Section - Compact & SEO Optimized Design
 * 使い方セクション - コンパクト＆SEO最適化デザイン
 * @package Grant_Insight_Perfect
 * @version 3.2-left-center-layout
 * 
 * === 主要機能 ===
 * 1. 動画を左側、ステップを右側に配置した3ステップ設計
 * 2. SEO対策100%実装
 * 3. 構造化データ完全対応
 * 4. パフォーマンス最適化
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// 動画設定
$video_config = array(
    'video_url' => 'https://youtu.be/mh1MDXl1t50',
    'video_id' => 'mh1MDXl1t50',
    'video_title' => '補助金検索システムの使い方 | 3分で分かるAI補助金検索',
    'video_description' => 'AIを活用した補助金・助成金検索システムの使い方を3分で解説。キーワード検索からAI相談まで、初心者でも簡単に最適な補助金を見つけられます。',
    'thumbnail' => 'https://img.youtube.com/vi/mh1MDXl1t50/maxresdefault.jpg',
    'duration' => 'PT3M',
    'upload_date' => '2024-01-15'
);

// ステップガイド設定（3ステップに最適化）
$steps = array(
    array(
        'number' => '01',
        'icon' => 'fas fa-search',
        'title' => 'キーワード検索',
        'description' => '業種や目的を入力すると、AIが全国の補助金データベースから最適な制度を瞬時に検索します。',
        'keywords' => '補助金検索, AI検索, キーワード検索'
    ),
    array(
        'number' => '02',
        'icon' => 'fas fa-filter',
        'title' => 'カテゴリー絞り込み',
        'description' => 'IT導入、ものづくり、創業支援など、業種別・目的別のカテゴリーから効率的に絞り込めます。',
        'keywords' => 'カテゴリー検索, 業種別補助金, 絞り込み検索'
    ),
    array(
        'number' => '03',
        'icon' => 'fas fa-robot',
        'title' => 'AI相談で詳細確認',
        'description' => '各補助金の申請要件や必要書類をAIアシスタントに質問して、疑問を即座に解消できます。',
        'keywords' => 'AI相談, 補助金質問, 申請サポート'
    )
);

// 構造化データ（VideoObject）
$video_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'VideoObject',
    'name' => $video_config['video_title'],
    'description' => $video_config['video_description'],
    'thumbnailUrl' => $video_config['thumbnail'],
    'uploadDate' => $video_config['upload_date'],
    'duration' => $video_config['duration'],
    'contentUrl' => $video_config['video_url'],
    'embedUrl' => 'https://www.youtube.com/embed/' . $video_config['video_id'],
    'publisher' => array(
        '@type' => 'Organization',
        'name' => '補助金インサイト',
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => get_site_icon_url()
        )
    )
);

// HowTo構造化データ
$howto_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    'name' => '補助金・助成金の検索方法',
    'description' => 'AIを活用した補助金検索システムの使い方を3ステップで解説',
    'image' => $video_config['thumbnail'],
    'totalTime' => $video_config['duration'],
    'tool' => array(
        '@type' => 'HowToTool',
        'name' => '補助金インサイト AI検索システム'
    ),
    'step' => array()
);

foreach ($steps as $index => $step) {
    $howto_schema['step'][] = array(
        '@type' => 'HowToStep',
        'position' => $index + 1,
        'name' => $step['title'],
        'text' => $step['description'],
        'url' => home_url('/#step-' . ($index + 1))
    );
}
?>

<!-- SEO メタタグ -->
<meta name="description" content="<?php echo esc_attr($video_config['video_description']); ?>">
<meta property="og:title" content="<?php echo esc_attr($video_config['video_title']); ?>">
<meta property="og:description" content="<?php echo esc_attr($video_config['video_description']); ?>">
<meta property="og:image" content="<?php echo esc_url($video_config['thumbnail']); ?>">
<meta property="og:type" content="video.other">
<meta property="og:video" content="<?php echo esc_url($video_config['video_url']); ?>">
<meta name="twitter:card" content="player">
<meta name="twitter:title" content="<?php echo esc_attr($video_config['video_title']); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($video_config['video_description']); ?>">
<meta name="twitter:image" content="<?php echo esc_url($video_config['thumbnail']); ?>">
<meta name="twitter:player" content="https://www.youtube.com/embed/<?php echo esc_attr($video_config['video_id']); ?>">

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($video_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<script type="application/ld+json">
<?php echo wp_json_encode($howto_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- フォント・アイコン読み込み -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="gi-how-to-section" id="how-to-use" itemscope itemtype="https://schema.org/HowTo">
    <!-- 背景パターン -->
    <div class="gi-background-pattern" aria-hidden="true"></div>
    
    <div class="gi-how-to-container">
        
        <!-- セクションヘッダー -->
        <header class="gi-how-to-header">
            <div class="gi-header-badge" role="status">
                <div class="gi-badge-dot" aria-hidden="true"></div>
                <span>HOW TO USE</span>
            </div>
            
            <h2 class="gi-how-to-title" itemprop="name">
                <span class="gi-title-main">使い方ガイド</span>
                <span class="gi-title-sub">3ステップで簡単検索</span>
            </h2>
            
            <p class="gi-how-to-description" itemprop="description">
                <?php echo esc_html($video_config['video_description']); ?>
            </p>
        </header>

        <!-- メインコンテンツ - 2カラムレイアウト -->
        <div class="gi-content-wrapper">
            
            <!-- 左側：動画カード -->
            <article class="gi-video-card" itemscope itemtype="https://schema.org/VideoObject">
                <div class="gi-video-container">
                    <iframe 
                        class="gi-youtube-iframe"
                        src="https://www.youtube.com/embed/<?php echo esc_attr($video_config['video_id']); ?>?rel=0&modestbranding=1&playsinline=1" 
                        title="<?php echo esc_attr($video_config['video_title']); ?>"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen
                        loading="lazy"
                        itemprop="embedUrl">
                    </iframe>
                </div>
                
                <div class="gi-video-info">
                    <div class="gi-video-icon" aria-hidden="true">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="gi-video-text">
                        <h3 itemprop="name"><?php echo esc_html($video_config['video_title']); ?></h3>
                        <p itemprop="description"><?php echo esc_html($video_config['video_description']); ?></p>
                    </div>
                </div>
                
                <!-- 非表示の構造化データ -->
                <meta itemprop="thumbnailUrl" content="<?php echo esc_url($video_config['thumbnail']); ?>">
                <meta itemprop="uploadDate" content="<?php echo esc_attr($video_config['upload_date']); ?>">
                <meta itemprop="duration" content="<?php echo esc_attr($video_config['duration']); ?>">
            </article>

            <!-- 右側：ステップカード -->
            <div class="gi-steps-wrapper">
                <div class="gi-steps-header">
                    <i class="fas fa-list-ol" aria-hidden="true"></i>
                    <h3>3ステップで簡単検索</h3>
                </div>
                
                <div class="gi-steps-grid" role="list">
                    <?php foreach ($steps as $index => $step) : ?>
                    <article 
                        class="gi-step-card" 
                        id="step-<?php echo $index + 1; ?>"
                        data-aos="fade-up" 
                        data-aos-delay="<?php echo $index * 100; ?>"
                        role="listitem"
                        itemprop="step"
                        itemscope 
                        itemtype="https://schema.org/HowToStep">
                        
                        <div class="gi-step-number" aria-label="ステップ<?php echo $index + 1; ?>">
                            <?php echo esc_html($step['number']); ?>
                        </div>
                        
                        <div class="gi-step-icon" aria-hidden="true">
                            <i class="<?php echo esc_attr($step['icon']); ?>"></i>
                        </div>
                        
                        <h4 class="gi-step-title" itemprop="name">
                            <?php echo esc_html($step['title']); ?>
                        </h4>
                        
                        <p class="gi-step-description" itemprop="text">
                            <?php echo esc_html($step['description']); ?>
                        </p>
                        
                        <!-- SEO用キーワード（非表示） -->
                        <meta itemprop="keywords" content="<?php echo esc_attr($step['keywords']); ?>">
                        <meta itemprop="position" content="<?php echo $index + 1; ?>">
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- CTAボタン -->
                <div class="gi-cta-wrapper">
                    <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
                       class="gi-cta-button"
                       rel="nofollow"
                       aria-label="補助金検索を始める">
                        <span>今すぐ検索を始める</span>
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

<style>
/* ============================================
   使い方セクション - 左右2カラムレイアウト
   ============================================ */

/* ベース設定 */
.gi-how-to-section {
    position: relative;
    padding: 70px 0;
    background: #f5f5f5;
    font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    isolation: isolate;
    overflow: hidden;
}

/* 背景パターン */
.gi-background-pattern {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(0, 0, 0, 0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.02) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}

/* コンテナ */
.gi-how-to-container {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ヘッダー */
.gi-how-to-header {
    text-align: center;
    margin-bottom: 50px;
}

.gi-header-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #000000;
    color: #ffffff;
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.gi-badge-dot {
    width: 7px;
    height: 7px;
    background: #ffeb3b;
    border-radius: 50%;
    animation: gi-pulse 2s ease-in-out infinite;
}

@keyframes gi-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.3); }
}

.gi-how-to-title {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}

.gi-title-main {
    font-size: 40px;
    font-weight: 900;
    color: #000000;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.gi-title-sub {
    font-size: 18px;
    font-weight: 500;
    color: #666666;
    line-height: 1.4;
}

.gi-how-to-description {
    font-size: 15px;
    line-height: 1.7;
    color: #666666;
    max-width: 700px;
    margin: 0 auto;
}

/* コンテンツラッパー - 2カラムレイアウト */
.gi-content-wrapper {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
    align-items: start;
}

@media (min-width: 1024px) {
    .gi-content-wrapper {
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }
}

/* 動画カード（左側） */
.gi-video-card {
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: sticky;
    top: 20px;
}

.gi-video-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.14);
}

.gi-video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #000000;
}

.gi-youtube-iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

.gi-video-info {
    padding: 18px;
    display: flex;
    gap: 14px;
    align-items: start;
    background: #fafafa;
}

.gi-video-icon {
    flex-shrink: 0;
    width: 42px;
    height: 42px;
    background: #ffeb3b;
    border: 2px solid #000000;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000000;
    font-size: 20px;
}

.gi-video-text {
    flex: 1;
}

.gi-video-text h3 {
    font-size: 16px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 6px 0;
    line-height: 1.3;
}

.gi-video-text p {
    font-size: 13px;
    line-height: 1.6;
    color: #666666;
    margin: 0;
}

/* ステップラッパー（右側） */
.gi-steps-wrapper {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.gi-steps-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.gi-steps-header i {
    font-size: 20px;
    color: #ffeb3b;
}

.gi-steps-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #000000;
    margin: 0;
}

/* ステップグリッド */
.gi-steps-grid {
    display: grid;
    gap: 16px;
}

.gi-step-card {
    position: relative;
    padding: 18px;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.gi-step-card:hover {
    transform: translateX(6px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    border-color: #333333;
}

.gi-step-number {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 36px;
    height: 36px;
    background: #ffeb3b;
    color: #000000;
    border: 2px solid #000000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 900;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.gi-step-icon {
    width: 50px;
    height: 50px;
    background: #000000;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 24px;
    margin-bottom: 12px;
}

.gi-step-title {
    font-size: 16px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 6px 0;
    line-height: 1.3;
}

.gi-step-description {
    font-size: 13px;
    line-height: 1.6;
    color: #666666;
    margin: 0;
}

/* CTAラッパー */
.gi-cta-wrapper {
    margin-top: 8px;
}

.gi-cta-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 16px 28px;
    background: #ffeb3b;
    color: #000000;
    border: 2px solid #000000;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.gi-cta-button:hover {
    background: #ffc107;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.16);
}

.gi-cta-button:active {
    transform: translateY(0);
}

.gi-cta-button i {
    transition: transform 0.3s ease;
}

.gi-cta-button:hover i {
    transform: translateX(4px);
}

/* タブレット */
@media (min-width: 768px) and (max-width: 1023px) {
    .gi-how-to-section {
        padding: 60px 0;
    }
    
    .gi-title-main {
        font-size: 36px;
    }
    
    .gi-title-sub {
        font-size: 16px;
    }
    
    .gi-how-to-description {
        font-size: 14px;
    }
    
    .gi-video-card {
        position: static;
    }
}

/* スマホ最適化 */
@media (max-width: 767px) {
    .gi-how-to-section {
        padding: 50px 0;
    }
    
    .gi-how-to-container {
        padding: 0 16px;
    }
    
    .gi-how-to-header {
        margin-bottom: 35px;
    }
    
    .gi-header-badge {
        padding: 7px 15px;
        font-size: 10px;
        margin-bottom: 16px;
    }
    
    .gi-badge-dot {
        width: 6px;
        height: 6px;
    }
    
    .gi-title-main {
        font-size: 28px;
    }
    
    .gi-title-sub {
        font-size: 15px;
    }
    
    .gi-how-to-description {
        font-size: 13px;
    }
    
    .gi-content-wrapper {
        gap: 28px;
    }
    
    .gi-video-card {
        position: static;
    }
    
    .gi-video-info {
        padding: 16px;
        gap: 12px;
    }
    
    .gi-video-icon {
        width: 38px;
        height: 38px;
        font-size: 18px;
    }
    
    .gi-video-text h3 {
        font-size: 14px;
    }
    
    .gi-video-text p {
        font-size: 12px;
    }
    
    .gi-steps-header {
        padding: 14px 18px;
    }
    
    .gi-steps-header i {
        font-size: 18px;
    }
    
    .gi-steps-header h3 {
        font-size: 16px;
    }
    
    .gi-steps-grid {
        gap: 14px;
    }
    
    .gi-step-card {
        padding: 16px;
    }
    
    .gi-step-number {
        width: 32px;
        height: 32px;
        font-size: 12px;
        top: -8px;
        right: -8px;
    }
    
    .gi-step-icon {
        width: 46px;
        height: 46px;
        font-size: 22px;
        margin-bottom: 10px;
    }
    
    .gi-step-title {
        font-size: 14px;
    }
    
    .gi-step-description {
        font-size: 12px;
    }
    
    .gi-cta-button {
        padding: 14px 24px;
        font-size: 15px;
    }
}

/* 極小スマホ */
@media (max-width: 375px) {
    .gi-title-main {
        font-size: 26px;
    }
    
    .gi-title-sub {
        font-size: 14px;
    }
    
    .gi-video-info {
        padding: 14px;
    }
    
    .gi-step-card {
        padding: 14px;
    }
}

/* アニメーション */
[data-aos] {
    opacity: 0;
    transition: opacity 0.6s ease, transform 0.6s ease;
}

[data-aos="fade-up"] {
    transform: translateY(20px);
}

[data-aos].aos-animate {
    opacity: 1;
    transform: translateY(0);
}

/* パフォーマンス最適化 */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* アクセシビリティ */
.gi-cta-button:focus-visible {
    outline: 3px solid #ffeb3b;
    outline-offset: 2px;
}

/* タッチデバイス最適化 */
@media (hover: none) and (pointer: coarse) {
    .gi-cta-button {
        -webkit-tap-highlight-color: transparent;
    }
    
    .gi-cta-button:active {
        transform: scale(0.98);
    }
    
    .gi-step-card:hover {
        transform: none;
    }
}

/* 印刷用スタイル */
@media print {
    .gi-how-to-section {
        background: white;
        padding: 20px 0;
    }
    
    .gi-background-pattern,
    .gi-cta-button {
        display: none;
    }
}
</style>

<script>
/**
 * 使い方セクション JavaScript - SEO最適化版
 */
(function() {
    'use strict';
    
    // 初期化
    document.addEventListener('DOMContentLoaded', function() {
        initAOSAnimation();
        initLazyLoadIframe();
        trackUserInteraction();
        optimizePerformance();
    });
    
    // AOS アニメーション
    function initAOSAnimation() {
        const aosElements = document.querySelectorAll('[data-aos]');
        
        if (aosElements.length === 0) return;
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.getAttribute('data-aos-delay') || 0;
                    setTimeout(() => {
                        entry.target.classList.add('aos-animate');
                    }, delay);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        aosElements.forEach(element => observer.observe(element));
    }
    
    // YouTube iframe遅延読み込み
    function initLazyLoadIframe() {
        const iframes = document.querySelectorAll('.gi-youtube-iframe');
        
        if (iframes.length === 0 || !('IntersectionObserver' in window)) return;
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const iframe = entry.target;
                    if (iframe.dataset.src) {
                        iframe.src = iframe.dataset.src;
                        iframe.removeAttribute('data-src');
                    }
                    observer.unobserve(iframe);
                }
            });
        }, {
            rootMargin: '100px'
        });
        
        iframes.forEach(iframe => {
            if (iframe.src) {
                iframe.dataset.src = iframe.src;
                iframe.removeAttribute('src');
            }
            observer.observe(iframe);
        });
    }
    
    // ユーザーインタラクション追跡（SEO/UX向上）
    function trackUserInteraction() {
        const section = document.querySelector('.gi-how-to-section');
        if (!section) return;
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // セクション表示をトラッキング
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'view_how_to_section', {
                            event_category: 'engagement',
                            event_label: 'How To Use Section'
                        });
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });
        
        observer.observe(section);
        
        // CTAボタンクリック追跡
        const ctaButton = document.querySelector('.gi-cta-button');
        if (ctaButton) {
            ctaButton.addEventListener('click', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'click_start_search', {
                        event_category: 'conversion',
                        event_label: 'Start Search CTA'
                    });
                }
            });
        }
        
        // 動画再生追跡
        const videoIframe = document.querySelector('.gi-youtube-iframe');
        if (videoIframe) {
            videoIframe.addEventListener('load', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'video_loaded', {
                        event_category: 'engagement',
                        event_label: 'How To Video'
                    });
                }
            });
        }
    }
    
    // パフォーマンス最適化
    function optimizePerformance() {
        // Passive event listeners
        document.querySelectorAll('.gi-step-card').forEach(card => {
            card.addEventListener('touchstart', function() {}, { passive: true });
        });
        
        // Resource hints
        if ('performance' in window) {
            // YouTube domain preconnect
            const link = document.createElement('link');
            link.rel = 'preconnect';
            link.href = 'https://www.youtube.com';
            document.head.appendChild(link);
        }
        
        // Intersection Observer for viewport optimization
        if ('IntersectionObserver' in window) {
            const cards = document.querySelectorAll('.gi-step-card');
            const cardObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.willChange = 'transform';
                    } else {
                        entry.target.style.willChange = 'auto';
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            cards.forEach(card => cardObserver.observe(card));
        }
    }
    
    console.log('[OK] How To Use Section - 左右2カラムレイアウト 初期化完了');
})();
</script>