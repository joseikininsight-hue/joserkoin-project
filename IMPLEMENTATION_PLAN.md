# Phase 1 実装計画書: クイックウィン機能（1-2ヶ月）

**プロジェクト**: Grant Insight Perfect 改善プロジェクト  
**期間**: 2025-11-01 ~ 2025-12-31 (8週間)  
**目標**: 即座にユーザー体験を向上させ、エンゲージメント+40%を達成  

---

## 📋 実装優先順位マトリクス

| 機能 | 影響度 | 実装難易度 | 優先度 | 期間 |
|------|--------|-----------|--------|------|
| **お気に入り機能** | 🔴 HIGH | 🟢 LOW | ★★★ | Week 1-2 |
| **AI検索可視化** | 🔴 HIGH | 🟢 LOW | ★★★ | Week 1-2 |
| **比較機能** | 🟡 MED | 🟡 MED | ★★☆ | Week 2-3 |
| **フィルタUI改善** | 🟡 MED | 🟢 LOW | ★★☆ | Week 3-4 |
| **モバイルUX最適化** | 🔴 HIGH | 🟡 MED | ★★★ | Week 3-5 |
| **CTAボタン最適化** | 🔴 HIGH | 🟢 LOW | ★★★ | Week 4-5 |
| **パフォーマンス改善** | 🟡 MED | 🟡 MED | ★★☆ | Week 5-6 |
| **コンテンツ追加** | 🟡 MED | 🟢 LOW | ★☆☆ | Week 6-8 |

---

## 🎯 Week 1-2: お気に入り機能 + AI検索可視化

### 【機能1】お気に入り（ブックマーク）機能

#### 要件定義
```
✅ ユーザー要件
- ワンクリックでお気に入り登録/解除
- お気に入り一覧の閲覧
- ローカルストレージ保存（非ログイン）
- お気に入り数の表示
- お気に入りからの削除

✅ 技術要件
- LocalStorage API活用
- 上限: 最大50件
- データ構造: JSON形式
- レスポンシブ対応
```

#### データ構造
```javascript
// LocalStorage保存形式
{
  "favorites": [
    {
      "id": 12345,
      "title": "IT導入補助金2024",
      "category": "IT・デジタル化",
      "prefecture": "東京都",
      "amount": "最大450万円",
      "deadline": "2024-12-31",
      "addedAt": "2024-10-25T12:34:56Z"
    }
  ],
  "version": "1.0",
  "lastUpdated": "2024-10-25T12:34:56Z"
}
```

#### UI設計
```
【お気に入りボタン】
位置: カード右上 + 詳細ページヘッダー
デザイン: 
  - 未登録: ♡（灰色・アウトライン）
  - 登録済: ♥（赤色・塗りつぶし）
  - ホバー: スケールアップ + 色変化
  - クリック: ハートアニメーション

【お気に入り一覧ページ】
URL: /favorites/ または /my-grants/
レイアウト:
  - ヘッダー: 「お気に入り ({count}件)」
  - ソート: 追加日順 / 締切順 / 金額順
  - 表示: グリッド or リスト切替
  - アクション: 削除、比較に追加、詳細表示
```

#### 実装ファイル
```
/assets/js/favorites.js      (新規作成)
/template-parts/favorites-button.php  (新規作成)
/page-favorites.php          (新規作成)
/style.css                   (追加スタイル)
```

#### コード例
```javascript
// /assets/js/favorites.js
class GrantFavorites {
    constructor() {
        this.storageKey = 'gi_favorites';
        this.maxItems = 50;
        this.init();
    }

    init() {
        this.loadFavorites();
        this.bindEvents();
    }

    loadFavorites() {
        const stored = localStorage.getItem(this.storageKey);
        this.favorites = stored ? JSON.parse(stored) : { favorites: [], version: '1.0' };
        this.updateUI();
    }

    saveFavorites() {
        this.favorites.lastUpdated = new Date().toISOString();
        localStorage.setItem(this.storageKey, JSON.stringify(this.favorites));
    }

    addFavorite(grantData) {
        if (this.favorites.favorites.length >= this.maxItems) {
            this.showNotification('お気に入りの上限（50件）に達しています', 'warning');
            return false;
        }

        if (this.isFavorite(grantData.id)) {
            this.showNotification('既にお気に入りに追加されています', 'info');
            return false;
        }

        this.favorites.favorites.unshift({
            id: grantData.id,
            title: grantData.title,
            category: grantData.category,
            prefecture: grantData.prefecture,
            amount: grantData.amount,
            deadline: grantData.deadline,
            addedAt: new Date().toISOString()
        });

        this.saveFavorites();
        this.updateUI();
        this.showNotification('お気に入りに追加しました', 'success');
        this.animateHeart(grantData.id);
        return true;
    }

    removeFavorite(grantId) {
        this.favorites.favorites = this.favorites.favorites.filter(item => item.id !== grantId);
        this.saveFavorites();
        this.updateUI();
        this.showNotification('お気に入りから削除しました', 'info');
        return true;
    }

    isFavorite(grantId) {
        return this.favorites.favorites.some(item => item.id === grantId);
    }

    getFavorites() {
        return this.favorites.favorites;
    }

    getFavoriteCount() {
        return this.favorites.favorites.length;
    }

    bindEvents() {
        // お気に入りボタンのクリックイベント
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.js-favorite-btn');
            if (!btn) return;

            e.preventDefault();
            const grantId = parseInt(btn.dataset.grantId);
            
            if (this.isFavorite(grantId)) {
                this.removeFavorite(grantId);
            } else {
                const grantData = this.extractGrantData(btn);
                this.addFavorite(grantData);
            }
        });
    }

    extractGrantData(btn) {
        return {
            id: parseInt(btn.dataset.grantId),
            title: btn.dataset.grantTitle || '',
            category: btn.dataset.grantCategory || '',
            prefecture: btn.dataset.grantPrefecture || '',
            amount: btn.dataset.grantAmount || '',
            deadline: btn.dataset.grantDeadline || ''
        };
    }

    updateUI() {
        // 全てのお気に入りボタンの状態を更新
        document.querySelectorAll('.js-favorite-btn').forEach(btn => {
            const grantId = parseInt(btn.dataset.grantId);
            const isFav = this.isFavorite(grantId);
            
            btn.classList.toggle('is-favorited', isFav);
            btn.setAttribute('aria-pressed', isFav);
            
            const icon = btn.querySelector('.favorite-icon');
            if (icon) {
                icon.textContent = isFav ? '♥' : '♡';
            }
        });

        // お気に入り数バッジの更新
        const count = this.getFavoriteCount();
        document.querySelectorAll('.js-favorite-count').forEach(el => {
            el.textContent = count;
            el.classList.toggle('has-items', count > 0);
        });
    }

    animateHeart(grantId) {
        const btn = document.querySelector(`.js-favorite-btn[data-grant-id="${grantId}"]`);
        if (!btn) return;

        btn.classList.add('animate-heart');
        setTimeout(() => btn.classList.remove('animate-heart'), 600);
    }

    showNotification(message, type = 'info') {
        // トースト通知を表示
        const notification = document.createElement('div');
        notification.className = `gi-notification gi-notification--${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => notification.classList.add('show'), 10);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // 比較機能との連携用
    addToComparison(grantId) {
        // 後で実装する比較機能と連携
        if (window.grantComparison) {
            const grant = this.favorites.favorites.find(item => item.id === grantId);
            if (grant) {
                window.grantComparison.add(grant);
            }
        }
    }
}

// 初期化
document.addEventListener('DOMContentLoaded', () => {
    window.grantFavorites = new GrantFavorites();
});
```

---

### 【機能2】AI検索の可視化

#### 要件定義
```
✅ 表示位置
- フッター固定ボタン（全ページ共通）
- 初回訪問時に軽くバウンドアニメーション
- スクロール時も常に表示

✅ デザイン
- アイコン: 🤖 または 💬
- テキスト: "AI相談"
- カラー: アクセントカラー（オレンジ系）
- サイズ: 60x60px（モバイル）、70x70px（デスクトップ）

✅ 動作
- クリックでモーダル展開
- チャットUI表示
- AIとの対話開始
```

#### UI設計
```css
/* フローティングAIボタン */
.gi-ai-chat-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 999;
    
    width: 60px;
    height: 60px;
    border-radius: 50%;
    
    background: linear-gradient(135deg, #f59e0b, #ea580c);
    color: white;
    
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    cursor: pointer;
    
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    
    transition: all 0.3s ease;
}

.gi-ai-chat-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6);
}

.gi-ai-chat-fab.has-notification::after {
    content: '';
    position: absolute;
    top: 8px;
    right: 8px;
    width: 12px;
    height: 12px;
    background: #ef4444;
    border: 2px solid white;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

/* 初回表示時のバウンスアニメーション */
.gi-ai-chat-fab.first-visit {
    animation: bounce-in 0.6s ease-out;
}

@keyframes bounce-in {
    0% { transform: scale(0); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* チャットモーダル */
.gi-ai-chat-modal {
    position: fixed;
    bottom: 100px;
    right: 24px;
    width: 400px;
    max-width: calc(100vw - 48px);
    height: 600px;
    max-height: calc(100vh - 150px);
    
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    
    display: none;
    flex-direction: column;
    overflow: hidden;
    
    z-index: 998;
}

.gi-ai-chat-modal.is-open {
    display: flex;
    animation: slide-up 0.3s ease-out;
}

@keyframes slide-up {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
```

#### 実装ファイル
```
/assets/js/ai-chat-widget.js   (新規作成)
/inc/ai-functions.php          (既存ファイルに追加)
/template-parts/ai-chat-fab.php (新規作成)
```

#### コード例
```javascript
// /assets/js/ai-chat-widget.js
class AIChat Widget {
    constructor() {
        this.isOpen = false;
        this.sessionId = this.generateSessionId();
        this.messages = [];
        this.init();
    }

    init() {
        this.createWidget();
        this.bindEvents();
        this.checkFirstVisit();
    }

    createWidget() {
        // FABボタンの作成
        const fab = document.createElement('button');
        fab.className = 'gi-ai-chat-fab';
        fab.innerHTML = '🤖';
        fab.setAttribute('aria-label', 'AI相談を開始');
        fab.setAttribute('title', 'AI相談 - 補助金について質問できます');
        document.body.appendChild(fab);
        this.fab = fab;

        // モーダルの作成
        const modal = document.createElement('div');
        modal.className = 'gi-ai-chat-modal';
        modal.innerHTML = this.getModalHTML();
        document.body.appendChild(modal);
        this.modal = modal;

        this.chatMessages = modal.querySelector('.gi-chat-messages');
        this.chatInput = modal.querySelector('.gi-chat-input');
        this.chatSend = modal.querySelector('.gi-chat-send');
    }

    getModalHTML() {
        return `
            <div class="gi-chat-header">
                <div class="gi-chat-header-content">
                    <span class="gi-chat-icon">🤖</span>
                    <div class="gi-chat-title">
                        <h3>AI補助金アシスタント</h3>
                        <p class="gi-chat-status">オンライン</p>
                    </div>
                </div>
                <button class="gi-chat-close" aria-label="閉じる">✕</button>
            </div>
            <div class="gi-chat-messages">
                <div class="gi-chat-message gi-chat-message--bot">
                    <div class="gi-chat-message-content">
                        こんにちは!👋<br>
                        補助金・助成金に関するご質問にお答えします。<br><br>
                        例えば:<br>
                        ・「東京都の製造業向け補助金は?」<br>
                        ・「IT導入補助金の申請方法は?」<br>
                        ・「創業時に使える助成金は?」
                    </div>
                </div>
            </div>
            <div class="gi-chat-input-wrapper">
                <input type="text" 
                       class="gi-chat-input" 
                       placeholder="メッセージを入力..."
                       aria-label="メッセージ入力"
                />
                <button class="gi-chat-send" aria-label="送信">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" 
                              stroke="currentColor" 
                              stroke-width="2" 
                              stroke-linecap="round" 
                              stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        `;
    }

    bindEvents() {
        // FABクリックでモーダル開閉
        this.fab.addEventListener('click', () => this.toggle());

        // モーダル閉じるボタン
        const closeBtn = this.modal.querySelector('.gi-chat-close');
        closeBtn.addEventListener('click', () => this.close());

        // メッセージ送信
        this.chatSend.addEventListener('click', () => this.sendMessage());
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        this.isOpen = true;
        this.modal.classList.add('is-open');
        this.fab.style.display = 'none';
        this.chatInput.focus();
        
        // 初回オープン時のメッセージ
        if (this.messages.length === 0) {
            this.addSystemMessage();
        }
    }

    close() {
        this.isOpen = false;
        this.modal.classList.remove('is-open');
        this.fab.style.display = 'flex';
    }

    async sendMessage() {
        const text = this.chatInput.value.trim();
        if (!text) return;

        // ユーザーメッセージを追加
        this.addMessage(text, 'user');
        this.chatInput.value = '';

        // タイピングインジケータ
        this.showTyping();

        try {
            // AI APIにリクエスト
            const response = await this.callAIAPI(text);
            this.hideTyping();
            this.addMessage(response, 'bot');
        } catch (error) {
            this.hideTyping();
            this.addMessage('申し訳ございません。エラーが発生しました。もう一度お試しください。', 'bot');
            console.error('AI API Error:', error);
        }
    }

    addMessage(text, type) {
        const message = document.createElement('div');
        message.className = `gi-chat-message gi-chat-message--${type}`;
        message.innerHTML = `<div class="gi-chat-message-content">${this.formatMessage(text)}</div>`;
        
        this.chatMessages.appendChild(message);
        this.scrollToBottom();
        
        this.messages.push({ text, type, timestamp: Date.now() });
    }

    formatMessage(text) {
        // URLをリンクに変換
        text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');
        // 改行を<br>に変換
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    showTyping() {
        const typing = document.createElement('div');
        typing.className = 'gi-chat-message gi-chat-message--bot gi-chat-typing';
        typing.innerHTML = `
            <div class="gi-chat-message-content">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
        `;
        this.chatMessages.appendChild(typing);
        this.scrollToBottom();
    }

    hideTyping() {
        const typing = this.chatMessages.querySelector('.gi-chat-typing');
        if (typing) typing.remove();
    }

    scrollToBottom() {
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
    }

    async callAIAPI(message) {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'gi_ai_chat',
                nonce: giAjax.nonce,
                message: message,
                session_id: this.sessionId,
                context: JSON.stringify(this.messages.slice(-5)) // 直近5メッセージ
            })
        });

        const data = await response.json();
        if (data.success) {
            return data.data.response;
        } else {
            throw new Error(data.data.message || 'API Error');
        }
    }

    checkFirstVisit() {
        const visited = localStorage.getItem('gi_ai_chat_visited');
        if (!visited) {
            this.fab.classList.add('first-visit');
            localStorage.setItem('gi_ai_chat_visited', 'true');
            
            // 3秒後にバウンスアニメーションを削除
            setTimeout(() => {
                this.fab.classList.remove('first-visit');
            }, 3000);
        }
    }

    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    addSystemMessage() {
        // システムメッセージ（初回のみ）
        const msg = `
            初めまして!私はAI補助金アシスタントです。🤖<br><br>
            補助金・助成金に関する質問に答えたり、<br>
            あなたに合った制度を提案したりできます。<br><br>
            何をお探しですか?
        `;
        this.addMessage(msg, 'bot');
    }
}

// 初期化
document.addEventListener('DOMContentLoaded', () => {
    window.aiChatWidget = new AIChatWidget();
});
```

---

## 🔄 Week 2-3: 比較機能

### 要件定義
```
✅ 機能
- 最大3件の補助金を比較
- 比較リストへの追加/削除
- 比較ページでの並列表示
- 差分の強調表示

✅ 比較項目
- 基本情報（名称、主催者、カテゴリ）
- 金額（上限額、補助率）
- 対象（業種、企業規模、用途）
- 期間（募集期間、締切）
- 申請難易度
- 必要書類
```

### UI設計
```
【比較ボタン】
位置: カード下部 + 詳細ページ
テキスト: "比較リストに追加"
カウンター: 比較リスト (2/3)

【比較ページ】
URL: /compare/
レイアウト: 3カラム並列表示
スティッキーヘッダー: 常に補助金名を表示
アクション: 削除、詳細表示、お気に入り追加
```

### データ構造
```javascript
// LocalStorage保存形式
{
  "comparison": [
    {
      "id": 12345,
      "title": "IT導入補助金2024",
      // ... (お気に入りと同じ構造)
    }
  ],
  "maxItems": 3,
  "version": "1.0"
}
```

---

## 🎨 Week 3-4: フィルタUI改善

### 改善ポイント
```
✅ 現状の問題
- フィルタが多すぎて迷う
- モバイルで使いづらい
- 選択状態が分かりにくい

✅ 改善策
- 初期表示は最小限
- 「詳細条件」で展開
- 選択中フィルタの明示
- ワンクリックリセット
- プリセット検索（おすすめ条件）
```

### デザイン案
```
【PC版】
┌────────────────────────────────┐
│ 補助金を探す                     │
├────────────────────────────────┤
│ 🔍 キーワード検索               │
│ ┌──────────────────────────┐   │
│ │ 例: IT導入、雇用、設備投資  │   │
│ └──────────────────────────┘   │
│                                 │
│ 📍 地域  [東京都 ▼]            │
│ 📂 カテゴリ [すべて ▼]         │
│                                 │
│ ＋ 詳細条件を表示 (6項目)       │
│                                 │
│ 💡 プリセット検索               │
│ [創業支援] [IT投資] [人材育成] │
└────────────────────────────────┘
```

---

## 📱 Week 3-5: モバイルUX最適化

### タッチ操作最適化
```css
/* タップターゲットサイズ */
.mobile-tap-target {
    min-width: 44px;
    min-height: 44px;
    padding: 12px;
}

/* スワイプジェスチャー対応 */
.grant-card {
    touch-action: pan-y;
    -webkit-user-select: none;
    user-select: none;
}

/* スムーズスクロール */
.scroll-container {
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-y: contain;
}
```

### ボトムナビゲーション
```
┌────────────────────────┐
│                        │ ← コンテンツエリア
│                        │
│                        │
└────────────────────────┘
┌────────────────────────┐
│ 🏠  🔍  ♡  👤  ☰     │ ← ボトムナビ
└────────────────────────┘
```

---

## ⚡ Week 5-6: パフォーマンス改善

### 実装項目
```
✅ 画像最適化
- WebP形式への変換
- Lazy Loading強化
- レスポンシブ画像

✅ JavaScript最適化
- コード分割
- 遅延読み込み
- デバウンス・スロットル

✅ CSS最適化
- Critical CSS抽出
- 未使用CSS削除
- CSS圧縮

✅ キャッシング
- ブラウザキャッシュ
- Service Worker
- LocalStorage活用
```

---

## 📝 Week 6-8: コンテンツ追加

### 追加コンテンツ
```
✅ FAQページ
- よくある質問30問
- カテゴリ分類
- 検索機能

✅ 申請ガイド
- 申請の流れ（ステップ解説）
- 必要書類チェックリスト
- よくある失敗パターン
- 採択率アップのコツ

✅ 成功事例
- 業種別事例5件
- 金額・効果の記載
- ビフォーアフター

✅ ブログ記事
- 補助金ニュース
- 制度解説
- 申請テクニック
```

---

## 🧪 テスト計画

### ユニットテスト
```javascript
// お気に入り機能のテスト
describe('GrantFavorites', () => {
    test('お気に入りに追加できる', () => {
        const favorites = new GrantFavorites();
        const grant = { id: 1, title: 'Test Grant' };
        expect(favorites.addFavorite(grant)).toBe(true);
        expect(favorites.isFavorite(1)).toBe(true);
    });

    test('上限50件を超えない', () => {
        const favorites = new GrantFavorites();
        for (let i = 0; i < 51; i++) {
            const result = favorites.addFavorite({ id: i, title: `Grant ${i}` });
            if (i < 50) {
                expect(result).toBe(true);
            } else {
                expect(result).toBe(false);
            }
        }
    });
});
```

### E2Eテスト（Playwright）
```javascript
test('お気に入り機能の一連の流れ', async ({ page }) => {
    await page.goto('/grants/');
    
    // お気に入りボタンをクリック
    await page.click('.js-favorite-btn:first-child');
    
    // トースト通知の確認
    await expect(page.locator('.gi-notification')).toContainText('お気に入りに追加しました');
    
    // お気に入りページへ移動
    await page.goto('/favorites/');
    
    // お気に入りに追加した補助金が表示されている
    await expect(page.locator('.grant-card')).toHaveCount(1);
});
```

---

## 📊 KPI測定

### 主要指標
```
【エンゲージメント】
- お気に入り登録率: 検索ユーザーの30%目標
- お気に入り平均件数: 1ユーザー3件以上
- 比較機能利用率: 検索ユーザーの15%目標
- AI相談開始率: 訪問者の10%目標

【パフォーマンス】
- ページ読み込み速度: 2秒以下
- Lighthouse Score: 90点以上
- モバイル体感速度向上: -30%

【コンバージョン】
- 問い合わせ数: +50%
- 詳細ページ遷移率: +40%
- 滞在時間: +60%
```

### 測定ツール
```
✅ Google Analytics 4
- イベントトラッキング設定
- カスタムイベント定義
- コンバージョン設定

✅ Hotjar / Microsoft Clarity
- ヒートマップ
- セッションレコーディング
- ファネル分析

✅ PageSpeed Insights
- Core Web Vitals
- パフォーマンススコア
- 最適化提案
```

---

## 🔒 セキュリティ・品質管理

### セキュリティチェックリスト
```
✅ XSS対策
- ユーザー入力のサニタイズ
- エスケープ処理
- Content Security Policy

✅ CSRF対策
- Nonceトークン使用
- Origin検証
- Same-Site Cookie

✅ データ保護
- LocalStorageの暗号化検討
- 個人情報の適切な管理
- HTTPS通信
```

### コード品質
```
✅ コーディング規約
- WordPress Coding Standards準拠
- ESLint / Prettier使用
- PHPStan / Psalm導入

✅ バージョン管理
- Git Flow採用
- コミットメッセージ規約
- プルリクエストレビュー

✅ ドキュメント
- README.md更新
- API仕様書作成
- 変更履歴記録
```

---

## 💻 開発環境セットアップ

### 必要ツール
```bash
# Node.js (v18以上)
node --version

# Composer (PHP依存管理)
composer --version

# WP-CLI (WordPress CLI)
wp --version

# Git
git --version
```

### ローカル開発環境
```bash
# プロジェクトクローン
git clone https://github.com/your-org/grant-insight.git
cd grant-insight

# 依存パッケージインストール
npm install
composer install

# 開発サーバー起動
npm run dev

# ビルド（本番用）
npm run build
```

### 開発ワークフロー
```bash
# 1. 新機能ブランチ作成
git checkout -b feature/favorites

# 2. 開発・テスト
npm run dev
npm run test

# 3. コミット
git add .
git commit -m "feat: お気に入り機能を実装"

# 4. プッシュ
git push origin feature/favorites

# 5. プルリクエスト作成
# GitHubでPR作成

# 6. レビュー・マージ
# レビュー承認後mainにマージ

# 7. 本番デプロイ
npm run build
# 本番環境へデプロイ
```

---

## 📅 マイルストーン

### Week 1 (11/01-11/07)
- [ ] お気に入り機能設計完了
- [ ] お気に入りUI実装
- [ ] LocalStorage実装
- [ ] AI検索FAB配置

### Week 2 (11/08-11/14)
- [ ] お気に入り一覧ページ実装
- [ ] AIチャットモーダル実装
- [ ] AIチャットUI完成
- [ ] 比較機能設計開始

### Week 3 (11/15-11/21)
- [ ] 比較機能実装
- [ ] フィルタUI改善開始
- [ ] モバイルUX調整開始

### Week 4 (11/22-11/28)
- [ ] フィルタUI完成
- [ ] モバイルボトムナビ実装
- [ ] CTAボタン最適化

### Week 5 (11/29-12/05)
- [ ] パフォーマンス改善開始
- [ ] 画像最適化
- [ ] JS/CSS最適化

### Week 6 (12/06-12/12)
- [ ] パフォーマンス改善完了
- [ ] コンテンツ作成開始
- [ ] FAQ・ガイド作成

### Week 7 (12/13-12/19)
- [ ] コンテンツ作成継続
- [ ] 成功事例5件追加
- [ ] ブログ記事投稿

### Week 8 (12/20-12/31)
- [ ] 総合テスト
- [ ] バグ修正
- [ ] 本番デプロイ
- [ ] KPI測定開始

---

## 🎉 完了基準

### Phase 1完了の定義
```
✅ 機能要件
- お気に入り機能が正常動作
- AI検索FABが全ページ表示
- 比較機能が実装済み
- フィルタUIが改善済み
- モバイルUXが最適化済み

✅ 品質要件
- バグゼロ
- Lighthouse Score 90+
- モバイル対応完璧
- アクセシビリティAA準拠

✅ ドキュメント
- ユーザーマニュアル作成
- 開発ドキュメント整備
- API仕様書完成

✅ KPI達成
- お気に入り登録率 20%以上
- AI相談開始率 5%以上
- ページ速度 2秒以下
- エンゲージメント +40%
```

---

## 📞 サポート・連絡先

**プロジェクトマネージャー**: [名前]  
**リードエンジニア**: [名前]  
**デザイナー**: [名前]  

**定例会議**: 毎週月曜 10:00-11:00  
**コミュニケーションツール**: Slack #grant-insight  
**課題管理**: GitHub Issues

---

**次回更新予定**: 2025-11-01  
**作成日**: 2025-10-25
