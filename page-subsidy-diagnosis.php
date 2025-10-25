<?php
/**
 * Template Name: 補助金診断ページ
 * Description: サンドボックスアプリケーションを埋め込んだ補助金診断ページ - SEO最適化版
 */

// SEO Meta情報
$page_title = '【2025年最新】AI補助金診断システム | あなたに最適な補助金を1分で診断';
$page_description = '業種・地域・目的に合わせてAIが最適な補助金を診断。3,809件のデータベースから補助金情報を無料で検索。中小企業・個人事業主向け。';
$page_keywords = '補助金診断,助成金診断,AI診断,補助金検索,助成金検索,中小企業支援,個人事業主,ものづくり補助金,IT導入補助金';
$canonical_url = home_url('/subsidy-diagnosis/');
$og_image = get_template_directory_uri() . '/assets/images/subsidy-diagnosis-og.png';

get_header(); 
?>

<!-- SEO最適化 メタタグ -->
<meta name="description" content="<?php echo esc_attr($page_description); ?>">
<meta name="keywords" content="<?php echo esc_attr($page_keywords); ?>">
<link rel="canonical" href="<?php echo esc_url($canonical_url); ?>">

<!-- Open Graph -->
<meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
<meta property="og:description" content="<?php echo esc_attr($page_description); ?>">
<meta property="og:url" content="<?php echo esc_url($canonical_url); ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?php echo esc_url($og_image); ?>">
<meta property="og:site_name" content="助成金・補助金インサイト">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($page_description); ?>">
<meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">

<!-- 構造化データ -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "AI補助金診断システム",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Any",
    "description": "<?php echo esc_js($page_description); ?>",
    "url": "<?php echo esc_url($canonical_url); ?>",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "JPY"
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "1250"
    },
    "author": {
        "@type": "Organization",
        "name": "助成金・補助金インサイト",
        "url": "<?php echo esc_url(home_url('/')); ?>"
    }
}
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "ホーム",
            "item": "<?php echo esc_url(home_url('/')); ?>"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "補助金診断",
            "item": "<?php echo esc_url($canonical_url); ?>"
        }
    ]
}
</script>

<style>
/* 既存デザインシステムの変数を継承 */
:root {
    --diagnosis-primary: var(--color-black, #000);
    --diagnosis-accent: #ffeb3b;
    --diagnosis-bg: var(--color-white, #fff);
    --diagnosis-surface: var(--color-gray-100, #f5f5f5);
    --diagnosis-text: var(--text-primary, #0a0a0a);
    --diagnosis-border: var(--border-medium, rgba(0,0,0,.12));
    --diagnosis-shadow: var(--shadow-lg, 0 8px 16px rgba(0,0,0,.08));
    --diagnosis-radius: var(--radius-xl, 8px);
    --diagnosis-transition: var(--transition-base, .25s cubic-bezier(.4,0,.2,1));
}

/* メインコンテナ */
.diagnosis-page-wrapper {
    position: relative;
    min-height: 100vh;
    background: var(--diagnosis-bg);
    padding-top: 120px;
    font-family: var(--font-secondary, 'Inter', -apple-system, BlinkMacSystemFont, sans-serif);
}

/* ヒーローセクション */
.diagnosis-hero {
    position: relative;
    padding: 60px 0;
    background: linear-gradient(135deg, var(--diagnosis-bg) 0%, var(--diagnosis-surface) 100%);
    border-bottom: 1px solid var(--diagnosis-border);
}

.diagnosis-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(0,0,0,.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,.02) 1px, transparent 1px);
    background-size: 50px 50px;
    pointer-events: none;
    opacity: 0.5;
}

.diagnosis-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 var(--space-6, 1.5rem);
    position: relative;
    z-index: 1;
}

/* バッジ */
.diagnosis-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--diagnosis-primary);
    color: var(--diagnosis-bg);
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
}

.diagnosis-badge-dot {
    width: 7px;
    height: 7px;
    background: var(--diagnosis-accent);
    border-radius: 50%;
    animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.3); }
}

/* タイトルセクション */
.diagnosis-title-section {
    text-align: center;
    margin-bottom: 40px;
}

.diagnosis-main-title {
    font-size: clamp(32px, 5vw, 56px);
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: -.03em;
    margin: 0 0 16px 0;
    color: var(--diagnosis-text);
    font-family: var(--font-primary, 'Outfit', sans-serif);
}

.diagnosis-highlight {
    position: relative;
    color: var(--diagnosis-primary);
}

.diagnosis-highlight::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 12px;
    background: var(--diagnosis-accent);
    z-index: -1;
}

.diagnosis-subtitle {
    font-size: clamp(16px, 2vw, 20px);
    color: var(--text-secondary, #4a4a4a);
    font-weight: 500;
    line-height: 1.6;
    max-width: 800px;
    margin: 0 auto 32px;
}

/* 特徴カード */
.diagnosis-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 48px;
}

.feature-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: var(--diagnosis-bg);
    border: 2px solid var(--diagnosis-primary);
    border-radius: var(--diagnosis-radius);
    transition: all var(--diagnosis-transition);
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--diagnosis-shadow);
    border-color: var(--diagnosis-accent);
}

.feature-icon {
    width: 48px;
    height: 48px;
    background: var(--diagnosis-primary);
    color: var(--diagnosis-bg);
    border-radius: var(--diagnosis-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.feature-content h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 4px 0;
    color: var(--diagnosis-text);
}

.feature-content p {
    font-size: 13px;
    color: var(--text-secondary, #4a4a4a);
    margin: 0;
    line-height: 1.5;
}

/* 診断フレームセクション */
.diagnosis-iframe-section {
    position: relative;
    background: var(--diagnosis-surface);
    border-radius: var(--diagnosis-radius);
    overflow: hidden;
    box-shadow: var(--diagnosis-shadow);
    border: 2px solid var(--diagnosis-primary);
    margin-bottom: 60px;
}

.iframe-header {
    background: var(--diagnosis-primary);
    color: var(--diagnosis-bg);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid var(--diagnosis-primary);
}

.iframe-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.iframe-status-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse-status 2s ease-in-out infinite;
}

@keyframes pulse-status {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.iframe-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    color: var(--diagnosis-bg);
}

.iframe-subtitle {
    font-size: 11px;
    opacity: 0.8;
    margin: 0;
    color: var(--diagnosis-bg);
}

.iframe-actions {
    display: flex;
    gap: 8px;
}

.iframe-action-btn {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--diagnosis-transition);
    color: var(--diagnosis-bg);
}

.iframe-action-btn:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}

/* iframe wrapper */
.iframe-wrapper {
    position: relative;
    width: 100%;
    background: var(--diagnosis-bg);
    min-height: 800px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 10;
    transition: opacity var(--diagnosis-transition);
}

.loading-overlay.hidden {
    opacity: 0;
    pointer-events: none;
}

.loading-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--diagnosis-surface);
    border-top-color: var(--diagnosis-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 16px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    font-size: 14px;
    font-weight: 600;
    color: var(--diagnosis-text);
    margin-top: 8px;
}

.diagnosis-iframe {
    width: 100%;
    height: 100vh;
    min-height: 800px;
    border: none;
    display: block;
    background: var(--diagnosis-bg);
}

/* エラーメッセージ */
.error-message {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: var(--diagnosis-radius);
    color: #856404;
    padding: 20px 24px;
    margin: 20px 0;
    display: none;
    align-items: center;
    gap: 12px;
}

.error-message.show {
    display: flex;
}

.error-icon {
    font-size: 24px;
    flex-shrink: 0;
}

/* 診断について説明セクション */
.diagnosis-info-section {
    background: var(--diagnosis-bg);
    padding: 60px 0;
    border-top: 1px solid var(--diagnosis-border);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 32px;
    margin-bottom: 48px;
}

.info-card {
    background: var(--diagnosis-surface);
    padding: 32px;
    border-radius: var(--diagnosis-radius);
    border: 2px solid transparent;
    transition: all var(--diagnosis-transition);
}

.info-card:hover {
    border-color: var(--diagnosis-primary);
    transform: translateY(-4px);
    box-shadow: var(--diagnosis-shadow);
}

.info-card-icon {
    width: 56px;
    height: 56px;
    background: var(--diagnosis-primary);
    color: var(--diagnosis-bg);
    border-radius: var(--diagnosis-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 20px;
}

.info-card h3 {
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 12px 0;
    color: var(--diagnosis-text);
}

.info-card-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-card-list li {
    padding: 8px 0;
    padding-left: 24px;
    position: relative;
    font-size: 14px;
    color: var(--text-secondary, #4a4a4a);
    line-height: 1.6;
}

.info-card-list li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--diagnosis-accent);
    font-weight: bold;
    font-size: 16px;
}

/* CTA セクション */
.diagnosis-cta-section {
    background: var(--diagnosis-primary);
    color: var(--diagnosis-bg);
    padding: 48px 32px;
    border-radius: var(--diagnosis-radius);
    text-align: center;
    margin-top: 48px;
}

.diagnosis-cta-section h3 {
    font-size: clamp(24px, 4vw, 32px);
    font-weight: 900;
    margin: 0 0 16px 0;
    color: var(--diagnosis-bg);
}

.diagnosis-cta-section p {
    font-size: 16px;
    opacity: 0.9;
    margin: 0 0 24px 0;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    color: var(--diagnosis-bg);
}

.diagnosis-cta-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    justify-content: center;
}

.diagnosis-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 16px 32px;
    border-radius: var(--diagnosis-radius);
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    transition: all var(--diagnosis-transition);
    cursor: pointer;
    border: 2px solid transparent;
}

.diagnosis-btn-primary {
    background: var(--diagnosis-accent);
    color: var(--diagnosis-primary);
    border-color: var(--diagnosis-accent);
}

.diagnosis-btn-primary:hover {
    background: #ffc107;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,235,59,.4);
}

.diagnosis-btn-secondary {
    background: transparent;
    color: var(--diagnosis-bg);
    border-color: var(--diagnosis-bg);
}

.diagnosis-btn-secondary:hover {
    background: var(--diagnosis-bg);
    color: var(--diagnosis-primary);
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .diagnosis-page-wrapper {
        padding-top: 80px;
    }
    
    .diagnosis-hero {
        padding: 40px 0;
    }
    
    .diagnosis-container {
        padding: 0 var(--space-4, 1rem);
    }
    
    .diagnosis-features {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .feature-card {
        padding: 16px;
    }
    
    .diagnosis-iframe {
        min-height: 600px;
        height: 80vh;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .info-card {
        padding: 24px;
    }
    
    .diagnosis-cta-section {
        padding: 32px 20px;
    }
    
    .diagnosis-cta-buttons {
        flex-direction: column;
        align-items: stretch;
    }
    
    .diagnosis-btn {
        justify-content: center;
    }
}

@media (max-width: 640px) {
    .diagnosis-main-title {
        font-size: 28px;
    }
    
    .diagnosis-subtitle {
        font-size: 14px;
    }
    
    .feature-icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    
    .feature-content h3 {
        font-size: 14px;
    }
    
    .feature-content p {
        font-size: 12px;
    }
    
    .iframe-header {
        padding: 12px 16px;
    }
    
    .iframe-title {
        font-size: 12px;
    }
    
    .iframe-actions {
        display: none;
    }
}

/* パフォーマンス最適化 */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* アクセシビリティ */
.diagnosis-btn:focus-visible,
.iframe-action-btn:focus-visible {
    outline: 3px solid var(--diagnosis-accent);
    outline-offset: 2px;
}

/* 印刷対応 */
@media print {
    .diagnosis-hero,
    .diagnosis-cta-section,
    .iframe-header,
    .loading-overlay {
        display: none !important;
    }
    
    .diagnosis-iframe-section {
        border: none;
        box-shadow: none;
    }
}
</style>

<main class="diagnosis-page-wrapper">
    <!-- パンくずリスト -->
    <div class="diagnosis-container">
        <nav class="breadcrumb" aria-label="パンくずリスト" style="padding: 16px 0; font-size: 13px; color: var(--text-tertiary, #8a8a8a);">
            <a href="<?php echo esc_url(home_url('/')); ?>" style="color: inherit; text-decoration: none;">ホーム</a>
            <span style="margin: 0 8px;">/</span>
            <span style="color: var(--diagnosis-text);">補助金診断</span>
        </nav>
    </div>

    <!-- ヒーローセクション -->
    <section class="diagnosis-hero">
        <div class="diagnosis-container">
            <div class="diagnosis-title-section">
                <div class="diagnosis-badge">
                    <div class="diagnosis-badge-dot"></div>
                    <span>AI DIAGNOSIS SYSTEM</span>
                </div>
                
                <h1 class="diagnosis-main-title">
                    <span class="diagnosis-highlight">1分で診断</span><br>
                    あなたに最適な補助金を発見
                </h1>
                
                <p class="diagnosis-subtitle">
                    業種・地域・目的を選ぶだけで、AIが3,809件のデータベースから最適な補助金を自動診断。
                </p>
            </div>
            
            <!-- 特徴カード -->
            <div class="diagnosis-features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="feature-content">
                        <h3>1分で診断完了</h3>
                        <p>簡単な質問に答えるだけで即座に結果表示</p>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="feature-content">
                        <h3>3,809件対応</h3>
                        <p>全国の補助金・助成金情報を網羅</p>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="feature-content">
                        <h3>AI自動マッチング</h3>
                        <p>最新AIが最適な制度を自動選定</p>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h3>完全無料</h3>
                        <p>登録不要で何度でも利用可能</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- エラーメッセージ -->
    <div class="diagnosis-container">
        <div class="error-message" id="errorMessage">
            <i class="fas fa-exclamation-triangle error-icon"></i>
            <div>
                <strong>注意:</strong> 診断システムの読み込みに問題が発生しました。ページを再読み込みしてください。
            </div>
        </div>
    </div>
    
    <!-- 診断システム埋め込みセクション -->
    <div class="diagnosis-container">
        <section class="diagnosis-iframe-section" role="application" aria-label="補助金診断システム">
            <!-- iframe ヘッダー -->
            <div class="iframe-header">
                <div class="iframe-header-left">
                    <div class="iframe-status-dot"></div>
                    <div>
                        <h2 class="iframe-title">補助金AI診断システム</h2>
                        <p class="iframe-subtitle">オンライン</p>
                    </div>
                </div>
                <div class="iframe-actions">
                    <button type="button" class="iframe-action-btn" onclick="reloadDiagnosis()" title="再読み込み" aria-label="診断システムを再読み込み">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="iframe-action-btn" onclick="openFullscreen()" title="全画面表示" aria-label="全画面で表示">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
            
            <!-- iframe wrapper -->
            <div class="iframe-wrapper">
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">診断システムを読み込んでいます...</div>
                </div>
                
                <iframe 
                    id="subsidyDiagnosisIframe"
                    class="diagnosis-iframe"
                    src="https://3000-ik18nppmde8rkxw7kbggl-0e616f0a.sandbox.novita.ai/"
                    title="補助金診断システム - あなたに最適な補助金を1分で診断"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-modals allow-popups-to-escape-sandbox"
                    loading="eager"
                    importance="high"
                    onload="handleIframeLoad()"
                    onerror="handleIframeError()">
                </iframe>
            </div>
        </section>
    </div>
    
    <!-- 診断システムについて -->
    <section class="diagnosis-info-section">
        <div class="diagnosis-container">
            <div class="diagnosis-title-section">
                <h2 class="diagnosis-main-title" style="font-size: clamp(28px, 4vw, 40px);">
                    補助金診断システムについて
                </h2>
                <p class="diagnosis-subtitle">
                    AIを活用した次世代の補助金検索・診断システムです
                </p>
            </div>
            
            <div class="info-grid">
                <!-- 診断の流れ -->
                <article class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>診断の流れ</h3>
                    <ul class="info-card-list">
                        <li>業種や企業規模などの基本情報を入力</li>
                        <li>事業の目的や課題を選択</li>
                        <li>AIが最適な補助金を自動選定</li>
                        <li>詳細情報の確認と申請サポート</li>
                    </ul>
                </article>
                
                <!-- 対応している補助金 -->
                <article class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <h3>対応している補助金</h3>
                    <ul class="info-card-list">
                        <li>ものづくり補助金</li>
                        <li>IT導入補助金</li>
                        <li>小規模事業者持続化補助金</li>
                        <li>事業再構築補助金</li>
                        <li>各都道府県・市町村の独自補助金</li>
                    </ul>
                </article>
                
                <!-- ご利用上の注意 -->
                <article class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3>ご利用上の注意</h3>
                    <ul class="info-card-list">
                        <li>診断結果は参考情報です</li>
                        <li>最終的な申請可否は各機関にご確認ください</li>
                        <li>募集状況は随時変更される可能性があります</li>
                        <li>個人情報は適切に管理されます</li>
                    </ul>
                </article>
            </div>
            
            <!-- CTA -->
            <div class="diagnosis-cta-section">
                <h3>まだ診断していない方へ</h3>
                <p>今すぐ無料診断を始めて、あなたのビジネスに最適な補助金を見つけましょう</p>
                <div class="diagnosis-cta-buttons">
                    <button type="button" onclick="scrollToDiagnosis()" class="diagnosis-btn diagnosis-btn-primary">
                        <i class="fas fa-arrow-up"></i>
                        <span>診断を開始する</span>
                    </button>
                    <a href="<?php echo esc_url(home_url('/grants/')); ?>" class="diagnosis-btn diagnosis-btn-secondary">
                        <i class="fas fa-search"></i>
                        <span>補助金一覧を見る</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
(function() {
    'use strict';
    
    let iframeLoadAttempts = 0;
    const MAX_LOAD_ATTEMPTS = 3;
    const LOAD_TIMEOUT = 15000;
    
    window.handleIframeLoad = function() {
        console.log('✅ 診断システム読み込み完了');
        setTimeout(function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.classList.add('hidden');
        }, 500);
        
        if (typeof gtag !== 'undefined') {
            gtag('event', 'diagnosis_system_loaded', {
                'event_category': 'engagement',
                'event_label': 'Diagnosis System'
            });
        }
    };
    
    window.handleIframeError = function() {
        console.error('❌ 診断システム読み込みエラー');
        const overlay = document.getElementById('loadingOverlay');
        const errorMsg = document.getElementById('errorMessage');
        
        if (overlay) overlay.classList.add('hidden');
        if (errorMsg) errorMsg.classList.add('show');
        
        if (iframeLoadAttempts < MAX_LOAD_ATTEMPTS) {
            iframeLoadAttempts++;
            setTimeout(reloadDiagnosis, 3000);
        }
        
        if (typeof gtag !== 'undefined') {
            gtag('event', 'diagnosis_system_error', {
                'event_category': 'error',
                'event_label': 'Load Failed',
                'value': iframeLoadAttempts
            });
        }
    };
    
    window.reloadDiagnosis = function() {
        const iframe = document.getElementById('subsidyDiagnosisIframe');
        const overlay = document.getElementById('loadingOverlay');
        const errorMsg = document.getElementById('errorMessage');
        
        if (errorMsg) errorMsg.classList.remove('show');
        if (overlay) overlay.classList.remove('hidden');
        
        if (iframe) {
            const currentSrc = iframe.src;
            iframe.src = '';
            setTimeout(function() { iframe.src = currentSrc; }, 100);
        }
    };
    
    window.openFullscreen = function() {
        const iframe = document.getElementById('subsidyDiagnosisIframe');
        if (!iframe) return;
        
        if (iframe.requestFullscreen) {
            iframe.requestFullscreen();
        } else if (iframe.webkitRequestFullscreen) {
            iframe.webkitRequestFullscreen();
        } else if (iframe.mozRequestFullScreen) {
            iframe.mozRequestFullScreen();
        } else if (iframe.msRequestFullscreen) {
            iframe.msRequestFullscreen();
        }
        
        if (typeof gtag !== 'undefined') {
            gtag('event', 'fullscreen_opened', {
                'event_category': 'engagement',
                'event_label': 'Diagnosis Fullscreen'
            });
        }
    };
    
    window.scrollToDiagnosis = function() {
        const iframe = document.getElementById('subsidyDiagnosisIframe');
        if (iframe) {
            iframe.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        if (typeof gtag !== 'undefined') {
            gtag('event', 'scroll_to_diagnosis', {
                'event_category': 'engagement',
                'event_label': 'CTA Button'
            });
        }
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 補助金診断ページ初期化');
        
        setTimeout(function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay && !overlay.classList.contains('hidden')) {
                overlay.classList.add('hidden');
                const errorMsg = document.getElementById('errorMessage');
                if (errorMsg) {
                    errorMsg.textContent = '診断システムの読み込みに時間がかかっています。';
                    errorMsg.classList.add('show');
                }
            }
        }, LOAD_TIMEOUT);
        
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_view', {
                'page_title': '<?php echo esc_js($page_title); ?>',
                'page_location': '<?php echo esc_url($canonical_url); ?>'
            });
        }
    });
})();
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "補助金診断システムは無料で使えますか？",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "はい、完全無料でご利用いただけます。登録不要で何度でも診断可能です。"
            }
        },
        {
            "@type": "Question",
            "name": "診断にはどのくらい時間がかかりますか？",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "約1分で診断が完了します。業種や目的などの簡単な質問に答えるだけで、AIが最適な補助金を自動的に選定します。"
            }
        }
    ]
}
</script>

<?php get_footer(); ?>