#!/usr/bin/env php
<?php
/**
 * Google Sheets連携 ステータスチェックスクリプト
 * 
 * 使用方法:
 * php check-sheets-status.php
 * 
 * または WordPress管理画面から:
 * ツール > Grant Sheets Sync > ステータス確認
 */

// WordPress環境の読み込み
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

if (!defined('ABSPATH')) {
    die("Error: WordPress environment not loaded\n");
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  📊 Google Sheets連携 ステータス診断                          ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// 1. 基本設定の確認
echo "┌─ 1️⃣  基本設定\n";
echo "│\n";

// クラスの存在確認
if (class_exists('GoogleSheetsSync')) {
    echo "│ ✅ GoogleSheetsSync クラス: 読み込み済み\n";
    $sheets = GoogleSheetsSync::getInstance();
    
    $spreadsheet_id = $sheets->get_spreadsheet_id();
    $sheet_name = $sheets->get_sheet_name();
    
    echo "│ 📋 スプレッドシートID: " . substr($spreadsheet_id, 0, 20) . "...\n";
    echo "│ 📄 シート名: {$sheet_name}\n";
} else {
    echo "│ ❌ GoogleSheetsSync クラス: 読み込みエラー\n";
}
echo "│\n";

// 2. 自動同期の状態
echo "├─ 2️⃣  自動同期の状態\n";
echo "│\n";

$auto_sync_enabled = get_option('gi_sheets_auto_sync_enabled', false);
$auto_sync_disabled_flag = get_option('gi_auto_sync_disabled_flag', false);
$scheduled_sync = wp_next_scheduled('gi_sheets_sync_cron');

if ($auto_sync_disabled_flag || !$auto_sync_enabled) {
    echo "│ ❌ 自動同期: 無効化されています\n";
    echo "│    理由: API制限対策・サーバー負荷軽減\n";
    echo "│    推奨: 手動同期を使用してください\n";
} else {
    echo "│ ✅ 自動同期: 有効\n";
}

if ($scheduled_sync) {
    echo "│ ⏰ 次回の定期同期: " . date('Y-m-d H:i:s', $scheduled_sync) . "\n";
} else {
    echo "│ ⏰ 定期同期: スケジュールなし\n";
}
echo "│\n";

// 3. 手動同期の状態
echo "├─ 3️⃣  手動同期\n";
echo "│\n";

$last_sync_time = get_option('gi_sheets_last_sync_time');
$last_sync_result = get_option('gi_sheets_last_sync_result');

if ($last_sync_time) {
    echo "│ ✅ 手動同期: 利用可能\n";
    echo "│ 🕐 最終同期: {$last_sync_time}\n";
    
    if ($last_sync_result && is_array($last_sync_result)) {
        echo "│ 📊 最終同期結果:\n";
        if (isset($last_sync_result['sheets_to_wp'])) {
            echo "│    ├─ Sheets→WP: {$last_sync_result['sheets_to_wp']}件\n";
        }
        if (isset($last_sync_result['wp_to_sheets'])) {
            echo "│    └─ WP→Sheets: {$last_sync_result['wp_to_sheets']}件\n";
        }
    }
} else {
    echo "│ ⚠️  まだ同期が実行されていません\n";
    echo "│    場所: ツール > Grant Sheets Sync\n";
}
echo "│\n";

// 4. Safe Sync Managerの状態
echo "├─ 4️⃣  Safe Sync Manager\n";
echo "│\n";

if (class_exists('SafeSyncManager')) {
    echo "│ ✅ Safe Sync Manager: 有効\n";
    
    $safe_sync = SafeSyncManager::getInstance();
    $emergency_stop = $safe_sync->is_emergency_stop_active();
    
    if ($emergency_stop) {
        echo "│ 🚨 緊急停止: アクティブ\n";
        $reason = get_option('gi_emergency_stop_reason', array());
        if (!empty($reason)) {
            echo "│    理由: " . json_encode($reason) . "\n";
        }
    } else {
        echo "│ 🟢 緊急停止: 非アクティブ\n";
    }
} else {
    echo "│ ⚠️  Safe Sync Manager: 未読み込み\n";
}
echo "│\n";

// 5. Google Apps Scriptの設定
echo "├─ 5️⃣  Google Apps Script\n";
echo "│\n";
echo "│ 📝 統合スクリプト: IntegratedSheetSync.gs\n";
echo "│ 🔧 必要なトリガー:\n";
echo "│    ├─ onEdit (編集時)\n";
echo "│    └─ onChange (変更時)\n";
echo "│\n";
echo "│ ⚠️  トリガー設定の確認方法:\n";
echo "│    1. Google Sheetsを開く\n";
echo "│    2. 拡張機能 > Apps Script\n";
echo "│    3. ⏰ アイコンをクリック\n";
echo "│\n";

// 6. 助成金投稿の統計
echo "├─ 6️⃣  助成金投稿の統計\n";
echo "│\n";

$grant_posts = get_posts(array(
    'post_type' => 'grant',
    'post_status' => array('publish', 'draft', 'private'),
    'numberposts' => -1
));

$published = 0;
$draft = 0;
$private = 0;

foreach ($grant_posts as $post) {
    switch ($post->post_status) {
        case 'publish':
            $published++;
            break;
        case 'draft':
            $draft++;
            break;
        case 'private':
            $private++;
            break;
    }
}

$total = count($grant_posts);

echo "│ 📊 投稿総数: {$total}件\n";
echo "│    ├─ 公開: {$published}件\n";
echo "│    ├─ 下書き: {$draft}件\n";
echo "│    └─ 非公開: {$private}件\n";
echo "│\n";

// 7. システム推奨事項
echo "└─ 7️⃣  推奨事項\n";
echo "\n";

if ($auto_sync_disabled_flag || !$auto_sync_enabled) {
    echo "   ✅ 現在の設定は推奨設定です（手動同期のみ）\n";
    echo "\n";
    echo "   📌 手動同期の使用方法:\n";
    echo "   1. WordPress管理画面にログイン\n";
    echo "   2. ツール > Grant Sheets Sync\n";
    echo "   3. 「今すぐ同期」をクリック\n";
    echo "\n";
} else {
    echo "   ⚠️  自動同期が有効です\n";
    echo "\n";
    echo "   注意事項:\n";
    echo "   - APIレート制限に注意してください\n";
    echo "   - Safe Sync Managerで監視を継続してください\n";
    echo "   - 定期的にログを確認してください\n";
    echo "\n";
}

// 8. 詳細情報へのリンク
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  📚 詳細情報                                                   ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "📄 GOOGLE-SHEETS-DIAGNOSIS.md    - 診断レポート\n";
echo "📄 ENABLE-AUTO-SYNC-GUIDE.md     - 自動同期有効化ガイド\n";
echo "📄 google-apps-script/           - Google Apps Scriptファイル\n";
echo "\n";

// 9. トラブルシューティング
if (!$last_sync_time || $emergency_stop) {
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  🔧 トラブルシューティング                                     ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    
    if (!$last_sync_time) {
        echo "⚠️  同期が一度も実行されていません\n";
        echo "\n";
        echo "解決方法:\n";
        echo "1. WordPress管理画面で「ツール > Grant Sheets Sync」を開く\n";
        echo "2. 「接続テスト」を実行して接続を確認\n";
        echo "3. 「今すぐ同期」を実行\n";
        echo "\n";
    }
    
    if ($emergency_stop) {
        echo "🚨 緊急停止がアクティブです\n";
        echo "\n";
        echo "解決方法:\n";
        echo "1. ツール > Safe Sync Manager を開く\n";
        echo "2. エラー内容を確認\n";
        echo "3. 問題解決後、「Deactivate Emergency Stop」をクリック\n";
        echo "\n";
    }
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  ✅ 診断完了                                                   ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// 終了コード
if ($emergency_stop) {
    exit(2); // 緊急停止中
} elseif (!$last_sync_time) {
    exit(1); // 警告: 未同期
} else {
    exit(0); // 正常
}
