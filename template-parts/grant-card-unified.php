<?php
/**
 * Grant Card List Portal - Light Mode Only v2.1 FINAL
 * template-parts/grant-card-list-portal.php
 * 
 * ポータルサイト風1列リスト表示テンプレート
 * AIチャット機能完全動作保証 + ライトモード固定
 * 
 * @package Grant_Insight_Portal
 * @version 2.1.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// グローバル変数から必要データを取得
global $post;

$post_id = get_the_ID();
if (!$post_id) {
    return;
}

// 基本データ取得
$title = get_the_title($post_id);
$permalink = get_permalink($post_id);
$excerpt = get_the_excerpt($post_id);

// 抜粋が空の場合は本文から生成
if (empty($excerpt)) {
    $content = get_the_content($post_id);
    $excerpt = wp_trim_words(strip_tags($content), 30, '...');
}

// ACFフィールド取得
$grant_data = array(
    'organization' => get_field('organization', $post_id) ?: '',
    'organization_type' => get_field('organization_type', $post_id) ?: 'national',
    'max_amount' => get_field('max_amount', $post_id) ?: '',
    'max_amount_numeric' => intval(get_field('max_amount_numeric', $post_id)),
    'min_amount' => intval(get_field('min_amount', $post_id)),
    'deadline' => get_field('deadline', $post_id) ?: '',
    'deadline_date' => get_field('deadline_date', $post_id) ?: '',
    'application_status' => get_field('application_status', $post_id) ?: 'open',
    'application_period' => get_field('application_period', $post_id) ?: '',
    'grant_target' => get_field('grant_target', $post_id) ?: '',
    'eligible_expenses' => get_field('eligible_expenses', $post_id) ?: '',
    'eligible_expenses_detailed' => get_field('eligible_expenses_detailed', $post_id) ?: '',
    'grant_difficulty' => get_field('grant_difficulty', $post_id) ?: 'normal',
    'difficulty_level' => get_field('difficulty_level', $post_id) ?: '中級',
    'adoption_rate' => floatval(get_field('adoption_rate', $post_id)),
    'required_documents' => get_field('required_documents', $post_id) ?: '',
    'required_documents_detailed' => get_field('required_documents_detailed', $post_id) ?: '',
    'subsidy_rate_detailed' => get_field('subsidy_rate_detailed', $post_id) ?: '',
    'application_method' => get_field('application_method', $post_id) ?: 'online',
    'contact_info' => get_field('contact_info', $post_id) ?: '',
    'official_url' => get_field('official_url', $post_id) ?: '',
    'is_featured' => get_field('is_featured', $post_id) ?: false,
    'ai_summary' => get_field('ai_summary', $post_id) ?: get_post_meta($post_id, 'ai_summary', true),
    'area_notes' => get_field('area_notes', $post_id) ?: '',
    'regional_limitation' => get_field('regional_limitation', $post_id) ?: 'nationwide',
);

extract($grant_data);

// タクソノミーデータ
$categories = get_the_terms($post_id, 'grant_category');
$prefectures = get_the_terms($post_id, 'grant_prefecture');

// メインカテゴリ
$main_category = '';
$main_category_slug = '';
if ($categories && !is_wp_error($categories)) {
    $main_category = $categories[0]->name;
    $main_category_slug = $categories[0]->slug;
}

// 地域表示（都道府県のみ）
$region_display = '全国';

if ($prefectures && !is_wp_error($prefectures)) {
    $prefecture_count = count($prefectures);
    
    if ($prefecture_count >= 47 || $prefecture_count >= 20) {
        $region_display = '全国';
    } elseif ($prefecture_count > 3) {
        $region_display = $prefecture_count . '都道府県';
    } elseif ($prefecture_count > 1) {
        $region_names = array_map(function($p) { return $p->name; }, array_slice($prefectures, 0, 2));
        $region_display = implode('・', $region_names);
        if ($prefecture_count > 2) {
            $region_display .= '他';
        }
    } else {
        $region_display = $prefectures[0]->name;
    }
}

// 金額フォーマット
$formatted_amount = '';
$amount_range = '';
if ($max_amount_numeric > 0) {
    if ($max_amount_numeric >= 100000000) {
        $formatted_amount = number_format($max_amount_numeric / 100000000, 1) . '億円';
    } elseif ($max_amount_numeric >= 10000) {
        $formatted_amount = number_format($max_amount_numeric / 10000) . '万円';
    } else {
        $formatted_amount = number_format($max_amount_numeric) . '円';
    }
    
    if ($min_amount > 0) {
        $formatted_min = '';
        if ($min_amount >= 10000) {
            $formatted_min = number_format($min_amount / 10000) . '万円';
        } else {
            $formatted_min = number_format($min_amount) . '円';
        }
        $amount_range = $formatted_min . ' 〜 ' . $formatted_amount;
    } else {
        $amount_range = '上限 ' . $formatted_amount;
    }
} elseif ($max_amount) {
    $formatted_amount = $max_amount;
    $amount_range = $max_amount;
}

// ステータス表示
$status_config = array(
    'open' => array('label' => '募集中', 'class' => 'status-open'),
    'upcoming' => array('label' => '募集予定', 'class' => 'status-upcoming'),
    'closed' => array('label' => '募集終了', 'class' => 'status-closed'),
    'suspended' => array('label' => '一時停止', 'class' => 'status-suspended'),
);
$status_data = $status_config[$application_status] ?? $status_config['open'];

// 締切日情報
$deadline_info = array();
$days_remaining = 0;

if ($deadline_date) {
    $deadline_timestamp = strtotime($deadline_date);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
        
        if ($days_remaining <= 0) {
            $deadline_info = array('class' => 'deadline-expired', 'text' => '締切済', 'urgent' => true);
        } elseif ($days_remaining <= 7) {
            $deadline_info = array('class' => 'deadline-critical', 'text' => '残り'.$days_remaining.'日', 'urgent' => true);
        } elseif ($days_remaining <= 30) {
            $deadline_info = array('class' => 'deadline-warning', 'text' => '残り'.$days_remaining.'日', 'urgent' => false);
        } else {
            $deadline_info = array('class' => 'deadline-normal', 'text' => date('Y/m/d', $deadline_timestamp), 'urgent' => false);
        }
    }
} elseif ($deadline) {
    $deadline_info = array('class' => 'deadline-normal', 'text' => $deadline, 'urgent' => false);
}

// 申請方法ラベル
$method_labels = array(
    'online' => 'オンライン',
    'mail' => '郵送',
    'visit' => '窓口',
    'mixed' => 'オンライン・郵送',
);
$method_label = $method_labels[$application_method] ?? '詳細参照';

// 組織タイプ表示
$org_type_labels = array(
    'national' => '国',
    'prefecture' => '都道府県',
    'city' => '市区町村',
    'public_org' => '公的機関',
    'private_org' => '民間',
    'other' => 'その他',
);
$org_type_label = $org_type_labels[$organization_type] ?? '';

// レコメンド理由を生成
$recommend_reasons = array();

if ($is_featured) {
    $recommend_reasons[] = '注目の助成金';
}

if ($adoption_rate >= 70) {
    $recommend_reasons[] = '高採択率';
} elseif ($adoption_rate >= 50) {
    $recommend_reasons[] = '採択実績あり';
}

if ($days_remaining > 0 && $days_remaining <= 30) {
    $recommend_reasons[] = '締切間近';
}

if ($max_amount_numeric >= 10000000) {
    $recommend_reasons[] = '高額助成';
}

if ($grant_difficulty === 'easy') {
    $recommend_reasons[] = '申請しやすい';
}

if (empty($recommend_reasons)) {
    $recommend_reasons[] = '新着情報';
}

// AI用Nonce生成
$ai_nonce = wp_create_nonce('gi_ajax_nonce');
?>

<style>
/* ============================================
   🎨 Portal List Card Design v2.1 FINAL
   Light Mode Only - ダークモード完全無効化
============================================ */

.grant-card-list-portal {
    /* ライトモード固定カラー */
    --portal-primary: #000000;
    --portal-secondary: #333333;
    --portal-accent: #FFEB3B;
    --portal-bg: #FFFFFF;
    --portal-surface: #F8F8F8;
    --portal-border: #E5E5E5;
    --portal-text: #1A1A1A;
    --portal-text-muted: #666666;
    --portal-text-light: #999999;
    --portal-success: #10B981;
    --portal-warning: #F59E0B;
    --portal-danger: #EF4444;
    --portal-info: #3B82F6;
    
    /* AI Modal colors - ライトモード固定 */
    --ai-modal-bg: #FFFFFF;
    --ai-modal-overlay: rgba(0, 0, 0, 0.75);
    --ai-modal-header-bg: #000000;
    --ai-modal-header-text: #FFFFFF;
    --ai-modal-border: #E5E5E5;
    --ai-modal-body-bg: #FAFAFA;
    --ai-message-assistant-bg: #F8F8F8;
    --ai-message-assistant-text: #1A1A1A;
    --ai-message-user-bg: #000000;
    --ai-message-user-text: #FFFFFF;
    --ai-input-bg: #F8F8F8;
    --ai-input-focus-bg: #FFFFFF;
    --ai-input-text: #1A1A1A;
    --ai-button-bg: #FFEB3B;
    --ai-button-text: #000000;
    --ai-button-hover-bg: #000000;
    --ai-button-hover-text: #FFFFFF;
    
    /* ダークモード強制無効化 */
    color-scheme: light !important;
}

/* ダークモードメディアクエリを無効化 */
@media (prefers-color-scheme: dark) {
    .grant-card-list-portal,
    .portal-ai-modal,
    .portal-ai-modal-container,
    .portal-ai-modal-header,
    .portal-ai-modal-body,
    .portal-ai-chat-messages,
    .portal-ai-message-content {
        /* 強制的にライトモードカラーを適用 */
        color-scheme: light !important;
        background: var(--portal-bg) !important;
        color: var(--portal-text) !important;
    }
}

.grant-card-list-portal {
    background: var(--portal-bg);
    border: 2px solid var(--portal-border);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    gap: 20px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.grant-card-list-portal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--portal-accent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.grant-card-list-portal:hover {
    border-color: var(--portal-primary);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.grant-card-list-portal:hover::before {
    opacity: 1;
}

/* ===== 左サイド：メイン情報 ===== */
.card-main-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ヘッダー */
.card-header-portal {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.card-badges-portal {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 6px;
}

.badge-featured {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #000000;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(255, 165, 0, 0.3);
}

.badge-category {
    background: var(--portal-surface);
    color: var(--portal-text);
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    border: 1px solid var(--portal-border);
}

.badge-status {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    border: 1px solid;
}

.status-open {
    background: #ECFDF5;
    color: #059669;
    border-color: #059669;
}

.status-upcoming {
    background: #EFF6FF;
    color: #2563EB;
    border-color: #2563EB;
}

.status-closed {
    background: #F3F4F6;
    color: #6B7280;
    border-color: #9CA3AF;
}

/* タイトル */
.card-title-portal {
    font-size: 17px;
    font-weight: 700;
    line-height: 1.4;
    margin: 0 0 10px 0;
    color: var(--portal-text);
}

.card-title-portal a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}

.card-title-portal a:hover {
    color: var(--portal-info);
}

/* 地域情報 */
.card-region-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--portal-text-muted);
    margin-bottom: 10px;
}

.region-icon {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
}

.region-main {
    font-weight: 600;
    color: var(--portal-text);
}

/* AI要約 */
.card-ai-summary-portal {
    background: linear-gradient(135deg, #FFFBEA 0%, #FFF9E6 100%);
    border: 2px solid var(--portal-accent);
    border-radius: 6px;
    padding: 12px;
    position: relative;
    margin-bottom: 12px;
}

.card-ai-summary-portal::before {
    content: 'AI要約';
    position: absolute;
    top: -9px;
    left: 10px;
    background: var(--portal-accent);
    color: var(--portal-primary);
    padding: 2px 8px;
    font-size: 9px;
    font-weight: 800;
    border-radius: 3px;
    letter-spacing: 0.5px;
}

.ai-summary-text {
    font-size: 12px;
    line-height: 1.6;
    color: var(--portal-secondary);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 詳細情報グリッド */
.card-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
    padding: 12px;
    background: var(--portal-surface);
    border-radius: 6px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.detail-item-icon {
    width: 13px;
    height: 13px;
    stroke: var(--portal-text-muted);
    stroke-width: 2;
    margin-bottom: 2px;
}

.detail-label {
    font-size: 10px;
    color: var(--portal-text-light);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.detail-value {
    font-size: 13px;
    color: var(--portal-text);
    font-weight: 600;
}

/* 実施機関情報 */
.card-organization-detail {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    flex-wrap: wrap;
}

.org-type-badge {
    background: var(--portal-primary);
    color: var(--portal-bg);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: 600;
}

/* 抜粋テキスト */
.card-excerpt-portal {
    padding: 12px;
    background: var(--portal-bg);
    border: 1px solid var(--portal-border);
    border-radius: 6px;
    margin-top: 12px;
}

.excerpt-label {
    font-size: 10px;
    color: var(--portal-text-light);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.excerpt-icon {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    stroke-width: 2;
}

.excerpt-text {
    font-size: 13px;
    line-height: 1.6;
    color: var(--portal-text);
    margin: 0;
}

/* タグリスト */
.card-tags-portal {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
}

.tag-item {
    background: var(--portal-bg);
    border: 1px solid var(--portal-border);
    color: var(--portal-text-muted);
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 500;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 3px;
}

.tag-icon {
    width: 11px;
    height: 11px;
    stroke: currentColor;
    stroke-width: 2;
}

.tag-item:hover {
    background: var(--portal-primary);
    color: var(--portal-bg);
    border-color: var(--portal-primary);
}

/* ===== 右サイド：アクション＆メタ情報 ===== */
.card-side-section {
    width: 240px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex-shrink: 0;
}

/* 金額表示 */
.card-amount-box {
    background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
    border: 2px solid #10B981;
    border-radius: 6px;
    padding: 12px;
    text-align: center;
}

.amount-icon {
    width: 20px;
    height: 20px;
    stroke: #059669;
    stroke-width: 2;
    margin: 0 auto 6px;
}

.amount-label {
    font-size: 10px;
    color: #065F46;
    font-weight: 600;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.amount-value {
    font-size: 20px;
    font-weight: 800;
    color: #059669;
    line-height: 1.2;
}

.amount-range {
    font-size: 11px;
    color: #047857;
    margin-top: 3px;
}

/* 締切表示 */
.card-deadline-box {
    background: var(--portal-surface);
    border: 2px solid var(--portal-border);
    border-radius: 6px;
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.deadline-icon {
    width: 20px;
    height: 20px;
    stroke: var(--portal-text-muted);
    stroke-width: 2;
    flex-shrink: 0;
}

.deadline-content {
    flex: 1;
}

.deadline-label {
    font-size: 9px;
    color: var(--portal-text-light);
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.deadline-value {
    font-size: 13px;
    font-weight: 700;
    color: var(--portal-text);
}

.deadline-critical {
    border-color: var(--portal-danger);
    background: #FEF2F2;
}

.deadline-critical .deadline-icon {
    stroke: var(--portal-danger);
}

.deadline-critical .deadline-value {
    color: var(--portal-danger);
}

.deadline-warning {
    border-color: var(--portal-warning);
    background: #FFFBEB;
}

.deadline-warning .deadline-icon {
    stroke: var(--portal-warning);
}

.deadline-warning .deadline-value {
    color: var(--portal-warning);
}

/* 採択率バー */
.adoption-rate-display {
    background: var(--portal-surface);
    border-radius: 6px;
    padding: 10px;
}

.rate-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.rate-label {
    font-size: 10px;
    color: var(--portal-text-muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 3px;
}

.rate-icon {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    stroke-width: 2;
}

.rate-percentage {
    font-size: 14px;
    font-weight: 700;
    color: var(--portal-success);
}

.rate-bar-container {
    height: 6px;
    background: var(--portal-border);
    border-radius: 3px;
    overflow: hidden;
}

.rate-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #10B981, #34D399);
    transition: width 1s ease-out;
    border-radius: 3px;
}

/* レコメンド表示 */
.card-recommend-box {
    background: var(--portal-surface);
    border: 2px solid var(--portal-border);
    border-radius: 6px;
    padding: 10px;
}

.recommend-label {
    font-size: 9px;
    color: var(--portal-text-light);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.recommend-icon {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    stroke-width: 2;
}

.recommend-reasons {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.recommend-reason {
    font-size: 11px;
    color: var(--portal-text);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.recommend-reason::before {
    content: '✓';
    font-size: 10px;
    font-weight: 700;
    color: var(--portal-success);
}

/* アクションボタン */
.card-actions-portal {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-portal {
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    border: 2px solid;
    pointer-events: auto !important;
    position: relative;
    z-index: 10;
}

.btn-icon {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
}

.btn-primary {
    background: var(--portal-primary);
    color: var(--portal-bg);
    border-color: var(--portal-primary);
}

.btn-primary:hover {
    background: var(--portal-secondary);
    border-color: var(--portal-secondary);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* AIボタン */
.btn-ai {
    background: var(--portal-bg);
    color: var(--portal-primary);
    border-color: var(--portal-primary);
}

.btn-ai:hover {
    background: var(--portal-primary);
    color: var(--portal-bg);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* ===== モバイル最適化 ===== */
@media (max-width: 1024px) {
    .grant-card-list-portal {
        flex-direction: column;
    }
    
    .card-side-section {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .card-actions-portal {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    
    .card-details-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .grant-card-list-portal {
        padding: 16px;
        gap: 14px;
    }
    
    .card-title-portal {
        font-size: 16px;
    }
    
    .card-side-section {
        grid-template-columns: 1fr;
    }
    
    .card-actions-portal {
        grid-template-columns: 1fr;
    }
    
    .card-details-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .amount-value {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .grant-card-list-portal {
        padding: 12px;
        gap: 12px;
    }
    
    .card-title-portal {
        font-size: 15px;
    }
    
    .card-badges-portal {
        gap: 4px;
    }
    
    .badge-featured,
    .badge-category,
    .badge-status {
        font-size: 9px;
        padding: 2px 6px;
    }
    
    .card-details-grid {
        padding: 10px;
        gap: 8px;
    }
    
    .detail-label {
        font-size: 9px;
    }
    
    .detail-value {
        font-size: 12px;
    }
    
    .excerpt-text {
        font-size: 12px;
    }
    
    .btn-portal {
        padding: 9px 14px;
        font-size: 12px;
    }
}

/* ============================================
   🤖 AI Modal Light Mode Only v2.1
   ダークモード完全無効化 + 視認性改善
============================================ */

/* Modal Container - ライトモード固定 */
.portal-ai-modal {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color-scheme: light !important;
}

.portal-ai-modal.active {
    opacity: 1;
    visibility: visible;
}

/* Overlay with Blur Effect */
.portal-ai-modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Modal Container */
.portal-ai-modal-container {
    position: relative;
    width: 90%;
    max-width: 600px;
    height: 80vh;
    max-height: 700px;
    background: #FFFFFF !important;
    border: 2px solid #E5E5E5;
    border-radius: 16px;
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.95) translateY(20px);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color-scheme: light !important;
}

.portal-ai-modal.active .portal-ai-modal-container {
    transform: scale(1) translateY(0);
}

/* Modal Header */
.portal-ai-modal-header {
    padding: 20px 24px;
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    color: #FFFFFF;
    border-bottom: 2px solid #E5E5E5;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.portal-ai-modal-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 8px;
    letter-spacing: -0.3px;
    color: #FFFFFF;
}

.portal-ai-modal-title svg {
    flex-shrink: 0;
    filter: drop-shadow(0 2px 4px rgba(255, 235, 59, 0.3));
}

.portal-ai-modal-subtitle {
    font-size: 13px;
    opacity: 0.85;
    max-width: 85%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
    padding-left: 32px;
    color: #FFFFFF;
}

/* Close Button */
.portal-ai-modal-close {
    position: absolute;
    top: 18px;
    right: 20px;
    width: 36px;
    height: 36px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: #FFFFFF;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 22px;
    font-weight: 700;
    line-height: 1;
    backdrop-filter: blur(10px);
}

.portal-ai-modal-close:hover {
    background: #FFEB3B;
    color: #000000;
    border-color: #FFEB3B;
    transform: rotate(90deg) scale(1.05);
    box-shadow: 0 4px 12px rgba(255, 235, 59, 0.4);
}

.portal-ai-modal-close:active {
    transform: rotate(90deg) scale(0.95);
}

/* Modal Body */
.portal-ai-modal-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #FAFAFA !important;
    color-scheme: light !important;
}

/* Chat Messages Area */
.portal-ai-chat-messages {
    flex: 1;
    padding: 24px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
    scroll-behavior: smooth;
    background: #FAFAFA !important;
    color-scheme: light !important;
}

/* Custom Scrollbar */
.portal-ai-chat-messages::-webkit-scrollbar {
    width: 8px;
}

.portal-ai-chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.portal-ai-chat-messages::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
    transition: background 0.2s;
}

.portal-ai-chat-messages::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Message Bubble */
.portal-ai-message {
    display: flex;
    gap: 12px;
    max-width: 85%;
    animation: messageSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.portal-ai-message--assistant {
    align-self: flex-start;
}

.portal-ai-message--user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

/* Message Avatar */
.portal-ai-message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.portal-ai-message:hover .portal-ai-message-avatar {
    transform: scale(1.05);
}

.portal-ai-message--assistant .portal-ai-message-avatar {
    background: linear-gradient(135deg, #000000 0%, #333333 100%);
    color: #FFEB3B;
    border-color: #000000;
}

.portal-ai-message--user .portal-ai-message-avatar {
    background: linear-gradient(135deg, #FFEB3B 0%, #FFD54F 100%);
    color: #000000;
    border-color: #FFEB3B;
}

/* Message Content */
.portal-ai-message-content {
    background: #F8F8F8 !important;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1px solid #E5E5E5;
    font-size: 14px;
    line-height: 1.7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    position: relative;
    transition: all 0.2s ease;
    color: #1A1A1A !important;
    color-scheme: light !important;
}

.portal-ai-message-content:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.portal-ai-message--user .portal-ai-message-content {
    background: #000000 !important;
    color: #FFFFFF !important;
    border-color: #000000;
}

/* Message Content Links */
.portal-ai-message-content a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: rgba(0, 0, 0, 0.05);
    color: inherit;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    border-radius: 20px;
    transition: all 0.2s ease;
    margin-top: 8px;
}

.portal-ai-message--user .portal-ai-message-content a {
    background: rgba(255, 255, 255, 0.2);
}

.portal-ai-message-content a:hover {
    background: #FFEB3B;
    color: #000000;
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(255, 235, 59, 0.3);
}

/* Chat Input Container */
.portal-ai-chat-input-container {
    padding: 20px 24px;
    background: #FFFFFF !important;
    border-top: 2px solid #E5E5E5;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
    color-scheme: light !important;
}

/* Input Wrapper */
.portal-ai-chat-input-wrapper {
    display: flex;
    gap: 12px;
    margin-bottom: 14px;
}

/* Chat Input */
.portal-ai-chat-input {
    flex: 1;
    padding: 14px 18px;
    border: 2px solid #E5E5E5;
    border-radius: 12px;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.5;
    resize: none;
    transition: all 0.2s ease;
    min-height: 52px;
    max-height: 120px;
    background: #F8F8F8 !important;
    color: #1A1A1A !important;
    color-scheme: light !important;
}

.portal-ai-chat-input::placeholder {
    color: #666666;
}

.portal-ai-chat-input:focus {
    outline: none;
    border-color: #000000;
    background: #FFFFFF !important;
    box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.05);
}

/* Send Button */
.portal-ai-chat-send {
    width: 52px;
    height: 52px;
    background: #FFEB3B;
    color: #000000;
    border: 2px solid #FFEB3B;
    border-radius: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 235, 59, 0.3);
}

.portal-ai-chat-send:hover:not(:disabled) {
    background: #000000;
    color: #FFFFFF;
    border-color: #000000;
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.portal-ai-chat-send:active:not(:disabled) {
    transform: scale(0.95);
}

.portal-ai-chat-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Suggestions */
.portal-ai-chat-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.portal-ai-suggestion {
    padding: 9px 16px;
    background: #FFFFFF;
    border: 2px solid #E5E5E5;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #666666;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.portal-ai-suggestion:hover {
    background: #FFEB3B;
    color: #000000;
    border-color: #FFEB3B;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 235, 59, 0.3);
}

.portal-ai-suggestion:active {
    transform: translateY(0);
}

/* Typing Indicator */
.portal-ai-typing {
    display: flex;
    gap: 5px;
    padding: 8px 0;
}

.portal-ai-typing span {
    width: 9px;
    height: 9px;
    background: #666666;
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out;
}

.portal-ai-typing span:nth-child(1) { animation-delay: 0s; }
.portal-ai-typing span:nth-child(2) { animation-delay: 0.2s; }
.portal-ai-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 80%, 100% { 
        transform: scale(0.7); 
        opacity: 0.4; 
    }
    40% { 
        transform: scale(1); 
        opacity: 1; 
    }
}

/* Spinning Animation */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* ===== Mobile Modal Optimizations ===== */
@media (max-width: 768px) {
    .portal-ai-modal-container {
        width: 100%;
        height: 100vh;
        max-height: 100vh;
        border-radius: 0;
        border: none;
        transform: translateY(100%);
    }
    
    .portal-ai-modal.active .portal-ai-modal-container {
        transform: translateY(0);
    }
    
    .portal-ai-modal-header {
        padding: 16px 20px;
    }
    
    .portal-ai-modal-title {
        font-size: 16px;
    }
    
    .portal-ai-modal-subtitle {
        font-size: 12px;
    }
    
    .portal-ai-modal-close {
        width: 32px;
        height: 32px;
        top: 14px;
        right: 16px;
    }
    
    .portal-ai-chat-messages {
        padding: 16px;
        gap: 14px;
    }
    
    .portal-ai-message {
        max-width: 90%;
    }
    
    .portal-ai-message-avatar {
        width: 36px;
        height: 36px;
    }
    
    .portal-ai-message-content {
        padding: 12px 16px;
        font-size: 13px;
    }
    
    .portal-ai-chat-input-container {
        padding: 16px;
    }
    
    .portal-ai-chat-input {
        padding: 12px 16px;
        font-size: 13px;
    }
    
    .portal-ai-chat-send {
        width: 48px;
        height: 48px;
    }
    
    .portal-ai-suggestion {
        padding: 8px 14px;
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .portal-ai-modal-title {
        font-size: 15px;
        gap: 8px;
    }
    
    .portal-ai-modal-subtitle {
        font-size: 11px;
        padding-left: 28px;
    }
    
    .portal-ai-chat-messages {
        padding: 12px;
        gap: 12px;
    }
    
    .portal-ai-message {
        max-width: 95%;
        gap: 10px;
    }
    
    .portal-ai-message-avatar {
        width: 32px;
        height: 32px;
    }
    
    .portal-ai-message-content {
        padding: 10px 14px;
        font-size: 12px;
        line-height: 1.6;
    }
    
    .portal-ai-chat-input-container {
        padding: 12px;
    }
    
    .portal-ai-chat-input-wrapper {
        gap: 8px;
    }
    
    .portal-ai-chat-input {
        padding: 10px 14px;
        font-size: 12px;
        min-height: 44px;
    }
    
    .portal-ai-chat-send {
        width: 44px;
        height: 44px;
    }
    
    .portal-ai-chat-suggestions {
        gap: 6px;
    }
    
    .portal-ai-suggestion {
        padding: 7px 12px;
        font-size: 10px;
    }
}

/* Accessibility Improvements */
@media (prefers-reduced-motion: reduce) {
    .portal-ai-modal,
    .portal-ai-modal-container,
    .portal-ai-message,
    .portal-ai-modal-close,
    .portal-ai-chat-send,
    .portal-ai-suggestion {
        animation: none;
        transition: none;
    }
}

/* ダークモード完全無効化 - 最優先ルール */
@media (prefers-color-scheme: dark) {
    .grant-card-list-portal,
    .portal-ai-modal,
    .portal-ai-modal *,
    .portal-ai-modal-container,
    .portal-ai-modal-header,
    .portal-ai-modal-body,
    .portal-ai-chat-messages,
    .portal-ai-message-content,
    .portal-ai-chat-input-container,
    .portal-ai-chat-input {
        color-scheme: light !important;
        background: var(--portal-bg) !important;
        color: var(--portal-text) !important;
    }
    
    .portal-ai-message--assistant .portal-ai-message-content {
        background: #F8F8F8 !important;
        color: #1A1A1A !important;
    }
    
    .portal-ai-message--user .portal-ai-message-content {
        background: #000000 !important;
        color: #FFFFFF !important;
    }
    
    .portal-ai-modal-body {
        background: #FAFAFA !important;
    }
    
    .portal-ai-chat-input-container {
        background: #FFFFFF !important;
    }
    
    .portal-ai-chat-input {
        background: #F8F8F8 !important;
        color: #1A1A1A !important;
    }
    
    .portal-ai-chat-input:focus {
        background: #FFFFFF !important;
    }
}
</style>

<article class="grant-card-list-portal" 
         data-post-id="<?php echo esc_attr($post_id); ?>"
         itemscope 
         itemtype="https://schema.org/GovernmentService"
         role="article"
         aria-label="<?php echo esc_attr($title); ?>">
    
    <!-- 左サイド：メイン情報 -->
    <div class="card-main-section">
        
        <!-- ヘッダー -->
        <div class="card-header-portal">
            <div style="flex: 1;">
                <!-- バッジ -->
                <div class="card-badges-portal">
                    <?php if ($is_featured): ?>
                        <span class="badge-featured">注目</span>
                    <?php endif; ?>
                    
                    <?php if ($main_category): ?>
                        <span class="badge-category" itemprop="category"><?php echo esc_html($main_category); ?></span>
                    <?php endif; ?>
                    
                    <span class="badge-status <?php echo esc_attr($status_data['class']); ?>">
                        <?php echo esc_html($status_data['label']); ?>
                    </span>
                </div>
                
                <!-- タイトル -->
                <h3 class="card-title-portal" itemprop="name">
                    <a href="<?php echo esc_url($permalink); ?>" 
                       itemprop="url"
                       aria-label="<?php echo esc_attr($title); ?>の詳細">
                        <?php echo esc_html($title); ?>
                    </a>
                </h3>
                
                <!-- 地域情報 -->
                <div class="card-region-info" itemprop="areaServed">
                    <svg class="region-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span class="region-main"><?php echo esc_html($region_display); ?></span>
                </div>
            </div>
        </div>
        
        <!-- AI要約 -->
        <?php if ($ai_summary): ?>
        <div class="card-ai-summary-portal">
            <p class="ai-summary-text" itemprop="description"><?php echo esc_html($ai_summary); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- 詳細情報グリッド -->
        <div class="card-details-grid">
            
            <!-- 対象者 -->
            <?php if ($grant_target): ?>
            <div class="detail-item">
                <span class="detail-label">
                    <svg class="detail-item-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    対象者
                </span>
                <span class="detail-value" itemprop="audience">
                    <?php echo wp_trim_words(strip_tags($grant_target), 10, '...'); ?>
                </span>
            </div>
            <?php endif; ?>
            
            <!-- 実施機関 -->
            <?php if ($organization): ?>
            <div class="detail-item">
                <span class="detail-label">
                    <svg class="detail-item-icon" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M7 8h10M7 12h7M7 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    実施機関
                </span>
                <div class="detail-value card-organization-detail" itemprop="provider" itemscope itemtype="https://schema.org/Organization">
                    <?php if ($org_type_label): ?>
                        <span class="org-type-badge"><?php echo esc_html($org_type_label); ?></span>
                    <?php endif; ?>
                    <span itemprop="name"><?php echo esc_html($organization); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 補助率 -->
            <?php if ($subsidy_rate_detailed): ?>
            <div class="detail-item">
                <span class="detail-label">
                    <svg class="detail-item-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    補助率
                </span>
                <span class="detail-value"><?php echo esc_html($subsidy_rate_detailed); ?></span>
            </div>
            <?php endif; ?>
            
            <!-- 申請方法 -->
            <div class="detail-item">
                <span class="detail-label">
                    <svg class="detail-item-icon" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    申請方法
                </span>
                <span class="detail-value"><?php echo esc_html($method_label); ?></span>
            </div>
            
        </div>
        
        <!-- 抜粋テキスト（SEO対策） -->
        <?php if ($excerpt): ?>
        <div class="card-excerpt-portal">
            <div class="excerpt-label">
                <svg class="excerpt-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M4 6h16M4 12h16M4 18h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                概要
            </div>
            <p class="excerpt-text" itemprop="abstract"><?php echo esc_html($excerpt); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- タグ -->
        <?php if ($eligible_expenses || $required_documents || $difficulty_level): ?>
        <div class="card-tags-portal">
            <?php if ($eligible_expenses): ?>
                <span class="tag-item">
                    <svg class="tag-icon" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    対象経費あり
                </span>
            <?php endif; ?>
            <?php if ($required_documents): ?>
                <span class="tag-item">
                    <svg class="tag-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
                        <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    必要書類あり
                </span>
            <?php endif; ?>
            <?php if ($difficulty_level): ?>
                <span class="tag-item">
                    <svg class="tag-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M18 20V10M12 20V4M6 20v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    難易度: <?php echo esc_html($difficulty_level); ?>
                </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
    </div>
    
    <!-- 右サイド：アクション＆メタ情報 -->
    <div class="card-side-section">
        
        <!-- 金額表示 -->
        <?php if ($formatted_amount): ?>
        <div class="card-amount-box" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
            <svg class="amount-icon" viewBox="0 0 24 24" fill="none">
                <line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div class="amount-label">最大助成額</div>
            <div class="amount-value" itemprop="price" content="<?php echo esc_attr($max_amount_numeric); ?>">
                <?php echo esc_html($formatted_amount); ?>
            </div>
            <meta itemprop="priceCurrency" content="JPY">
            <?php if ($amount_range && $amount_range !== $formatted_amount): ?>
                <div class="amount-range"><?php echo esc_html($amount_range); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- 締切表示 -->
        <?php if (!empty($deadline_info)): ?>
        <div class="card-deadline-box <?php echo esc_attr($deadline_info['class']); ?>">
            <svg class="deadline-icon" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div class="deadline-content">
                <div class="deadline-label">締切</div>
                <div class="deadline-value" itemprop="validThrough" content="<?php echo esc_attr($deadline_date); ?>">
                    <?php echo esc_html($deadline_info['text']); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 採択率 -->
        <?php if ($adoption_rate > 0): ?>
        <div class="adoption-rate-display">
            <div class="rate-header">
                <span class="rate-label">
                    <svg class="rate-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    採択率
                </span>
                <span class="rate-percentage"><?php echo esc_html($adoption_rate); ?>%</span>
            </div>
            <div class="rate-bar-container">
                <div class="rate-bar-fill" style="width: <?php echo esc_attr($adoption_rate); ?>%"></div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- レコメンド表示 -->
        <?php if (!empty($recommend_reasons)): ?>
        <div class="card-recommend-box">
            <div class="recommend-label">
                <svg class="recommend-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2"/>
                </svg>
                おすすめポイント
            </div>
            <div class="recommend-reasons">
                <?php foreach (array_slice($recommend_reasons, 0, 3) as $reason): ?>
                    <span class="recommend-reason"><?php echo esc_html($reason); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- アクションボタン -->
        <div class="card-actions-portal">
            
            <a href="<?php echo esc_url($permalink); ?>" class="btn-portal btn-primary">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
                詳細を見る
            </a>
            
            <button class="btn-portal btn-ai grant-ai-trigger-portal" 
                    type="button"
                    data-post-id="<?php echo esc_attr($post_id); ?>"
                    data-grant-id="<?php echo esc_attr($post_id); ?>" 
                    data-grant-title="<?php echo esc_attr($title); ?>"
                    data-grant-permalink="<?php echo esc_url($permalink); ?>"
                    data-nonce="<?php echo esc_attr($ai_nonce); ?>"
                    aria-label="AIアシスタントに質問">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
                    <circle cx="9" cy="10" r="1" fill="currentColor"/>
                    <circle cx="15" cy="10" r="1" fill="currentColor"/>
                </svg>
                AIに質問
            </button>
            
        </div>
        
    </div>
    
</article>

<script>
// ============================================
// 🔥 Portal Card AI Chat - Light Mode Only v2.1
// ダークモード完全無効化版
// ============================================
(function() {
    'use strict';
    
    console.log('🚀 Portal Card AI Chat Script v2.1 - Light Mode Only');
    
    let currentEscHandler = null;
    
    // DOMContentLoadedを待つ
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPortalAIChat);
    } else {
        initPortalAIChat();
    }
    
    function initPortalAIChat() {
        console.log('✅ Portal AI Chat initialization started');
        
        // AI Searchセクションのコントローラーを利用
        const searchSection = document.getElementById('ai-search-section');
        if (searchSection && searchSection._aiController) {
            console.log('✅ Using AI Search controller (preferred method)');
            bindPortalCardsToAISearch(searchSection._aiController);
        } else {
            console.log('⚠️ AI Search controller not found, using standalone mode');
            initStandalonePortalAI();
        }
    }
    
    // ========================================
    // Method 1: AI Searchコントローラー利用
    // ========================================
    function bindPortalCardsToAISearch(controller) {
        document.addEventListener('click', function(e) {
            const aiButton = e.target.closest('.grant-ai-trigger-portal');
            if (aiButton) {
                e.preventDefault();
                e.stopPropagation();
                
                const postId = aiButton.dataset.postId || aiButton.dataset.grantId;
                const grantTitle = aiButton.dataset.grantTitle;
                const grantPermalink = aiButton.dataset.grantPermalink;
                
                console.log('🎯 Portal AI button clicked (AI Search):', { postId, grantTitle, grantPermalink });
                
                if (postId && grantTitle && grantPermalink) {
                    controller.showGrantAssistant(postId, grantTitle, grantPermalink);
                } else {
                    console.error('❌ Missing grant data');
                }
            }
        });
        
        console.log('✅ Portal cards bound to AI Search controller');
    }
    
    // ========================================
    // Method 2: スタンドアロンモード（独自モーダル）
    // ========================================
    function initStandalonePortalAI() {
        document.addEventListener('click', function(e) {
            const aiButton = e.target.closest('.grant-ai-trigger-portal');
            if (aiButton) {
                e.preventDefault();
                e.stopPropagation();
                
                const postId = aiButton.dataset.postId || aiButton.dataset.grantId;
                const grantTitle = aiButton.dataset.grantTitle;
                const grantPermalink = aiButton.dataset.grantPermalink;
                const nonce = aiButton.dataset.nonce;
                
                console.log('🎯 Portal AI button clicked (Standalone):', { postId, grantTitle, grantPermalink, nonce });
                
                if (postId && grantTitle && grantPermalink) {
                    showPortalAIModal(postId, grantTitle, grantPermalink, nonce);
                } else {
                    console.error('❌ Missing grant data');
                    alert('エラー: 助成金データが不足しています');
                }
            }
        });
        
        console.log('✅ Standalone Portal AI initialized');
    }
    
    // ========================================
    // Portal AI Modal Creation
    // ========================================
    function showPortalAIModal(postId, grantTitle, grantPermalink, nonce) {
        console.log('📱 Opening Portal AI Modal:', { postId, grantTitle });
        
        // 既存のモーダルを削除
        const existingModal = document.querySelector('.portal-ai-modal');
        if (existingModal) {
            existingModal.remove();
        }
        
        const modalHTML = `
            <div class="portal-ai-modal" id="portal-ai-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                <div class="portal-ai-modal-overlay" aria-hidden="true"></div>
                <div class="portal-ai-modal-container">
                    <div class="portal-ai-modal-header">
                        <div class="portal-ai-modal-title" id="modal-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                <circle cx="9" cy="10" r="1"/>
                                <circle cx="15" cy="10" r="1"/>
                            </svg>
                            <span>AI助成金アシスタント</span>
                        </div>
                        <div class="portal-ai-modal-subtitle">${escapeHtml(grantTitle)}</div>
                        <button class="portal-ai-modal-close" aria-label="モーダルを閉じる">×</button>
                    </div>
                    <div class="portal-ai-modal-body">
                        <div class="portal-ai-chat-messages" id="portal-ai-chat-messages-${postId}" role="log" aria-live="polite" aria-atomic="false">
                            <div class="portal-ai-message portal-ai-message--assistant">
                                <div class="portal-ai-message-avatar" aria-hidden="true">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2v20M2 12h20"/>
                                    </svg>
                                </div>
                                <div class="portal-ai-message-content">
                                    こんにちは！この助成金について何でもお聞きください。<br>
                                    申請条件、必要書類、申請方法、対象経費など、詳しくお答えします。
                                </div>
                            </div>
                        </div>
                        <div class="portal-ai-chat-input-container">
                            <div class="portal-ai-chat-input-wrapper">
                                <textarea 
                                    class="portal-ai-chat-input" 
                                    id="portal-ai-chat-input-${postId}"
                                    placeholder="例：申請条件は何ですか?"
                                    rows="2"
                                    aria-label="質問を入力してください"></textarea>
                                <button 
                                    class="portal-ai-chat-send" 
                                    id="portal-ai-chat-send-${postId}"
                                    data-post-id="${postId}"
                                    data-nonce="${nonce}"
                                    data-permalink="${grantPermalink}"
                                    aria-label="質問を送信">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <line x1="22" y1="2" x2="11" y2="13"/>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="portal-ai-chat-suggestions" role="group" aria-label="質問の候補">
                                <button class="portal-ai-suggestion" data-question="申請条件を詳しく教えてください">
                                    申請条件は?
                                </button>
                                <button class="portal-ai-suggestion" data-question="必要な書類を教えてください">
                                    必要書類は?
                                </button>
                                <button class="portal-ai-suggestion" data-question="どんな費用が対象になりますか?">
                                    対象経費は?
                                </button>
                                <button class="portal-ai-suggestion" data-question="申請方法を教えてください">
                                    申請方法は?
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Body scroll lock
        document.body.style.overflow = 'hidden';
        
        // モーダルをアクティブ化
        setTimeout(() => {
            const modal = document.getElementById('portal-ai-modal');
            if (modal) {
                modal.classList.add('active');
            }
        }, 10);
        
        setupPortalModalEvents(postId, grantPermalink, nonce);
        
        // 入力フォーカス
        setTimeout(() => {
            const input = document.getElementById(`portal-ai-chat-input-${postId}`);
            if (input) input.focus();
        }, 100);
    }
    
    // ========================================
    // Modal Event Listeners
    // ========================================
    function setupPortalModalEvents(postId, grantPermalink, nonce) {
        const modal = document.getElementById('portal-ai-modal');
        if (!modal) return;
        
        // Close handlers
        modal.querySelector('.portal-ai-modal-overlay')?.addEventListener('click', closePortalAIModal);
        modal.querySelector('.portal-ai-modal-close')?.addEventListener('click', closePortalAIModal);
        
        // Send button
        const sendBtn = document.getElementById(`portal-ai-chat-send-${postId}`);
        if (sendBtn) {
            sendBtn.addEventListener('click', () => {
                const inputId = `portal-ai-chat-input-${postId}`;
                sendPortalAIQuestion(postId, inputId, sendBtn, grantPermalink, nonce);
            });
        }
        
        // Input Enter key
        const input = document.getElementById(`portal-ai-chat-input-${postId}`);
        if (input) {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    const sendBtn = document.getElementById(`portal-ai-chat-send-${postId}`);
                    sendPortalAIQuestion(postId, input.id, sendBtn, grantPermalink, nonce);
                }
            });
            
            // Auto-resize
            input.addEventListener('input', () => {
                input.style.height = 'auto';
                input.style.height = Math.min(input.scrollHeight, 120) + 'px';
            });
        }
        
        // Suggestion buttons
        modal.querySelectorAll('.portal-ai-suggestion').forEach(btn => {
            btn.addEventListener('click', function() {
                selectPortalSuggestion(postId, this.getAttribute('data-question'), grantPermalink, nonce);
            });
        });
        
        // Escape key
        currentEscHandler = (e) => {
            if (e.key === 'Escape') closePortalAIModal();
        };
        document.addEventListener('keydown', currentEscHandler);
    }
    
    function closePortalAIModal() {
        const modal = document.querySelector('.portal-ai-modal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            setTimeout(() => {
                modal.remove();
                if (currentEscHandler) {
                    document.removeEventListener('keydown', currentEscHandler);
                    currentEscHandler = null;
                }
            }, 300);
        }
    }
    
    function selectPortalSuggestion(postId, question, grantPermalink, nonce) {
        const input = document.getElementById(`portal-ai-chat-input-${postId}`);
        if (input) {
            input.value = question;
            input.focus();
            const sendBtn = document.getElementById(`portal-ai-chat-send-${postId}`);
            setTimeout(() => sendPortalAIQuestion(postId, input.id, sendBtn, grantPermalink, nonce), 300);
        }
    }
    
    // ========================================
    // AI Question Sending
    // ========================================
    function sendPortalAIQuestion(postId, inputId, sendBtn, grantPermalink, nonce) {
        const input = document.getElementById(inputId);
        const messagesContainer = document.getElementById(`portal-ai-chat-messages-${postId}`);
        
        if (!input || !messagesContainer) {
            console.error('❌ Input or messages container not found');
            return;
        }
        
        const question = input.value.trim();
        if (!question) return;
        
        console.log('📤 Sending AI question:', { postId, question });
        
        // Disable send button
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="animate-spin"><circle cx="12" cy="12" r="10"/></svg>';
        }
        
        // Add user message
        addPortalMessage(messagesContainer, question, 'user');
        input.value = '';
        input.style.height = 'auto';
        
        // Prepare request
        const formData = new FormData();
        formData.append('action', 'handle_grant_ai_question');
        formData.append('post_id', postId);
        formData.append('question', question);
        formData.append('nonce', nonce || '<?php echo wp_create_nonce('gi_ajax_nonce'); ?>');
        
        const ajaxUrl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';
        
        console.log('🌐 Sending request to:', ajaxUrl);
        console.log('📋 Request data:', { action: 'handle_grant_ai_question', post_id: postId, question, nonce });
        
        // Show typing indicator
        const typingIndicator = addTypingIndicator(messagesContainer);
        
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('📥 Response received:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Response data:', data);
            
            // Remove typing indicator
            if (typingIndicator) {
                typingIndicator.remove();
            }
            
            if (data.success) {
                addPortalMessage(messagesContainer, data.data.response, 'assistant', grantPermalink);
                
                // Update suggestions if provided
                if (data.data.suggestions) {
                    updatePortalSuggestions(postId, data.data.suggestions, grantPermalink, nonce);
                }
            } else {
                const errorMsg = data.data?.message || '申し訳ございません。エラーが発生しました。';
                console.error('❌ AI response error:', errorMsg);
                addPortalMessage(messagesContainer, errorMsg, 'assistant');
            }
        })
        .catch(error => {
            console.error('❌ Fetch error:', error);
            
            if (typingIndicator) {
                typingIndicator.remove();
            }
            
            addPortalMessage(messagesContainer, '通信エラーが発生しました。もう一度お試しください。', 'assistant');
        })
        .finally(() => {
            // Re-enable send button
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>';
            }
            input.focus();
        });
    }
    
    // ========================================
    // Message Rendering
    // ========================================
    function addPortalMessage(container, text, type, grantPermalink = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `portal-ai-message portal-ai-message--${type}`;
        messageDiv.setAttribute('role', 'article');
        messageDiv.setAttribute('aria-label', type === 'assistant' ? 'AIからの回答' : 'あなたの質問');
        
        const avatarSvg = type === 'assistant' 
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        
        let messageContent = escapeHtml(text).replace(/\n/g, '<br>');
        
        // Add detail link for assistant messages
        if (type === 'assistant' && grantPermalink) {
            messageContent += `
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(0,0,0,0.1);">
                    <a href="${grantPermalink}" target="_blank" rel="noopener noreferrer" style="
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                        padding: 8px 16px;
                        background: rgba(0,0,0,0.05);
                        color: inherit;
                        text-decoration: none;
                        font-size: 12px;
                        font-weight: 600;
                        border-radius: 20px;
                        transition: all 0.2s;
                    ">
                        詳細ページで確認
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            `;
        }
        
        messageDiv.innerHTML = `
            <div class="portal-ai-message-avatar" aria-hidden="true">${avatarSvg}</div>
            <div class="portal-ai-message-content">${messageContent}</div>
        `;
        
        container.appendChild(messageDiv);
        container.scrollTop = container.scrollHeight;
        
        // Announce to screen readers
        if (type === 'assistant') {
            const announcement = document.createElement('div');
            announcement.setAttribute('role', 'status');
            announcement.setAttribute('aria-live', 'polite');
            announcement.className = 'sr-only';
            announcement.textContent = 'AIから回答が届きました';
            document.body.appendChild(announcement);
            setTimeout(() => announcement.remove(), 1000);
        }
    }
    
    function addTypingIndicator(container) {
        const indicator = document.createElement('div');
        indicator.className = 'portal-ai-message portal-ai-message--assistant';
        indicator.setAttribute('aria-label', 'AIが入力中');
        indicator.innerHTML = `
            <div class="portal-ai-message-avatar" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="animate-spin">
                    <circle cx="12" cy="12" r="10"/>
                </svg>
            </div>
            <div class="portal-ai-message-content">
                <div class="portal-ai-typing" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        container.appendChild(indicator);
        container.scrollTop = container.scrollHeight;
        return indicator;
    }
    
    function updatePortalSuggestions(postId, suggestions, grantPermalink, nonce) {
        const suggestionsContainer = document.querySelector(`#portal-ai-modal .portal-ai-chat-suggestions`);
        if (!suggestionsContainer) return;
        
        suggestionsContainer.innerHTML = suggestions.map(suggestion => `
            <button class="portal-ai-suggestion" data-question="${escapeHtml(suggestion)}">
                ${escapeHtml(suggestion)}
            </button>
        `).join('');
        
        // Re-bind events
        suggestionsContainer.querySelectorAll('.portal-ai-suggestion').forEach(btn => {
            btn.addEventListener('click', function() {
                selectPortalSuggestion(postId, this.getAttribute('data-question'), grantPermalink, nonce);
            });
        });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    console.log('✅ Portal Card AI Chat Script v2.1 fully loaded');
    
})();
</script>