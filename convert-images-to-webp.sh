#!/bin/bash
# ============================================
# WordPress画像をWebPに変換するスクリプト
# ============================================
# 使い方:
#   chmod +x convert-images-to-webp.sh
#   ./convert-images-to-webp.sh
#
# 必要なツール:
#   - cwebp (libwebpパッケージ)
#   Ubuntu/Debian: sudo apt install webp
#   macOS: brew install webp
# ============================================

# 色付き出力用
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}  WordPress画像 → WebP変換スクリプト${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo

# cwebpコマンドの確認
if ! command -v cwebp &> /dev/null; then
    echo -e "${RED}エラー: cwebpコマンドが見つかりません${NC}"
    echo -e "${YELLOW}以下のコマンドでインストールしてください:${NC}"
    echo "  Ubuntu/Debian: sudo apt install webp"
    echo "  macOS: brew install webp"
    exit 1
fi

# 対象ディレクトリ
UPLOAD_DIR="wp-content/uploads"

if [ ! -d "$UPLOAD_DIR" ]; then
    echo -e "${RED}エラー: $UPLOAD_DIR ディレクトリが見つかりません${NC}"
    echo "スクリプトをWordPressルートディレクトリで実行してください"
    exit 1
fi

echo -e "${YELLOW}対象ディレクトリ:${NC} $UPLOAD_DIR"
echo

# 変換設定
QUALITY=85  # WebP品質 (0-100, 推奨: 80-90)
CONVERTED=0
SKIPPED=0
FAILED=0

# PNG画像を変換
echo -e "${GREEN}PNG画像を変換中...${NC}"
while IFS= read -r -d '' file; do
    # WebPファイル名
    webp_file="${file%.*}.webp"
    
    # 既にWebPが存在する場合はスキップ
    if [ -f "$webp_file" ]; then
        echo -e "${YELLOW}[SKIP]${NC} $file (既存)"
        ((SKIPPED++))
        continue
    fi
    
    # 変換実行
    if cwebp -q $QUALITY "$file" -o "$webp_file" > /dev/null 2>&1; then
        # ファイルサイズ比較
        ORIGINAL_SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
        WEBP_SIZE=$(stat -f%z "$webp_file" 2>/dev/null || stat -c%s "$webp_file" 2>/dev/null)
        REDUCTION=$(awk "BEGIN {printf \"%.1f\", (1 - $WEBP_SIZE / $ORIGINAL_SIZE) * 100}")
        
        echo -e "${GREEN}[OK]${NC} $file → ${webp_file##*/} (${REDUCTION}% 削減)"
        ((CONVERTED++))
    else
        echo -e "${RED}[ERROR]${NC} $file"
        ((FAILED++))
    fi
done < <(find "$UPLOAD_DIR" -type f \( -iname "*.png" \) -print0)

# JPG/JPEG画像を変換
echo
echo -e "${GREEN}JPG/JPEG画像を変換中...${NC}"
while IFS= read -r -d '' file; do
    webp_file="${file%.*}.webp"
    
    if [ -f "$webp_file" ]; then
        echo -e "${YELLOW}[SKIP]${NC} $file (既存)"
        ((SKIPPED++))
        continue
    fi
    
    if cwebp -q $QUALITY "$file" -o "$webp_file" > /dev/null 2>&1; then
        ORIGINAL_SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
        WEBP_SIZE=$(stat -f%z "$webp_file" 2>/dev/null || stat -c%s "$webp_file" 2>/dev/null)
        REDUCTION=$(awk "BEGIN {printf \"%.1f\", (1 - $WEBP_SIZE / $ORIGINAL_SIZE) * 100}")
        
        echo -e "${GREEN}[OK]${NC} $file → ${webp_file##*/} (${REDUCTION}% 削減)"
        ((CONVERTED++))
    else
        echo -e "${RED}[ERROR]${NC} $file"
        ((FAILED++))
    fi
done < <(find "$UPLOAD_DIR" -type f \( -iname "*.jpg" -o -iname "*.jpeg" \) -print0)

# 結果サマリー
echo
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}  変換完了${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✓ 変換成功:${NC} $CONVERTED ファイル"
echo -e "${YELLOW}○ スキップ:${NC} $SKIPPED ファイル"
if [ $FAILED -gt 0 ]; then
    echo -e "${RED}✗ 失敗:${NC} $FAILED ファイル"
fi
echo

echo -e "${YELLOW}次のステップ:${NC}"
echo "1. テーマで <picture> タグを使用してWebP画像を提供:"
echo "   <picture>"
echo "     <source srcset=\"image.webp\" type=\"image/webp\">"
echo "     <img src=\"image.jpg\" alt=\"...\">"
echo "   </picture>"
echo
echo "2. または、WordPressプラグインを使用:"
echo "   - ShortPixel Image Optimizer"
echo "   - Smush"
echo "   - EWWW Image Optimizer"
echo
echo -e "${GREEN}完了！${NC}"
