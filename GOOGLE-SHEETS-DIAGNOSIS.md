# Google Sheets連携 診断レポート

## 🔍 診断日時
2025年10月25日

## 📊 現在の状況

### 1. 自動同期機能
- **ステータス**: ❌ 無効化されています
- **理由**: `disable-auto-sync.php`により完全に無効化
- **影響**: 
  - スプレッドシートで編集してもWordPressに自動反映されません
  - WordPressで編集してもスプレッドシートに自動反映されません
  - 手動同期のみ利用可能

### 2. 手動同期機能
- **ステータス**: ✅ 利用可能
- **場所**: WordPress管理画面 > ツール > Google Sheets連携
- **機能**:
  - 「今すぐ同期」ボタンで手動実行
  - WordPress → Sheets 同期
  - Sheets → WordPress 同期
  - 双方向同期

### 3. Google Apps Script
- **ステータス**: ✅ 設定されています
- **機能**:
  - `IntegratedSheetSync.gs` が実装済み
  - Webhook経由でWordPressと通信
  - トリガー設定が必要（`onEdit`, `onChange`）

### 4. 認証情報
- **ステータス**: ✅ 設定されています
- **懸念事項**: 
  - サービスアカウントキーがソースコードにハードコード
  - 推奨: 環境変数またはwp-config.phpへの移行

## 🔧 問題解決の手順

### Option A: 手動同期を使い続ける（推奨 - 現在の設定）
現在の設定は手動同期のみをサポートしています。これは以下の理由で推奨されています：
- API制限の回避
- サーバー負荷の軽減
- 予期しない同期エラーの防止

**使用方法:**
1. WordPress管理画面にログイン
2. **ツール > Grant Sheets Sync** に移動
3. 「今すぐ同期」ボタンをクリック
4. 同期完了を確認

### Option B: 自動同期を再有効化する（非推奨）
自動同期を再度有効にする場合は以下の手順が必要です：

1. **`disable-auto-sync.php`の無効化**
   - `functions.php`から読み込みを削除
   - または`gi_disable_auto_sync_completely()`関数を無効化

2. **Google Sheets IntegrationのWebhook設定**
   - Webhook URLを設定
   - Secret Keyを設定
   - REST APIエンドポイントを有効化

3. **Google Apps Scriptのトリガー設定**
   - `onEdit`トリガーを設定（セル編集時）
   - `onChange`トリガーを設定（シート変更時）
   - Webhookエンドポイントを正しく設定

## 🚨 重要な注意事項

### API制限について
Google Sheets APIには以下の制限があります：
- **1分あたり**: 60リクエスト
- **1日あたり**: 無制限（サービスアカウント）
- **同時接続**: 最大100

自動同期を有効にすると、これらの制限に達する可能性があります。

### セキュリティ
- サービスアカウントキーは機密情報です
- Git にコミットしないよう`.gitignore`に追加
- 可能であれば環境変数に移行

## 🔍 診断手順

### 1. 接続テスト
WordPress管理画面で以下を確認：
```
ツール > Grant Sheets Sync > 接続テスト
```

### 2. エラーログの確認
```bash
# WordPressデバッグログ
wp-content/debug.log

# サーバーエラーログ  
/var/log/apache2/error.log
/var/log/nginx/error.log
```

### 3. Google Apps Scriptログ
Google Apps Scriptエディタで：
```
表示 > ログ
```

## 📞 トラブルシューティング

### 問題: 手動同期が動作しない
**確認事項:**
1. サービスアカウントキーが正しく設定されているか
2. スプレッドシートIDが正しいか
3. スプレッドシートの共有設定でサービスアカウントメールアドレスに編集権限があるか

**解決方法:**
```php
// inc/google-sheets-integration.php
// Line 64: スプレッドシートIDを確認
$this->spreadsheet_id = '1kGc1Eb4AYvURkSfdzMwipNjfe8xC6iGCM2q1sUgIfWg';
```

### 問題: トークン生成エラー
**確認事項:**
1. サービスアカウントキーの秘密鍵が完全にコピーされているか
2. OpenSSL拡張機能が有効か

**解決方法:**
```bash
# PHPのOpenSSLサポート確認
php -m | grep openssl
```

### 問題: シート書き込みエラー
**確認事項:**
1. スプレッドシートの共有設定
2. シート名が正しいか（`grant_import`）
3. 列数が31列（A-AE）に対応しているか

## 📚 参考情報

### スプレッドシート情報
- **ID**: `1kGc1Eb4AYvURkSfdzMwipNjfe8xC6iGCM2q1sUgIfWg`
- **シート名**: `grant_import`
- **列数**: 31列（A-AE）

### サービスアカウント
- **Email**: `grant-sheets-service@grant-sheets-integration.iam.gserviceaccount.com`
- **Project ID**: `grant-sheets-integration`

### 対応フィールド（31列）
```
A: ID
B: タイトル
C: 内容
D: 抜粋
E: ステータス
F: 作成日
G: 更新日
H-S: ACFフィールド
T: 都道府県
U: 市町村
V: カテゴリ
W: タグ
X-AD: 新規追加フィールド
AE: シート更新日
```

## 🎯 推奨アクション

### 今すぐ実行すべきこと
1. ✅ 手動同期の動作確認
2. ✅ スプレッドシートの共有設定確認
3. ✅ 接続テストの実行

### 中期的な対応
1. セキュリティ強化（認証情報の環境変数化）
2. 同期履歴の監視
3. エラーアラートの設定

### 長期的な検討事項
1. 自動同期の必要性の再評価
2. API使用量の監視
3. バックアップ戦略の構築

---

## 💬 サポートが必要な場合

具体的な問題や質問がある場合は、以下の情報を提供してください：
1. エラーメッセージ
2. 実行した操作
3. WordPressデバッグログ
4. Google Apps Scriptログ
