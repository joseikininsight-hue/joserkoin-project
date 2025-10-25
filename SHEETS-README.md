# 📊 Google Sheets連携 - ドキュメント一覧

## 🎯 このフォルダについて

このフォルダには、Google Sheets連携に関するすべてのドキュメントが含まれています。

---

## 📚 ドキュメント一覧

### 🚀 初心者向け

#### 1. **SHEETS-QUICK-START.md** 
⭐ **まずはこれを読んでください！**

- 今すぐ使える3ステップガイド
- よくある質問と回答
- トラブルシューティング
- ベストプラクティス

**推奨読者:** すべてのユーザー  
**所要時間:** 5分

---

#### 2. **GOOGLE-SHEETS-DIAGNOSIS.md**
🔍 **詳細な診断レポート**

- 現在の設定状況
- 問題解決の手順
- API制限についての説明
- セキュリティに関する注意事項

**推奨読者:** 管理者、トラブル時  
**所要時間:** 10分

---

### 🔧 上級者向け

#### 3. **ENABLE-AUTO-SYNC-GUIDE.md**
⚙️ **自動同期の設定ガイド**

- 自動同期の再有効化手順
- Google Apps Scriptのトリガー設定
- Webhook設定
- 監視とメンテナンス

**推奨読者:** 上級管理者、開発者  
**所要時間:** 30分  
**注意:** リスクあり、推奨されない

---

### 🛠️ 開発者向け

#### 4. **check-sheets-status.php**
💻 **診断スクリプト**

現在のGoogle Sheets連携の状態を確認

**使用方法:**
```bash
cd /home/user/webapp
php check-sheets-status.php
```

**出力内容:**
- 基本設定の状態
- 自動/手動同期の状態
- 最終同期情報
- Safe Sync Managerの状態
- 統計情報
- 推奨事項

**推奨読者:** 開発者、トラブルシューティング時

---

## 🗂️ ファイル構造

```
/home/user/webapp/
├── SHEETS-README.md                     # このファイル
├── SHEETS-QUICK-START.md                # クイックスタートガイド
├── GOOGLE-SHEETS-DIAGNOSIS.md           # 診断レポート
├── ENABLE-AUTO-SYNC-GUIDE.md            # 自動同期設定ガイド
├── check-sheets-status.php              # 診断スクリプト
│
├── inc/
│   ├── google-sheets-integration.php    # メイン実装
│   ├── safe-sync-manager.php            # セーフティ機能
│   └── disable-auto-sync.php            # 自動同期無効化
│
└── google-apps-script/
    └── IntegratedSheetSync.gs           # Google Apps Script
```

---

## 🚀 クイックリファレンス

### 📍 よく使うコマンド

#### 状態確認
```bash
cd /home/user/webapp
php check-sheets-status.php
```

#### エラーログ確認
```bash
cd /home/user/webapp
tail -f wp-content/debug.log | grep "gi_log_error"
```

#### 最新20件のエラー
```bash
cd /home/user/webapp
cat wp-content/debug.log | grep "gi_log_error" | tail -20
```

---

### 📍 よく使うWordPress管理画面のリンク

#### 手動同期
```
ツール > Grant Sheets Sync > 今すぐ同期
```

#### Safe Sync Manager
```
ツール > Safe Sync Manager
```

#### 接続テスト
```
ツール > Grant Sheets Sync > 接続テスト
```

---

## 🎓 学習パス

### レベル1: 基礎（すべてのユーザー）
1. ✅ `SHEETS-QUICK-START.md`を読む
2. ✅ WordPress管理画面で接続テストを実行
3. ✅ 初めての手動同期を実行
4. ✅ 結果を確認

**所要時間:** 15分

---

### レベル2: 運用（管理者）
1. ✅ `GOOGLE-SHEETS-DIAGNOSIS.md`を読む
2. ✅ `check-sheets-status.php`を実行
3. ✅ Safe Sync Managerの使い方を学ぶ
4. ✅ 定期メンテナンス計画を立てる

**所要時間:** 1時間

---

### レベル3: カスタマイズ（上級者・開発者）
1. ✅ `ENABLE-AUTO-SYNC-GUIDE.md`を読む
2. ✅ ソースコードを理解
3. ✅ Google Apps Scriptをカスタマイズ
4. ✅ 自動同期を設定（任意）

**所要時間:** 3時間  
**前提知識:** PHP, JavaScript, Google Apps Script

---

## 📊 現在の設定状況（概要）

### ✅ 有効
- Google Sheets API認証
- 手動同期機能
- Safe Sync Manager
- レート制限保護
- エラー監視

### ❌ 無効（意図的）
- 自動同期
- Cronスケジュール
- リアルタイム同期

### ⚙️ 設定値
- **スプレッドシートID**: `1kGc1Eb4AYvURkSfdzMwipNjfe8xC6iGCM2q1sUgIfWg`
- **シート名**: `grant_import`
- **列数**: 31列（A-AE）
- **サービスアカウント**: `grant-sheets-service@grant-sheets-integration.iam.gserviceaccount.com`

---

## ❓ FAQ

### Q: どのドキュメントから読めばいい？
**A:** まず`SHEETS-QUICK-START.md`を読んでください。

### Q: エラーが出た場合は？
**A:** 以下の順序で確認：
1. `check-sheets-status.php`を実行
2. `GOOGLE-SHEETS-DIAGNOSIS.md`のトラブルシューティングを確認
3. WordPressのデバッグログを確認

### Q: 自動同期は推奨されない？
**A:** はい。以下の理由で手動同期を推奨：
- APIレート制限のリスク
- サーバー負荷の増加
- 予期しないエラーの可能性

自動同期が必要な場合は、`ENABLE-AUTO-SYNC-GUIDE.md`を参照してください。

### Q: どのくらいの頻度で同期すればいい？
**A:** 用途によりますが：
- **日次更新**: 1日1回
- **週次更新**: 週1回
- **大量更新後**: すぐに実行

### Q: Google Sheetsが更新されない
**A:** チェックリスト：
1. スプレッドシートの共有設定を確認
2. サービスアカウントに編集権限があるか
3. シート名が`grant_import`か
4. 列数が31列（A-AE）か

---

## 🛡️ セキュリティに関する注意

### ⚠️ 重要
- サービスアカウントの秘密鍵は機密情報です
- Gitにコミットしないでください
- `.gitignore`に追加されていることを確認してください

### ✅ 推奨
- 環境変数での管理を検討
- 定期的なキーのローテーション
- アクセスログの監視

詳細は`GOOGLE-SHEETS-DIAGNOSIS.md`の「セキュリティ」セクションを参照。

---

## 📞 サポート

### トラブルシューティング時に必要な情報

1. **エラーメッセージ**
   - スクリーンショットまたはテキスト

2. **診断スクリプトの出力**
   ```bash
   php check-sheets-status.php > diagnosis.txt
   ```

3. **エラーログ（最新20行）**
   ```bash
   cat wp-content/debug.log | grep "gi_log_error" | tail -20 > error_log.txt
   ```

4. **実行した操作**
   - ステップバイステップで説明

5. **環境情報**
   - WordPress バージョン
   - PHP バージョン
   - サーバー環境

---

## 🔄 更新履歴

### 2025-10-25
- 初版作成
- 診断レポート作成
- クイックスタートガイド作成
- 自動同期設定ガイド作成
- 診断スクリプト作成

---

## 📝 関連リンク

### 外部ドキュメント
- [Google Sheets API Documentation](https://developers.google.com/sheets/api)
- [Google Apps Script Guide](https://developers.google.com/apps-script)
- [WordPress REST API](https://developer.wordpress.org/rest-api/)

### 内部リンク
- [テーマドキュメント](README-TAILWIND.md)
- [デプロイガイド](DEPLOYMENT-GUIDE.md)
- [検証レポート](VERIFICATION_REPORT.md)

---

**最終更新**: 2025年10月25日  
**バージョン**: 1.0.0  
**メンテナー**: Grant Insight Perfect Team
