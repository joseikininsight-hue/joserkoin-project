# 🚨 緊急修正デプロイ手順

## エラー内容

```
Fatal error: Call to undefined function gi_load_page_template()
```

このエラーは、`gi_load_page_template()` 関数が定義されていないために発生しています。

---

## ✅ 修正内容

- `inc/theme-foundation.php` に `gi_load_page_template()` 関数を追加
- `functions.php` から重複コードを削除し、88行のシンプルな構造に整理
- Tailwind CSS Build Edition の統合

---

## 🚀 デプロイ方法（3つの選択肢）

### 方法1️⃣: SSHでgit pull（最も推奨）

サーバーにSSHでログイン：

```bash
# テーマディレクトリに移動
cd /home/keishi0804/joseikin-insight.com/public_html/wp-content/themes/joserkoin-project-main/

# 最新版を取得
git pull origin main

# 確認
git log --oneline -3
```

期待される出力：
```
cb72543 fix: Add gi_load_page_template() function
2164baf fix: Remove duplicate gi_enqueue_scripts function
df7772f docs: Add deployment guide
```

---

### 方法2️⃣: FTPで緊急アップロード

以下のファイルをFTPでアップロード：

#### 必須ファイル（上書き）

1. **functions.php** (88行のシンプル版)
   - アップロード先: `/wp-content/themes/joserkoin-project-main/functions.php`

2. **inc/theme-foundation.php** (gi_load_page_template関数を含む)
   - アップロード先: `/wp-content/themes/joserkoin-project-main/inc/theme-foundation.php`

3. **Tailwind CSSビルドファイル**
   - `assets/css/tailwind-build.css`
   - `assets/css/tailwind-build.min.css`

---

### 方法3️⃣: WordPressテーマエディタで直接編集（緊急時のみ）

⚠️ **注意**: この方法は緊急時のみ使用してください。

#### Step 1: functions.phpを確認

1. WordPress管理画面 → 外観 → テーマエディター
2. 右側のファイル一覧から `functions.php` を選択
3. 88行になっているか確認（2000行以上ある場合は古いバージョン）

#### Step 2: inc/theme-foundation.phpに関数を追加

1. FTPで `/wp-content/themes/joserkoin-project-main/inc/theme-foundation.php` を開く
2. 以下のコードを `add_action('wp_enqueue_scripts', 'gi_enqueue_scripts');` の直後に追加：

```php
/**
 * ページテンプレートをロード
 * 
 * @param string $template_name テンプレート名（例: 'about', 'contact'）
 * @param string $page_title ページタイトル（未使用、後方互換性のため）
 */
function gi_load_page_template($template_name, $page_title = '') {
    $template_path = get_template_directory() . '/pages/templates/page-' . $template_name . '.php';
    
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        // テンプレートが見つからない場合は404
        get_header();
        echo '<div class="container"><h1>ページが見つかりません</h1><p>テンプレートファイルが存在しません。</p></div>';
        get_footer();
    }
}
```

3. ファイルを保存

---

## 🔍 デプロイ後の確認

### 1. エラーが解消されたか確認

以下のページにアクセス：

- ✅ https://joseikin-insight.com/about/
- ✅ https://joseikin-insight.com/faq/
- ✅ https://joseikin-insight.com/privacy/
- ✅ https://joseikin-insight.com/terms/

### 2. Tailwind CSSが読み込まれているか確認

1. ブラウザでサイトを開く
2. F12キーで開発者ツールを開く
3. Networkタブで `tailwind-build.min.css` が読み込まれているか確認

### 3. WordPressデバッグログを確認

WordPress管理画面 → プラグイン → Debug Log Manager

エラーが消えていることを確認

---

## 🗑️ 古いテーマディレクトリの削除

エラーログに以下のディレクトリが表示されています：

```
/wp-content/themes/keishi5-genspark_ai_developer/
```

この古いテーマディレクトリは使用されていないので、削除することを推奨します：

```bash
# SSHで
cd /home/keishi0804/joseikin-insight.com/public_html/wp-content/themes/
rm -rf keishi5-genspark_ai_developer/
```

または、FTPで `keishi5-genspark_ai_developer` ディレクトリを削除

---

## 📝 チェックリスト

デプロイ前：
- [ ] サーバーへのアクセス権限を確認
- [ ] バックアップを取得（FTPまたはSSH）
- [ ] 使用するデプロイ方法を決定

デプロイ中：
- [ ] git pull または FTPアップロード実行
- [ ] ファイルのパーミッション確認（644推奨）

デプロイ後：
- [ ] about, faq, privacy, termsページにアクセスしてエラーがないか確認
- [ ] トップページのデザイン確認
- [ ] Tailwind CSSが正しく読み込まれているか確認（開発者ツール）
- [ ] WordPressデバッグログでエラーが消えているか確認
- [ ] 古いテーマディレクトリを削除

---

## 🆘 トラブルシューティング

### エラーが続く場合

#### 1. WordPressキャッシュをクリア

- WP Super Cache: 「キャッシュを削除」
- W3 Total Cache: 「すべてのキャッシュを削除」

#### 2. ブラウザキャッシュをクリア

- Chrome: Ctrl+Shift+R（強制再読み込み）
- Firefox: Ctrl+F5

#### 3. PHPオペコードキャッシュをクリア

```bash
# SSHで
cd /home/keishi0804/joseikin-insight.com/public_html/
touch wp-config.php
```

これによりPHPのオペコードキャッシュがリセットされます。

#### 4. functions.phpが正しく読み込まれているか確認

```bash
# SSHで
cd /home/keishi0804/joseikin-insight.com/public_html/wp-content/themes/joserkoin-project-main/
wc -l functions.php
```

期待される出力: `88 functions.php`

2000行以上の場合は、古いバージョンです。

---

## 📞 緊急連絡先

問題が解決しない場合は、以下を確認してください：

1. **サーバーのエラーログ**: `/home/keishi0804/joseikin-insight.com/public_html/wp-content/debug.log`
2. **PHPバージョン**: PHP 7.4以上推奨
3. **ファイルパーミッション**: 644 (ファイル), 755 (ディレクトリ)

---

## ✅ 完了後

すべての確認が完了したら、このファイルは削除しても構いません。

デプロイ完了日時: _________________

担当者: _________________

確認項目にすべてチェックが入ったら完了です！🎉
