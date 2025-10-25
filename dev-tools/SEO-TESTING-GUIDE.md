# SEO 最適化機能 テスト手順書

**作成日**: 2025-10-25  
**対象**: joseikin-insight テーマ v2.0.0  
**目的**: SEO タイトル・説明文自動生成機能の動作確認

---

## ✅ Phase D: テスト実施手順

### 前提条件
- WordPress 管理画面にログイン済み
- joseikin-insight テーマが有効化されている
- ACF (Advanced Custom Fields) プラグインが有効化されている
- `grant` カスタム投稿タイプが存在する

---

## 📋 テスト 1: 新規投稿での自動生成テスト

### 手順
1. **WordPress 管理画面** → **助成金** → **新規追加**
2. 以下の情報を入力：
   - **タイトル**: `東京都中小企業向けDX推進助成金 令和7年度版`
   - **ACF フィールド**:
     - `max_amount_numeric`: `10000000` (1000万円)
     - `deadline_date`: `2025-12-31`
     - `organization`: `東京都産業労働局`
     - `ai_summary`: `東京都内の中小企業を対象に、デジタルトランスフォーメーション（DX）の推進を支援する助成金です。システム導入費用、コンサルティング費用などが対象となります。`
   - **タクソノミー**:
     - `grant_municipality`: `東京都`
     - `grant_category`: `IT・デジタル化`

3. **公開** ボタンをクリック

4. 投稿編集画面で **カスタムフィールド** セクションを確認：
   - `_gi_seo_title` フィールドが存在するか
   - `_gi_seo_description` フィールドが存在するか
   - `_gi_seo_generated_at` フィールドが存在するか

### 期待される結果

**自動生成された SEO タイトル** (`_gi_seo_title`):
```
【東京】IT・デジタル化助成金｜最大1000万円｜締切12/31
```

**自動生成された SEO 説明文** (`_gi_seo_description`):
```
東京都で最大1000万円のIT・デジタル化助成金。東京都産業労働局が実施。申請締切は2025年12月31日。中小企業向けDX推進助成金の詳細情報をチェック。
```

**生成日時** (`_gi_seo_generated_at`):
```
2025-10-25 12:34:56
```

---

## 📋 テスト 2: フロントエンド HTML 出力確認

### 手順
1. 上記で作成した投稿ページをブラウザで開く
2. **右クリック** → **ページのソースを表示**
3. HTML の `<head>` セクションを確認

### 期待される HTML 出力

```html
<head>
    <!-- 基本タイトル -->
    <title>【東京】IT・デジタル化助成金｜最大1000万円｜締切12/31 | 助成金インサイト</title>
    
    <!-- Meta Description -->
    <meta name="description" content="東京都で最大1000万円のIT・デジタル化助成金。東京都産業労働局が実施。申請締切は2025年12月31日。中小企業向けDX推進助成金の詳細情報をチェック。">
    
    <!-- OGP Tags -->
    <meta property="og:title" content="【東京】IT・デジタル化助成金｜最大1000万円｜締切12/31">
    <meta property="og:description" content="東京都で最大1000万円のIT・デジタル化助成金。東京都産業労働局が実施。申請締切は2025年12月31日。中小企業向けDX推進助成金の詳細情報をチェック。">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://joseikin-insight.com/grant/test-post/">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="【東京】IT・デジタル化助成金｜最大1000万円｜締切12/31">
    <meta name="twitter:description" content="東京都で最大1000万円のIT・デジタル化助成金。東京都産業労働局が実施。申請締切は2025年12月31日。中小企業向けDX推進助成金の詳細情報をチェック。">
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "【東京】IT・デジタル化助成金｜最大1000万円｜締切12/31",
        "description": "東京都で最大1000万円のIT・デジタル化助成金。東京都産業労働局が実施。申請締切は2025年12月31日。中小企業向けDX推進助成金の詳細情報をチェック。",
        ...
    }
    </script>
</head>
```

### ✅ 確認ポイント
- [ ] `<title>` タグが1つだけ存在する（重複なし）
- [ ] Meta description が1つだけ存在する（重複なし）
- [ ] OGP タグが正しく出力されている
- [ ] Twitter Card タグが正しく出力されている
- [ ] JSON-LD structured data が正しく出力されている
- [ ] タイトルが40文字以内
- [ ] 説明文が120-160文字の範囲内

---

## 📋 テスト 3: 内部リンク自動挿入確認

### 手順
1. 上記の投稿ページをブラウザで開く
2. ページをスクロールして **本文の最後** を確認

### 期待される出力

```html
<div class="gi-internal-links">
    <h3>関連する助成金</h3>
    <ul>
        <li><a href="...">【大阪】IT・デジタル化助成金｜最大500万円｜締切11/30</a></li>
        <li><a href="...">【神奈川】中小企業DX支援補助金｜最大800万円｜締切10/31</a></li>
        <li><a href="...">【東京】創業支援助成金｜最大300万円｜締切随時</a></li>
    </ul>
</div>
```

### ✅ 確認ポイント
- [ ] 関連する助成金リンクが3件表示されている
- [ ] リンク先が正しく設定されている
- [ ] カテゴリーまたは地域が類似している投稿が表示されている

---

## 📋 テスト 4: 既存投稿の更新テスト

### 手順
1. **WordPress 管理画面** → **助成金** → 既存の投稿を選択
2. 投稿編集画面で **更新** ボタンをクリック（内容変更なしでOK）
3. カスタムフィールドを確認

### 期待される結果
- SEO タイトル・説明文が自動生成される
- 既に生成済みの場合は再生成される（`_gi_seo_generated_at` が更新される）

---

## 📋 テスト 5: SEO バリデーションツール確認

### 手順
1. 投稿ページの URL をコピー
2. 以下のツールで検証：

#### Google Rich Results Test
- URL: https://search.google.com/test/rich-results
- 期待結果: **エラーなし**、Article schema が検出される

#### Facebook Sharing Debugger
- URL: https://developers.facebook.com/tools/debug/
- 期待結果: OGP タグが正しく読み込まれる、画像が表示される

#### Twitter Card Validator
- URL: https://cards-dev.twitter.com/validator
- 期待結果: Twitter Card が正しく表示される

---

## 🔧 トラブルシューティング

### 問題: SEO フィールドが自動生成されない

**原因**:
- `save_post_grant` フックが動作していない
- ACF フィールドが存在しない

**解決策**:
```bash
# WordPress 管理画面でテーマを再有効化
外観 → テーマ → joseikin-insight を無効化 → 再度有効化
```

### 問題: 重複した title タグが表示される

**原因**:
- single-grant.php にまだインライン SEO タグが残っている
- 他のプラグインが title を出力している

**解決策**:
```bash
# single-grant.php の lines 186-410 が削除されているか確認
grep -n '<title>' single-grant.php
# 結果が空ならOK
```

### 問題: 内部リンクが表示されない

**原因**:
- 関連する投稿が存在しない（3件未満）
- `the_content` フィルターが動作していない

**解決策**:
```bash
# 最低4件以上の grant 投稿が必要
# WordPress 管理画面 → 助成金 → 投稿一覧で件数を確認
```

---

## 📊 テスト結果記録シート

| テスト項目 | 実施日 | 結果 | 備考 |
|----------|--------|------|------|
| 新規投稿での自動生成 | YYYY-MM-DD | ✅ / ❌ | |
| フロントエンド HTML 出力 | YYYY-MM-DD | ✅ / ❌ | |
| 内部リンク自動挿入 | YYYY-MM-DD | ✅ / ❌ | |
| 既存投稿の更新 | YYYY-MM-DD | ✅ / ❌ | |
| Google Rich Results | YYYY-MM-DD | ✅ / ❌ | |
| Facebook OGP | YYYY-MM-DD | ✅ / ❌ | |
| Twitter Card | YYYY-MM-DD | ✅ / ❌ | |

---

## ✅ 次のステップ

すべてのテストが成功したら、**Phase E: バルクアップデート実行** に進んでください。

手順書: `SEO-BULK-UPDATE-GUIDE.md`
