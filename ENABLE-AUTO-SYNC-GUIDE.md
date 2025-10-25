# Google Sheets 自動同期 再有効化ガイド

## ⚠️ 重要な警告
このガイドは上級者向けです。自動同期を有効にすると以下のリスクがあります：
- Google Sheets APIのレート制限に達する可能性
- サーバー負荷の増加
- 予期しない同期エラー

**推奨:** 手動同期の使用を継続してください。

---

## 🔧 自動同期を再有効化する手順

### Step 1: `disable-auto-sync.php` の無効化

#### 方法A: functions.phpから読み込みを削除
```php
// /home/user/webapp/functions.php
// 以下の行をコメントアウトまたは削除

// 'disable-auto-sync.php'          // 自動同期無効化 ← この行を削除
```

#### 方法B: 無効化フラグをリセット
```php
// WordPress管理画面 > ツール > WP-CLI
wp option delete gi_auto_sync_disabled_flag
wp option update gi_sheets_auto_sync_enabled 1
```

### Step 2: Google Sheets Integrationの自動同期フックを再有効化

`/home/user/webapp/inc/google-sheets-integration.php` の編集:

```php
// Line 80-90 あたりを以下のように変更

/**
 * WordPressフックの追加（自動同期を含む）
 */
private function add_hooks() {
    // 🔄 自動同期フックを再有効化
    
    // 投稿保存時に自動同期
    add_action('save_post_grant', array($this, 'on_post_save'), 10, 3);
    add_action('post_updated', array($this, 'on_post_updated'), 10, 3);
    add_action('delete_post', array($this, 'on_post_deleted'), 10, 1);
    
    // 定期同期（1時間ごと）
    if (!wp_next_scheduled('gi_sheets_sync_cron')) {
        wp_schedule_event(time(), 'hourly', 'gi_sheets_sync_cron');
    }
    add_action('gi_sheets_sync_cron', array($this, 'scheduled_sync'));
    
    // AJAX ハンドラー
    add_action('wp_ajax_gi_manual_sheets_sync', array($this, 'ajax_manual_sync'));
    add_action('wp_ajax_gi_test_sheets_connection', array($this, 'ajax_test_connection'));
    add_action('wp_ajax_gi_setup_field_validation', array($this, 'ajax_setup_field_validation'));
    add_action('wp_ajax_gi_test_specific_fields', array($this, 'ajax_test_specific_fields'));
}
```

### Step 3: 自動同期メソッドの実装

`google-sheets-integration.php` に以下のメソッドを追加:

```php
/**
 * 投稿保存時の自動同期
 */
public function on_post_save($post_id, $post, $update) {
    // 自動保存や改訂版の場合はスキップ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // grant投稿タイプのみ
    if ($post->post_type !== 'grant') {
        return;
    }
    
    // スプレッドシートに同期
    try {
        $this->sync_post_to_sheet($post_id);
        gi_log_error('Auto sync completed', array(
            'post_id' => $post_id,
            'trigger' => 'save_post'
        ));
    } catch (Exception $e) {
        gi_log_error('Auto sync failed', array(
            'post_id' => $post_id,
            'error' => $e->getMessage()
        ));
    }
}

/**
 * 投稿削除時の処理
 */
public function on_post_deleted($post_id) {
    $post = get_post($post_id);
    
    if ($post && $post->post_type === 'grant') {
        try {
            // スプレッドシートから該当行を削除
            // またはステータスを'deleted'に変更
            gi_log_error('Post deleted, updating sheet', array(
                'post_id' => $post_id
            ));
        } catch (Exception $e) {
            gi_log_error('Failed to sync deleted post', array(
                'post_id' => $post_id,
                'error' => $e->getMessage()
            ));
        }
    }
}

/**
 * 定期同期（Cron）
 */
public function scheduled_sync() {
    try {
        // レート制限チェック
        $safe_sync = SafeSyncManager::getInstance();
        if ($safe_sync->is_emergency_stop_active()) {
            gi_log_error('Scheduled sync skipped - emergency stop active');
            return;
        }
        
        // 双方向同期を実行
        $result = $this->full_bidirectional_sync();
        
        gi_log_error('Scheduled sync completed', $result);
        
    } catch (Exception $e) {
        gi_log_error('Scheduled sync failed', array(
            'error' => $e->getMessage()
        ));
    }
}
```

### Step 4: Google Apps Scriptのトリガー設定

#### Google Apps Scriptエディターで:

1. **スクリプトを開く**
   - Google Sheetsを開く
   - **拡張機能 > Apps Script** をクリック

2. **トリガーを設定**
   - ⏰ アイコン（トリガー）をクリック
   - **トリガーを追加** をクリック

3. **onEdit トリガー**
   - 関数: `onEdit`
   - イベントのソース: スプレッドシートから
   - イベントの種類: 編集時
   - **保存**

4. **onChange トリガー**
   - 関数: `onChange`
   - イベントのソース: スプレッドシートから
   - イベントの種類: 変更時
   - **保存**

### Step 5: Webhook設定の確認

Google Apps Scriptの`WORDPRESS_CONFIG`を確認:

```javascript
const WORDPRESS_CONFIG = {
  WEBHOOK_URL: 'https://your-domain.com/?gi_sheets_webhook=true',
  REST_API_URL: 'https://your-domain.com/wp-json/gi/v1/sheets-webhook',
  SECRET_KEY: 'your_webhook_secret_key_here',
  SHEET_NAME: 'grant_import',
  WORDPRESS_BASE_URL: 'https://joseikin-insight.com',
  DEBUG_MODE: true
};
```

**実際のURLに置き換えてください。**

### Step 6: 接続テスト

1. WordPress管理画面で:
   ```
   ツール > Grant Sheets Sync > 接続テスト
   ```

2. Google Sheetsでテスト:
   - セルを編集
   - WordPressに反映されるか確認

3. WordPressでテスト:
   - 助成金投稿を編集
   - Google Sheetsに反映されるか確認

---

## 🚨 トラブルシューティング

### 問題: トリガーが動作しない

**確認事項:**
1. Google Apps Scriptのトリガーが正しく設定されているか
2. Webhook URLが正しいか
3. SECRET_KEYが一致しているか

**解決方法:**
```javascript
// Google Apps Scriptで接続テスト関数を実行
function testWordPressConnection() {
  const testData = {
    action: 'test',
    timestamp: new Date().toISOString()
  };
  
  const result = syncRowToWordPress('connection_test', testData);
  Logger.log('Test result: ' + result);
}
```

### 問題: APIレート制限エラー

**確認事項:**
1. 1分間のリクエスト数が60未満か
2. Safe Sync Managerが有効か

**解決方法:**
```
WordPress管理画面 > ツール > Safe Sync Manager
緊急停止を確認
```

### 問題: 同期が一方向のみ

**確認事項:**
1. Google Apps Scriptのトリガーが設定されているか（Sheets→WP）
2. WordPressのフックが有効か（WP→Sheets）

---

## 📊 監視とメンテナンス

### 同期ログの確認

```bash
# WordPressデバッグログ
tail -f wp-content/debug.log | grep "gi_log_error"
```

### Safe Sync Managerの監視

WordPress管理画面で:
```
ツール > Safe Sync Manager
```

確認項目:
- ✅ Emergency Stop: Inactive
- ✅ Requests (Last Hour): < 500
- ✅ Blocked Requests: 0

### 定期的なチェック

1. **毎日**: 同期ログの確認
2. **毎週**: API使用量の確認
3. **毎月**: スプレッドシートとWordPressの整合性チェック

---

## 🔒 セキュリティ強化（推奨）

### 認証情報の環境変数化

`wp-config.php` に以下を追加:

```php
// Google Sheets サービスアカウント設定
define('GI_SHEETS_SERVICE_ACCOUNT', json_encode([
    "type" => "service_account",
    "project_id" => "grant-sheets-integration",
    "private_key_id" => getenv('GI_SHEETS_PRIVATE_KEY_ID'),
    "private_key" => getenv('GI_SHEETS_PRIVATE_KEY'),
    "client_email" => getenv('GI_SHEETS_CLIENT_EMAIL'),
    // ... 他の設定
]));

define('GI_SHEETS_SPREADSHEET_ID', getenv('GI_SHEETS_SPREADSHEET_ID'));
```

`google-sheets-integration.php`を修正:

```php
private function init_settings() {
    // 環境変数から設定を読み込む
    if (defined('GI_SHEETS_SERVICE_ACCOUNT')) {
        $this->service_account_key = json_decode(GI_SHEETS_SERVICE_ACCOUNT, true);
    }
    
    if (defined('GI_SHEETS_SPREADSHEET_ID')) {
        $this->spreadsheet_id = GI_SHEETS_SPREADSHEET_ID;
    }
    
    // ... 残りの設定
}
```

---

## ⚠️ 最終確認

自動同期を有効化する前に、以下を確認してください:

- [ ] バックアップを取得済み
- [ ] テスト環境で動作確認済み
- [ ] Safe Sync Managerが有効
- [ ] レート制限設定が適切
- [ ] エラーログの監視体制が整っている

---

## 💬 サポート

問題が発生した場合は、以下の情報を提供してください:
1. エラーメッセージ
2. WordPressデバッグログ
3. Google Apps Scriptログ
4. 実行した操作の詳細
