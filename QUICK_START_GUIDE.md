# 🚀 クイックスタートガイド: 補助金ポータルNo.1への道

**対象**: 開発チーム、プロジェクト関係者  
**所要時間**: 30分（概要把握）  
**最終更新**: 2025-10-25

---

## 📖 このガイドについて

このドキュメントは、**Grant Insight Perfect**を補助金ポータルサイトNo.1にするための改善プロジェクトの全体像を15分で把握できるように作成されています。

---

## 🎯 プロジェクト概要

### 現状
- ✅ 優れた技術基盤（WordPress + モジュール設計）
- ✅ AI機能実装済み（OpenAI連携）
- ✅ データ管理システム（Google Sheets統合）
- ⚠️ ユーザーエンゲージメント機能が不足
- ⚠️ AI機能の可視性が低い
- ⚠️ コンバージョン導線が弱い

### 目標（3ヶ月後）
```
📈 トラフィック増加: +50%
📈 エンゲージメント向上: +40%
📈 問い合わせ数増加: +50%
📈 滞在時間延長: +60%
📈 Lighthouse Score: 90点以上
```

---

## 🗺️ 全体ロードマップ

```
Phase 1 (1-2ヶ月) ← 【ココから開始】
└─ クイックウィン機能
   ├─ お気に入り機能
   ├─ AI検索可視化
   ├─ 比較機能
   ├─ モバイルUX改善
   └─ パフォーマンス最適化

Phase 2 (3-4ヶ月)
└─ 差別化機能
   ├─ マイページ
   ├─ 申請進捗管理
   ├─ AIおすすめエンジン
   └─ 専門家相談予約

Phase 3 (5-6ヶ月)
└─ プラットフォーム化
   ├─ コミュニティ機能
   ├─ API公開
   ├─ モバイルアプリ
   └─ 外部連携
```

---

## ⚡ Phase 1: 最優先実装項目（今すぐ始める）

### 1️⃣ お気に入り機能 ★★★
```
💡 なぜ重要?
- ユーザーの再訪を促進
- 検討中の補助金を保存できる
- エンゲージメント指標が向上

🔧 実装内容
- ワンクリックでお気に入り登録/解除
- ローカルストレージ保存（最大50件）
- お気に入り一覧ページ
- ソート・フィルタ機能

📅 期間: Week 1-2
💰 コスト: 低
🎯 期待効果: 登録率30%、再訪率+50%
```

**今すぐできること:**
```bash
# ファイル作成
touch assets/js/favorites.js
touch template-parts/favorites-button.php
touch page-favorites.php

# スタイル追加準備
# style.css にお気に入りボタンのスタイルを追加
```

### 2️⃣ AI検索の可視化 ★★★
```
💡 なぜ重要?
- 隠れた優れた機能を全面に
- ユーザーの問題解決を即座にサポート
- 競合との差別化ポイント

🔧 実装内容
- フッター固定AIチャットボタン（🤖）
- クリックでモーダル展開
- 自然言語での補助金相談
- チャット履歴保存

📅 期間: Week 1-2
💰 コスト: 低
🎯 期待効果: 相談開始率10%、満足度向上
```

**今すぐできること:**
```bash
# AIチャットウィジェット作成
touch assets/js/ai-chat-widget.js
touch template-parts/ai-chat-fab.php

# footer.phpに挿入箇所を確認
# <?php get_template_part('template-parts/ai-chat-fab'); ?>
```

### 3️⃣ 比較機能 ★★☆
```
💡 なぜ重要?
- 複数の補助金を並べて検討できる
- 意思決定をサポート
- 滞在時間が延長

🔧 実装内容
- 最大3件の補助金を比較
- 比較リストへの追加/削除
- 比較ページでの並列表示
- 差分の強調表示

📅 期間: Week 2-3
💰 コスト: 中
🎯 期待効果: 利用率15%、コンバージョン+20%
```

---

## 📁 ファイル構成（追加予定）

```
/home/user/webapp/
├── assets/
│   └── js/
│       ├── favorites.js           ← 新規
│       ├── ai-chat-widget.js      ← 新規
│       ├── comparison.js          ← 新規
│       └── mobile-ux.js           ← 新規
├── template-parts/
│   ├── favorites-button.php       ← 新規
│   ├── ai-chat-fab.php            ← 新規
│   └── comparison-bar.php         ← 新規
├── page-favorites.php             ← 新規
├── page-compare.php               ← 新規
├── inc/
│   ├── favorites-api.php          ← 新規
│   └── comparison-api.php         ← 新規
└── style.css                      ← 追加スタイル
```

---

## 🛠️ 開発環境セットアップ（5分）

### 1. 前提条件確認
```bash
# Node.js確認（v18以上）
node --version

# npm確認
npm --version

# Git確認
git --version
```

### 2. プロジェクトセットアップ
```bash
# 現在のディレクトリ確認
cd /home/user/webapp && pwd

# 既存のpackage.jsonがあるか確認
ls -la package.json

# 依存パッケージインストール（既にある場合）
npm install

# 開発サーバー起動（もしスクリプトがあれば）
# npm run dev
```

### 3. ブランチ作成
```bash
# メインブランチから新しい機能ブランチを作成
git checkout -b feature/phase1-quick-wins

# または個別機能ごと
git checkout -b feature/favorites
git checkout -b feature/ai-chat-visibility
git checkout -b feature/comparison
```

---

## 📝 タスク管理

### GitHub Issues テンプレート

#### Issue #1: お気に入り機能実装
```markdown
## 概要
ユーザーが補助金をお気に入り登録・管理できる機能を実装する

## 実装内容
- [ ] LocalStorageによるデータ保存
- [ ] お気に入りボタン実装
- [ ] お気に入り一覧ページ実装
- [ ] お気に入り数バッジ表示
- [ ] トースト通知実装

## 受入基準
- [ ] ワンクリックで登録/解除可能
- [ ] 最大50件まで保存可能
- [ ] お気に入り一覧でソート可能
- [ ] モバイル対応完璧
- [ ] バグなし

## 見積もり
- 開発: 5日
- テスト: 2日
- 合計: 7日

## 優先度: High
## 担当者: @developer-name
## 期限: 2025-11-14
```

---

## 🧪 テスト手順

### 手動テスト（基本フロー）

#### お気に入り機能
```
1. トップページにアクセス
2. 補助金カードの♡ボタンをクリック
   ✓ ハートが赤色に変わる
   ✓ トースト通知「お気に入りに追加しました」が表示
   ✓ ヘッダーのお気に入りバッジが「1」に

3. 別の補助金をお気に入り登録
   ✓ 2件目が正常に追加される

4. お気に入りページ（/favorites/）にアクセス
   ✓ 登録した2件が表示される
   ✓ ソート機能が動作する

5. お気に入りから削除
   ✓ ♥ボタンをクリックで削除
   ✓ トースト通知「お気に入りから削除しました」

6. ページリロード
   ✓ お気に入り状態が保持されている
```

#### AIチャット機能
```
1. 任意のページにアクセス
   ✓ 右下にAIチャットボタン（🤖）が表示

2. AIチャットボタンをクリック
   ✓ モーダルがスライドアップで表示
   ✓ ウェルカムメッセージが表示

3. 「東京都の製造業向け補助金は?」と入力
   ✓ タイピングインジケータが表示
   ✓ AI回答が表示される

4. モーダルを閉じる
   ✓ スムーズに閉じる
   ✓ FABボタンが再表示

5. モバイルで確認
   ✓ レスポンシブ対応OK
   ✓ タッチ操作スムーズ
```

---

## 📊 成果測定（Week 1から開始）

### Google Analytics 4 設定

#### イベント定義
```javascript
// お気に入り登録
gtag('event', 'add_to_favorites', {
  grant_id: grantId,
  grant_title: grantTitle,
  category: category
});

// AI相談開始
gtag('event', 'ai_chat_start', {
  source_page: location.pathname
});

// 比較機能利用
gtag('event', 'add_to_comparison', {
  grant_id: grantId,
  comparison_count: count
});
```

#### カスタムレポート作成
```
1. GA4管理画面 > レポート > 作成
2. カスタムレポート「Phase 1 KPI」
3. 指標追加:
   - add_to_favorites (イベント数)
   - ai_chat_start (イベント数)
   - add_to_comparison (イベント数)
   - セッション時間
   - エンゲージメント率
```

### 週次レビュー項目
```
毎週月曜 10:00-11:00

【確認事項】
□ お気に入り登録数（累計/週次）
□ AI相談開始数（累計/週次）
□ 比較機能利用数（累計/週次）
□ ページ速度（PC/モバイル）
□ Lighthouse Score
□ バグ報告数
□ ユーザーフィードバック

【議論事項】
□ 進捗状況（予定 vs 実績）
□ 問題点・ブロッカー
□ 次週のタスク確認
□ リソース調整
```

---

## 🚨 よくある質問（FAQ）

### Q1: LocalStorageのデータが消えたらどうする?
**A**: 
- LocalStorageは基本的に永続保存されますが、ユーザーがブラウザデータを削除すると消えます
- Phase 2でユーザーアカウント機能を実装し、サーバー側にも保存予定
- それまでは「お気に入りデータのエクスポート」機能を提供

### Q2: AI APIの料金が心配...
**A**:
- 現在GPT-3.5-turboを使用（比較的低コスト）
- 1リクエスト約0.5円程度
- 月間10,000リクエストでも5,000円程度
- Phase 2でキャッシング実装により、同じ質問の重複リクエストを削減

### Q3: モバイル対応は大丈夫?
**A**:
- 全機能でモバイルファーストで設計
- タッチ操作、スワイプジェスチャー対応
- ボトムナビゲーション実装予定
- PWA化も検討中

### Q4: 既存のWordPress機能との競合は?
**A**:
- 既存機能は一切変更しない
- 新機能は独立したモジュールとして追加
- functions.phpの肥大化を避けるため/inc/に分離
- 必要に応じてロールバック可能

### Q5: テスト環境は?
**A**:
- ステージング環境を推奨
- なければローカル開発環境でテスト
- 本番へのデプロイ前に必ず全機能を検証
- ユーザー受入テスト（UAT）も実施

---

## 🎓 学習リソース

### WordPress開発
- [WordPress Developer Handbook](https://developer.wordpress.org/)
- [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/)

### JavaScript/UX
- [MDN Web Docs](https://developer.mozilla.org/)
- [Web.dev](https://web.dev/)
- [UX Design Patterns](https://ui-patterns.com/)

### パフォーマンス
- [Google PageSpeed Insights](https://pagespeed.web.dev/)
- [Lighthouse](https://developers.google.com/web/tools/lighthouse)
- [Web Vitals](https://web.dev/vitals/)

### AI/OpenAI
- [OpenAI API Documentation](https://platform.openai.com/docs/)
- [OpenAI Cookbook](https://github.com/openai/openai-cookbook)

---

## 📞 サポート・連絡先

### プロジェクトチーム
```
プロジェクトマネージャー: [名前] (@slack-handle)
リードエンジニア: [名前] (@slack-handle)
フロントエンド開発: [名前] (@slack-handle)
バックエンド開発: [名前] (@slack-handle)
デザイナー: [名前] (@slack-handle)
QAエンジニア: [名前] (@slack-handle)
```

### コミュニケーション
```
Slack: #grant-insight-project
定例会議: 毎週月曜 10:00-11:00 (Zoom)
緊急連絡: @プロジェクトマネージャー
課題管理: GitHub Issues
ドキュメント: Notion / Google Docs
```

---

## 🎉 次のステップ

### 今日やること（30分）
```
□ このドキュメントを全員で読む
□ ANALYSIS_REPORT.mdを確認
□ IMPLEMENTATION_PLAN.mdを確認
□ GitHub Projectボードを作成
□ 開発環境をセットアップ
□ キックオフミーティングの日程調整
```

### 今週やること（Week 1）
```
□ お気に入り機能の設計レビュー
□ UI/UXプロトタイプ作成（Figma）
□ データ構造の最終確認
□ お気に入りボタンコンポーネント実装
□ LocalStorageクラス実装
□ ユニットテスト作成
□ AI検索FABボタン配置
```

### 来週やること（Week 2）
```
□ お気に入り一覧ページ実装
□ ソート・フィルタ機能追加
□ AIチャットモーダル実装
□ AIチャットUI完成
□ E2Eテスト実施
□ バグ修正
□ ステージング環境デプロイ
```

---

## 📚 関連ドキュメント

1. **ANALYSIS_REPORT.md** - 詳細分析レポート（全体戦略）
2. **IMPLEMENTATION_PLAN.md** - Phase 1実装計画（技術詳細）
3. **QUICK_START_GUIDE.md** - このドキュメント（概要）

---

## ✅ チェックリスト: プロジェクト開始前

```
□ 全員がドキュメントを読んだ
□ プロジェクトの目標を理解した
□ 開発環境がセットアップ済み
□ Slackチャンネルに参加した
□ GitHub Projectボードにアクセス可能
□ 役割分担が明確
□ 最初のタスクが割り当て済み
□ キックオフミーティング完了
```

---

**🎯 目標を達成しましょう!**

補助金ポータルサイトNo.1への道は、このPhase 1から始まります。  
小さな成功を積み重ね、ユーザーに価値を提供し続けることが成功の鍵です。

**Let's build the best grant portal in Japan! 🇯🇵**

---

**作成日**: 2025-10-25  
**最終更新**: 2025-10-25  
**バージョン**: 1.0.0
