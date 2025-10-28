<?php
/**
 * Front Page - Column Section
 * フロントページ - コラムセクション
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// コラムを取得
$recent_columns = gi_get_recent_columns(6);
$column_categories = get_terms(array(
    'taxonomy' => 'column_category',
    'hide_empty' => true,
    'number' => 6,
));

if (empty($recent_columns)) {
    return; // コラムがない場合は何も表示しない
}
?>

<style>
/* ============================================
   フロントページ - コラムセクション
   ============================================ */

.column-section {
    padding: 80px 0;
    background: #fafafa;
    position: relative;
}

.column-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, #e0e0e0 50%, transparent 100%);
}

.column-container {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 20px;
}

/* セクションヘッダー */
.column-section-header {
    text-align: center;
    margin-bottom: 48px;
}

.column-section-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #000;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    border-radius: 999px;
    margin-bottom: 16px;
}

.column-section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #000;
    margin: 0 0 16px 0;
    line-height: 1.3;
}

@media (min-width: 768px) {
    .column-section-title {
        font-size: 2.5rem;
    }
}

.column-section-description {
    font-size: 1.125rem;
    color: #666;
    margin: 0;
    line-height: 1.6;
}

/* カテゴリータブ */
.column-categories-tabs {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 40px;
}

.column-category-tab {
    padding: 10px 20px;
    background: #fff;
    color: #666;
    border: 1px solid #e0e0e0;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

.column-category-tab:hover {
    background: #000;
    color: #fff;
    border-color: #000;
    transform: translateY(-2px);
}

/* コラムグリッド */
.columns-preview-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-bottom: 48px;
}

@media (min-width: 640px) {
    .columns-preview-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .columns-preview-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.column-preview-card {
    display: block;
    text-decoration: none;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.column-preview-card:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.12);
    transform: translateY(-8px);
    border-color: #ffeb3b;
}

.column-preview-thumbnail {
    width: 100%;
    height: 180px;
    object-fit: cover;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.column-preview-content {
    padding: 20px;
}

.column-preview-meta {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 12px;
}

.column-preview-category {
    display: inline-block;
    padding: 4px 10px;
    background: #000;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 999px;
}

.column-preview-date {
    font-size: 0.8125rem;
    color: #999;
}

.column-preview-title {
    font-size: 1rem;
    font-weight: 600;
    color: #000;
    margin: 0 0 10px 0;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 3em;
}

.column-preview-excerpt {
    font-size: 0.875rem;
    color: #666;
    line-height: 1.6;
    margin: 0 0 12px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.column-preview-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-top: 12px;
    border-top: 1px solid #f0f0f0;
    font-size: 0.8125rem;
    color: #999;
}

.column-preview-reading-time {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* 「もっと見る」ボタン */
.column-view-more {
    text-align: center;
}

.column-view-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    background: #000;
    color: #fff;
    border-radius: 999px;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s;
    border: 2px solid #000;
}

.column-view-more-btn:hover {
    background: #fff;
    color: #000;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.column-view-more-btn i {
    transition: transform 0.3s;
}

.column-view-more-btn:hover i {
    transform: translateX(4px);
}

/* レスポンシブ調整 */
@media (max-width: 640px) {
    .column-section {
        padding: 60px 0;
    }
    
    .column-section-header {
        margin-bottom: 32px;
    }
    
    .column-section-title {
        font-size: 1.75rem;
    }
    
    .column-section-description {
        font-size: 1rem;
    }
    
    .columns-preview-grid {
        gap: 20px;
        margin-bottom: 32px;
    }
}
</style>

<section class="column-section" id="column-section">
    <div class="column-container">
        
        <!-- セクションヘッダー -->
        <header class="column-section-header">
            <div class="column-section-badge">
                <i class="fas fa-book-open"></i>
                COLUMN
            </div>
            <h2 class="column-section-title">
                補助金活用のノウハウ
            </h2>
            <p class="column-section-description">
                最新の補助金情報や申請のコツ、成功事例などをお届けします
            </p>
        </header>
        
        <!-- カテゴリータブ -->
        <?php if (!empty($column_categories) && !is_wp_error($column_categories)) : ?>
            <div class="column-categories-tabs">
                <?php foreach ($column_categories as $category) : ?>
                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="column-category-tab">
                        <?php echo esc_html($category->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- コラムグリッド -->
        <div class="columns-preview-grid">
            <?php foreach ($recent_columns as $column) : setup_postdata($column); ?>
                <a href="<?php echo get_permalink($column->ID); ?>" class="column-preview-card">
                    <?php if (has_post_thumbnail($column->ID)) : ?>
                        <?php echo get_the_post_thumbnail($column->ID, 'medium', array('class' => 'column-preview-thumbnail')); ?>
                    <?php else : ?>
                        <div class="column-preview-thumbnail"></div>
                    <?php endif; ?>
                    
                    <div class="column-preview-content">
                        <div class="column-preview-meta">
                            <?php
                            $categories = get_the_terms($column->ID, 'column_category');
                            if ($categories && !is_wp_error($categories)) :
                            ?>
                                <span class="column-preview-category"><?php echo esc_html($categories[0]->name); ?></span>
                            <?php endif; ?>
                            
                            <time class="column-preview-date" datetime="<?php echo get_the_date('c', $column->ID); ?>">
                                <?php echo get_the_date('Y.m.d', $column->ID); ?>
                            </time>
                        </div>
                        
                        <h3 class="column-preview-title"><?php echo esc_html($column->post_title); ?></h3>
                        
                        <p class="column-preview-excerpt">
                            <?php echo gi_get_column_excerpt($column->ID, 60); ?>
                        </p>
                        
                        <div class="column-preview-footer">
                            <span class="column-preview-reading-time">
                                <i class="far fa-clock"></i>
                                <?php echo gi_get_column_reading_time($column->ID); ?>分
                            </span>
                            <span>•</span>
                            <span>
                                <i class="far fa-eye"></i>
                                <?php echo number_format(gi_get_column_view_count($column->ID)); ?>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        
        <!-- 「もっと見る」ボタン -->
        <div class="column-view-more">
            <a href="<?php echo esc_url(get_post_type_archive_link('column')); ?>" class="column-view-more-btn">
                <span>コラム一覧を見る</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
    </div>
</section>
