# 動的CSS生成システム - 実装ガイド

## 📊 実装完了日
2025年10月27日

## 🎯 実装目的

助成金・補助金投稿（カスタム投稿タイプ「grant」）の本文内容を自動分析し、コンテンツタイプに応じて最適なCSSクラスを動的に生成・適用することで、統一感のある白黒スタイリッシュなデザインを実現します。

---

## 🏗️ システム構成

### 1. コアファイル

#### `/inc/grant-dynamic-css-generator.php`
- **役割**: コンテンツ分析エンジン + 動的CSSクラス生成システム
- **クラス**: `Grant_Dynamic_CSS_Generator`
- **バージョン**: 1.0.0

#### `/assets/css/grant-dynamic-styles.css`
- **役割**: 白黒モノクロームデザインのスタイルシート
- **サイズ**: 約18KB
- **バージョン**: 1.0.0

---

## 🔍 機能詳細

### 自動検出される要素タイプ

システムは以下の要素を自動的に検出し、適切なCSSクラスを適用します：

| 要素タイプ | 検出対象 | 自動生成されるクラス |
|-----------|---------|-------------------|
| **テーブル** | `<table>` | `.gdc-table`, `.gdc-table--monochrome`, `.gdc-table--striped` |
| **リスト** | `<ul>`, `<ol>` | `.gdc-list`, `.gdc-list--unordered`, `.gdc-list--ordered` |
| **引用** | `<blockquote>` | `.gdc-blockquote`, `.gdc-blockquote--monochrome` |
| **画像** | `<img>` | `.gdc-image`, `.gdc-image--monochrome`, `.gdc-image--responsive` |
| **段落** | `<p>` | `.gdc-paragraph`, `.gdc-paragraph--lead` |
| **見出し** | `<h2>`-`<h6>` | `.gdc-heading`, `.gdc-heading--h2`, `.gdc-heading--h3` |
| **強調** | `<strong>`, `<em>` | `.gdc-strong`, `.gdc-emphasis` |
| **リンク** | `<a>` | `.gdc-link`, `.gdc-link--internal`, `.gdc-link--external` |

### 動的クラス生成ロジック

#### テーブル
- **基本クラス**: `.gdc-table`, `.gdc-table--monochrome`
- **構造検出**:
  - `<thead>` 存在時: `.gdc-table--with-header`
  - 行数 > 10: `.gdc-table--large`
  - 行数 ≤ 3: `.gdc-table--compact`
- **デフォルト**: `.gdc-table--striped` (ストライプパターン)

#### リスト
- **基本クラス**: `.gdc-list`, `.gdc-list--monochrome`
- **タイプ別**:
  - `<ul>`: `.gdc-list--unordered`
  - `<ol>`: `.gdc-list--ordered`
- **長さ検出**:
  - 項目数 > 10: `.gdc-list--long`
  - 項目数 ≤ 3: `.gdc-list--short`
- **ネスト検出**: 親リスト内に存在する場合: `.gdc-list--nested`

#### 引用
- **基本クラス**: `.gdc-blockquote`, `.gdc-blockquote--monochrome`
- **長さ検出**:
  - テキスト長 > 200文字: `.gdc-blockquote--long`
  - テキスト長 ≤ 200文字: `.gdc-blockquote--short`

#### 画像
- **基本クラス**: `.gdc-image`, `.gdc-image--monochrome`, `.gdc-image--responsive`
- **配置検出**:
  - 中央揃え: `.gdc-image--center`
  - 左揃え: `.gdc-image--left`
  - 右揃え: `.gdc-image--right`
- **自動最適化**: `loading="lazy"` 属性を自動追加

#### 段落
- **基本クラス**: `.gdc-paragraph`
- **長さ検出**:
  - テキスト長 > 300文字: `.gdc-paragraph--long`
  - テキスト長 < 50文字: `.gdc-paragraph--short`
- **特殊クラス**: 最初の段落には `.gdc-paragraph--lead` を自動付与

#### 見出し
- **基本クラス**: `.gdc-heading`, `.gdc-heading--monochrome`
- **レベル別**: `.gdc-heading--h2`, `.gdc-heading--h3`, `.gdc-heading--h4`, `.gdc-heading--h5`, `.gdc-heading--h6`
- **長さ検出**: テキスト長 > 50文字: `.gdc-heading--long`

#### リンク
- **基本クラス**: `.gdc-link`
- **タイプ検出**:
  - 内部リンク: `.gdc-link--internal`
  - 外部リンク: `.gdc-link--external` + `rel="noopener noreferrer"`

---

## 🎨 デザインシステム

### カラーパレット（モノクローム）

```css
--gdc-white: #ffffff;
--gdc-black: #000000;
--gdc-gray-50: #fafafa;
--gdc-gray-100: #f5f5f5;
--gdc-gray-200: #eeeeee;
--gdc-gray-300: #e0e0e0;
--gdc-gray-400: #bdbdbd;
--gdc-gray-500: #9e9e9e;
--gdc-gray-600: #757575;
--gdc-gray-700: #616161;
--gdc-gray-800: #424242;
--gdc-gray-900: #212121;
```

### タイポグラフィ

| サイズ | 変数 | 値 |
|-------|------|-----|
| Extra Small | `--gdc-text-xs` | 0.75rem (12px) |
| Small | `--gdc-text-sm` | 0.875rem (14px) |
| Base | `--gdc-text-base` | 1rem (16px) |
| Medium | `--gdc-text-md` | 1.0625rem (17px) |
| Large | `--gdc-text-lg` | 1.125rem (18px) |
| Extra Large | `--gdc-text-xl` | 1.25rem (20px) |
| 2X Large | `--gdc-text-2xl` | 1.5rem (24px) |
| 3X Large | `--gdc-text-3xl` | 1.875rem (30px) |

### スペーシング

| サイズ | 変数 | 値 |
|-------|------|-----|
| 1 | `--gdc-space-1` | 0.25rem (4px) |
| 2 | `--gdc-space-2` | 0.5rem (8px) |
| 3 | `--gdc-space-3` | 0.75rem (12px) |
| 4 | `--gdc-space-4` | 1rem (16px) |
| 5 | `--gdc-space-5` | 1.25rem (20px) |
| 6 | `--gdc-space-6` | 1.5rem (24px) |
| 8 | `--gdc-space-8` | 2rem (32px) |
| 10 | `--gdc-space-10` | 2.5rem (40px) |
| 12 | `--gdc-space-12` | 3rem (48px) |

---

## 🔧 使用方法

### 基本的な使い方

システムは**完全自動**で動作します。カスタム投稿タイプ「grant」の投稿を表示すると、自動的にコンテンツが分析され、適切なCSSクラスが適用されます。

### デバッグモード

管理者ユーザーは、URLに `?gdc_debug=1` を追加することで、デバッグ情報を表示できます。

```
https://example.com/grant/example-post/?gdc_debug=1
```

デバッグ情報には以下が含まれます：
- 検出された要素タイプ
- 各要素の数
- 生成されたCSSクラス一覧

### 手動でのクラス追加

必要に応じて、WordPressエディタで手動でもクラスを追加できます：

```html
<table class="gdc-table gdc-table--monochrome gdc-table--striped">
  <!-- テーブル内容 -->
</table>
```

---

## 📱 レスポンシブデザイン

### ブレークポイント

| デバイス | ブレークポイント | 調整内容 |
|---------|---------------|---------|
| **デスクトップ** | > 1024px | 標準サイズ |
| **タブレット** | ≤ 1024px | フォントサイズ微調整 |
| **スマートフォン** | ≤ 768px | テーブル横スクロール対応、スペーシング削減 |
| **小型スマホ** | ≤ 375px | フォントサイズさらに縮小 |

### テーブルのレスポンシブ対応

スマートフォン表示では、テーブルが自動的に横スクロール可能になります：

```css
@media (max-width: 768px) {
    .gdc-table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}
```

---

## ♿ アクセシビリティ対応

### 実装済み機能

1. **フォーカス状態の明確化**
   - リンクやボタンにフォーカス時、2pxの黒枠を表示
   
2. **ハイコントラストモード対応**
   - `prefers-contrast: high` に対応
   - ボーダー幅を自動的に太くする

3. **モーション削減対応**
   - `prefers-reduced-motion: reduce` に対応
   - アニメーションを最小限に抑制

4. **外部リンクのセキュリティ**
   - 外部リンクに自動的に `rel="noopener noreferrer"` を追加

5. **画像の遅延読み込み**
   - すべての画像に `loading="lazy"` を自動付与

---

## 🔍 SEO最適化との統合

### 既存のSEO機能との連携

本システムは、以下の既存SEOシステムと完全に統合されています：

#### 1. `grant-content-seo-optimizer.php`
- セマンティックHTML5構造の自動適用
- 見出し階層の最適化
- Schema.org構造化データの生成

#### 2. `grant-advanced-seo-enhancer.php`
- OGPメタタグの自動生成
- JSON-LD構造化データの拡張
- 内部リンク戦略

### 統合のメリット

1. **コンテンツ構造の最適化**
   - SEOオプティマイザーがセマンティックHTMLを生成
   - 動的CSSジェネレーターが視覚的なスタイルを適用

2. **一貫したデザイン**
   - すべての助成金投稿で統一されたスタイル
   - ブランドイメージの向上

3. **パフォーマンス向上**
   - 単一のCSSファイルで全投稿をカバー
   - キャッシュ効率の向上

---

## 🎯 スタイルサンプル

### テーブルのスタイル

```html
<table class="gdc-table gdc-table--monochrome gdc-table--striped">
  <thead>
    <tr>
      <th>項目</th>
      <th>内容</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>助成金名</td>
      <td>○○支援補助金</td>
    </tr>
    <tr>
      <td>最大金額</td>
      <td>100万円</td>
    </tr>
  </tbody>
</table>
```

**表示結果**:
- 黒ヘッダー、白背景
- ストライプパターン（奇数行グレー）
- ホバー時に行がハイライト

### リストのスタイル

```html
<ul class="gdc-list gdc-list--unordered gdc-list--monochrome">
  <li>中小企業</li>
  <li>個人事業主</li>
  <li>NPO法人</li>
</ul>
```

**表示結果**:
- 黒い丸マーカー
- 適切なスペーシング
- 読みやすい行間

### 引用のスタイル

```html
<blockquote class="gdc-blockquote gdc-blockquote--monochrome">
  <p>この助成金は中小企業のDX推進を支援します。</p>
</blockquote>
```

**表示結果**:
- 左側に太い黒線
- グレー背景
- イタリック体
- 左上に引用符装飾

---

## 🧪 テスト方法

### 1. 基本動作テスト

1. 助成金投稿（`grant`）を表示
2. ブラウザの開発者ツールで要素を検証
3. 自動生成されたクラスを確認

### 2. デバッグモードテスト

1. URLに `?gdc_debug=1` を追加
2. ページ上部にデバッグ情報が表示されることを確認
3. 検出された要素と生成されたクラスをチェック

### 3. レスポンシブテスト

1. ブラウザウィンドウをリサイズ
2. タブレット・スマートフォン表示を確認
3. テーブルの横スクロールが機能することを確認

### 4. アクセシビリティテスト

1. キーボードのみでナビゲーション
2. スクリーンリーダーでのテスト
3. ハイコントラストモードでの表示確認

---

## 🚀 パフォーマンス最適化

### キャッシュバスティング

CSSファイルはバージョン番号を使用してキャッシュされます：

```php
wp_enqueue_style(
    'grant-dynamic-styles',
    $css_file,
    array(),
    '1.0.0',  // バージョン番号
    'all'
);
```

### 条件付き読み込み

CSSファイルは `grant` 投稿タイプでのみ読み込まれます：

```php
if (!is_singular('grant')) {
    return; // grant投稿以外ではスキップ
}
```

### ファイルサイズ

- **PHP**: 約18KB（コメント込み）
- **CSS**: 約18KB（コメント込み）
- **合計**: 約36KB（圧縮可能）

---

## 🔄 拡張性

### カスタムクラスの追加

新しいコンテンツタイプに対応する場合、以下のメソッドを追加：

```php
private function style_custom_element($xpath) {
    $elements = $xpath->query('//custom-tag');
    
    foreach ($elements as $index => $elem) {
        $existing_class = $elem->getAttribute('class');
        $new_classes = array('gdc-custom', 'gdc-custom--monochrome');
        
        // カスタムロジック
        
        $combined_class = trim($existing_class . ' ' . implode(' ', $new_classes));
        $elem->setAttribute('class', $combined_class);
    }
}
```

### CSSスタイルの追加

`grant-dynamic-styles.css` に新しいスタイルを追加：

```css
.gdc-custom {
    /* カスタムスタイル */
}

.gdc-custom--monochrome {
    /* モノクロームバリエーション */
}
```

---

## 📝 トラブルシューティング

### スタイルが適用されない

1. **キャッシュのクリア**
   - ブラウザキャッシュをクリア
   - WordPressのキャッシュプラグインをクリア

2. **ファイルの確認**
   - `/assets/css/grant-dynamic-styles.css` が存在するか確認
   - ファイルのパーミッションを確認（644）

3. **エンキューの確認**
   - ブラウザの開発者ツールで、CSSファイルが読み込まれているか確認

### デバッグ情報が表示されない

1. **管理者権限の確認**
   - 管理者ユーザーでログインしているか確認

2. **URLパラメータの確認**
   - `?gdc_debug=1` が正しく追加されているか確認

3. **プラグインの競合**
   - キャッシュプラグインを一時的に無効化

### テーブルのレイアウトが崩れる

1. **親要素の幅を確認**
   - コンテナ要素に十分な幅があるか確認

2. **レスポンシブ表示の確認**
   - スマートフォンでは横スクロールが有効か確認

3. **カスタムCSSの競合**
   - 他のテーマやプラグインのCSSが上書きしていないか確認

---

## 🔐 セキュリティ考慮事項

### XSS対策

- DOMParserを使用してHTMLを安全に解析
- `esc_html()`, `esc_attr()` などのWordPress関数を使用
- ユーザー入力は直接HTMLに出力しない

### 外部リンクのセキュリティ

- 外部リンクに自動的に `rel="noopener noreferrer"` を追加
- リンクのtarget属性を適切に処理

### 権限チェック

- デバッグモードは管理者のみアクセス可能
- `current_user_can('edit_posts')` で権限を確認

---

## 📊 今後の改善予定

### v1.1.0（予定）

- [ ] ダークモード対応
- [ ] カラースキームのカスタマイズ機能
- [ ] 管理画面での設定UI

### v1.2.0（予定）

- [ ] A/Bテスト機能
- [ ] ヒートマップ連携
- [ ] パフォーマンスメトリクスの測定

### v2.0.0（予定）

- [ ] AIによる自動スタイル提案
- [ ] 投稿ごとのカスタムテーマ
- [ ] リアルタイムプレビュー機能

---

## 📚 関連ドキュメント

- [SEO実装ガイド](GRANT_SEO_IMPLEMENTATION.md)
- [デプロイメントガイド](DEPLOYMENT-GUIDE.md)
- [Tailwind設定ガイド](README-TAILWIND.md)

---

## 🤝 貢献

バグ報告や機能要望は、GitHubのIssueまたはPull Requestでお願いします。

---

## 📄 ライセンス

このプロジェクトはGrant Insight Perfectテーマの一部として提供されています。

---

**実装日**: 2025年10月27日  
**バージョン**: 1.0.0  
**開発**: Grant Insight Perfect Development Team
