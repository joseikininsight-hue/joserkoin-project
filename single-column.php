<?php
/**
 * Single Column Template
 * コラム詳細ページテンプレート
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

get_header();

// 記事情報を取得
$post_id = get_the_ID();
$reading_time = gi_get_column_reading_time($post_id);
$author_info = gi_get_column_author_info($post_id);
$view_count = gi_get_column_view_count($post_id);
$categories = get_the_terms($post_id, 'column_category');
$tags = get_the_terms($post_id, 'column_tag');
$related_grants = gi_get_column_related_grants($post_id, 3);
$related_columns = gi_get_related_columns($post_id, 3);

?>

<style>
/* ============================================
   コラム詳細ページスタイル
   ============================================ */
   
.column-single {
    background: #ffffff;
    min-height: 100vh;
}

.column-container {
    max-width: 960px;
    margin: 0 auto;
    padding: 100px 20px 60px;
}

/* パンくずリスト */
.column-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 32px;
    padding: 12px 16px;
    background: #f5f5f5;
    border-radius: 8px;
}

.column-breadcrumb-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.column-breadcrumb-item a {
    color: #666;
    text-decoration: none;
    transition: color 0.2s;
}

.column-breadcrumb-item a:hover {
    color: #000;
}

.column-breadcrumb-separator {
    color: #ccc;
}

/* ヘッダー */
.column-header {
    margin-bottom: 40px;
}

.column-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
    margin-bottom: 20px;
}

.column-category-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    background: #000;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 999px;
    text-decoration: none;
    transition: background 0.2s;
}

.column-category-badge:hover {
    background: #333;
}

.column-date {
    font-size: 0.875rem;
    color: #666;
}

.column-reading-time {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.875rem;
    color: #666;
}

.column-view-count {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.875rem;
    color: #666;
}

.column-title {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1.4;
    color: #000;
    margin: 0 0 20px 0;
}

@media (min-width: 768px) {
    .column-title {
        font-size: 2.5rem;
    }
}

.column-excerpt {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #666;
    margin: 0 0 24px 0;
}

.column-author {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f9f9f9;
    border-left: 4px solid #ffeb3b;
    border-radius: 4px;
}

.column-author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #666;
}

.column-author-info {
    flex: 1;
}

.column-author-name {
    font-weight: 600;
    font-size: 1rem;
    color: #000;
    margin: 0 0 4px 0;
}

.column-author-title {
    font-size: 0.875rem;
    color: #666;
    margin: 0;
}

/* アイキャッチ画像 */
.column-thumbnail {
    width: 100%;
    margin: 32px 0;
    border-radius: 12px;
    overflow: hidden;
}

.column-thumbnail img {
    width: 100%;
    height: auto;
    display: block;
}

/* コンテンツ */
.column-content {
    font-size: 1.0625rem;
    line-height: 1.9;
    color: #333;
}

.column-content h2 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 48px 0 24px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
}

.column-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 40px 0 20px 0;
}

.column-content p {
    margin: 0 0 24px 0;
}

.column-content ul,
.column-content ol {
    margin: 0 0 24px 0;
    padding-left: 1.5em;
}

.column-content li {
    margin-bottom: 12px;
}

.column-content a {
    color: #0066cc;
    text-decoration: underline;
}

.column-content a:hover {
    color: #0052a3;
}

.column-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 24px 0;
}

.column-content blockquote {
    margin: 32px 0;
    padding: 20px 24px;
    background: #f9f9f9;
    border-left: 4px solid #ffeb3b;
    font-style: italic;
    color: #666;
}

/* タグ */
.column-tags {
    margin: 40px 0;
    padding: 20px 0;
    border-top: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
}

.column-tags-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #666;
    margin: 0 0 12px 0;
}

.column-tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.column-tag {
    display: inline-block;
    padding: 6px 12px;
    background: #f5f5f5;
    color: #666;
    font-size: 0.875rem;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s;
}

.column-tag:hover {
    background: #e0e0e0;
    color: #000;
}

/* 関連助成金 */
.column-related-grants {
    margin: 40px 0;
    padding: 24px;
    background: #fafafa;
    border-radius: 12px;
}

.column-section-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 20px 0;
    color: #000;
}

.grants-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}

@media (min-width: 768px) {
    .grants-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.grant-card {
    padding: 16px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.grant-card:hover {
    border-color: #ffeb3b;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.grant-card-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #000;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.grant-card-meta {
    font-size: 0.8125rem;
    color: #666;
}

/* 関連コラム */
.column-related {
    margin: 60px 0 40px 0;
}

.related-columns-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

@media (min-width: 768px) {
    .related-columns-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.related-column-card {
    display: block;
    text-decoration: none;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    border: 1px solid #e0e0e0;
    transition: all 0.3s;
}

.related-column-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    transform: translateY(-4px);
}

.related-column-thumbnail {
    width: 100%;
    height: 180px;
    object-fit: cover;
    background: #f0f0f0;
}

.related-column-info {
    padding: 16px;
}

.related-column-category {
    font-size: 0.75rem;
    color: #666;
    margin: 0 0 8px 0;
}

.related-column-title {
    font-size: 1rem;
    font-weight: 600;
    color: #000;
    margin: 0;
    line-height: 1.4;
}

/* シェアボタン */
.column-share {
    margin: 40px 0;
    padding: 24px;
    background: #f9f9f9;
    border-radius: 12px;
    text-align: center;
}

.column-share-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 16px 0;
    color: #000;
}

.share-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s;
}

.share-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.share-btn-twitter {
    background: #1da1f2;
}

.share-btn-facebook {
    background: #1877f2;
}

.share-btn-line {
    background: #00b900;
}

.share-btn-copy {
    background: #666;
}
</style>

<main class="column-single">
    <div class="column-container">
        <?php while (have_posts()) : the_post(); ?>
            
            <!-- パンくずリスト -->
            <nav class="column-breadcrumb" aria-label="パンくずリスト">
                <?php
                $breadcrumb = gi_get_column_breadcrumb($post_id);
                $total = count($breadcrumb);
                foreach ($breadcrumb as $index => $item) :
                    $is_last = ($index === $total - 1);
                ?>
                    <div class="column-breadcrumb-item">
                        <?php if ($is_last) : ?>
                            <span><?php echo esc_html($item['title']); ?></span>
                        <?php else : ?>
                            <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                            <span class="column-breadcrumb-separator">›</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </nav>
            
            <!-- 記事ヘッダー -->
            <header class="column-header">
                <div class="column-meta">
                    <?php if ($categories && !is_wp_error($categories)) : ?>
                        <a href="<?php echo esc_url(get_term_link($categories[0])); ?>" class="column-category-badge">
                            <?php echo esc_html($categories[0]->name); ?>
                        </a>
                    <?php endif; ?>
                    
                    <time class="column-date" datetime="<?php echo get_the_date('c'); ?>">
                        <i class="far fa-calendar"></i>
                        <?php echo get_the_date('Y年n月j日'); ?>
                    </time>
                    
                    <div class="column-reading-time">
                        <i class="far fa-clock"></i>
                        <?php echo esc_html($reading_time); ?>分で読めます
                    </div>
                    
                    <div class="column-view-count">
                        <i class="far fa-eye"></i>
                        <?php echo number_format($view_count); ?> views
                    </div>
                </div>
                
                <h1 class="column-title"><?php the_title(); ?></h1>
                
                <?php if (has_excerpt()) : ?>
                    <div class="column-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($author_info['name'])) : ?>
                    <div class="column-author">
                        <div class="column-author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="column-author-info">
                            <div class="column-author-name"><?php echo esc_html($author_info['name']); ?></div>
                            <?php if (!empty($author_info['title'])) : ?>
                                <div class="column-author-title"><?php echo esc_html($author_info['title']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </header>
            
            <!-- アイキャッチ画像 -->
            <?php if (has_post_thumbnail()) : ?>
                <figure class="column-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </figure>
            <?php endif; ?>
            
            <!-- 本文 -->
            <article class="column-content">
                <?php the_content(); ?>
            </article>
            
            <!-- タグ -->
            <?php if ($tags && !is_wp_error($tags)) : ?>
                <div class="column-tags">
                    <div class="column-tags-title">
                        <i class="fas fa-tags"></i> タグ
                    </div>
                    <div class="column-tags-list">
                        <?php foreach ($tags as $tag) : ?>
                            <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="column-tag">
                                #<?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- シェアボタン -->
            <div class="column-share">
                <div class="column-share-title">この記事をシェアする</div>
                <div class="share-buttons">
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                       class="share-btn share-btn-twitter" target="_blank" rel="noopener">
                        <i class="fab fa-twitter"></i>
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                       class="share-btn share-btn-facebook" target="_blank" rel="noopener">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode(get_permalink()); ?>" 
                       class="share-btn share-btn-line" target="_blank" rel="noopener">
                        <i class="fab fa-line"></i>
                        LINE
                    </a>
                    <button class="share-btn share-btn-copy" onclick="copyToClipboard('<?php echo esc_js(get_permalink()); ?>')">
                        <i class="fas fa-link"></i>
                        URLコピー
                    </button>
                </div>
            </div>
            
            <!-- 関連助成金 -->
            <?php if (!empty($related_grants)) : ?>
                <aside class="column-related-grants">
                    <h2 class="column-section-title">
                        <i class="fas fa-coins"></i> この記事に関連する助成金
                    </h2>
                    <div class="grants-grid">
                        <?php foreach ($related_grants as $grant) : ?>
                            <a href="<?php echo get_permalink($grant->ID); ?>" class="grant-card">
                                <div class="grant-card-title"><?php echo esc_html($grant->post_title); ?></div>
                                <div class="grant-card-meta">
                                    <?php
                                    $grant_categories = get_the_terms($grant->ID, 'grant_category');
                                    if ($grant_categories && !is_wp_error($grant_categories)) {
                                        echo esc_html($grant_categories[0]->name);
                                    }
                                    ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </aside>
            <?php endif; ?>
            
            <!-- 関連コラム -->
            <?php if (!empty($related_columns)) : ?>
                <aside class="column-related">
                    <h2 class="column-section-title">
                        <i class="fas fa-book-open"></i> 関連するコラム
                    </h2>
                    <div class="related-columns-grid">
                        <?php foreach ($related_columns as $related_column) : setup_postdata($related_column); ?>
                            <a href="<?php echo get_permalink($related_column->ID); ?>" class="related-column-card">
                                <?php if (has_post_thumbnail($related_column->ID)) : ?>
                                    <?php echo get_the_post_thumbnail($related_column->ID, 'medium', array('class' => 'related-column-thumbnail')); ?>
                                <?php else : ?>
                                    <div class="related-column-thumbnail" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                                <?php endif; ?>
                                <div class="related-column-info">
                                    <?php
                                    $rel_categories = get_the_terms($related_column->ID, 'column_category');
                                    if ($rel_categories && !is_wp_error($rel_categories)) :
                                    ?>
                                        <div class="related-column-category"><?php echo esc_html($rel_categories[0]->name); ?></div>
                                    <?php endif; ?>
                                    <h3 class="related-column-title"><?php echo esc_html($related_column->post_title); ?></h3>
                                </div>
                            </a>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div>
                </aside>
            <?php endif; ?>
            
        <?php endwhile; ?>
    </div>
</main>

<script>
// URLをクリップボードにコピー
function copyToClipboard(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
            alert('URLをコピーしました！');
        }).catch(function(err) {
            console.error('コピーに失敗しました:', err);
        });
    } else {
        // フォールバック
        const textarea = document.createElement('textarea');
        textarea.value = url;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('URLをコピーしました！');
    }
}
</script>

<!-- 構造化データ (Article Schema) -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "<?php echo esc_js(get_the_title()); ?>",
    "image": "<?php echo esc_url(gi_get_column_thumbnail_url($post_id)); ?>",
    "datePublished": "<?php echo get_the_date('c'); ?>",
    "dateModified": "<?php echo get_the_modified_date('c'); ?>",
    "author": {
        "@type": "Person",
        "name": "<?php echo esc_js(!empty($author_info['name']) ? $author_info['name'] : get_the_author()); ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>"
        }
    },
    "description": "<?php echo esc_js(gi_get_column_excerpt($post_id, 150)); ?>",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo esc_url(get_permalink()); ?>"
    }
}
</script>

<?php get_footer(); ?>
