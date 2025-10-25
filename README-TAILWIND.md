# Tailwind CSS Build Edition - セットアップガイド

## 概要

このテーマは Tailwind CSS のビルド版を使用しています。CDN版から移行することで、以下のメリットがあります：

- ✅ **パフォーマンス向上**: 未使用のCSSが削除され、ファイルサイズが大幅に削減
- ✅ **カスタマイズ性**: tailwind.config.jsでテーマ独自の設定が可能
- ✅ **本番環境最適化**: 最小化されたCSSで高速なページ読み込み
- ✅ **開発効率**: JITモードで開発中の変更がすぐに反映

## ディレクトリ構造

```
/home/user/webapp/
├── assets/
│   └── css/
│       ├── src/
│       │   └── tailwind.css          # Tailwindソースファイル
│       ├── tailwind-build.css        # ビルド結果（開発用）
│       └── tailwind-build.min.css    # ビルド結果（本番用・最小化版）
├── tailwind.config.js                # Tailwind設定ファイル
├── postcss.config.js                 # PostCSS設定ファイル
├── package.json                      # npm設定・スクリプト
└── style.css                         # WordPress テーマスタイルシート

```

## セットアップ手順

### 1. 依存関係のインストール

```bash
cd /home/user/webapp
npm install
```

### 2. 開発モード（変更を監視）

開発中はこのコマンドでTailwind CSSをウォッチモードで実行します：

```bash
npm run dev
```

ファイルを変更すると自動的にCSSが再ビルドされます。

### 3. 本番ビルド

本番環境用に最小化されたCSSをビルド：

```bash
npm run build:prod
```

または通常のビルド（最小化のみ）：

```bash
npm run build
```

## 利用可能なnpmスクリプト

| コマンド | 説明 |
|---------|------|
| `npm run dev` | 開発モード（ファイル監視） |
| `npm run build` | 通常ビルド（最小化） |
| `npm run build:prod` | 本番ビルド（最小化 + 最適化） |

## Tailwind設定のカスタマイズ

`tailwind.config.js`を編集してテーマ独自の設定を追加できます：

```javascript
module.exports = {
  content: [
    './*.php',
    './inc/**/*.php',
    './template-parts/**/*.php',
    './pages/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#059669',
          dark: '#047857',
          // ...
        },
      },
      // その他のカスタマイズ
    },
  },
}
```

## CSS読み込みの仕組み

### functions.php

```php
function gi_enqueue_scripts() {
    // 開発モードではtailwind-build.css、本番ではtailwind-build.min.cssを使用
    $tailwind_file = (defined('WP_DEBUG') && WP_DEBUG) 
        ? 'tailwind-build.css' 
        : 'tailwind-build.min.css';
    
    wp_enqueue_style(
        'gi-tailwind', 
        get_template_directory_uri() . '/assets/css/' . $tailwind_file, 
        array(), 
        GI_THEME_VERSION
    );
}
```

### 環境による切り替え

- **開発環境** (`WP_DEBUG = true`): `tailwind-build.css` を使用
- **本番環境** (`WP_DEBUG = false`): `tailwind-build.min.css` を使用（最小化版）

## カスタムCSSの追加

Tailwindで定義されていない追加のスタイルは以下の方法で追加できます：

### 1. assets/css/src/tailwind.cssに追加

```css
@layer components {
  .custom-component {
    @apply bg-white rounded-lg shadow-md p-6;
  }
}
```

### 2. style.cssに追加

Tailwindを使用しない独自のCSSは`style.css`に直接記述できます。

## ビルドプロセス

1. `assets/css/src/tailwind.css` を編集
2. `npm run build:prod` を実行
3. `assets/css/tailwind-build.min.css` が生成される
4. WordPressが自動的に最新のCSSを読み込む

## トラブルシューティング

### ビルドが失敗する場合

```bash
# node_modulesを削除して再インストール
rm -rf node_modules package-lock.json
npm install
```

### CSSの変更が反映されない場合

1. WordPressのキャッシュをクリア
2. ブラウザのキャッシュをクリア
3. 再度ビルドを実行：`npm run build:prod`

### browserslist警告が表示される場合

```bash
npx update-browserslist-db@latest
```

## デプロイ時の注意事項

### 本番環境にデプロイする前に

1. `npm run build:prod` を実行してビルド
2. 以下のファイルをコミット：
   - `assets/css/tailwind-build.css`
   - `assets/css/tailwind-build.min.css`
   - `tailwind.config.js`
   - `postcss.config.js`
   - `package.json`

### 除外するファイル（.gitignore）

- `node_modules/` - npm依存関係
- `package-lock.json` - ローカルロックファイル

**注意**: ビルド済みCSSファイル（`tailwind-build*.css`）は本番で使用するため、**コミットする必要があります**。

## 参考リンク

- [Tailwind CSS 公式ドキュメント](https://tailwindcss.com/docs)
- [Tailwind CSS Configuration](https://tailwindcss.com/docs/configuration)
- [PostCSS](https://postcss.org/)

## バージョン履歴

- **v5.2.0** - Tailwind CSS Build Editionに移行
- **v5.1.0** - Tailwind CSS Play CDN Edition
