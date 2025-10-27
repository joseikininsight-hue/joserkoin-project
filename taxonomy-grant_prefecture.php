<?php
/**
 * 都道府県別助成金アーカイブページ - Complete Edition v5.0
 * 
 * SEO 100% + 完全デザイン + 全機能統合
 * 
 * @package Grant_Insight_Portal
 * @version 5.0.0
 */

get_header();

// 現在の都道府県情報を取得
$current_prefecture = get_queried_object();
$prefecture_name = $current_prefecture->name;
$prefecture_slug = $current_prefecture->slug;
$prefecture_description = $current_prefecture->description;
$prefecture_count = $current_prefecture->count;

// SEO用データ
$current_year = date('Y');
$page_title = $prefecture_name . 'の助成金・補助金一覧｜' . $current_year . '年度最新情報';
$page_description = $prefecture_name . 'で利用できる助成金・補助金情報を' . $prefecture_count . '件掲載中。地域特有の制度から国の制度まで、' . $current_year . '年度の最新情報をお届けします。';
$canonical_url = get_term_link($current_prefecture);
$og_image = get_template_directory_uri() . '/assets/images/og-grant-prefecture.jpg';

// カテゴリーデータ
$all_categories = get_terms([
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC'
]);

// 都道府県データ
$prefectures = gi_get_all_prefectures();
$current_region = '';
$related_prefectures = [];

// 現在の都道府県の地域を特定
foreach ($prefectures as $pref) {
    if ($pref['slug'] === $prefecture_slug) {
        $current_region = $pref['region'];
        break;
    }
}

// 同じ地域の他の都道府県を取得
if ($current_region) {
    foreach ($prefectures as $pref) {
        if ($pref['region'] === $current_region && $pref['slug'] !== $prefecture_slug) {
            $related_prefectures[] = $pref;
        }
    }
}

// 地域名マッピング
$region_names = [
    'hokkaido' => '北海道',
    'tohoku' => '東北',
    'kanto' => '関東',
    'chubu' => '中部',
    'kinki' => '近畿',
    'chugoku' => '中国',
    'shikoku' => '四国',
    'kyushu' => '九州・沖縄'
];

$region_display_name = isset($region_names[$current_region]) ? $region_names[$current_region] : '';

// パンくずリスト
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')],
    ['name' => $prefecture_name, 'url' => '']
];
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo esc_html($page_title); ?></title>
    <meta name="title" content="<?php echo esc_attr($page_title); ?>">
    <meta name="description" content="<?php echo esc_attr($page_description); ?>">
    <meta name="keywords" content="<?php echo esc_attr($prefecture_name); ?>,助成金,補助金,<?php echo $current_year; ?>年度,申請,募集,中小企業,個人事業主,スタートアップ,<?php echo esc_attr($region_display_name); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta name="geo.region" content="JP">
    <meta name="geo.placename" content="<?php echo esc_attr($prefecture_name); ?>">
    <link rel="canonical" href="<?php echo esc_url($canonical_url); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url($canonical_url); ?>">
    <meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($page_description); ?>">
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta property="og:locale" content="ja_JP">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo esc_url($canonical_url); ?>">
    <meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($page_description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    
    <!-- Preconnect -->
    <link rel="preconnect" href="<?php echo esc_url(admin_url()); ?>">
    <link rel="dns-prefetch" href="<?php echo esc_url(admin_url()); ?>">
    
    <?php wp_head(); ?>
</head>

<!-- 構造化データ（JSON-LD） -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": <?php echo json_encode($page_title); ?>,
  "description": <?php echo json_encode($page_description); ?>,
  "url": <?php echo json_encode($canonical_url); ?>,
  "inLanguage": "ja-JP",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      <?php foreach ($breadcrumbs as $index => $crumb): ?>
      {
        "@type": "ListItem",
        "position": <?php echo $index + 1; ?>,
        "name": <?php echo json_encode($crumb['name']); ?>
        <?php if ($crumb['url']): ?>,"item": <?php echo json_encode($crumb['url']); ?><?php endif; ?>
      }<?php if ($index < count($breadcrumbs) - 1): ?>,<?php endif; ?>
      <?php endforeach; ?>
    ]
  },
  "mainEntity": {
    "@type": "ItemList",
    "name": <?php echo json_encode($prefecture_name . 'の助成金・補助金一覧'); ?>,
    "numberOfItems": <?php echo intval($prefecture_count); ?>
  },
  "publisher": {
    "@type": "Organization",
    "name": <?php echo json_encode(get_bloginfo('name')); ?>,
    "url": <?php echo json_encode(home_url()); ?>
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "GovernmentService",
  "name": <?php echo json_encode($prefecture_name . 'の助成金・補助金サービス'); ?>,
  "description": <?php echo json_encode($page_description); ?>,
  "provider": {
    "@type": "GovernmentOrganization",
    "name": <?php echo json_encode($prefecture_name . '庁'); ?>
  },
  "areaServed": {
    "@type": "AdministrativeArea",
    "name": <?php echo json_encode($prefecture_name); ?>,
    "addressCountry": "JP"
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": <?php echo json_encode($prefecture_name . 'の助成金はどのように検索できますか?'); ?>,
      "acceptedAnswer": {
        "@type": "Answer",
        "text": <?php echo json_encode('当ページでは' . $prefecture_name . 'で利用できる助成金・補助金を' . $prefecture_count . '件掲載しています。カテゴリー、市町村、金額、募集状況などで絞り込み検索が可能です。'); ?>
      }
    },
    {
      "@type": "Question",
      "name": <?php echo json_encode($prefecture_name . '独自の助成金制度はありますか?'); ?>,
      "acceptedAnswer": {
        "@type": "Answer",
        "text": <?php echo json_encode('はい。' . $prefecture_name . 'では地域産業の振興や地域課題の解決を目的とした独自の助成金制度を実施しています。'); ?>
      }
    },
    {
      "@type": "Question",
      "name": <?php echo json_encode($prefecture_name . 'の助成金申請窓口はどこですか?'); ?>,
      "acceptedAnswer": {
        "@type": "Answer",
        "text": <?php echo json_encode($prefecture_name . '庁の各担当課、商工会、商工会議所などが主な窓口となります。'); ?>
      }
    }
  ]
}
</script>

<main class="prefecture-archive-complete" id="main-content">

    <!-- パンくずリスト -->
    <nav class="breadcrumb-nav" aria-label="パンくずリスト">
        <div class="container">
            <ol class="breadcrumb-list">
                <?php foreach ($breadcrumbs as $crumb): ?>
                <li class="breadcrumb-item">
                    <?php if ($crumb['url']): ?>
                        <a href="<?php echo esc_url($crumb['url']); ?>"><?php echo esc_html($crumb['name']); ?></a>
                    <?php else: ?>
                        <span><?php echo esc_html($crumb['name']); ?></span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>

    <!-- ヒーローセクション -->
    <section class="prefecture-hero-section">
        <div class="container">
            <div class="hero-content">
                
                <!-- バッジ -->
                <div class="prefecture-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>都道府県別助成金</span>
                </div>

                <!-- メインタイトル -->
                <h1 class="hero-title">
                    <span class="prefecture-name"><?php echo esc_html($prefecture_name); ?></span>
                    <span class="title-text">の助成金・補助金</span>
                    <span class="year-badge"><?php echo $current_year; ?>年度版</span>
                </h1>

                <!-- リード文 -->
                <div class="hero-lead">
                    <?php if ($prefecture_description): ?>
                    <p class="lead-text"><?php echo esc_html($prefecture_description); ?></p>
                    <?php endif; ?>
                    <p class="lead-sub">
                        <?php echo esc_html($prefecture_name); ?>で利用できる助成金・補助金を<strong><?php echo number_format($prefecture_count); ?>件</strong>掲載。
                        <?php if ($region_display_name): ?>
                        <?php echo esc_html($region_display_name); ?>地方の地域特性を活かした制度から、国の制度まで幅広くカバーしています。
                        <?php endif; ?>
                    </p>
                </div>

                <!-- メタ情報 -->
                <div class="hero-meta">
                    <div class="meta-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11H7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V11h-2v8H9v-8z"/>
                            <path d="M13 7h2l-5-5-5 5h2v4h6V7z"/>
                        </svg>
                        <strong><?php echo number_format($prefecture_count); ?></strong>件の助成金
                    </div>
                    <?php if ($region_display_name): ?>
                    <div class="meta-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <?php echo esc_html($region_display_name); ?>地方
                    </div>
                    <?php endif; ?>
                    <div class="meta-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <?php echo $current_year; ?>年度最新
                    </div>
                    <div class="meta-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        毎日更新
                    </div>
                </div>

                <!-- 特徴カード -->
                <div class="feature-cards">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        <h3>リアルタイム更新</h3>
                        <p><?php echo esc_html($prefecture_name); ?>の最新募集情報を毎日チェック</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <h3>地域密着型</h3>
                        <p>県と市町村の制度を一括検索</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <h3>申請サポート</h3>
                        <p>専門家監修の詳細ガイド付き</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- フィルターセクション -->
    <section class="filter-section">
        <div class="container">
            
            <!-- フィルターヘッダー -->
            <div class="filter-header">
                <h2 class="filter-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    絞り込み検索
                </h2>
                <button class="filter-reset" id="reset-all" style="display: none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1 4 1 10 7 10"/>
                        <polyline points="23 20 23 14 17 14"/>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                    </svg>
                    すべてリセット
                </button>
            </div>

            <!-- 検索バー -->
            <div class="search-bar">
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="search" 
                           id="keyword-search" 
                           class="search-input" 
                           placeholder="助成金名、実施機関、対象事業で検索...">
                    <button class="search-clear" id="search-clear" style="display: none;">×</button>
                    <button class="search-btn" id="search-btn">検索</button>
                </div>
            </div>

            <!-- フィルターグリッド -->
            <div class="filter-grid">
                
                <!-- カテゴリー -->
                <div class="filter-item">
                    <label class="filter-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                        </svg>
                        カテゴリー
                        <span class="multi-badge" id="cat-badge" style="display: none;">0</span>
                    </label>
                    <div class="custom-select multi" id="category-select">
                        <button class="select-trigger">
                            <span class="select-value">選択してください</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" style="display: none;">
                            <div class="select-search">
                                <input type="search" class="select-search-input" placeholder="カテゴリーを検索...">
                            </div>
                            <div class="select-options" id="cat-options">
                                <div class="select-option all">
                                    <input type="checkbox" id="cat-all" class="option-check">
                                    <label for="cat-all">すべて</label>
                                </div>
                                <?php foreach ($all_categories as $i => $cat): ?>
                                <div class="select-option" data-value="<?php echo esc_attr($cat->slug); ?>" data-name="<?php echo esc_attr($cat->name); ?>">
                                    <input type="checkbox" id="cat-<?php echo $i; ?>" class="option-check" value="<?php echo esc_attr($cat->slug); ?>">
                                    <label for="cat-<?php echo $i; ?>"><?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)</label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="select-actions">
                                <button class="select-action clear" id="clear-cat">クリア</button>
                                <button class="select-action apply" id="apply-cat">適用</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 市町村 -->
                <div class="filter-item">
                    <label class="filter-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 9v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9"/>
                            <path d="M9 22V12h6v10M2 10.6L12 2l10 8.6"/>
                        </svg>
                        市町村
                    </label>
                    <div class="custom-select" id="municipality-select">
                        <button class="select-trigger">
                            <span class="select-value">すべて</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" style="display: none;">
                            <div class="select-search">
                                <input type="search" class="select-search-input" placeholder="市町村を検索...">
                            </div>
                            <div class="select-options" id="muni-options">
                                <div class="select-option active" data-value="">すべて</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 助成金額 -->
                <div class="filter-item">
                    <label class="filter-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        助成金額
                    </label>
                    <div class="custom-select" id="amount-select">
                        <button class="select-trigger">
                            <span class="select-value">指定なし</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" style="display: none;">
                            <div class="select-options">
                                <div class="select-option active" data-value="">指定なし</div>
                                <div class="select-option" data-value="0-100">〜100万円</div>
                                <div class="select-option" data-value="100-500">100万円〜500万円</div>
                                <div class="select-option" data-value="500-1000">500万円〜1000万円</div>
                                <div class="select-option" data-value="1000-3000">1000万円〜3000万円</div>
                                <div class="select-option" data-value="3000+">3000万円以上</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 募集状況 -->
                <div class="filter-item">
                    <label class="filter-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        募集状況
                    </label>
                    <div class="custom-select" id="status-select">
                        <button class="select-trigger">
                            <span class="select-value">すべて</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" style="display: none;">
                            <div class="select-options">
                                <div class="select-option active" data-value="">すべて</div>
                                <div class="select-option" data-value="active">
                                    <span class="status-dot active"></span>募集中
                                </div>
                                <div class="select-option" data-value="upcoming">
                                    <span class="status-dot upcoming"></span>募集予定
                                </div>
                                <div class="select-option" data-value="closed">
                                    <span class="status-dot closed"></span>募集終了
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 難易度 -->
                <div class="filter-item">
                    <label class="filter-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        難易度
                    </label>
                    <div class="custom-select" id="difficulty-select">
                        <button class="select-trigger">
                            <span class="select-value">指定なし</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" style="display: none;">
                            <div class="select-options">
                                <div class="select-option active" data-value="">指定なし</div>
                                <div class="select-option" data-value="easy">易しい</div>
                                <div class="select-option" data-value="normal">普通</div>
                                <div class="select-option" data-value="hard">難しい</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 並び順 -->
                <div class="filter-item">
                    <label class="filter-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="4" y1="6" x2="16" y2="6"/>
                            <line x1="4" y1="12" x2="14" y2="12"/>
                            <line x1="4" y1="18" x2="12" y2="18"/>
                        </svg>
                        並び順
                    </label>
                    <div class="custom-select" id="sort-select">
                        <button class="select-trigger">
                            <span class="select-value">新着順</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" style="display: none;">
                            <div class="select-options">
                                <div class="select-option active" data-value="date_desc">新着順</div>
                                <div class="select-option" data-value="amount_desc">金額が高い順</div>
                                <div class="select-option" data-value="deadline_asc">締切が近い順</div>
                                <div class="select-option" data-value="featured_first">注目順</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- アクティブフィルター -->
            <div class="active-filters" id="active-filters" style="display: none;">
                <span class="active-label">適用中:</span>
                <div class="filter-tags" id="filter-tags"></div>
            </div>
        </div>
    </section>

    <!-- 結果セクション -->
    <section class="results-section">
        <div class="container">
            
            <!-- 結果ヘッダー -->
            <div class="results-header">
                <div class="results-info">
                    <h2 class="results-title">検索結果</h2>
                    <div class="results-meta">
                        <span class="total"><strong id="count">0</strong>件</span>
                        <span class="range">（<span id="from">1</span>〜<span id="to">12</span>件を表示）</span>
                    </div>
                </div>

                <div class="view-controls">
                    <button class="view-btn active" data-view="single">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="2" y="2" width="20" height="20"/>
                        </svg>
                    </button>
                    <button class="view-btn" data-view="grid">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- ローディング -->
            <div class="loading" id="loading" style="display: none;">
                <div class="spinner"></div>
                <p>検索中...</p>
            </div>

            <!-- 助成金リスト -->
            <div class="grants-list" id="grants-list" data-view="single">
                <?php
                $query = new WP_Query([
                    'post_type' => 'grant',
                    'posts_per_page' => 12,
                    'tax_query' => [
                        [
                            'taxonomy' => 'grant_prefecture',
                            'field' => 'slug',
                            'terms' => $prefecture_slug,
                        ],
                    ],
                    'orderby' => 'date',
                    'order' => 'DESC'
                ]);
                
                if ($query->have_posts()) :
                    while ($query->have_posts()) : 
                        $query->the_post();
                        get_template_part('template-parts/grant-card-list-portal');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>

            <!-- 結果なし -->
            <div class="no-results" id="no-results" style="display: none;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <h3>該当する助成金が見つかりませんでした</h3>
                <p>検索条件を変更して再度お試しください。</p>
            </div>

            <!-- ページネーション -->
            <div class="pagination-wrapper" id="pagination-wrapper" style="display: none;">
                <nav class="pagination" id="pagination"></nav>
            </div>
        </div>
    </section>

    <!-- 関連都道府県 -->
    <?php if (!empty($related_prefectures)): ?>
    <aside class="related-section">
        <div class="container">
            <h2 class="section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
                <?php echo esc_html($region_display_name); ?>地方の他の都道府県
            </h2>

            <div class="related-grid">
                <?php $count = 0; foreach ($related_prefectures as $pref): 
                    if ($count >= 6) break;
                    $term = get_term_by('slug', $pref['slug'], 'grant_prefecture');
                    $pref_count = $term ? $term->count : 0;
                    $count++;
                ?>
                <a href="<?php echo esc_url(get_term_link($term)); ?>" class="related-card">
                    <div class="card-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html($pref['name']); ?></h3>
                    <p><?php echo number_format($pref_count); ?>件の助成金</p>
                    <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
    <?php endif; ?>

    <!-- SEOコンテンツ -->
    <section class="seo-section">
        <div class="container">
            <div class="seo-content">
                <h2><?php echo esc_html($prefecture_name); ?>の助成金について</h2>
                <p>
                    <?php echo esc_html($prefecture_name); ?>では、地域産業の振興と経済活性化を目的として、
                    様々な分野において多様な助成金制度を実施しています。
                    当サイトでは、<?php echo $current_year; ?>年度に募集される<?php echo esc_html($prefecture_name); ?>の助成金情報を
                    <?php echo number_format($prefecture_count); ?>件掲載しており、
                    県の制度から市町村独自の制度まで幅広くカバーしています。
                </p>
                <p>
                    <?php echo esc_html($prefecture_name); ?>の助成金は、<?php echo esc_html($region_display_name); ?>地方の特色を活かした事業や、
                    地域課題の解決に取り組む事業者を重点的に支援しています。
                    また、国の制度と組み合わせることで、より手厚い支援を受けることも可能です。
                    各助成金の詳細な申請要件や手続きについては、各制度の詳細ページでご確認ください。
                </p>
            </div>
        </div>
    </section>

</main>

<!-- 完全版CSS -->
<style>
/* ===== Variables ===== */
:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-accent: #FFEB3B;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #e5e5e5;
    --color-gray-300: #d4d4d4;
    --color-gray-400: #a3a3a3;
    --color-gray-500: #737373;
    --color-gray-600: #525252;
    --color-gray-700: #404040;
    --color-gray-800: #262626;
    --color-gray-900: #171717;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.prefecture-archive-complete {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif;
    color: var(--color-primary);
    background: var(--color-secondary);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ===== Breadcrumb ===== */
.breadcrumb-nav {
    padding: 20px 0;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.breadcrumb-list {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 14px;
}

.breadcrumb-item:not(:last-child)::after {
    content: '>';
    margin-left: 8px;
    color: var(--color-gray-400);
}

.breadcrumb-item a {
    color: var(--color-gray-600);
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: var(--color-primary);
}

.breadcrumb-item span {
    color: var(--color-primary);
    font-weight: 600;
}

/* ===== Hero Section ===== */
.prefecture-hero-section {
    padding: 60px 0;
    background: linear-gradient(135deg, #f0f8ff 0%, #ffffff 100%);
    border-bottom: 2px solid var(--color-gray-200);
}

.hero-content {
    text-align: center;
    max-width: 900px;
    margin: 0 auto;
}

.prefecture-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 24px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 24px;
}

.hero-title {
    font-size: 48px;
    font-weight: 800;
    color: var(--color-primary);
    margin: 0 0 24px 0;
    line-height: 1.2;
}

.prefecture-name {
    color: var(--color-primary);
    background: linear-gradient(180deg, transparent 65%, rgba(255, 235, 59, 0.3) 65%);
    padding: 0 8px;
}

.title-text {
    color: var(--color-gray-700);
    font-weight: 600;
}

.year-badge {
    display: inline-block;
    margin-left: 12px;
    padding: 4px 12px;
    background: var(--color-accent);
    color: var(--color-primary);
    border-radius: 16px;
    font-size: 18px;
    font-weight: 700;
}

.hero-lead {
    margin: 0 0 32px 0;
}

.lead-text {
    font-size: 18px;
    color: var(--color-gray-700);
    margin: 0 0 16px 0;
    line-height: 1.7;
}

.lead-sub {
    font-size: 16px;
    color: var(--color-gray-600);
    margin: 0;
    line-height: 1.7;
}

.lead-sub strong {
    color: var(--color-primary);
    font-weight: 700;
}

.hero-meta {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
    margin: 0 0 40px 0;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--color-gray-600);
}

.meta-item svg {
    color: var(--color-gray-500);
}

.meta-item strong {
    color: var(--color-primary);
    font-weight: 700;
}

.feature-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 40px;
}

.feature-card {
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-200);
    border-radius: 12px;
    padding: 24px;
    text-align: left;
    transition: all 0.3s;
}

.feature-card:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.feature-icon {
    width: 48px;
    height: 48px;
    background: var(--color-primary);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-secondary);
    margin-bottom: 16px;
}

.feature-card h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 8px 0;
}

.feature-card p {
    font-size: 14px;
    color: var(--color-gray-600);
    margin: 0;
    line-height: 1.6;
}

/* ===== Filter Section ===== */
.filter-section {
    padding: 40px 0;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0;
}

.filter-reset {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-reset:hover {
    background: var(--color-gray-800);
}

.search-bar {
    margin-bottom: 24px;
}

.search-input-wrapper {
    display: flex;
    align-items: center;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-300);
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.3s;
}

.search-input-wrapper:focus-within {
    border-color: var(--color-primary);
}

.search-icon {
    margin-left: 16px;
    color: var(--color-gray-400);
}

.search-input {
    flex: 1;
    padding: 14px 16px;
    border: none;
    outline: none;
    font-size: 15px;
}

.search-clear {
    padding: 0 12px;
    background: none;
    border: none;
    color: var(--color-gray-500);
    font-size: 20px;
    cursor: pointer;
    transition: color 0.2s;
}

.search-clear:hover {
    color: var(--color-primary);
}

.search-btn {
    padding: 14px 24px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.search-btn:hover {
    background: var(--color-gray-800);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-primary);
}

.filter-label svg {
    color: var(--color-gray-500);
}

.multi-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
}

.custom-select {
    position: relative;
}

.select-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-300);
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.select-trigger:hover {
    border-color: var(--color-primary);
}

.custom-select.active .select-trigger {
    border-color: var(--color-primary);
}

.select-value {
    color: var(--color-primary);
    font-weight: 500;
}

.select-arrow {
    color: var(--color-gray-500);
    transition: transform 0.2s;
}

.custom-select.active .select-arrow {
    transform: rotate(180deg);
}

.select-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
    border-radius: 6px;
    box-shadow: var(--shadow-lg);
    z-index: 100;
    max-height: 320px;
    overflow: hidden;
}

.select-search {
    padding: 12px;
    border-bottom: 1px solid var(--color-gray-200);
}

.select-search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--color-gray-300);
    border-radius: 4px;
    font-size: 14px;
    outline: none;
}

.select-search-input:focus {
    border-color: var(--color-primary);
}

.select-options {
    max-height: 240px;
    overflow-y: auto;
}

.select-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    font-size: 14px;
    color: var(--color-primary);
    cursor: pointer;
    transition: background 0.2s;
}

.select-option:hover {
    background: var(--color-gray-100);
}

.select-option.active {
    background: var(--color-gray-200);
    font-weight: 600;
}

.option-check {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-dot.active {
    background: #22c55e;
}

.status-dot.upcoming {
    background: #3b82f6;
}

.status-dot.closed {
    background: #ef4444;
}

.select-actions {
    display: flex;
    gap: 8px;
    padding: 12px;
    border-top: 1px solid var(--color-gray-200);
}

.select-action {
    flex: 1;
    padding: 8px;
    border: none;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.select-action.clear {
    background: var(--color-gray-200);
    color: var(--color-primary);
}

.select-action.clear:hover {
    background: var(--color-gray-300);
}

.select-action.apply {
    background: var(--color-primary);
    color: var(--color-secondary);
}

.select-action.apply:hover {
    background: var(--color-gray-800);
}

.active-filters {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: rgba(255, 235, 59, 0.1);
    border-radius: 8px;
    flex-wrap: wrap;
}

.active-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-primary);
}

.filter-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-tag {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 16px;
    font-size: 13px;
    font-weight: 500;
}

.filter-tag button {
    background: none;
    border: none;
    color: var(--color-secondary);
    font-size: 16px;
    cursor: pointer;
    padding: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===== Results Section ===== */
.results-section {
    padding: 60px 0;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 20px;
}

.results-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 8px 0;
}

.results-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: var(--color-gray-600);
}

.results-meta strong {
    color: var(--color-primary);
    font-weight: 700;
}

.view-controls {
    display: flex;
    gap: 4px;
    background: var(--color-gray-200);
    border-radius: 6px;
    padding: 4px;
}

.view-btn {
    padding: 8px 12px;
    background: transparent;
    border: none;
    border-radius: 4px;
    color: var(--color-gray-600);
    cursor: pointer;
    transition: all 0.2s;
}

.view-btn:hover {
    color: var(--color-primary);
}

.view-btn.active {
    background: var(--color-primary);
    color: var(--color-secondary);
}

.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 0;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid var(--color-gray-200);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 16px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.grants-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 40px;
}

.grants-list[data-view="grid"] {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
}

.no-results {
    text-align: center;
    padding: 80px 20px;
}

.no-results svg {
    color: var(--color-gray-300);
    margin-bottom: 20px;
}

.no-results h3 {
    font-size: 20px;
    font-weight: 600;
    color: var(--color-primary);
    margin: 0 0 12px 0;
}

.no-results p {
    font-size: 15px;
    color: var(--color-gray-600);
    margin: 0;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

.pagination {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
    border-radius: 8px;
    padding: 12px 20px;
}

.pagination-btn {
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    background: transparent;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-primary);
    cursor: pointer;
    transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
    background: var(--color-gray-100);
}

.pagination-btn.active {
    background: var(--color-primary);
    color: var(--color-secondary);
}

.pagination-btn:disabled {
    color: var(--color-gray-400);
    cursor: not-allowed;
}

.pagination-ellipsis {
    padding: 0 8px;
    color: var(--color-gray-400);
}

/* ===== Related Section ===== */
.related-section {
    padding: 60px 0;
    background: var(--color-gray-50);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 32px 0;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.related-card {
    position: relative;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-200);
    border-radius: 12px;
    padding: 24px;
    text-decoration: none;
    transition: all 0.3s;
}

.related-card:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.related-card .card-icon {
    width: 48px;
    height: 48px;
    background: var(--color-accent);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    margin-bottom: 16px;
}

.related-card h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 8px 0;
}

.related-card p {
    font-size: 14px;
    color: var(--color-gray-600);
    margin: 0 0 16px 0;
}

.related-card .arrow {
    position: absolute;
    bottom: 24px;
    right: 24px;
    color: var(--color-gray-400);
    transition: all 0.3s;
}

.related-card:hover .arrow {
    color: var(--color-primary);
    transform: translateX(4px);
}

/* ===== SEO Section ===== */
.seo-section {
    padding: 60px 0;
    background: var(--color-secondary);
    border-top: 1px solid var(--color-gray-200);
}

.seo-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.seo-content h2 {
    font-size: 28px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 24px 0;
}

.seo-content p {
    font-size: 16px;
    color: var(--color-gray-700);
    line-height: 1.8;
    margin: 0 0 20px 0;
    text-align: left;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .hero-title {
        font-size: 32px;
    }

    .year-badge {
        display: block;
        margin: 12px auto 0;
        width: fit-content;
    }

    .hero-meta {
        flex-direction: column;
        gap: 12px;
    }

    .feature-cards {
        grid-template-columns: 1fr;
    }

    .filter-grid {
        grid-template-columns: 1fr;
    }

    .results-header {
        flex-direction: column;
        align-items: stretch;
    }

    .grants-list[data-view="grid"] {
        grid-template-columns: 1fr;
    }

    .related-grid {
        grid-template-columns: 1fr;
    }

    .pagination {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 16px;
    }

    .hero-title {
        font-size: 28px;
    }

    .filter-header {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
}
</style>

<!-- JavaScript（完全版） -->
<script>
(function() {
    'use strict';
    
    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    const NONCE = '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>';
    const PREF_SLUG = '<?php echo esc_js($prefecture_slug); ?>';
    
    const state = {
        page: 1,
        perPage: 12,
        view: 'single',
        filters: {
            search: '',
            category: [],
            prefecture: [PREF_SLUG],
            municipality: '',
            amount: '',
            status: '',
            difficulty: '',
            sort: 'date_desc'
        },
        isLoading: false,
        tempCat: []
    };
    
    const el = {
        list: document.getElementById('grants-list'),
        loading: document.getElementById('loading'),
        noResults: document.getElementById('no-results'),
        count: document.getElementById('count'),
        from: document.getElementById('from'),
        to: document.getElementById('to'),
        pagination: document.getElementById('pagination'),
        paginationWrapper: document.getElementById('pagination-wrapper'),
        activeFilters: document.getElementById('active-filters'),
        filterTags: document.getElementById('filter-tags'),
        
        search: document.getElementById('keyword-search'),
        searchBtn: document.getElementById('search-btn'),
        searchClear: document.getElementById('search-clear'),
        
        catSelect: document.getElementById('category-select'),
        catOptions: document.getElementById('cat-options'),
        catBadge: document.getElementById('cat-badge'),
        clearCat: document.getElementById('clear-cat'),
        applyCat: document.getElementById('apply-cat'),
        
        muniSelect: document.getElementById('municipality-select'),
        muniOptions: document.getElementById('muni-options'),
        
        amountSelect: document.getElementById('amount-select'),
        statusSelect: document.getElementById('status-select'),
        difficultySelect: document.getElementById('difficulty-select'),
        sortSelect: document.getElementById('sort-select'),
        
        viewBtns: document.querySelectorAll('.view-btn'),
        resetAll: document.getElementById('reset-all')
    };
    
    function init() {
        console.log('Prefecture archive initialized:', PREF_SLUG);
        loadMunicipalities();
        setupSelects();
        setupEvents();
        loadGrants();
    }
    
    function loadMunicipalities() {
        const fd = new FormData();
        fd.append('action', 'gi_get_municipalities_for_prefecture');
        fd.append('prefecture_slug', PREF_SLUG);
        fd.append('nonce', NONCE);
        
        fetch(AJAX_URL, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            let munis = [];
            if (data.success && data.data && data.data.data && Array.isArray(data.data.data.municipalities)) {
                munis = data.data.data.municipalities;
            } else if (data.success && data.data && Array.isArray(data.data.municipalities)) {
                munis = data.data.municipalities;
            }
            
            if (munis.length > 0) {
                renderMunicipalities(munis);
            }
        })
        .catch(e => console.error('Municipality error:', e));
    }
    
    function renderMunicipalities(munis) {
        let html = '<div class="select-option active" data-value="">すべて</div>';
        munis.forEach(m => {
            html += `<div class="select-option" data-value="${m.slug}">${m.name}</div>`;
        });
        el.muniOptions.innerHTML = html;
        
        el.muniOptions.querySelectorAll('.select-option').forEach(opt => {
            opt.addEventListener('click', () => {
                const val = opt.dataset.value;
                const txt = opt.textContent.trim();
                
                el.muniOptions.querySelectorAll('.select-option').forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                
                el.muniSelect.querySelector('.select-value').textContent = txt;
                closeSelect(el.muniSelect);
                
                state.filters.municipality = val;
                state.page = 1;
                loadGrants();
            });
        });
    }
    
    function setupSelects() {
        setupMultiSelect(el.catSelect, el.catOptions, el.clearCat, el.applyCat);
        setupSingleSelect(el.muniSelect);
        setupSingleSelect(el.amountSelect);
        setupSingleSelect(el.statusSelect);
        setupSingleSelect(el.difficultySelect);
        setupSingleSelect(el.sortSelect);
    }
    
    function setupMultiSelect(select, options, clearBtn, applyBtn) {
        const trigger = select.querySelector('.select-trigger');
        const dropdown = select.querySelector('.select-dropdown');
        const allCheck = document.getElementById('cat-all');
        const checks = options.querySelectorAll('.option-check');
        
        trigger.addEventListener('click', () => {
            const isActive = select.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                select.classList.add('active');
                dropdown.style.display = 'block';
                state.tempCat = [...state.filters.category];
                updateCatChecks();
            }
        });
        
        if (allCheck) {
            allCheck.addEventListener('change', e => {
                if (e.target.checked) {
                    state.tempCat = [];
                    checks.forEach(c => {
                        if (c !== allCheck) c.checked = false;
                    });
                }
            });
        }
        
        checks.forEach(check => {
            if (check !== allCheck) {
                check.addEventListener('change', e => {
                    const val = e.target.value;
                    if (e.target.checked) {
                        if (!state.tempCat.includes(val)) {
                            state.tempCat.push(val);
                        }
                        allCheck.checked = false;
                    } else {
                        const idx = state.tempCat.indexOf(val);
                        if (idx > -1) {
                            state.tempCat.splice(idx, 1);
                        }
                        if (state.tempCat.length === 0) {
                            allCheck.checked = true;
                        }
                    }
                });
            }
        });
        
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                state.tempCat = [];
                updateCatChecks();
                allCheck.checked = true;
            });
        }
        
        if (applyBtn) {
            applyBtn.addEventListener('click', () => {
                state.filters.category = [...state.tempCat];
                updateCatDisplay();
                closeSelect(select);
                state.page = 1;
                loadGrants();
            });
        }
    }
    
    function updateCatChecks() {
        const checks = el.catOptions.querySelectorAll('.option-check');
        const allCheck = document.getElementById('cat-all');
        
        checks.forEach(check => {
            if (check !== allCheck) {
                check.checked = state.tempCat.includes(check.value);
            }
        });
        
        allCheck.checked = state.tempCat.length === 0;
    }
    
    function updateCatDisplay() {
        const val = el.catSelect.querySelector('.select-value');
        const count = state.filters.category.length;
        
        if (count === 0) {
            val.textContent = '選択してください';
            el.catBadge.style.display = 'none';
        } else {
            val.textContent = `${count}件選択中`;
            el.catBadge.textContent = count;
            el.catBadge.style.display = 'inline-flex';
        }
    }
    
    function setupSingleSelect(select) {
        const trigger = select.querySelector('.select-trigger');
        const dropdown = select.querySelector('.select-dropdown');
        const options = select.querySelectorAll('.select-option');
        const val = select.querySelector('.select-value');
        
        trigger.addEventListener('click', () => {
            const isActive = select.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                select.classList.add('active');
                dropdown.style.display = 'block';
            }
        });
        
        options.forEach(opt => {
            opt.addEventListener('click', () => {
                const value = opt.dataset.value;
                const text = opt.textContent.trim();
                
                options.forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                
                val.textContent = text;
                closeSelect(select);
                
                if (select === el.amountSelect) {
                    state.filters.amount = value;
                } else if (select === el.statusSelect) {
                    state.filters.status = value;
                } else if (select === el.difficultySelect) {
                    state.filters.difficulty = value;
                } else if (select === el.sortSelect) {
                    state.filters.sort = value;
                }
                
                state.page = 1;
                loadGrants();
            });
        });
    }
    
    function closeSelect(select) {
        select.classList.remove('active');
        const dropdown = select.querySelector('.select-dropdown');
        if (dropdown) {
            dropdown.style.display = 'none';
        }
    }
    
    function closeAllSelects() {
        document.querySelectorAll('.custom-select').forEach(s => closeSelect(s));
    }
    
    document.addEventListener('click', e => {
        if (!e.target.closest('.custom-select')) {
            closeAllSelects();
        }
    });
    
    function setupEvents() {
        if (el.search) {
            el.search.addEventListener('input', debounce(() => {
                const q = el.search.value.trim();
                el.searchClear.style.display = q ? 'block' : 'none';
            }, 300));
            
            el.search.addEventListener('keypress', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearch();
                }
            });
        }
        
        if (el.searchBtn) {
            el.searchBtn.addEventListener('click', handleSearch);
        }
        
        if (el.searchClear) {
            el.searchClear.addEventListener('click', () => {
                el.search.value = '';
                state.filters.search = '';
                el.searchClear.style.display = 'none';
                state.page = 1;
                loadGrants();
            });
        }
        
        el.viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                el.viewBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                state.view = this.dataset.view;
                el.list.setAttribute('data-view', state.view);
            });
        });
        
        if (el.resetAll) {
            el.resetAll.addEventListener('click', resetFilters);
        }
    }
    
    function handleSearch() {
        const q = el.search.value.trim();
        state.filters.search = q;
        state.page = 1;
        loadGrants();
    }
    
    function resetFilters() {
        state.filters = {
            search: '',
            category: [],
            prefecture: [PREF_SLUG],
            municipality: '',
            amount: '',
            status: '',
            difficulty: '',
            sort: 'date_desc'
        };
        state.tempCat = [];
        state.page = 1;
        
        el.search.value = '';
        el.searchClear.style.display = 'none';
        
        updateCatDisplay();
        updateCatChecks();
        
        resetSelect(el.muniSelect, 'すべて');
        resetSelect(el.amountSelect, '指定なし');
        resetSelect(el.statusSelect, 'すべて');
        resetSelect(el.difficultySelect, '指定なし');
        resetSelect(el.sortSelect, '新着順');
        
        loadGrants();
    }
    
    function resetSelect(select, defaultText) {
        const val = select.querySelector('.select-value');
        const opts = select.querySelectorAll('.select-option');
        
        val.textContent = defaultText;
        opts.forEach(o => o.classList.remove('active'));
        opts[0].classList.add('active');
    }
    
    function loadGrants() {
        if (state.isLoading) return;
        
        state.isLoading = true;
        showLoading(true);
        
        const fd = new FormData();
        fd.append('action', 'gi_ajax_load_grants');
        fd.append('nonce', NONCE);
        fd.append('page', state.page);
        fd.append('posts_per_page', state.perPage);
        fd.append('view', state.view);
        
        if (state.filters.search) {
            fd.append('search', state.filters.search);
        }
        
        if (state.filters.category.length > 0) {
            fd.append('categories', JSON.stringify(state.filters.category));
        }
        
        fd.append('prefectures', JSON.stringify([PREF_SLUG]));
        
        if (state.filters.municipality) {
            fd.append('municipalities', JSON.stringify([state.filters.municipality]));
        }
        
        if (state.filters.amount) {
            fd.append('amount', state.filters.amount);
        }
        
        if (state.filters.status) {
            fd.append('status', JSON.stringify([state.filters.status]));
        }
        
        if (state.filters.difficulty) {
            fd.append('difficulty', JSON.stringify([state.filters.difficulty]));
        }
        
        fd.append('sort', state.filters.sort);
        
        fetch(AJAX_URL, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                displayGrants(data.data.grants);
                updateStats(data.data.stats);
                updatePagination(data.data.pagination);
                updateActiveFilters();
            }
        })
        .catch(e => console.error('Load error:', e))
        .finally(() => {
            state.isLoading = false;
            showLoading(false);
        });
    }
    
    function displayGrants(grants) {
        if (!grants || grants.length === 0) {
            el.list.innerHTML = '';
            el.list.style.display = 'none';
            el.noResults.style.display = 'block';
            return;
        }
        
        el.list.style.display = state.view === 'single' ? 'flex' : 'grid';
        el.noResults.style.display = 'none';
        
        el.list.innerHTML = grants.map(g => g.html).join('');
        
        // Setup AI button listeners for dynamically loaded grant cards
        if (typeof setupAIButtonListeners === 'function') {
            setupAIButtonListeners();
        }
    }
    
    function updateStats(stats) {
        if (el.count) el.count.textContent = (stats.total_found || 0).toLocaleString();
        if (el.from) el.from.textContent = (stats.showing_from || 0).toLocaleString();
        if (el.to) el.to.textContent = (stats.showing_to || 0).toLocaleString();
    }
    
    function updatePagination(pag) {
        if (!pag || pag.total_pages <= 1) {
            el.paginationWrapper.style.display = 'none';
            return;
        }
        
        el.paginationWrapper.style.display = 'block';
        
        let html = '';
        const cur = pag.current_page;
        const tot = pag.total_pages;
        
        html += `<button class="pagination-btn" ${cur === 1 ? 'disabled' : ''} data-page="${cur - 1}">前へ</button>`;
        
        const max = 7;
        let start = Math.max(1, cur - Math.floor(max / 2));
        let end = Math.min(tot, start + max - 1);
        
        if (end - start < max - 1) {
            start = Math.max(1, end - max + 1);
        }
        
        if (start > 1) {
            html += `<button class="pagination-btn" data-page="1">1</button>`;
            if (start > 2) {
                html += `<span class="pagination-ellipsis">...</span>`;
            }
        }
        
        for (let i = start; i <= end; i++) {
            html += `<button class="pagination-btn ${i === cur ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        
        if (end < tot) {
            if (end < tot - 1) {
                html += `<span class="pagination-ellipsis">...</span>`;
            }
            html += `<button class="pagination-btn" data-page="${tot}">${tot}</button>`;
        }
        
        html += `<button class="pagination-btn" ${cur === tot ? 'disabled' : ''} data-page="${cur + 1}">次へ</button>`;
        
        el.pagination.innerHTML = html;
        
        el.pagination.querySelectorAll('.pagination-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.disabled) {
                    state.page = parseInt(this.dataset.page);
                    loadGrants();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }
    
    function updateActiveFilters() {
        const tags = [];
        
        if (state.filters.search) {
            tags.push({ type: 'search', label: `検索: "${state.filters.search}"`, value: state.filters.search });
        }
        
        if (state.filters.category.length > 0) {
            state.filters.category.forEach(slug => {
                const opt = document.querySelector(`.select-option[data-value="${slug}"]`);
                if (opt) {
                    tags.push({ type: 'category', label: opt.dataset.name || opt.textContent.trim(), value: slug });
                }
            });
        }
        
        if (state.filters.municipality) {
            const opt = Array.from(el.muniOptions.querySelectorAll('.select-option')).find(o => o.dataset.value === state.filters.municipality);
            if (opt) {
                tags.push({ type: 'municipality', label: `市町村: ${opt.textContent.trim()}`, value: state.filters.municipality });
            }
        }
        
        if (state.filters.amount) {
            const labels = {
                '0-100': '〜100万円',
                '100-500': '100万円〜500万円',
                '500-1000': '500万円〜1000万円',
                '1000-3000': '1000万円〜3000万円',
                '3000+': '3000万円以上'
            };
            tags.push({ type: 'amount', label: `金額: ${labels[state.filters.amount]}`, value: state.filters.amount });
        }
        
        if (state.filters.status) {
            const labels = { 'active': '募集中', 'upcoming': '募集予定', 'closed': '募集終了' };
            tags.push({ type: 'status', label: `状況: ${labels[state.filters.status]}`, value: state.filters.status });
        }
        
        if (state.filters.difficulty) {
            const labels = { 'easy': '易しい', 'normal': '普通', 'hard': '難しい' };
            tags.push({ type: 'difficulty', label: `難易度: ${labels[state.filters.difficulty]}`, value: state.filters.difficulty });
        }
        
        if (tags.length === 0) {
            el.activeFilters.style.display = 'none';
            el.resetAll.style.display = 'none';
            return;
        }
        
        el.activeFilters.style.display = 'flex';
        el.resetAll.style.display = 'flex';
        
        el.filterTags.innerHTML = tags.map(t => `
            <div class="filter-tag">
                <span>${esc(t.label)}</span>
                <button data-type="${t.type}" data-value="${esc(t.value)}">×</button>
            </div>
        `).join('');
        
        el.filterTags.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function() {
                removeFilter(this.dataset.type, this.dataset.value);
            });
        });
    }
    
    function removeFilter(type, value) {
        switch(type) {
            case 'search':
                el.search.value = '';
                state.filters.search = '';
                el.searchClear.style.display = 'none';
                break;
            case 'category':
                const idx = state.filters.category.indexOf(value);
                if (idx > -1) {
                    state.filters.category.splice(idx, 1);
                }
                state.tempCat = [...state.filters.category];
                updateCatDisplay();
                updateCatChecks();
                break;
            case 'municipality':
                state.filters.municipality = '';
                resetSelect(el.muniSelect, 'すべて');
                break;
            case 'amount':
                state.filters.amount = '';
                resetSelect(el.amountSelect, '指定なし');
                break;
            case 'status':
                state.filters.status = '';
                resetSelect(el.statusSelect, 'すべて');
                break;
            case 'difficulty':
                state.filters.difficulty = '';
                resetSelect(el.difficultySelect, '指定なし');
                break;
        }
        
        state.page = 1;
        loadGrants();
    }
    
    function showLoading(show) {
        if (el.loading) el.loading.style.display = show ? 'flex' : 'none';
        if (el.list) el.list.style.opacity = show ? '0.5' : '1';
    }
    
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }
    
    function esc(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

<?php get_footer(); ?>