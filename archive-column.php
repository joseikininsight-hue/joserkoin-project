<?php
/**
 * Column Archive Template
 * コラム一覧ページテンプレート
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

get_header();

// 統計情報を取得
$stats = gi_get_column_stats();
$current_category = get_queried_object();
$is_category_archive = is_tax('column_category');
$is_tag_archive = is_tax('column_tag');

// 検索パラメータ
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$selected_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$sort_by = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'latest';

?>

<style>
/* ============================================
   コラムアーカイブページスタイル
   ============================================ */

.column-archive {
    background: #ffffff;
    min-height: 100vh;
}

.column-archive-container {
    max-width: 960px;
    margin: 0 auto;
    padding: 100px 20px 60px;
}

/* ヘッダー */
.column-archive-header {
    text-align: center;
    margin-bottom: 48px;
}

.column-archive-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #000;
    margin: 0 0 16px 0;
}

.column-archive-description {
    font-size: 1.125rem;
    color: #666;
    margin: 0 0 24px 0;
    line-height: 1.6;
}

.column-archive-stats {
    display: flex;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
    padding: 24px;
    background: #f9f9f9;
    border-radius: 12px;
    margin-top: 24px;
}

.column-stat-item {
    text-align: center;
}

.column-stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #000;
    display: block;
}

.column-stat-label {
    font-size: 0.875rem;
    color: #666;
    display: block;
    margin-top: 4px;
}

/* 検索・フィルター */
.column-search-filter {
    margin-bottom: 40px;
    padding: 24px;
    background: #f9f9f9;
    border-radius: 12px;
}

.search-filter-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

@media (min-width: 768px) {
    .search-filter-row {
        grid-template-columns: 2fr 1fr 1fr auto;
    }
}

.search-input-wrapper {
    position: relative;
}

.search-input {
    width: 100%;
    padding: 12px 16px 12px 44px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.9375rem;
    transition: border-color 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: #000;
}

.search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.filter-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.9375rem;
    background: #fff;
    cursor: pointer;
    transition: border-color 0.2s;
}

.filter-select:focus {
    outline: none;
    border-color: #000;
}

.search-btn {
    padding: 12px 24px;
    background: #000;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    white-space: nowrap;
}

.search-btn:hover {
    background: #333;
}

/* カテゴリータブ */
.column-categories {
    margin-bottom: 40px;
}

.category-tabs {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: center;
}

.category-tab {
    padding: 10px 20px;
    background: #f5f5f5;
    color: #666;
    border: 1px solid #e0e0e0;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.9375rem;
    font-weight: 500;
    transition: all 0.2s;
}

.category-tab:hover {
    background: #e0e0e0;
    color: #000;
}

.category-tab.active {
    background: #000;
    color: #fff;
    border-color: #000;
}

.category-tab-count {
    margin-left: 6px;
    font-size: 0.8125rem;
    opacity: 0.7;
}

/* コラムグリッド */
.columns-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 32px;
    margin-bottom: 60px;
}

@media (min-width: 640px) {
    .columns-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .columns-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.column-card {
    display: block;
    text-decoration: none;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    border: 1px solid #e0e0e0;
    transition: all 0.3s;
}

.column-card:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.12);
    transform: translateY(-8px);
    border-color: #ffeb3b;
}

.column-card-thumbnail {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.column-card-content {
    padding: 20px;
}

.column-card-meta {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.column-card-category {
    display: inline-block;
    padding: 4px 12px;
    background: #000;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 999px;
}

.column-card-date {
    font-size: 0.8125rem;
    color: #999;
}

.column-card-reading-time {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8125rem;
    color: #999;
}

.column-card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #000;
    margin: 0 0 12px 0;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.column-card-excerpt {
    font-size: 0.9375rem;
    color: #666;
    line-height: 1.6;
    margin: 0 0 16px 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.column-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid #f0f0f0;
}

.column-card-author {
    font-size: 0.8125rem;
    color: #666;
}

.column-card-views {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8125rem;
    color: #999;
}

/* 注目コラム */
.featured-columns {
    margin-bottom: 60px;
}

.featured-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #000;
    margin: 0 0 24px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.featured-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: #ffeb3b;
    color: #000;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 4px;
}

.featured-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 768px) {
    .featured-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.featured-card {
    display: block;
    text-decoration: none;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.featured-card:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.16);
    transform: translateY(-4px);
}

.featured-card-thumbnail {
    width: 100%;
    height: 280px;
    object-fit: cover;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.featured-card-content {
    padding: 24px;
}

.featured-card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #000;
    margin: 0 0 12px 0;
    line-height: 1.4;
}

/* ページネーション */
.column-pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 60px;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: 0 16px;
    background: #f5f5f5;
    color: #666;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.pagination-btn:hover {
    background: #e0e0e0;
    color: #000;
}

.pagination-btn.current {
    background: #000;
    color: #fff;
    border-color: #000;
}

.pagination-btn.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}

/* 検索結果なし */
.no-results {
    text-align: center;
    padding: 80px 20px;
}

.no-results-icon {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 24px;
}

.no-results-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #000;
    margin: 0 0 16px 0;
}

.no-results-text {
    font-size: 1rem;
    color: #666;
    margin: 0;
}

/* ローディング */
.loading {
    text-align: center;
    padding: 40px;
}

.loading-spinner {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 4px solid #f0f0f0;
    border-top-color: #000;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<main class="column-archive">
    <div class="column-archive-container">
        
        <!-- ヘッダー -->
        <header class="column-archive-header">
            <?php if ($is_category_archive) : ?>
                <h1 class="column-archive-title">
                    <?php echo esc_html($current_category->name); ?>
                </h1>
                <?php if ($current_category->description) : ?>
                    <p class="column-archive-description">
                        <?php echo esc_html($current_category->description); ?>
                    </p>
                <?php endif; ?>
            <?php elseif ($is_tag_archive) : ?>
                <h1 class="column-archive-title">
                    #<?php echo esc_html($current_category->name); ?>
                </h1>
            <?php else : ?>
                <h1 class="column-archive-title">コラム</h1>
                <p class="column-archive-description">
                    補助金・助成金に関する最新情報や活用ノウハウをお届けします
                </p>
                
                <div class="column-archive-stats">
                    <div class="column-stat-item">
                        <span class="column-stat-number"><?php echo number_format($stats['total_columns']); ?></span>
                        <span class="column-stat-label">記事数</span>
                    </div>
                    <div class="column-stat-item">
                        <span class="column-stat-number"><?php echo number_format($stats['total_categories']); ?></span>
                        <span class="column-stat-label">カテゴリー</span>
                    </div>
                </div>
            <?php endif; ?>
        </header>
        
        <!-- 検索・フィルター -->
        <div class="column-search-filter">
            <form method="get" action="<?php echo esc_url(get_post_type_archive_link('column')); ?>">
                <div class="search-filter-row">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" 
                               name="s" 
                               class="search-input" 
                               placeholder="コラムを検索..." 
                               value="<?php echo esc_attr($search_query); ?>">
                    </div>
                    
                    <select name="category" class="filter-select">
                        <option value="">すべてのカテゴリー</option>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'column_category',
                            'hide_empty' => true,
                        ));
                        if ($categories && !is_wp_error($categories)) {
                            foreach ($categories as $cat) {
                                printf(
                                    '<option value="%s"%s>%s (%d)</option>',
                                    esc_attr($cat->slug),
                                    selected($selected_category, $cat->slug, false),
                                    esc_html($cat->name),
                                    $cat->count
                                );
                            }
                        }
                        ?>
                    </select>
                    
                    <select name="sort" class="filter-select">
                        <option value="latest"<?php selected($sort_by, 'latest'); ?>>新着順</option>
                        <option value="popular"<?php selected($sort_by, 'popular'); ?>>人気順</option>
                        <option value="oldest"<?php selected($sort_by, 'oldest'); ?>>古い順</option>
                    </select>
                    
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                        検索
                    </button>
                </div>
            </form>
        </div>
        
        <!-- カテゴリータブ -->
        <?php if (!$is_category_archive && !$is_tag_archive) : ?>
            <div class="column-categories">
                <div class="category-tabs">
                    <a href="<?php echo esc_url(get_post_type_archive_link('column')); ?>" 
                       class="category-tab<?php echo !$selected_category ? ' active' : ''; ?>">
                        すべて
                        <span class="category-tab-count"><?php echo $stats['total_columns']; ?></span>
                    </a>
                    <?php
                    if ($categories && !is_wp_error($categories)) {
                        foreach ($categories as $cat) {
                            $is_active = ($selected_category === $cat->slug);
                            printf(
                                '<a href="%s" class="category-tab%s">%s<span class="category-tab-count">%d</span></a>',
                                esc_url(get_term_link($cat)),
                                $is_active ? ' active' : '',
                                esc_html($cat->name),
                                $cat->count
                            );
                        }
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- 注目コラム（トップページのみ） -->
        <?php if (!$is_category_archive && !$is_tag_archive && !$search_query && !isset($_GET['paged'])) : ?>
            <?php
            $featured_columns = gi_get_featured_columns(2);
            if (!empty($featured_columns)) :
            ?>
                <section class="featured-columns">
                    <h2 class="featured-title">
                        <span class="featured-badge">
                            <i class="fas fa-star"></i>
                            注目コラム
                        </span>
                    </h2>
                    <div class="featured-grid">
                        <?php foreach ($featured_columns as $column) : setup_postdata($column); ?>
                            <a href="<?php echo get_permalink($column->ID); ?>" class="featured-card">
                                <?php if (has_post_thumbnail($column->ID)) : ?>
                                    <?php echo get_the_post_thumbnail($column->ID, 'large', array('class' => 'featured-card-thumbnail')); ?>
                                <?php else : ?>
                                    <div class="featured-card-thumbnail"></div>
                                <?php endif; ?>
                                <div class="featured-card-content">
                                    <h3 class="featured-card-title"><?php echo esc_html($column->post_title); ?></h3>
                                    <div class="column-card-meta">
                                        <?php
                                        $cats = get_the_terms($column->ID, 'column_category');
                                        if ($cats && !is_wp_error($cats)) :
                                        ?>
                                            <span class="column-card-category"><?php echo esc_html($cats[0]->name); ?></span>
                                        <?php endif; ?>
                                        <span class="column-card-reading-time">
                                            <i class="far fa-clock"></i>
                                            <?php echo gi_get_column_reading_time($column->ID); ?>分
                                        </span>
                                    </div>
                                    <p class="column-card-excerpt"><?php echo gi_get_column_excerpt($column->ID, 100); ?></p>
                                </div>
                            </a>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- コラムグリッド -->
        <?php if (have_posts()) : ?>
            <div class="columns-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <a href="<?php the_permalink(); ?>" class="column-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('class' => 'column-card-thumbnail')); ?>
                        <?php else : ?>
                            <div class="column-card-thumbnail"></div>
                        <?php endif; ?>
                        
                        <div class="column-card-content">
                            <div class="column-card-meta">
                                <?php
                                $categories = get_the_terms(get_the_ID(), 'column_category');
                                if ($categories && !is_wp_error($categories)) :
                                ?>
                                    <span class="column-card-category"><?php echo esc_html($categories[0]->name); ?></span>
                                <?php endif; ?>
                                
                                <time class="column-card-date" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date('Y.m.d'); ?>
                                </time>
                                
                                <span class="column-card-reading-time">
                                    <i class="far fa-clock"></i>
                                    <?php echo gi_get_column_reading_time(); ?>分
                                </span>
                            </div>
                            
                            <h3 class="column-card-title"><?php the_title(); ?></h3>
                            
                            <p class="column-card-excerpt">
                                <?php echo gi_get_column_excerpt(get_the_ID(), 80); ?>
                            </p>
                            
                            <div class="column-card-footer">
                                <span class="column-card-author">
                                    <?php
                                    $author_info = gi_get_column_author_info();
                                    echo esc_html($author_info['name']);
                                    ?>
                                </span>
                                <span class="column-card-views">
                                    <i class="far fa-eye"></i>
                                    <?php echo number_format(gi_get_column_view_count()); ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
            
            <!-- ページネーション -->
            <?php
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $total_pages = $wp_query->max_num_pages;
            
            if ($total_pages > 1) :
            ?>
                <nav class="column-pagination" aria-label="ページネーション">
                    <?php if ($paged > 1) : ?>
                        <a href="<?php echo get_pagenum_link($paged - 1); ?>" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php else : ?>
                        <span class="pagination-btn disabled">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $paged - 2);
                    $end_page = min($total_pages, $paged + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++) :
                        if ($i == $paged) :
                    ?>
                        <span class="pagination-btn current"><?php echo $i; ?></span>
                    <?php else : ?>
                        <a href="<?php echo get_pagenum_link($i); ?>" class="pagination-btn"><?php echo $i; ?></a>
                    <?php
                        endif;
                    endfor;
                    ?>
                    
                    <?php if ($paged < $total_pages) : ?>
                        <a href="<?php echo get_pagenum_link($paged + 1); ?>" class="pagination-btn">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else : ?>
                        <span class="pagination-btn disabled">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
            
        <?php else : ?>
            <!-- 検索結果なし -->
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h2 class="no-results-title">コラムが見つかりませんでした</h2>
                <p class="no-results-text">
                    検索条件を変更して、もう一度お試しください。
                </p>
            </div>
        <?php endif; ?>
        
    </div>
</main>

<!-- 構造化データ (CollectionPage Schema) -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "<?php echo esc_js(wp_get_document_title()); ?>",
    "description": "補助金・助成金に関する最新コラムと活用ノウハウ",
    "url": "<?php echo esc_url(get_post_type_archive_link('column')); ?>",
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo esc_js(get_bloginfo('name')); ?>"
    }
}
</script>

<?php get_footer(); ?>
