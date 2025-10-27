<?php
/**
 * Grant Insight Perfect - Ultra Stylish Black & White Footer Template
 * 超スタイリッシュ版 - モダンミニマルデザイン + イエローアクセント
 * 
 * @package Grant_Insight_Perfect
 * @version 9.1.0-ultra-stylish-compact
 */

// 既存ヘルパー関数との完全連携
if (!function_exists('gi_get_sns_urls')) {
    function gi_get_sns_urls() {
        return [
            'twitter' => gi_get_theme_option('sns_twitter_url', ''),
            'facebook' => gi_get_theme_option('sns_facebook_url', ''),
            'linkedin' => gi_get_theme_option('sns_linkedin_url', ''),
            'instagram' => gi_get_theme_option('sns_instagram_url', ''),
            'youtube' => gi_get_theme_option('sns_youtube_url', '')
        ];
    }
}
?>

    </main>

    <!-- スタイルとスクリプトはfunctions.phpでエンキューされます -->

    <!-- Ultra Stylish Minimalist Footer -->
    <footer class="site-footer bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-12">
            
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
                <!-- Brand Section -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <?php if (has_custom_logo()): ?>
                            <?php the_custom_logo(); ?>
                        <?php endif; ?>
                        <div>
                            <h3 class="font-bold text-xl text-black"><?php bloginfo('name'); ?></h3>
                            <p class="text-sm text-gray-600"><?php bloginfo('description'); ?></p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed max-w-md">
                        日本全国の助成金・補助金情報を一元化し、最適な支援制度を見つけるお手伝いをします。
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold text-black mb-4">クイックリンク</h4>
                    <nav class="space-y-2">
                        <a href="<?php echo esc_url(home_url()); ?>" class="block text-gray-600 hover:text-black transition-colors text-sm">ホーム</a>
                        <a href="<?php echo esc_url(home_url('/how-to-use/')); ?>" class="block text-gray-600 hover:text-black transition-colors text-sm">使い方ガイド</a>
                        <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="block text-gray-600 hover:text-black transition-colors text-sm">助成金検索</a>
                        <a href="<?php echo esc_url(home_url('/about')); ?>" class="block text-gray-600 hover:text-black transition-colors text-sm">サービスについて</a>
                        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="block text-gray-600 hover:text-black transition-colors text-sm">お問い合わせ</a>
                    </nav>
                </div>

                <!-- Legal Links -->
                <div>
                    <h4 class="font-semibold text-black mb-4">法的情報</h4>
                    <nav class="space-y-2">
                        <a href="<?php echo esc_url(home_url('/privacy')); ?>" class="block text-gray-600 hover:text-black transition-colors text-sm">プライバシーポリシー</a>
                        <a href="<?php echo esc_url(home_url('/terms')); ?>" class="block text-gray-600 hover:text-black transition-colors text-sm">利用規約</a>
                        <a href="<?php echo esc_url(home_url('/faq')); ?>" class="block text-gray-600 hover:text-black transition-colors text-sm">よくある質問</a>
                    </nav>
                </div>
            </div>

            <!-- Stats Section -->
            <?php
            $stats = gi_get_cached_stats();
            if ($stats && !empty($stats['total_grants'])):
            ?>
            <div class="flex flex-wrap justify-center gap-8 py-8 border-t border-b border-gray-100">
                <div class="text-center">
                    <div class="text-2xl font-bold text-black"><?php echo number_format($stats['total_grants']); ?></div>
                    <div class="text-sm text-gray-600">掲載助成金数</div>
                </div>
                <?php if (!empty($stats['active_grants'])): ?>
                <div class="text-center">
                    <div class="text-2xl font-bold text-black"><?php echo number_format($stats['active_grants']); ?></div>
                    <div class="text-sm text-gray-600">募集中</div>
                </div>
                <?php endif; ?>
                <div class="text-center">
                    <div class="text-2xl font-bold text-black">47</div>
                    <div class="text-sm text-gray-600">都道府県対応</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Copyright -->
            <div class="text-center pt-8">
                <p class="text-sm text-gray-500">
                    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 w-12 h-12 bg-black text-white rounded-full shadow-lg opacity-0 invisible transition-all duration-300 hover:bg-gray-800 z-50">
        <i class="fas fa-arrow-up text-sm"></i>
    </button>

    <script>
    // Back to Top functionality
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopBtn = document.getElementById('back-to-top');
        
        function updateBackToTop() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.remove('opacity-0', 'invisible');
                backToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                backToTopBtn.classList.add('opacity-0', 'invisible');
                backToTopBtn.classList.remove('opacity-100', 'visible');
            }
        }
        
        // Initial check
        updateBackToTop();
        
        // Update on scroll (passive for better performance)
        window.addEventListener('scroll', updateBackToTop, { passive: true });
        
        // Smooth scroll to top on click
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
