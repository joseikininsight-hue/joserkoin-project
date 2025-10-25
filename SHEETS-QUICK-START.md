# 📊 Google Sheets連携 クイックスタートガイド

## 🎯 現在の状況

あなたのGoogle Sheets連携設定は**正常に動作しています**。

✅ 手動同期モードで安定運用中  
✅ API制限の心配なし  
✅ サーバー負荷最小限

---

## 🚀 今すぐ使う（3ステップ）

### Step 1: WordPress管理画面にログイン
```
https://your-site.com/wp-admin
```

### Step 2: Google Sheets連携ページを開く
```
ツール > Grant Sheets Sync
```

### Step 3: 同期を実行
```
「今すぐ同期」ボタンをクリック
```

**完了！** これだけでWordPressとGoogle Sheetsが同期されます。

---

## 📝 同期の種類

### 1. WordPress → Sheets（WP to Sheets）
WordPressの助成金投稿をGoogle Sheetsにコピー

**使用タイミング:**
- WordPressで新しい助成金を追加した後
- WordPress上で助成金情報を更新した後

### 2. Sheets → WordPress（Sheets to WP）
Google SheetsのデータをWordPressにコピー

**使用タイミング:**
- Google Sheetsで助成金データを編集した後
- 一括でデータを更新した後

### 3. 双方向同期（Bidirectional Sync）
両方向の同期を一度に実行（推奨）

**使用タイミング:**
- 定期メンテナンス時
- 大きな更新の後
- データの整合性を確認したい時

---

## ⚙️ 設定内容

### 現在の設定
- **同期方式**: 手動同期のみ
- **自動同期**: 無効（意図的）
- **スプレッドシート**: 設定済み
- **認証**: Google サービスアカウント

### この設定のメリット
✅ **安定性**: 予期しないエラーなし  
✅ **コントロール**: 同期タイミングを完全管理  
✅ **効率性**: API制限の心配なし  
✅ **安心**: サーバー負荷を最小限に

---

## 🔍 状態確認方法

### 方法1: WordPress管理画面
```
ツール > Grant Sheets Sync > 最終同期情報を確認
```

### 方法2: コマンドライン（上級者向け）
```bash
cd /home/user/webapp
php check-sheets-status.php
```

---

## ❓ よくある質問

### Q1: なぜ自動同期が無効なの？
**A:** 以下の理由で手動同期を推奨しています：
- Google Sheets APIのレート制限対策
- サーバー負荷の軽減
- 予期しないエラーの防止
- より安定した運用

### Q2: どのくらいの頻度で同期すればいい？
**A:** 用途によりますが、推奨は：
- 日次更新: 1日1回
- 週次更新: 週1回
- 大量更新後: すぐに実行

### Q3: 自動同期を有効にしたい
**A:** 上級者向けガイドを参照してください：
```
/home/user/webapp/ENABLE-AUTO-SYNC-GUIDE.md
```

⚠️ 注意: 自動同期にはリスクがあります

### Q4: エラーが出た場合は？
**A:** 以下を確認してください：
1. 接続テストを実行
2. エラーログを確認
3. 診断レポートを参照

```
/home/user/webapp/GOOGLE-SHEETS-DIAGNOSIS.md
```

### Q5: Google Sheetsが更新されない
**A:** チェックリスト：
- [ ] スプレッドシートの共有設定を確認
- [ ] サービスアカウントに編集権限があるか
- [ ] シート名が`grant_import`か
- [ ] 接続テストが成功するか

---

## 📚 詳細ドキュメント

### 基本
- `GOOGLE-SHEETS-DIAGNOSIS.md` - 詳細な診断レポート
- `SHEETS-QUICK-START.md` - このファイル

### 上級者向け
- `ENABLE-AUTO-SYNC-GUIDE.md` - 自動同期の設定方法
- `google-apps-script/IntegratedSheetSync.gs` - Apps Script

### 開発者向け
- `inc/google-sheets-integration.php` - メイン実装
- `inc/safe-sync-manager.php` - セーフティ機能
- `inc/disable-auto-sync.php` - 自動同期無効化

---

## 🛠️ トラブルシューティング

### 問題: 「接続に失敗しました」
**原因:** 認証エラーまたはネットワーク問題

**解決方法:**
1. ネットワーク接続を確認
2. Google Sheetsが共有されているか確認
3. サービスアカウントのメールアドレスに編集権限があるか確認

### 問題: 「データが同期されません」
**原因:** スプレッドシートの設定問題

**解決方法:**
1. シート名が`grant_import`であることを確認
2. 列数が31列（A-AE）であることを確認
3. ヘッダー行が正しいことを確認

### 問題: 「緊急停止がアクティブ」
**原因:** レート制限違反の検知

**解決方法:**
1. `ツール > Safe Sync Manager` を開く
2. 原因を確認
3. 問題解決後、「Deactivate Emergency Stop」をクリック

---

## 📞 サポート情報

### すぐにヘルプが必要な場合

#### ステップ1: 診断スクリプトを実行
```bash
cd /home/user/webapp
php check-sheets-status.php
```

#### ステップ2: エラーログを確認
```bash
# WordPressデバッグログ
cat wp-content/debug.log | grep "gi_log_error" | tail -20
```

#### ステップ3: 診断レポートを確認
```bash
cat GOOGLE-SHEETS-DIAGNOSIS.md
```

### 情報を提供する際は以下を含めてください
1. エラーメッセージ
2. 実行した操作
3. 診断スクリプトの出力
4. エラーログ（最新20行）

---

## ✅ チェックリスト

同期を実行する前に確認:

- [ ] WordPressにログインしている
- [ ] 最新のバックアップがある
- [ ] Google Sheetsが正しく共有されている
- [ ] Safe Sync Managerが正常
- [ ] 緊急停止が非アクティブ

---

## 🎓 ベストプラクティス

### 1. 定期的な同期
- 週1回、決まった時間に同期を実行
- 大量更新の後は必ず同期

### 2. データの確認
- 同期後、WordPressとGoogle Sheetsの両方でデータを確認
- 不整合があればすぐに対応

### 3. バックアップ
- 同期前にバックアップを取得
- Google Sheetsのバージョン履歴を活用

### 4. 監視
- 月1回、Safe Sync Managerを確認
- エラーログを定期的にチェック

---

## 🚀 次のステップ

### 初めて使う場合
1. ✅ このガイドを読む
2. ✅ 接続テストを実行
3. ✅ 最初の同期を実行
4. ✅ データを確認

### 日常的な使用
1. データを編集
2. 同期を実行
3. 結果を確認
4. 以上！

### より高度な使用
1. `ENABLE-AUTO-SYNC-GUIDE.md`を読む
2. 自動同期の設定を検討
3. Google Apps Scriptのカスタマイズ

---

**🎉 以上でGoogle Sheets連携の準備は完了です！**

何か問題があれば、診断レポートを確認するか、サポートにお問い合わせください。
